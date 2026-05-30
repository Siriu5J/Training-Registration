<?php
/**
 * Class settings_page_content
 *
 * This class handles rendering admin settings pages using templates.
 *
 * @since 2020-5-19
 * @version 1.2
 *
 * @package training-registration
 */

class settings_page_content {
    use TemplateRenderer;

    public function overview($home_table) {
        $this->render('admin/overview', array('home_table' => $home_table));
    }

    public function new_event($data, $tools) {
        $this->render('admin/new-event', array('data' => $data, 'tools' => $tools));
    }

    public function view_event($tools, $my_mode, $id) {
        if (!class_exists('StaffRegTableCN') || !class_exists('StaffRegTableMY')) {
            require_once(ER_PLUGIN_DIR . '/admin/staff_reg_table.php');
        }

        if ($my_mode == 1) {
            $reg_table = new StaffRegTableMY($tools);
        } else {
            $reg_table = new StaffRegTableCN($tools);
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

    public function view_settings($show_available, $my_enabled) {
        $this->render('admin/settings', array(
            'show_available' => $show_available,
            'my_enabled'     => $my_enabled
        ));
    }
}
