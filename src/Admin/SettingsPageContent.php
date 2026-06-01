<?php

namespace SOT\TrainingRegistration\Admin;

defined('ABSPATH') || exit;

use SOT\TrainingRegistration\Traits\TemplateRenderer;
use SOT\TrainingRegistration\Data\Repositories\EventRepository;

/**
 * Class SettingsPageContent
 *
 * This class handles rendering admin settings pages using templates.
 *
 * @package SOT\TrainingRegistration\Admin
 */
class SettingsPageContent {
    use TemplateRenderer;

    public function overview($home_table, $stats = []) {
        $this->render('admin/overview', [
            'home_table' => $home_table,
            'stats'      => $stats
        ]);
    }

    public function new_event($data, $tools) {
        $this->render('admin/new-event', ['data' => $data, 'tools' => $tools]);
    }

    public function view_event($tools, $my_mode, $id) {
        $reg_table = match ((int)$my_mode) {
            1 => new StaffRegistrationTableMY($tools),
            default => new StaffRegistrationTableCN($tools),
        };

        $reg_table->set_event_id($id);
        $reg_table->prepare_items();

        $this->render('admin/view-event', [
            'tools'     => $tools,
            'id'        => $id,
            'reg_table' => $reg_table
        ]);
    }

    public function manage_reg($tools, $my_mode) {
        $event_repo = new EventRepository();
        $time_now = current_time('mysql');
        
        $trainings = $event_repo->get_all_upcoming($time_now);

        if (!empty($trainings)) {
            $this->render('admin/manage-reg', [
                'tools'     => $tools,
                'my_mode'   => $my_mode,
                'trainings' => $trainings
            ]);
            return;
        }

        ?>
        <div class="wrap">
            <h1>Manage Registrations</h1>
            <div style="display: contents; justify-content: center;">
                <h3 align="center">No Activated and Upcoming Trainings Found!<br>
                    <p align="center">This page will only allow you to manage registrations of activated and upcoming (start date set to time in the future) trainings.<br>Make sure trainings you want to manage fulfill both requirements.</p>
            </div>
        </div>
    }

    /**
     * Render the settings page.
     */
    public function view_settings() {
        $this->render('admin/settings');
    }
}
