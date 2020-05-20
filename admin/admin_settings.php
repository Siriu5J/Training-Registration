<?php
class training_registration_acp {
    // Objects
    protected $tools;           // Some helpful tools
    protected $content;         // All the html contents are stored here
    protected $admin_notice;    // All admin notices are stored here

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
        add_submenu_page('er_gen_set', 'View Trainings', 'View Trainings', 'edit_plugins', 'er_event_view_set', array($this, 'erViewEvent'));
        add_submenu_page('er_gen_set', 'Manage Registrations', 'Manage Registrations', 'edit_plugins', 'er_event_view_reg', array($this, 'erViewEventReg'));
        add_submenu_page('er_gen_set', 'Settings', 'Settings', 'edit_plugins', 'er_settings', array($this, 'erSettings') );

        register_setting('reading', 'show_availability');
    }

    /**
     * MAIN SETTINGS PAGE
     */
    public function erSettingsPage() {
        $this->content->overview();
    }

    /**
     * CREATE TRAINING PAGE
     */
    public function erNewEvent() {
        global $wpdb;
        // Create new table for the new event and fill first data
        if($_POST['create_training']) {
            $event_name     = $_POST['event-name'];
            $location       = $_POST['location'];
            $start_date     = $_POST['start-date'];
            $limit_max      = (int)$_POST['max-limit'];
            $max            = $_POST['max'];

            if($max == 0 && $limit_max == '1') {
                add_action('admin_notices', $this->admin_notice->createEventNotAllowed());
            } elseif ($this->tools->isValidEvent($event_name, $location, $start_date)) { // Check if the training name is valid
                // If max is unfilled, set as -999
                if ($max == 0) {
                    $max = -999;
                }

                $success = $wpdb->insert(ER_EVENT_LIST, array(
                    "event_name"    =>  $_POST['event-name'],
                    "max"           =>  $max,
                    "open_time"     =>  $_POST['open-date'],
                    "close_time"    =>  $_POST['close-date'],
                    "start_time"    =>  $start_date,
                    "end_time"      =>  $_POST['end-date'],
                    "location"      =>  $_POST['location'],
                    "limit_max"     =>  $limit_max,
                    "comment"       =>  $_POST['comment'],
                    "activated"     =>  (int)$_POST['activated'],
                    "num_reg"       =>  0,
                ));

                if ($success) {add_action('admin_notices', $this->admin_notice->tableSuccessCreation());}
                else {add_action('admin_notices', $this->admin_notice->tableFailedCreation());}
            } else {
                add_action('admin_notices', $this->admin_notice->tableAlreadyExist());
            }
        }

        $this->content->new_event();
    }

    /**
     * VIEW TRAINING PAGE
     */
    public function erViewEvent() {
        global $wpdb;

        // Second Confirmation. Remove the training and its registration records
        if ($_POST['remove-2']) {
            $wpdb->delete(ER_EVENT_LIST, array(
                'id'    => $_POST['removal_id']
            ));
            $wpdb->delete(ER_REGISTRATION_LIST, array(
                'event_id'  => $_POST['removal_id']
            ));
        }

        // Flip activation stat using CURRENT_STAT XOR 1
        if ($_POST['confirm-activation']) {
            $event_table = ER_EVENT_LIST;
            $id = $_POST['select'];
            $wpdb->update($event_table, array(
                'activated' => (int)$wpdb->get_var("SELECT `activated` FROM $event_table WHERE `id` = $id") ^ 1
            ), array(
                'id' => $id
            ));
        }

        // Update training after edit
        if ($_POST['confirm-update']) {

            $event_name     = $_POST['event-name'];
            $location       = $_POST['location'];
            $start_date     = $_POST['start-date'];
            $limit_max      = (int)$_POST['max-limit'];
            $max            = $_POST['max'];

            if($max == 0 && $limit_max == '1') {
                add_action('admin_notices', $this->admin_notice->createEventNotAllowed());
            } elseif ($this->tools->isValidEvent($event_name, $location, $start_date)) { // Check if the training name is valid
                // If max is unfilled, set as -999
                if ($max == 0) {
                    $max = -999;
                }

                $wpdb->update(ER_EVENT_LIST, array(
                    "event_name"    =>  $_POST['update-event-name'],
                    "max"           =>  $max,
                    "open_time"     =>  $_POST['update-open-date'],
                    "close_time"    =>  $_POST['update-close-date'],
                    "start_time"    =>  $_POST['update-start-date'],
                    "end_time"      =>  $_POST['update-end-date'],
                    "location"      =>  $location,
                    "limit_max"     =>  $limit_max,
                    "comment"       =>  $_POST['update-comment'],
                ), array(
                    "id"            =>  $_POST['update-event_id'],
                ));
                add_action('admin_notices', $this->admin_notice->tableSuccessUpdate());
            } else {
                add_action('admin_notices', $this->admin_notice->tableFailedUpdate());
            }
        }

        $this->content->view_event($this->tools);
    }

    /**
     * MANAGE REGISTRATION PAGE
     */
    public function erViewEventReg() {

        $this->content->manage_reg($this->tools);
    }

    /**
     * VIEW SETTINGS PAGE
     */
    public function erSettings() {
        if ($_POST['save-settings']) {
            if ($_POST['show-available'] == 1) {
                update_option( 'show_availability', 1);
            } else {
                update_option( 'show_availability', 0);
            }

            add_action('admin_notices', 'settingsUpdated');
        }

        $this->content->view_settings();
    }
}
global $wpdb;

// Download Excel form
// Generates xlsx
// Library from PHPExcel
if (isset($_POST['download-xls'])) {
    $data_array = array(
        array(
            'Registration Time',
            'First Name',
            'Last Name',
            'Name in Native Language',
            'Sex/Gender',
            'Age',
            'School',
            'School Username',
            'Email',
            'Phone',
            'Position in LC',
            'LC',
            '# of trainings attended',
            '# of CEC attended',
            'Highest Degree',
            'Year of Graduation',
            'Major',
            'Minor',
            'Institution',
            'Comment'
        )
    );

    $registration_list = ER_REGISTRATION_LIST;
    $event_list        = ER_EVENT_LIST;
    $staff_profile     = ER_STAFF_PROFILE;
    $event_id          = $_POST['event-id'];
    $registrations     = $wpdb->get_results("SELECT * FROM $registration_list WHERE `event_id` = $event_id");
    $event_info        = $wpdb->get_row("SELECT * FROM $event_list WHERE id = $event_id");
    $worksheet_name    = $event_info->event_name . ' at ' . $event_info->location . ' ' . date("Y", strtotime($event_info->start_time));

    foreach($registrations as $trainee) {
        $trainee_id     = $trainee->staff;
        $reg_time       = date("F j", strtotime($trainee->reg_time));
        $trainee_data   = $wpdb->get_row("SELECT * FROM $staff_profile WHERE id = $trainee_id");

        // Get school nickname
        $school_id		= $wpdb->get_var("SELECT `ID` FROM $wpdb->users WHERE `user_login` = '$trainee_data->school'");
        $school_nick	= $wpdb->get_var("SELECT `meta_value` FROM $wpdb->usermeta WHERE `user_id` = $school_id AND `meta_key` = 'nickname'");

        array_push($data_array, array(
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

        ));
    }

    // Fix the issue of Excel not being able to generate excel when there's only one registration by pushing an empty row to the array
    array_push($data_array, array(""));

    $filename = 'Training_Registration_' . $event_info->location . '_' . date("Y-m-d", strtotime($event_info->start_time)) . 'xls';

    require_once(ER_PLUGIN_DIR . '/lib/PHPExcel.php');
    $objPHPExcel = new PHPExcel();
    // Set Properties
    $objPHPExcel->getProperties()->setCreator("Training Registration Plugin")
        ->setTitle($filename);

    // Write data
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setTitle($worksheet_name);
    $objPHPExcel->getActiveSheet()->fromArray($data_array, null, 'A1');

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Cache-Control: no-store, no-cache");
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header('Content-Disposition: attachment;filename="'. $filename .'"');
    header ('Pragma: public'); // HTTP/1.0
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

    $objWriter->save('php://output');

    exit();
}