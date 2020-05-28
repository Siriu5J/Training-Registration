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

    public function new_event() {
        $warning_text_registration = "Please make sure that the start time is always before the end time. Also, the start time must be in the future to have the system recognize the training as an upcoming training.";
        $warning_text_cap = "If you chose to toggle limit maximum registration on, the max registration must be greater than 0.";
        $question_mark = "Unchecking this option will keep this training hidden to the public even when its open for registration.";
        ?>
        <h1 class="er-admin-header">Create New Training</h1>
        <form id="new-event" name="new-event" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
            <div id="er-display-two-columns">
                <div id="er-main-content">
                    <div id="er-title">
                        <input type="text" name="event-name" placeholder="Enter Training Name Here" id="event-name" spellcheck="true" required/>
                    </div>
                    <div id="er-information">
                        <label for="location">Location:<br></label>
                        <input class="er_input" type="text" name="location" id="location" required/>
                        <br/>
                        <label for="comment">Training Information:<br></label>
                        <textarea name="comment" id="comment" cols="45" rows="5" spellcheck="true"></textarea>
                    </div>
                </div>

                <div id="er-side-bar">
                    <div class="er-sidebar-block">
                        <div class="er-block-title">
                            <h3>Registration Dates <span class="dashicons dashicons-warning" title="<?php echo $warning_text_registration ?>"></span></h3>
                        </div>
                        <div class="er-block-content">
                            <label for="open-date">Registration Open Date:<br></label><input class="er_input" type="datetime-local" name="open-date" id="open-date" value="<?php echo date("Y-m-d\TH:i", mktime(0,0)) ?>" required/>
                            <br /><br />
                            <label for="close-date">Registration Close Date:<br></label><input class="er_input" type="datetime-local" name="close-date" id="close-date" value="<?php echo date("Y-m-d\TH:i", strtotime("+31 days" ,mktime(0,0))) ?>" required/>
                            <br /><br />
                            <label for="start-date">Training Start Date:<br></label><input class="er_input" type="datetime-local" name="start-date" id="start-date" value="<?php echo date("Y-m-d\TH:i", strtotime("+31 days" ,mktime(0,0))) ?>" required/>
                            <br /><br />
                            <label for="end-date">Training End Date:<br></label><input class="er_input" type="datetime-local" name="end-date" id="end-date" value="<?php echo date("Y-m-d\TH:i", strtotime("+31 days" ,mktime(0,0))) ?>" required/>
                        </div>
                    </div>

                    <div class="er-sidebar-block">
                        <div class="er-block-title">
                            <h3>Capacity and Status <span class="dashicons dashicons-warning" title="<?php echo $warning_text_cap ?>"></span></h3>
                        </div>
                        <div class="er-block-content">
                            <label for="max">Max Registration:<br></label><input class="er_input" type="number" name="max" id="max" min="0" step="1"/>
                            <br /><br />
                            <table>
                                <tr>
                                    <td><input type="checkbox" name="max-limit" id="max-limit" value="1"/></td>
                                    <td><label for="max-limit">Limit Maximum registration?</label></td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="activated" id="activated" value="1" checked/></td>
                                    <td><label for="activated">Activated? <span class="dashicons dashicons-editor-help" title="<?php echo $question_mark ?>"></span></label></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <br />
            <input class="er-submit-button" type="submit" name="create_training" id="create_training" value="Create Training" />
        </form>
        <?php
    }

    public function view_event($tools) {
        global $wpdb;
        $event_table = ER_EVENT_LIST;
        ;
        if ($wpdb->get_var("SELECT COUNT(*) from $event_table") != 0) {
            ?>
            <h1>View Trainings</h1>
            <p>This page contains all trainings created (past, current, and future). You can manage those trainings
                including editing the details of each training as well as changing its state (activated or deactivated).
                When a training is deactivated, it will not be visible to schools regardless of its availability. The color
                of the activation column indicates the activation stat of the training (<span style="color: darkgreen;">GREEN</span>
                for activated, <span style="color: darkred;">RED</span> for deactivated). To change stat of the training,
                select the radio button of the training you would like to change and press "Activation Stat Switch". This
                will flip the activation status.</p>
            <table style="width:100%; border-collapse: collapse">
                <tr style="outline: thin solid; text-align: left;">
                    <th style="width: 30%">Training Name</th>
                    <th style="width: 15%">Location</th>
                    <th style="width: 10%">Reg Open Time</th>
                    <th style="width: 10%">Reg Close Time</th>
                    <th style="width: 13%">Available Slots</th>
                    <th style="width: 13%">Registration Stat</th>
                    <th style="width: 3%">Activation</th>
                    <th style="width: auto; text-align: center">Select</th>

                </tr>
                <form id="manage-events" name="manage-events" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    <?php

                    $trainings = $wpdb->get_results("SELECT * FROM $event_table ORDER BY `start_time` DESC");
                    $trainingNumber = 0;    // This will keep track of the number of row the foreach loop is on to set the background of every other row

                    foreach ($trainings as $training) {
                        $trainingNumber++;
                        ?>
                        <tr <?php if ($trainingNumber % 2 == 0) {
                            echo "bgcolor=\"#A9A9A9\"";
                        } ?> style="height: 25pt; ">
                            <td><?php echo $training->event_name ?></td>
                            <td><?php echo $training->location ?></td>
                            <td><?php echo date("Y-m-d", strtotime($training->open_time)) ?></td>
                            <td><?php echo date("Y-m-d", strtotime($training->close_time)) ?></td>
                            <td><?php echo $tools->spotsOpen($training->max, $training->num_reg) ?></td>
                            <td><?php echo $tools->availability($training) ?></td>
                            <td style="text-align: center; vertical-align: middle;">
                                <div style="<?php if ($training->activated == 1) {
                                    echo 'background-color: darkgreen';
                                } else {
                                    echo 'background-color: darkred';
                                } ?>">&nbsp;
                                </div>
                            </td>
                            <td style="text-align: center; vertical-align: middle; padding-top: 2.75pt"><input type="radio" name="select" value="<?php echo $training->id ?>" required/>
                            </td>
                        </tr>
                        <?php
                    }

                    // Bottom Buttons
                    ?>
            </table>
            <br>
            <!-- Delete button will be disabled and turn grey while waiting for the second confirmation -->
            <input type="submit" <?php if (!$_POST['remove-1']) {
                echo 'style="float: right; background-image: linear-gradient(#c62828, #c62828); border-color: #8e0000"';
            } else {
                echo 'style="float: right; background-image: linear-gradient(grey, grey); border-color: grey"';
            } ?> name="remove-1" id="remove-1" value="Remove Training" <?php if ($_POST['remove-1']) {echo 'disabled';} ?>/>
            <input style="float: right; background-image: linear-gradient(#E0E0E0, #E0E0E0); border-color: #BEBEBE;"
                   type="submit" name="confirm-activation" id="confirm-activation" value="Switch Activation Stat"/>
            <input style="float: right; background-image: linear-gradient(#E0E0E0, #E0E0E0); border-color: #BEBEBE;"
                   type="submit" name="confirm-edit" id="confirm-edit" value="Edit Training"/>
            </form>
            <br><br>
            <hr/>
            <?php

            // First Confirmation for removing a training
            if ($_POST['remove-1']) {
                $event_table = ER_EVENT_LIST;
                $training_id = $_POST['select'];
                $training_name = $wpdb->get_var("SELECT `event_name` FROM $event_table where `id` = $training_id");
                ?>
                <div style="background-color: #c62828; color: white; margin: 15pt; padding: 15pt; border-radius: 5px;">
                    <p>Are you sure you want to remove "<b><?php echo $training_name ?></b>?" Doing this will remove the
                        training event and registration records from the database.</p>
                    <form id="confirm-remove-event" name="confirm-remove-event" method="post"
                          action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <input type="hidden" name="removal_id" id="removal_id" value="<?php echo $_POST['select'] ?>"/>
                        <label for="remove_confirm">Type "<b>I understand</b>" (without quotation mark) and click "Yes,
                            Remove Training" to remove training.</label>
                        <br>
                        <input type="text" style="width: 50%" name="confirm" id="remove_confirm" required
                               pattern="I understand" autocomplete="off">
                        <br><br>
                        <input
                                style="float: left; background-image: linear-gradient(#ffffff, #ffffff); border-color: #E0E0E0;"
                                type="submit" name="remove-2" id="remove-2" value="Yes, Remove Training">
                    </form>
                    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <input
                                style="float: left; background-image: linear-gradient(#ffffff, #ffffff); border-color: #E0E0E0;"
                                type="submit" name="cancel" id="cancel" value="Cancel">
                    </form>
                    <br><br>
                </div>

                <?php
            }

            // Show Edit Training Form
            if ($_POST['confirm-edit']) {
                $event_id   = $_POST['select'];
                $event_list = ER_EVENT_LIST;
                $event_row  = $wpdb->get_row( "SELECT * FROM $event_list WHERE `id` = $event_id" );

                ?>
                <h3>Editing <?php echo $event_row->event_name ?></h3>
                <form id="update-event" name="update-event" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    <label for="update-event-name">Training Name:<br></label><input class="er_input" type="text" name="update-event-name"
                                                                                    id="update-event-name"
                                                                                    value="<?php echo $event_row->event_name ?>"
                                                                                    required/>
                    <br><br>
                    <label for="update-open-date">Registration Open Date:<br></label><input class="er_input" type="datetime-local"
                                                                                            name="update-open-date" id="update-open-date"
                                                                                            value="<?php echo date( "Y-m-d\TH:i:s", strtotime( $event_row->open_time ) ); ?>"
                                                                                            required/>
                    <br><br>
                    <label for="update-close-date">Registration Close Date:<br></label><input class="er_input" type="datetime-local"
                                                                                              name="update-close-date" id="update-close-date"
                                                                                              value="<?php echo date( "Y-m-d\TH:i:s", strtotime( $event_row->close_time ) ); ?>"
                                                                                              required/>
                    <br><br>
                    <label for="update-start-date">Training Start Date:<br></label><input class="er_input" type="datetime-local"
                                                                                          name="update-start-date" id="update-start-date"
                                                                                          value="<?php echo date( "Y-m-d\TH:i:s", strtotime( $event_row->start_time ) ); ?>"
                                                                                          required/>
                    <br><br>
                    <label for="update-end-date">Training End Date:<br></label><input class="er_input" type="datetime-local"
                                                                                      name="update-end-date" id="update-end-date"
                                                                                      value="<?php echo date( "Y-m-d\TH:i:s", strtotime( $event_row->end_time ) ); ?>"
                                                                                      required/>
                    <br><br>
                    <label for="max">Max Registration (optional):<br></label><input class="er_input" type="number" name="update-max"
                                                                                    id="update-max"
                                                                                    value="<?php if ( $event_row->max != -999 ) {
                                                                                        echo $event_row->max;
                                                                                    } ?>"/>
                    <br>
                    <p>Notice: Limiting the maximum after the registration has overflowed will not remove the overflown registrations. It will only stop schools from registering.</p>
                    <label for="max-limit">Limit Maximum? &nbsp;&nbsp;&nbsp;</label><input class="er_input" type="checkbox" name="update-max-limit" id="update-max-limit" value="1" <?php if($event_row->limit_max == 1) {echo 'checked';}?>/>
                    <br><br>
                    <label for="location">Location:<br></label><input class="er_input" type="text" name="update-location" id="update-location" value="<?php echo $event_row->location ?>" required/>
                    <br><br>
                    <label for="comment">Information on Training Event<br></label><textarea name="update-comment" id="update-comment"
                                                                                            cols="45"
                                                                                            rows="5"><?php echo $event_row->comment ?></textarea>
                    <br><br>
                    <input type="hidden" name="update-event_id" id="update-event_id" value="<?php echo $event_id ?>"/>
                    <input type="submit" name="confirm-update" id="confirm-update" value="Update"/>
                    <input type="submit" name="close" id="close" value="Cancel" />
                </form>
                <br>
                <?php
            }
        } else {    // No trainings? Show the way to create event
            ?>
            <h1>View Trainings</h1>
            <div class="wrap" style="position: fixed; top: 50%; left: 50%;">
                <h3 align="center">No Trainings Found!</h3>
                <h2 align="center"><a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=er_new_event_set');?>">Add New Training</a></h2>
            </div>
            <?php
        }
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
                    <h3>Registrations for <?php echo $training->event_name; ?></h3>
                    <form id="staff-reg" method="GET" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
                        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                        <!-- Now we can render the completed list table -->
                        <?php $reg_table->display() ?>
                    </form>
                </div>
                <br>
                <form id="manage-events" name="manage-events" method="POST" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
                    <input type="hidden" name="event-id" value="<?php echo $training->id; ?>">
                    <input type="hidden" name="my-mode" value="<?php echo $my_mode; ?>">
                    <input style="float: left; background-image: linear-gradient(#387039, #387039); border-color: #2a5936; color: white"
                           type="submit" name="download-xls" id="download-xls" value="Download This Training Registration as Excel Spreadsheet"/>
                </form>
                <br/><br/><br>
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