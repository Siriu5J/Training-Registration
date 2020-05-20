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
        $this->content->new_event();
        // Create new table for the new event and fill first data
        if($_POST['create_training']) {
            $event_name     = $_POST['event-name'];
            $location       = $_POST['location'];
            $limit_max      = (int)$_POST['max-limit'];
            $max            = $_POST['max'];

            if($max == 0 && $limit_max == '1') {
                add_action('admin_notices', array($this->admin_notice, 'createEventNotAllowed'));
            } elseif ($this->tools->isValidEvent($event_name, $location)) { // Check if the training name is valid
                $open_time  =   $_POST['open-date'];
                $close_time =   $_POST['close-date'];
                $start_time =   $_POST['start-date'];
                $end_time   =   $_POST['end-date'];
                $activated  =   (int)$_POST['activated'];

                $comment    =   $_POST['comment'];

                // If max is unfilled, set as -999
                if ($max == 0) {
                    $max = -999;
                }

                $success = $wpdb->insert(ER_EVENT_LIST, array(
                    "event_name"    =>  $event_name,
                    "max"           =>  $max,
                    "open_time"     =>  $open_time,
                    "close_time"    =>  $close_time,
                    "start_time"    =>  $start_time,
                    "end_time"      =>  $end_time,
                    "location"      =>  $location,
                    "limit_max"     =>  $limit_max,
                    "comment"       =>  $comment,
                    "activated"     =>  $activated,
                    "num_reg"       =>  0,
                ));

                if ($success) {add_action('admin_notices', array($this->admin_notice, 'tableSuccessCreation'));}
                else {add_action('admin_notices', array($this->admin_notice, 'tableFailedCreation'));}
            } else {
                add_action('admin_notices', array($this->admin_notice, 'tableAlreadyExist'));
            }
        }
    }
}


