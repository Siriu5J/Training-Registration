<?php
/**
 * Class settings_page_content
 *
 * This class contains all the HTML/PHP hybrid content for the admin settings pages.
 *
 * @since 2020-5-19
 * @version 1.1
 *
 * @package training-registration
 */

class settings_page_content {
    public function overview($home_table) {
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Event Registration Plugin</h1>
            <a class="page-title-action" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=er_new_event_set');?>">Add New Training</a>
            <hr class="wp-header-end">
        <?php

        $home_table->prepare_items();
        ?>
            <div id="er-home-table">
                <?php
                $home_table->views();
                ?>
                <form method="post">
                    <input type="hidden" name="page" value="home_table_search" />
                    <?php
                    $home_table->search_box('Search Trainings', 'search_id');
                    $home_table->display();
                    ?>
                </form>
            </div>
        </div>
        <?php
    }

    public function new_event($data, $tools) {
        // Set Appropriate text
        if ($data->event_name != '') {
            $header = "View/Edit Training";
            $save   = "Save Training Details";
            $submit_id = 'submit_edit';
            $show_stat = true;
        } else {
            $header = "Create New Training";
            $save   = "Create Training";
            $submit_id = 'create_training';
            $show_stat = false;
        }

        // Set Appropriate max values
        if ($data->max == -999) {
            $data->max = '';
        }

        $warning_text_registration = "Please make sure that the start time is always before the end time. Also, the start time must be in the future to have the system recognize the training as an upcoming training.";
        $warning_text_cap = "If you chose to toggle limit maximum registration on, the max registration must be greater than 0.";
        $question_mark = "Unchecking this option will keep this training hidden to the public even when its open for registration.";
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php echo $header ?></h1>
            <hr class="wp-header-end">
            <br />
            <form id="new-event" name="new-event" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
                <input type="hidden" name="event-id" value="<?php echo $data->id ?>" />
                <div id="er-display-two-columns">
                    <div id="er-main-content">
                        <div id="er-title">
                            <input type="text" name="event-name" placeholder="Enter Training Name Here" id="event-name" spellcheck="true" value="<?php echo $data->event_name ?>" required/>
                        </div>
                        <div id="er-information">
                            <label for="location">Location:<br></label>
                            <input class="er_input" type="text" name="location" id="location" value="<?php echo $data->location ?>" required/>
                            <br/>
                            <label for="comment">Training Information:<br></label>
                            <textarea name="comment" id="comment" cols="45" rows="5" spellcheck="true"><?php echo $data->comment ?></textarea>
                        </div>
                        <div>
                            <br />
                            <br />
                            <input class="er-submit-button" type="submit" name="<?php echo $submit_id ?>" id="create_training" value="<?php echo $save ?>" />
                        </div>
                    </div>

                    <div id="er-side-bar">
                        <?php
                        // Show stat block
                        if ($show_stat) {
                            ?>
                            <div class="er-sidebar-block">
                                <div class="er-block-title">
                                    <h3>Statistics</h3>
                                </div>
                                <div class="er-block-content">
                                    <table>
                                        <tr>
                                            <td class="er-tb-cell"><b>Registration status:</b></td>
                                            <td class="er-tb-cell"><?php echo $tools->availability($data) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="er-tb-cell"><b>Currently registered:</b></td>
                                            <td class="er-tb-cell"><?php echo $data->num_reg ?></td>
                                        </tr>
                                        <tr>
                                            <td class="er-tb-cell"><b>Activated?</b></td>
                                            <td class="er-tb-cell"><?php echo ($data->activated == 1? 'Yes' : 'No'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                        <div class="er-sidebar-block">
                            <div class="er-block-title">
                                <h3>Registration Dates <span class="dashicons dashicons-warning" title="<?php echo $warning_text_registration ?>"></span></h3>
                            </div>
                            <div class="er-block-content">
                                <label for="open-date">Registration Open Date:<br></label><input class="er_input" type="datetime-local" name="open-date" id="open-date" value="<?php echo date( "Y-m-d\TH:i:s", strtotime($data->open_time)); ?>" required/>
                                <br /><br />
                                <label for="close-date">Registration Close Date:<br></label><input class="er_input" type="datetime-local" name="close-date" id="close-date" value="<?php echo date( "Y-m-d\TH:i:s", strtotime($data->close_time)); ?>" required/>
                                <br /><br />
                                <label for="start-date">Training Start Date:<br></label><input class="er_input" type="datetime-local" name="start-date" id="start-date" value="<?php echo date( "Y-m-d\TH:i:s", strtotime($data->start_time)); ?>" required/>
                                <br /><br />
                                <label for="end-date">Training End Date:<br></label><input class="er_input" type="datetime-local" name="end-date" id="end-date" value="<?php echo date( "Y-m-d\TH:i:s", strtotime($data->end_time)); ?>" required/>
                            </div>
                        </div>

                        <div class="er-sidebar-block">
                            <div class="er-block-title">
                                <h3>Capacity and Status <span class="dashicons dashicons-warning" title="<?php echo $warning_text_cap ?>"></span></h3>
                            </div>
                            <div class="er-block-content">
                                <label for="max">Max Registration:<br></label><input class="er_input" type="number" name="max" id="max" min="0" step="1" value="<?php echo $data->max ?>"/>
                                <br /><br />
                                <table>
                                    <tr>
                                        <td><input type="checkbox" name="max-limit" id="max-limit" value="1" <?php echo ($data->limit_max == 1 ? 'checked' : '') ?>/></td>
                                        <td><label for="max-limit">Limit Maximum registration?</label></td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" name="activated" id="activated" value="1" <?php echo ($data->activated == 1 ? 'checked' : '') ?>/></td>
                                        <td><label for="activated">Activation <span class="dashicons dashicons-editor-help" title="<?php echo $question_mark ?>"></span></label></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
            </form>

                        <?php
                        // Show danger zone
                        if ($show_stat) {
                            ?>
                            <div class="er-sidebar-block">
                                <div class="er-block-title">
                                    <h3>Danger Zone</h3>
                                </div>
                                <div class="er-block-content">
                                    <p><b>Remove Training</b><br />
                                        Warning! Doing this will remove the training event and registration records from the database. Type "remove training" in the box below and click remove to remove the training.
                                    </p>
                                    <form id="confirm-remove-event" name="confirm-remove-event" method="post" action="<?php echo get_site_url() . '/wp-admin/admin.php?page=er_gen_set';?>">
                                        <input type="hidden" name="removal-id" id="removal-id" value="<?php echo $data->id ?>" />
                                        <br />
                                        <input type="text" class="er_input" name="confirm" required pattern="remove training" autocomplete="off" />
                                        <br />
                                        <br />
                                        <input type="submit" id="confirm_remove_button" name="confirm_remove" value="Remove Training">
                                    </form>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <br />
        </div>
        <?php
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

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Registrations for <?php echo $tools->getFieldById(ER_EVENT_LIST, 'event_name', $id).' at '. $tools->getFieldById(ER_EVENT_LIST, 'location', $id) ?></h1>
            <hr class="wp-header-end">

            <form id="staff-reg" method="GET" action="<?php echo $_SERVER['REQUEST_URI']?>">
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="event-id" value="<?php echo $id ?>" />
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <!-- Now we can render the completed list table -->
                <?php $reg_table->display() ?>
            </form>
        </div>
        <?php
    }

    public function manage_reg($tools, $my_mode) {

        global $wpdb;
        $event_table = ER_EVENT_LIST;
        require_once(ER_PLUGIN_DIR . '/admin/staff_reg_table.php');

        $time_now = current_time('mysql');

        // Only show information if there are upcoming and activated trainings
        if ($wpdb->get_var("SELECT COUNT(*) FROM $event_table WHERE `activated` = 1 AND `start_time` > '$time_now'") != 0) {
            ?>
            <h1>Manage Registrations</h1>
            <p>All the registrations of <b>upcoming AND activated trainings</b> can be seen here. To see the registration list of a particular training, click on the name of the training. You can use the bulk action to remove trainee(s) from a training. You can also choose to download the registration list as Excel Spreadsheet (.xls) by clicking on the "Download Training Registration as Excel Spreadsheet" button under each training.</p>
            <table style="width:100%; border-collapse: collapse">
                <tr style="outline: thin solid; text-align: left;">
                    <th style="width: 30%">Training Name</th>
                    <th style="width: 15%">Location</th>
                    <th style="width: 10%">Training Start Time</th>
                    <th style="width: 10%">Training End Time</th>
                    <th style="width: 13%">Available Slots</th>
                    <th style="width: 13%">Registration Stat</th>

                </tr>
                <?php

                // Only show trainings that are upcoming and activated
                $trainings = $wpdb->get_results("SELECT * FROM $event_table WHERE `activated` = 1 AND `start_time` > '$time_now' ORDER BY `start_time` DESC");
                $trainingNumber = 0;    // This will keep track of the number of row the foreach loop is on to set the background of every other row

                foreach ($trainings as $training) {
                    $trainingNumber++;
                    ?>
                    <tr <?php if ($trainingNumber % 2 == 0) {
                        echo "bgcolor=\"#A9A9A9\"";
                    } ?> style="height: 25pt; ">
                        <td><a href="#<?php echo $training->id; ?>"><?php echo $training->event_name ?></a></td>
                        <td><?php echo $training->location ?></td>
                        <td><?php echo date("Y-m-d", strtotime($training->start_time)) ?></td>
                        <td><?php echo date("Y-m-d", strtotime($training->end_time)) ?></td>
                        <td><?php echo $tools->spotsOpen($training->max, $training->num_reg) ?></td>
                        <td><?php echo $tools->availability($training) ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <br>
            <br><br>
            <hr/>
            <?php

            // Take care of the view registration form
            foreach ($trainings as $training) {
                // Create a new WP List Table for each training
                if ($my_mode == 1) {
                    $reg_table = new StaffRegTableMY($tools);
                } else {
                    $reg_table = new StaffRegTableCN($tools);
                }
                $reg_table->set_event_id($training->id);
                $reg_table->prepare_items();

                // Due to WP's restrictions on using certain functions in global scope, I had to pre-fetch the school nicknames and send them through the form
                // This will be a two dimensional array which contains all

                ?>
                <div class="wrap" id="<?php echo $training->id; ?>">
                    <h3>Registrations for <?php echo $training->event_name . ' at ' . $training->location; ?></h3>
                    <form id="staff-reg" method="GET" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
                        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                        <!-- Now we can render the completed list table -->
                        <?php $reg_table->display() ?>
                    </form>
                </div>
                <br>
                <br/>
                <hr/>
                <?php

            }
        } else {    // No trainings? Show the way to create event
            ?>
            <h1>Manage Registrations</h1>
            <div style="display: contents; justify-content: center;">
                <h3 align="center">No Activated and Upcoming Trainings Found!<br>
                    <p align="center">This page will only allow you to manage registrations of activated and upcoming (start date set to time in the future) trainings.<br>Make sure trainings you want to manage fulfill both requirements.</p>
            </div>
            <?php
        }
    }

    public function view_settings($show_available, $my_enabled) {
        ?>
        <h1>Settings</h1>
        <form id="update-settings" name="update-settings" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <table class="form-table">
                <tbody>
                <tr>
                    <th>Show Available Seats</th>
                    <td>
                        <fieldset>
                            <label for="show-available"><input type="checkbox" name="show-available" value="1" <?php if ($show_available == 1) {echo 'checked';} ?>> Disabling this option will hide the number of seats remaining in a training to schools.</label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th>Enable SOTAM Forms</th>
                    <td>
                        <fieldset>
                            <label for="enable-my"><input type="checkbox" name="enable-my" value="1" <?php if ($my_enabled == 1) {echo 'checked';} ?>> Enable SOTAM requested form formats.</label>
                        </fieldset>
                    </td>
                </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" class="button button-primary" name="save-settings" id="save-settings" value="Save Settings">
            </p>
        </form>
        <hr>
        <form id="create" name="create" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <table class="form-table">
                <tbody>
                <tr>
                    <th>Create Necessary Pages</th>
                    <td>
                        <input type="submit" class="button button-primary" name="create-page" id="create-page" value="Create Pages">
                    </td>
                </tr>
                </tbody>
            </table>

        </form>
        <?php
    }
}