<?php

namespace SOT\TrainingRegistration\UI;

use SOT\TrainingRegistration\Traits\TemplateRenderer;
use SOT\TrainingRegistration\Data\Repositories\EventRepository;
use SOT\TrainingRegistration\Data\Repositories\StaffRepository;
use SOT\TrainingRegistration\Data\Repositories\RegistrationRepository;
use SOT\TrainingRegistration\Data\Strategies\RegistrationModeFactory;
use SOT\TrainingRegistration\Core\Tools;

/**
 * Class TrainingRegistrationUI
 *
 * This class handles the frontend shortcode logic using templates.
 *
 * @package SOT\TrainingRegistration\UI
 */
class TrainingRegistrationUI {
    use TemplateRenderer;

    // Helpful tools
    protected $tools;
    protected $ui_content;

    protected $event_repo;
    protected $staff_repo;
    protected $registration_repo;

    public function __construct() {
        $this->tools = new Tools();
        $this->ui_content = new UIContent();

        $this->event_repo = new EventRepository();
        $this->staff_repo = new StaffRepository();
        $this->registration_repo = new RegistrationRepository();
    }

    public function enqueue_ui_css() {
        wp_enqueue_style('dashicons');
        wp_enqueue_style('ui_styles', plugins_url('../../assets/css/ui/ui.css', __FILE__), array('dashicons'));
    }


    public function uiDashboard() {
        ob_start();

        if (!is_user_logged_in()) {
            $this->render('ui/notice', ['type' => 'red', 'message' => 'You must be logged in to view the dashboard.']);
            return ob_get_clean();
        }

        $username = wp_get_current_user()->user_login;

        $stats = [
            'total_staff'        => $this->staff_repo->get_count_by_school($username),
            'upcoming_trainings' => $this->event_repo->get_count_upcoming(current_time('mysql')),
            'my_registrations'   => $this->registration_repo->get_total_count_by_school($username),
            'agenda'             => $this->registration_repo->get_school_agenda($username, 90)
        ];

        $this->ui_content->dashboard($stats);
        return ob_get_clean();
    }

    public function staffFormCreation() {
        ob_start();
        $username = wp_get_current_user()->user_login;
        $mode_strategy = RegistrationModeFactory::get_current_mode();

        if (isset($_POST['create_staff']) && wp_verify_nonce($_POST['staff_nonce_field'], 'create_staff_nonce')) {
            $result = $mode_strategy->handle_staff_creation($_POST);
            
            $type = is_wp_error($result) || !$result ? 'red' : 'green';
            $message = match (true) {
                is_wp_error($result) => $result->get_error_message(),
                $result => "Profile for " . sanitize_text_field($_POST['first_name']) . " " . sanitize_text_field($_POST['last_name']) . " created",
                default => 'Cannot create staff profile. Please contact the Site Admin for support.',
            };

            $this->render('ui/notice', ['type' => $type, 'message' => $message]);
        }

        $mode_strategy->render_staff_creation_form($username, $this->ui_content);
        return ob_get_clean();
    }

    public function eventRegistration() {
        ob_start();
        $time_now = current_time('mysql');

        if (isset($_POST['reg-training']) && wp_verify_nonce($_POST['reg_nonce_field'], 'reg_nonce')) {
            $event_id = intval($_POST['training']);
            $staff_id = intval($_POST['staff']);

            if ($event_id === 0 || $staff_id === 0) {
                $this->render('ui/notice', ['type' => 'red', 'message' => 'You must select a training AND a staff to register.']);
            } else {
                $training = $this->event_repo->get_by_id($event_id);
                $isAvailable = $training && $training->open_time < $time_now && $training->close_time > $time_now && ($training->limit_max == 0 || $training->max == -999 || $training->max - $training->num_reg > 0);

                if (!$isAvailable) {
                    $this->render('ui/notice', ['type' => 'red', 'message' => 'Staff cannot be registered. Try to refresh the page and try again.']);
                } elseif ($this->registration_repo->check_duplicate($staff_id, $event_id) > 0) {
                    $this->render('ui/notice', ['type' => 'red', 'message' => $this->tools->idtoName($staff_id) . ' has already been registered to ' . $training->event_name . ' at ' . $training->location]);
                } else {
                    $this->registration_repo->insert([
                        "event_id" => $event_id,
                        "staff"    => $staff_id,
                        "reg_time" => $time_now,
                        "school"   => wp_get_current_user()->user_login,
                        "comment"  => sanitize_textarea_field($_POST['comment']),
                    ]);
                    $this->event_repo->increment_registration_count($event_id);
                    $this->render('ui/notice', ['type' => 'green', 'message' => $this->tools->idtoName($staff_id) . ' is successfully registered to ' . $training->event_name . ' at ' . $training->location]);
                }
            }
        }

        $username = wp_get_current_user()->user_login;
        $this->render('ui/event-registration', [
            'time_now'          => $time_now,
            'trainings_to_show' => $this->event_repo->get_all_upcoming($time_now),
            'staff_available'   => $this->staff_repo->get_all_by_school($username),
            'username'          => $username,
            'tools'             => $this->tools,
            'show_available'    => get_option('show_availability', 0)
        ]);

        return ob_get_clean();
    }

    public function viewEditStaff() {
        ob_start();
        $username = wp_get_current_user()->user_login;
        $time_now = current_time('mysql');
        $mode_strategy = RegistrationModeFactory::get_current_mode();
        $nonce = $_POST['staff_nonce_field'] ?? '';

        // Update the database after editing profile
        if (isset($_POST['update-profile']) && wp_verify_nonce($nonce, 'create_staff_nonce')) {
            if ($mode_strategy->handle_staff_update(intval($_POST['id']), $_POST)) {
                $this->render('ui/notice', ['type' => 'green', 'message' => 'Staff Profile Updated']);
            }
        }

        // Withdraw from a training
        if (isset($_POST['confirm-remove']) && wp_verify_nonce($nonce, 'create_staff_nonce')) {
            $staff_id = intval($_POST['staff_id']);
            foreach (($_POST['training-id'] ?? []) as $training_id) {
                $this->registration_repo->delete_by_event_and_staff($training_id, $staff_id);
                $this->event_repo->decrement_registration_count($training_id);
            }
            $this->render('ui/notice', ['type' => 'green', 'message' => 'Registration(s) Cancelled']);
        }

        $this->render('ui/manage-staff', [
            'username'      => $username,
            'all_staff'     => $this->staff_repo->get_all_by_school($username),
            'my_mode'       => get_option('my_mode'),
            'time_now'      => $time_now,
            'tools'         => $this->tools,
            'event_repo'    => $this->event_repo,
            'registration_repo' => $this->registration_repo
        ]);

        // Edit Staff profile
        if (isset($_POST['edit-profile']) && wp_verify_nonce($nonce, 'create_staff_nonce')) {
            $staff_id = intval($_POST['edit-profile']);
            $profile = $this->staff_repo->get_by_id($staff_id);
            $mode_strategy->render_staff_edit_form($username, $profile, $staff_id, $this->ui_content);
        }

        // Edit Staff Registration
        if (isset($_POST['edit-reg']) && wp_verify_nonce($nonce, 'create_staff_nonce')) {
            $staff_id = intval($_POST['edit-reg']);
            $this->render('ui/cancel-registration', [
                'staff_id'              => $staff_id,
                'trainings_registered'  => $this->registration_repo->get_by_staff($staff_id),
                'time_now'              => $time_now,
                'tools'                 => $this->tools,
                'event_repo'            => $this->event_repo
            ]);
        }
        
        return ob_get_clean();
    }
}
