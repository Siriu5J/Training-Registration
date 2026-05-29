<?php

/**
 * Class training_registration_acp
 *
 * This class contains all the callback functions for the admin menu settings page. However, the HTML contents are not stored here.
 * This class uses the tools library as well
 *
 * @since 2020-5-19
 * @version 1.2
 *
 * @package training-registration
 */

use PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class training_registration_acp {
    // Objects
    protected $tools;           // Some helpful tools
    protected $content;         // All the html contents are stored here
    protected $admin_notice;    // All admin notices are stored here

    protected $home_table;

    // Constructor. Instantiates the protected variables
    public function __construct() {
        require_once(ER_PLUGIN_DIR . '/includes/tools.php');
        require_once(ER_PLUGIN_DIR . '/admin/settings_page_content.php');
        require_once(ER_PLUGIN_DIR . '/admin/admin_messages.php');

        $this->tools = new training_registration_tools();
        $this->content = new settings_page_content();
        $this->admin_notice = new admin_messages();
    }

    // Registers the settings items into WordPress
    public function adminSettingsPageRegistration() {
        add_menu_page('Training Registration', 'Training Registration', 'edit_plugins', 'er_gen_set', array($this, 'erSettingsPage'), 'dashicons-id-alt', 4);
        add_submenu_page('er_gen_set', 'Create Training', 'Create Training', 'edit_plugins', 'er_new_event_set', array($this, 'erNewEvent'));
        add_submenu_page('er_gen_set', 'View Trainings', 'View Registrations', 'edit_plugins', 'er_view_reg_set', array($this, 'erViewEvent'));
        add_submenu_page('er_gen_set', 'Settings', 'Settings', 'edit_plugins', 'er_settings', array($this, 'erSettings') );

        register_setting('reading', 'show_availability');
        register_setting('reading', 'my_mode');
    }

    // Registers the required CSS
    public function enqueue_new_training_CSS() {
        wp_enqueue_style('new_training_style', plugins_url('stylesheet/add_new_training_styles.css', __FILE__));
    }

    public function enqueue_home_CSS() {
        wp_enqueue_style('home_styles', plugins_url('stylesheet/home_styles.css', __FILE__));
    }


    /**
     * MAIN SETTINGS PAGE
     */
    public function erSettingsPage() {
        // Add capability check
        if (!current_user_can('edit_plugins')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        global $wpdb;

        // Inject CSS
        add_action('admin_enqueue_scripts', $this->enqueue_home_CSS(), 5);

        //TODO: Add screen options

        //Handle Training Delete
        if (isset($_POST['confirm_remove']) && wp_verify_nonce($_POST['remove_training_nonce_field'], 'remove_training_nonce')) {
            $removal_id = intval($_POST['removal-id']);
            $wpdb->delete(ER_EVENT_LIST, array(
                'id'    => $removal_id
            ));
            $wpdb->delete(ER_REGISTRATION_LIST, array(
                'event_id'  => $removal_id
            ));
        }

        // The home table
        if (!class_exists('admin_home_table')) {
            require_once(ER_PLUGIN_DIR . '/admin/admin_home_table.php');
        }
        $this->home_table = new admin_home_table();

        $this->content->overview($this->home_table);
    }

    /**
     * CREATE TRAINING PAGE
     */
    public function erNewEvent() {
        // Add capability check
        if (!current_user_can('edit_plugins')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // Inject CSS
        add_action('admin_enqueue_scripts', $this->enqueue_new_training_CSS(), 5);

        global $wpdb;
        // Create new table for the new event and fill first data
        if((isset($_POST['create_training']) && wp_verify_nonce($_POST['training_nonce_field'], 'create_training_nonce')) ||
           (isset($_POST['submit_edit']) && wp_verify_nonce($_POST['training_nonce_field'], 'edit_training_nonce'))) {
            // Sanitize inputs
            $event_name     = sanitize_text_field($_POST['event-name']);
            $location       = sanitize_text_field($_POST['location']);
            $start_date     = sanitize_text_field($_POST['start-date']);
            $limit_max      = intval($_POST['max-limit']);
            $max            = sanitize_text_field($_POST['max']);
            $event_id       = intval($_POST['event-id']);
            $open_date      = sanitize_text_field($_POST['open-date']);
            $close_date     = sanitize_text_field($_POST['close-date']);
            $end_date       = sanitize_text_field($_POST['end-date']);
            $comment        = sanitize_textarea_field($_POST['comment']);
            $activated      = intval($_POST['activated']);

            if($max == 0 && $limit_max == 1) {
                add_action('admin_notices', $this->admin_notice->createEventNotAllowed());
            } elseif ($this->tools->isValidEvent($event_name, $location, $start_date, $event_id)) { // Check if the training name is valid
                // If max is unfilled, set as -999
                if ($max == 0) {
                    $max = -999;
                }

                if (isset($_POST['create_training'])) {
                    $success = $wpdb->insert(ER_EVENT_LIST, array(
                        "event_name"    =>  $event_name,
                        "max"           =>  $max,
                        "open_time"     =>  $open_date,
                        "close_time"    =>  $close_date,
                        "start_time"    =>  $start_date,
                        "end_time"      =>  $end_date,
                        "location"      =>  $location,
                        "limit_max"     =>  $limit_max,
                        "comment"       =>  $comment,
                        "activated"     =>  $activated,
                        "num_reg"       =>  0,
                    ));

                    if ($success) {add_action('admin_notices', $this->admin_notice->tableSuccessCreation());}
                    else {add_action('admin_notices', $this->admin_notice->tableFailedCreation());}
                } else {
                    $wpdb->update(ER_EVENT_LIST, array(
                        "event_name"    =>  $event_name,
                        "max"           =>  $max,
                        "open_time"     =>  $open_date,
                        "close_time"    =>  $close_date,
                        "start_time"    =>  $start_date,
                        "end_time"      =>  $end_date,
                        "location"      =>  $location,
                        "limit_max"     =>  $limit_max,
                        "comment"       =>  $comment,
                        "activated"     =>  $activated,
                    ), array(
                        'id'    =>  $event_id
                    ));

                    add_action('admin_notices', $this->admin_notice->tableSuccessUpdate());
                }
            } else {
                add_action('admin_notices', $this->admin_notice->tableAlreadyExist());
            }
        }

        // Check if user came here to view the training
        if (isset($_GET['view-event'])) {
            $id = intval($_GET['event-id']);
            $data = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".ER_EVENT_LIST." WHERE `id` = %d", $id));
            $this->content->new_event($data, $this->tools);
        } else {
            // If the user is here to create new event, send preset data to produce an empty training form
            $default_empty = array(
                'id'            =>  -1,
                'event_name'    =>  '',
                'max'           =>  '',
                'open_time'     =>  date("Y-m-d\TH:i", mktime(0,0)),
                'close_time'    =>  date("Y-m-d\TH:i", strtotime("+31 days" ,mktime(0,0))),
                'start_time'    =>  date("Y-m-d\TH:i", strtotime("+31 days" ,mktime(0,0))),
                'end_time'      =>  date("Y-m-d\TH:i", strtotime("+31 days" ,mktime(0,0))),
                'location'      =>  '',
                'limit_max'     =>  0,
                'activated'     =>  1,
                'comment'       =>  '',
                'num_reg'       =>  0
            );

            $this->content->new_event((object) $default_empty, $this->tools);
        }


    }

    /**
     * VIEW REGISTRATION PAGE
     */
    public function erViewEvent() {
        // Add capability check
        if (!current_user_can('edit_plugins')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        global $wpdb;

        if (isset($_GET['event-id'])) {
            $this->content->view_event($this->tools, get_option('my_mode'), $_GET['event-id']);
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

        if ($_POST['save-settings']) {
            // Update show available
            if ($_POST['show-available'] == 1) {
                update_option( 'show_availability', 1);
            } else {
                update_option( 'show_availability', 0);
            }
            // Update MY mode
            if ($_POST['enable-my'] == 1) {
                update_option('my_mode', 1);
            } else {
                update_option('my_mode', 0);
            }

            add_action('admin_notices', $this->admin_notice->settingsUpdated());
        }

        if ($_POST['create-page']) {
            require_once(ER_PLUGIN_DIR . '/includes/create_page.php');
            $creator = new create_page();
            $creator->run();
        }

        $availability = get_option('show_availability');
        $my_mode = get_option('my_mode');
        $this->content->view_settings($availability, $my_mode);
    }
}

/**
 * Download to Excel
 * I couldn't find another place tp store this because of the header issues.
 * This function migrated from PHPExcel to PHPSpreadsheet
 */
global $wpdb;
if (isset($_GET['print-excel']) && $_GET['print-excel'] == "true" && wp_verify_nonce($_GET['nonce'], 'excel_export_nonce')) {
    // Add capability check for Excel export
    if (!current_user_can('edit_plugins')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    // Load PHP Spreadsheet
    // Only load our autoloader if the class doesn't already exist.
    if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
        require_once(ER_PLUGIN_DIR . '/vendor/autoload.php');
    }

    $my_mode = (int)$_GET['mode'];

    $registration_list = ER_REGISTRATION_LIST;
    $event_list        = ER_EVENT_LIST;
    $staff_profile     = ER_STAFF_PROFILE;
    $event_id          = intval($_GET['id']);
    $registrations     = $wpdb->get_results($wpdb->prepare("SELECT * FROM $registration_list WHERE `event_id` = %d", $event_id));
    $event_info        = $wpdb->get_row($wpdb->prepare("SELECT * FROM $event_list WHERE id = %d", $event_id));
    $data_array        = array();

    foreach($registrations as $trainee) {
        $trainee_id     = $trainee->staff;
        $reg_time       = date("F j", strtotime($trainee->reg_time));
        $trainee_data   = $wpdb->get_row($wpdb->prepare("SELECT * FROM $staff_profile WHERE id = %d", $trainee_id));

        // Get school nickname
        $school_id		= $wpdb->get_var($wpdb->prepare("SELECT `ID` FROM $wpdb->users WHERE `user_login` = %s", $trainee_data->school));
        $school_nick	= $wpdb->get_var($wpdb->prepare("SELECT `meta_value` FROM $wpdb->usermeta WHERE `user_id` = %d AND `meta_key` = %s", $school_id, 'nickname'));

        if ($my_mode == 1) {
            $data_array[] = array(
                $reg_time,
                $trainee_data->first_name,
                $trainee_data->last_name,
                $trainee_data->mid_name,
                $trainee_data->sex,
                $trainee_data->cn_name,
                $school_nick,
                $trainee_data->school,
                $trainee_data->phone,
                $trainee_data->pos,
                $trainee_data->grad_year,
                stripslashes($trainee_data->lc),
                stripslashes($trainee_data->degree),
                $trainee_data->comment

            );
        } else {
            $data_array[] = array(
                $reg_time,
                $trainee_data->first_name,
                $trainee_data->last_name,
                $trainee_data->cn_name,
                $trainee_data->sex,
                $trainee_data->age,
                $school_nick,
                $trainee_data->school,
                $trainee_data->email,
                $trainee_data->phone,
                $trainee_data->pos,
                $trainee_data->lc,
                $trainee_data->training_exp,
                $trainee_data->cec_exp,
                $trainee_data->degree,
                $trainee_data->grad_year,
                $trainee_data->major,
                $trainee_data->minor,
                $trainee_data->institution,
                $trainee_data->comment

            );
        }
    }

    error_log(implode(', ', $data_array));

    // Set filenames
    if ($my_mode == 1) {
        $template_file = ER_PLUGIN_DIR . '/files/SOTAM_Excel_Template.xlsx';
    } else {
        $template_file = ER_PLUGIN_DIR . '/files/Default_Excel_Template.xlsx';
    }
    $output_filename = $event_info->event_name . '_' . $event_info->location . '_' . date("Y-m-d", strtotime($event_info->start_time)) . '.xlsx';

    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);

    // Read the template registration form
    $registration_sheet = IOFactory::load($template_file);

    // Get the first sheet
    $data_sheet = $registration_sheet->getActiveSheet();

    // Write data
    $data_sheet->fromArray($data_array, null, 'A2');
    $data_sheet->getStyle('A2:N2')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

    // Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'. $output_filename .'"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = IOFactory::createWriter($registration_sheet, IOFactory::WRITER_XLSX);
    $objWriter->save('php://output');
    exit();
}
