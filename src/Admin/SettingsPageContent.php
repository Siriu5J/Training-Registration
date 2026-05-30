<?php

namespace SOT\TrainingRegistration\Admin;

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

    public function overview($home_table, $stats = array()) {
        $this->render('admin/overview', array(
            'home_table' => $home_table,
            'stats'      => $stats
        ));
    }

    public function new_event($data, $tools) {
        $this->render('admin/new-event', array('data' => $data, 'tools' => $tools));
    }

    public function view_event($tools, $my_mode, $id) {
        if ($my_mode == 1) {
            $reg_table = new StaffRegistrationTableMY($tools);
        } else {
            $reg_table = new StaffRegistrationTableCN($tools);
        }

        $reg_table->set_event_id($id);
        $reg_table->prepare_items();

        $this->render('admin/view-event', array(
            'tools'     => $tools,
            'id'        => $id,
            'reg_table' => $reg_table
        ));
    }

    public function manage_reg($tools, $my_mode) {
        $event_repo = new EventRepository();
        $time_now = current_time('mysql');
        
        $count = $event_repo->get_count_upcoming($time_now);

        // Only show information if there are upcoming and activated trainings
        if ($count != 0) {
            $trainings = $event_repo->get_all_upcoming($time_now);
            $this->render('admin/manage-reg', array(
                'tools'     => $tools,
                'my_mode'   => $my_mode,
                'trainings' => $trainings
            ));
        } else {    // No trainings? Show the way to create event
            ?>
            <div class="wrap">
                <h1>Manage Registrations</h1>
                <div style="display: contents; justify-content: center;">
                    <h3 align="center">No Activated and Upcoming Trainings Found!<br>
                        <p align="center">This page will only allow you to manage registrations of activated and upcoming (start date set to time in the future) trainings.<br>Make sure trainings you want to manage fulfill both requirements.</p>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Render the settings page.
     */
    public function view_settings() {
        $this->render('admin/settings');
    }
}
