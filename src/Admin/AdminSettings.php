<?php

namespace SOT\TrainingRegistration\Admin;

use SOT\TrainingRegistration\Data\Repositories\EventRepository;
use SOT\TrainingRegistration\Data\Repositories\StaffRepository;
use SOT\TrainingRegistration\Data\Repositories\RegistrationRepository;
use SOT\TrainingRegistration\Data\Strategies\RegistrationModeFactory;
use SOT\TrainingRegistration\Core\Tools;
use SOT\TrainingRegistration\Core\PageCreator;
use SOT\TrainingRegistration\Admin\HomeTable;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * Class AdminSettings
 *
 * This class contains all the callback functions for the admin menu settings page.
 *
 * @package SOT\TrainingRegistration\Admin
 */
class AdminSettings {
    // Objects
    protected $tools;           // Some helpful tools
    protected $content;         // All the html contents are stored here
    protected $admin_notice;    // All admin notices are stored here

    /** @var \SOT\TrainingRegistration\Data\Repositories\EventRepository */
    protected $event_repo;
    /** @var \SOT\TrainingRegistration\Data\Repositories\StaffRepository */
    protected $staff_repo;
    /** @var \SOT\TrainingRegistration\Data\Repositories\RegistrationRepository */
    protected $registration_repo;

    protected $home_table;

    // Constructor. Instantiates the protected variables
    public function __construct() {
        $this->tools = new Tools();
        $this->content = new SettingsPageContent();
        $this->admin_notice = new AdminMessages();

        $this->event_repo = new EventRepository();
        $this->staff_repo = new StaffRepository();
        $this->registration_repo = new RegistrationRepository();
    }

    // Registers the settings items into WordPress
    public function adminSettingsPageRegistration() {
        $parent_slug = 'er_gen_set';
        $main_page = add_menu_page('Training Registration', 'Training Registration', 'edit_plugins', $parent_slug, array($this, 'erSettingsPage'), 'dashicons-id-alt', 4);
        $new_event_page = add_submenu_page($parent_slug, 'Create Training', 'Create Training', 'edit_plugins', 'er_new_event_set', array($this, 'erNewEvent'));
        $view_reg_page = add_submenu_page($parent_slug, 'View Trainings', 'View Registrations', 'edit_plugins', 'er_view_reg_set', array($this, 'erViewEvent'));
        $settings_page = add_submenu_page($parent_slug, 'Settings', 'Settings', 'edit_plugins', 'er_settings', array($this, 'erSettings') );

        // Handle POST actions before headers are sent
        add_action("load-$main_page", array($this, 'handleOverviewPageActions'));
        add_action("load-$new_event_page", array($this, 'handleEventSubmission'));
        add_action("load-$settings_page", array($this, 'handleSettingsPageActions'));

        // Properly enqueue styles for specific pages
        add_action("admin_print_styles-$main_page", array($this, 'load_home_style'));
        add_action("admin_print_styles-$new_event_page", array($this, 'enqueue_new_training_CSS'));
        add_action("admin_print_styles-$view_reg_page", array($this, 'load_home_style'));
    }

    public function handleOverviewPageActions() {
        if (isset($_POST['confirm_remove']) && wp_verify_nonce($_POST['remove_training_nonce_field'], 'remove_training_nonce')) {
            $removal_id = intval($_POST['removal-id']);
            $this->event_repo->delete($removal_id);
            $this->registration_repo->delete_by_event($removal_id);
            
            wp_safe_redirect(admin_url('admin.php?page=er_gen_set&deleted=true'));
            exit;
        }

        if (($_GET['deleted'] ?? '') === 'true') {
            add_action('admin_notices', array($this->admin_notice, 'tableSuccessDeletion'));
        }

        if (($_GET['created'] ?? '') === 'true') {
            add_action('admin_notices', array($this->admin_notice, 'tableSuccessCreation'));
        }
    }

    public function handleSettingsPageActions() {
        if (isset($_POST['create-page']) && wp_verify_nonce($_POST['create_page_nonce_field'], 'create_page_nonce')) {
            (new PageCreator())->run();
            wp_safe_redirect(admin_url('admin.php?page=er_settings&pages-created=true'));
            exit;
        }

        if (($_GET['pages-created'] ?? '') === 'true') {
            add_action('admin_notices', array($this->admin_notice, 'pagesCreated'));
        }
    }

    /**
     * Register Settings API fields
     */
    public function registerSettingsFields() {
        register_setting('er_settings_group', 'show_availability');
        register_setting('er_settings_group', 'my_mode');

        add_settings_section(
            'er_general_settings_section',
            'General Settings',
            null,
            'er_gen_set'
        );

        add_settings_field(
            'show_availability',
            'Show Available Seats',
            array($this, 'renderShowAvailabilityField'),
            'er_gen_set',
            'er_general_settings_section'
        );

        add_settings_field(
            'my_mode',
            'Enable SOTAM Forms',
            array($this, 'renderMyModeField'),
            'er_gen_set',
            'er_general_settings_section'
        );
    }

    public function renderShowAvailabilityField() {
        $val = get_option('show_availability');
        ?>
        <fieldset>
            <label for="show_availability">
                <input type="checkbox" name="show_availability" id="show_availability" value="1" <?php checked(1, $val); ?>>
                Disabling this option will hide the number of seats remaining in a training to schools.
            </label>
        </fieldset>
        <?php
    }

    public function renderMyModeField() {
        $val = get_option('my_mode');
        ?>
        <fieldset>
            <label for="my_mode">
                <input type="checkbox" name="my_mode" id="my_mode" value="1" <?php checked(1, $val); ?>>
                Enable SOTAM requested form formats.
            </label>
        </fieldset>
        <?php
    }

    // Registers the required CSS
    public function enqueue_new_training_CSS() {
        wp_enqueue_style('new_training_style', plugins_url('../../assets/css/admin/add_new_training_styles.css', __FILE__));
    }

    public function load_home_style() {
        wp_enqueue_style('home_styles', plugins_url('../../assets/css/admin/home_styles.css', __FILE__));
    }


    /**
     * MAIN SETTINGS PAGE
     */
    public function erSettingsPage() {
        // Add capability check
        if (!current_user_can('edit_plugins')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // The home table
        $this->home_table = new HomeTable();
        $this->home_table->prepare_items();

        // Get stats for the dashboard
        $stats = array(
            'total_events'   => $this->event_repo->get_total_count(),
            'upcoming_events' => $this->event_repo->get_count_upcoming(),
            'open_events'     => $this->event_repo->get_count_open(),
            'total_staff'    => $this->staff_repo->get_total_count(),
            'total_reg'      => $this->registration_repo->get_total_count(),
            'recent_reg'     => $this->registration_repo->get_recent_count(30),
        );

        $this->content->overview($this->home_table, $stats);
    }

    public function erNewEvent() {
        if (!current_user_can('edit_plugins')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $id = isset($_GET['event-id']) ? intval($_GET['event-id']) : -1;
        $data = ($id !== -1) ? $this->event_repo->get_by_id($id) : null;

        if ($id !== -1 && !$data) {
            wp_die(__('Training event not found.'));
        }

        $data ??= (object) [
            'id'            => -1,
            'event_name'    => '',
            'max'           => '',
            'open_time'     => date("Y-m-d\TH:i", current_time('timestamp')),
            'close_time'    => date("Y-m-d\TH:i", strtotime("+15 days", current_time('timestamp'))),
            'start_time'    => date("Y-m-d\TH:i", strtotime("+30 days", current_time('timestamp'))),
            'end_time'      => date("Y-m-d\TH:i", strtotime("+37 days", current_time('timestamp'))),
            'location'      => '',
            'limit_max'     => 0,
            'activated'     => 1,
            'comment'       => '',
            'num_reg'       => 0
        ];

        $this->content->new_event($data, $this->tools);
    }

    public function handleEventSubmission() {
        $nonce = $_POST['training_nonce_field'] ?? '';
        $is_create = isset($_POST['create_training']) && wp_verify_nonce($nonce, 'create_training_nonce');
        $is_edit = isset($_POST['submit_edit']) && wp_verify_nonce($nonce, 'edit_training_nonce');

        if (!$is_create && !$is_edit) {
            return;
        }

        $max = intval($_POST['max']);
        $open_date = $_POST['open-date'];
        $close_date = $_POST['close-date'];
        $start_date = $_POST['start-date'];
        $end_date = $_POST['end-date'];
        $limit_max = isset($_POST['max-limit']) ? 1 : 0;
        $event_name = sanitize_text_field($_POST['event-name']);
        $location = sanitize_text_field($_POST['location']);

        // Validations
        if ($max < 0) {
            add_action('admin_notices', array($this->admin_notice, 'invalidMaxTrainee'));
            return;
        }

        if (strtotime($close_date) <= strtotime($open_date) || strtotime($end_date) <= strtotime($start_date)) {
            add_action('admin_notices', array($this->admin_notice, 'invalidTimeOrder'));
            return;
        }

        if ($max === 0 && $limit_max === 1) {
            add_action('admin_notices', array($this->admin_notice, 'createEventNotAllowed'));
            return;
        }

        if (!$this->tools->isValidEvent($event_name, $location, $start_date, $end_date, intval($_POST['event-id']))) {
            add_action('admin_notices', array($this->admin_notice, 'tableAlreadyExist'));
            return;
        }

        $event_data = [
            "event_name"    => $event_name,
            "max"           => ($max === 0) ? -999 : $max,
            "open_time"     => $open_date,
            "close_time"    => $close_date,
            "start_time"    => $start_date,
            "end_time"      => $end_date,
            "location"      => $location,
            "limit_max"     => $limit_max,
            "comment"       => sanitize_textarea_field($_POST['comment']),
            "activated"     => isset($_POST['activated']) ? 1 : 0,
        ];

        if ($is_edit) {
            $this->event_repo->update(intval($_POST['event-id']), $event_data);
            add_action('admin_notices', array($this->admin_notice, 'tableSuccessUpdate'));
            return;
        }

        $event_data["num_reg"] = 0;
        if ($this->event_repo->insert($event_data)) {
            wp_safe_redirect(admin_url('admin.php?page=er_gen_set&created=true'));
            exit;
        }

        add_action('admin_notices', array($this->admin_notice, 'tableFailedCreation'));
    }

    /**
     * VIEW REGISTRATION PAGE
     */
    public function erViewEvent() {
        // Add capability check
        if (!current_user_can('edit_plugins')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (isset($_GET['event-id'])) {
            $this->content->view_event($this->tools, get_option('my_mode'), intval($_GET['event-id']));
        } else {
            $this->content->manage_reg($this->tools, get_option('my_mode'));
        }


    }

    /**
     * VIEW SETTINGS PAGE
     */
    public function erSettings() {
        // Add capability check
        if (!current_user_can('edit_plugins')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $this->content->view_settings();
    }

    public function exportRegistrationsToExcel() {
        if (($_GET['print-excel'] ?? '') !== "true") {
            return;
        }

        if (!wp_verify_nonce($_GET['nonce'] ?? '', 'excel_export_nonce')) {
            wp_die(__('Invalid nonce.'));
        }

        if (!current_user_can('edit_plugins')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $event_id = intval($_GET['id']);
        $event_info = $this->event_repo->get_by_id($event_id);

        if (!$event_info) {
            wp_die(__('Event not found.'));
        }

        $mode_strategy = RegistrationModeFactory::get_current_mode();
        $registrations = $this->registration_repo->get_by_event($event_id);
        $data_array = [];

        global $wpdb;

        foreach ($registrations as $trainee) {
            $trainee_data = $this->staff_repo->get_by_id($trainee->staff);
            $reg_time = date("F j", strtotime($trainee->reg_time));

            // Get school nickname
            $school_id = $wpdb->get_var($wpdb->prepare("SELECT `ID` FROM $wpdb->users WHERE `user_login` = %s", $trainee_data->school));
            $school_nick = $wpdb->get_var($wpdb->prepare("SELECT `meta_value` FROM $wpdb->usermeta WHERE `user_id` = %d AND `meta_key` = %s", $school_id, 'nickname'));

            $data_array[] = $mode_strategy->get_staff_fields($trainee_data, $reg_time, $school_nick);
        }

        $template_file = $mode_strategy->get_excel_template();
        $output_filename = "{$event_info->event_name}_{$event_info->location}_" . date("Y-m-d", strtotime($event_info->start_time)) . '.xlsx';

        $registration_sheet = IOFactory::load($template_file);
        $data_sheet = $registration_sheet->getActiveSheet();

        $data_sheet->fromArray($data_array, null, 'A2');
        $data_sheet->getStyle($mode_strategy->get_excel_column_format())->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $output_filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        IOFactory::createWriter($registration_sheet, 'Xlsx')->save('php://output');
        exit();
    }
}
