<?php

namespace SOT\TrainingRegistration\UI;

defined('ABSPATH') || exit;

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

    /**
     * Redirect unauthenticated users to the login page when accessing plugin pages.
     */
    public function handle_unauthenticated_access() {
        if (is_user_logged_in()) {
            return;
        }

        $plugin_pages = [
            get_option('er_dashboard_page_id'),
            get_option('er_create_staff_page_id'),
            get_option('er_manage_staff_page_id'),
            get_option('er_manage_registrations_page_id'),
            get_option('er_register_training_page_id'),
        ];

        if (is_page($plugin_pages)) {
            wp_safe_redirect(wp_login_url(get_permalink()));
            exit;
        }
    }

    public function uiDashboard() {
        ob_start();

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

    public function manageRegistrations() {
        ob_start();

        $username = wp_get_current_user()->user_login;
        $time_now = current_time('mysql');
        $event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

        if ($event_id === 0) {
            $this->render('ui/notice', ['type' => 'red', 'message' => 'No training selected. Please go back to the dashboard and select a training from the agenda.']);
            return ob_get_clean();
        }

        $event = $this->event_repo->get_by_id($event_id);
        if (!$event) {
            $this->render('ui/notice', ['type' => 'red', 'message' => 'Training not found.']);
            return ob_get_clean();
        }

        // Handle Withdrawal
        if (isset($_POST['withdraw-staff']) && wp_verify_nonce($_POST['reg_nonce_field'], 'withdraw_staff_nonce')) {
            $staff_id = intval($_POST['staff_id']);
            
            // Server-side validation: check if registration period is still open
            if ($time_now > $event->close_time) {
                $this->render('ui/notice', ['type' => 'red', 'message' => 'Cannot withdraw staff. The registration period for this training has ended. Please contact the training organizer.']);
            } else {
                $this->registration_repo->delete_by_event_and_staff($event_id, $staff_id);
                $this->event_repo->decrement_registration_count($event_id);
                $this->render('ui/notice', ['type' => 'green', 'message' => $this->tools->idtoName($staff_id) . ' has been successfully withdrawn from the training.']);
            }
        }

        $this->render('ui/manage-event-registrations', [
            'event'             => $event,
            'registrations'     => $this->registration_repo->get_by_event_and_school($event_id, $username),
            'time_now'          => $time_now,
            'tools'             => $this->tools,
            'staff_repo'        => $this->staff_repo,
            'dashboard_url'     => get_permalink(get_option('er_dashboard_page_id'))
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
            $count = 0;
            foreach (($_POST['training-id'] ?? []) as $training_id) {
                $event = $this->event_repo->get_by_id($training_id);
                if ($event && $time_now <= $event->close_time) {
                    $this->registration_repo->delete_by_event_and_staff($training_id, $staff_id);
                    $this->event_repo->decrement_registration_count($training_id);
                    $count++;
                }
            }
            if ($count > 0) {
                $this->render('ui/notice', ['type' => 'green', 'message' => 'Registration(s) Withdrawn']);
            } else {
                $this->render('ui/notice', ['type' => 'red', 'message' => 'No registrations could be withdrawn. Registration periods may have closed.']);
            }
        }

        // Remove Staff
        if (isset($_POST['remove-staff']) && wp_verify_nonce($nonce, 'create_staff_nonce')) {
            $staff_id = intval($_POST['remove-staff']);
            $registrations = $this->registration_repo->get_by_staff($staff_id);
            $can_delete = true;
            $failed_event = '';

            foreach ($registrations as $reg) {
                $event = $this->event_repo->get_by_id($reg->event_id);
                if ($event && $time_now > $event->close_time) {
                    $can_delete = false;
                    $failed_event = $event->event_name;
                    break;
                }
            }

            if (!$can_delete) {
                $this->render('ui/notice', ['type' => 'red', 'message' => 'Cannot remove staff member. The registration period for "' . $failed_event . '" has already ended. Please contact the training organizer to manually withdraw them first.']);
            } else {
                // Withdraw from all trainings first
                foreach ($registrations as $reg) {
                    $this->registration_repo->delete_by_event_and_staff($reg->event_id, $staff_id);
                    $this->event_repo->decrement_registration_count($reg->event_id);
                }
                // Delete staff profile
                $this->staff_repo->delete($staff_id);
                $this->render('ui/notice', ['type' => 'green', 'message' => 'Staff member successfully removed and withdrawn from all trainings.']);
            }
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
