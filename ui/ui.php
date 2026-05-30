<?php

/**
 * Class training_registration_ui
 *
 * This class handles the frontend shortcode logic using templates.
 *
 * @since 2019-12
 * @version 2.2
 *
 * @package training-registration
 */
class training_registration_ui {
    use TemplateRenderer;

    // Helpful tools
    protected $tools;
    protected $ui_content;

    protected $event_repo;
    protected $staff_repo;
    protected $registration_repo;

    public function __construct() {
        require_once(ER_PLUGIN_DIR . '/includes/tools.php');
        require_once(ER_PLUGIN_DIR . '/ui/ui_content.php');
        $this->tools = new training_registration_tools();
        $this->ui_content = new ui_content();

        $this->event_repo = new EventRepository();
        $this->staff_repo = new StaffRepository();
        $this->registration_repo = new RegistrationRepository();

        // Add CSS
        add_action('wp_enqueue_scripts', array($this, 'enqueue_ui_css'));
    }

    public function enqueue_ui_css() {
        wp_enqueue_style('ui_styles', plugins_url('stylesheet/ui.css', __FILE__));
        wp_enqueue_style('ui_styles');
    }

    /*
    STAFF PROFILE REGISTRATION FORM
     */
    public function staffFormCreation() {
        $username = wp_get_current_user()->user_login;  // Get Current username for school name
        $mode_strategy = RegistrationModeFactory::get_current_mode();

        // Handle staff creation
        if (isset($_POST['create_staff']) && wp_verify_nonce($_POST['staff_nonce_field'], 'create_staff_nonce')) {
            $result = $mode_strategy->handle_staff_creation($_POST);

            if (is_wp_error($result)) {
                $this->render('ui/notice', array('type' => 'red', 'message' => $result->get_error_message()));
            } elseif ($result) {
                $first_name = sanitize_text_field($_POST['first_name']);
                $last_name  = sanitize_text_field($_POST['last_name']);
                $this->render('ui/notice', array('type' => 'green', 'message' => "Profile for {$first_name} {$last_name} created"));
            } else {
                $this->render('ui/notice', array('type' => 'red', 'message' => 'Cannot create staff profile. Please contact the Site Admin for support.'));
            }
        }

        $mode_strategy->render_staff_creation_form($username, $this->ui_content);
    }

    /*
    REGISTER TO TRAINING
     */
    public function eventRegistration() {
        $time_now = current_time('mysql');

        // Take care of the form
        if (isset($_POST['reg-training']) && wp_verify_nonce($_POST['reg_nonce_field'], 'reg_nonce')) {
            $event_id = intval($_POST['training']);
            $staff_id = intval($_POST['staff']);

            if ($event_id != 0 && $staff_id != 0) {
                $training = $this->event_repo->get_by_id($event_id);
                if ($training && $training->open_time < $time_now && $training->close_time > $time_now && ($training->limit_max == 0 || $training->max == -999 || $training->max - $training->num_reg > 0)) {

                    if ($this->registration_repo->check_duplicate($staff_id, $event_id) == 0) {
                        $this->registration_repo->insert(array(
                            "event_id" => $event_id,
                            "staff"    => $staff_id,
                            "reg_time" => $time_now,
                            "school"   => $_POST['school'],
                            "comment"  => $_POST['comment'],
                        ));
                        $this->event_repo->increment_registration_count($event_id);
                        $this->render('ui/notice', array('type' => 'green', 'message' => $this->tools->idtoName($staff_id) . ' is successfully registered to ' . $training->event_name . ' at ' . $training->location));
                    } else {
                        $this->render('ui/notice', array('type' => 'red', 'message' => $this->tools->idtoName($staff_id) . ' has already been registered to ' . $training->event_name . ' at ' . $training->location));
                    }
                } else {
                    $this->render('ui/notice', array('type' => 'red', 'message' => 'Staff cannot be registered. Try to refresh the page and try again.'));
                }
            } else {
                $this->render('ui/notice', array('type' => 'red', 'message' => 'You must select a training AND a staff to register.'));
            }
        }

        $username = wp_get_current_user()->user_login;
        $trainings_to_show = $this->event_repo->get_all_upcoming($time_now);
        $staff_available = $this->staff_repo->get_all_by_school($username);

        $this->render('ui/event-registration', array(
            'time_now'          => $time_now,
            'trainings_to_show' => $trainings_to_show,
            'staff_available'   => $staff_available,
            'username'          => $username,
            'tools'             => $this->tools,
            'show_available'    => get_option('show_availability', 0)
        ));
    }

    /**
    *MANAGE STAFF PROFILES
    */
    function viewEditStaff() {
        $username = wp_get_current_user()->user_login;
        $time_now = current_time('mysql');
        $my_mode = get_option('my_mode');
        $mode_strategy = RegistrationModeFactory::get_current_mode();

        // Update the database after editing profile
        if (isset($_POST['update-profile']) && wp_verify_nonce($_POST['staff_nonce_field'], 'create_staff_nonce')) {
            $staff_id = intval($_POST['id']);
            $success = $mode_strategy->handle_staff_update($staff_id, $_POST);

            if ($success) {
                $this->render('ui/notice', array('type' => 'green', 'message' => 'Staff Profile Updated'));
            }
        }

        // Withdraw from a training
        if (isset($_POST['confirm-remove']) && wp_verify_nonce($_POST['staff_nonce_field'], 'create_staff_nonce')) {
            $trainings_to_remove = $_POST['training-id'];
            $staff_id = intval($_POST['staff_id']);

            foreach($trainings_to_remove as $training_id) {
                $this->registration_repo->delete_by_event_and_staff($training_id, $staff_id);
                $this->event_repo->decrement_registration_count($training_id);
            }
            $this->render('ui/notice', array('type' => 'green', 'message' => 'Registration(s) Cancelled'));
        }

        $all_staff = $this->staff_repo->get_all_by_school($username);

        $this->render('ui/manage-staff', array(
            'username'      => $username,
            'all_staff'     => $all_staff,
            'my_mode'       => $my_mode,
            'time_now'      => $time_now,
            'tools'         => $this->tools,
            'event_repo'    => $this->event_repo,
            'registration_repo' => $this->registration_repo
        ));

        // Edit Staff profile
        if (isset($_POST['edit-profile']) && wp_verify_nonce($_POST['staff_nonce_field'], 'create_staff_nonce')) {
            $staff_id = intval($_POST['select']);
            $profile = $this->staff_repo->get_by_id($staff_id);
            $mode_strategy->render_staff_edit_form($username, $profile, $staff_id, $this->ui_content);
        }

        // Edit Staff Registration
        if (isset($_POST['edit-reg']) && wp_verify_nonce($_POST['staff_nonce_field'], 'create_staff_nonce')) {
            $staff_id = intval($_POST['select']);
            $trainings_registered = $this->registration_repo->get_by_staff($staff_id);
            
            $this->render('ui/cancel-registration', array(
                'staff_id'              => $staff_id,
                'trainings_registered'  => $trainings_registered,
                'time_now'              => $time_now,
                'tools'                 => $this->tools,
                'event_repo'            => $this->event_repo
            ));
        }
    }
}
