<?php

// Register the plugin settings in ACP
add_action('admin_menu', 'adminSettingsPageRegistration');

function adminSettingsPageRegistration() {
    add_menu_page('Training Registration', 'Training Registration', 'edit_plugins', 'er_gen_set', 'erSettingsPage', 'dashicons-id-alt', 4);
    add_submenu_page('er_gen_set', 'Create Training', 'Create Training', 'edit_plugins', 'er_new_event_set', 'erNewEvent');
    add_submenu_page('er_gen_set', 'View Trainings', 'View Trainings', 'edit_plugins', 'er_event_view_set', 'erViewEvent');
    add_submenu_page('er_gen_set', 'Manage Registrations', 'Manage Registrations', 'edit_plugins', 'er_event_view_reg', 'erViewEventReg');
    add_submenu_page('er_gen_set', 'Settings', 'Settings', 'edit_plugins', 'er_settings', 'erSettings' );

    register_setting('reading', 'show_availability');
}


// Admin Message Boxes
function createEventNotAllowed() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p>You cannot limit training registration number with an unlimited registration!</p>
    </div>
    <?php
}
function tableSuccessCreation() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p>Training has been created successfully! Click <a href="../wp-admin/admin.php?page=er_event_view_set">here</a> to see the training you created.</p>
    </div>
    <?php
}
function tableSuccessUpdate() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p>Training has been updated successfully!</p>
    </div>
    <?php
}
function tableFailedCreation() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p>Failed to create training!</p>
    </div>
    <?php
}
function tableAlreadyExist() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p>Event already exist!</p>
    </div>
    <?php
}
function settingsUpdated() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p>Settings Updated! (You might need to refresh this page to see the updated settings on this page)</p>
    </div>
    <?php
}


/*
MAIN SETTINGS PAGE
 */
function erSettingsPage() {
    ?>

    <h1>Event Registration Plugin V2</h1>
    <br>
    <p>V1 of this plugin is an unpublished beta version of this plugin. The V2 version is a rewritten version of V1, focusing on optimizing the codes and providing some minor visual update.</p>

    <?php
}


/*
CREATE TRAINING PAGE
 */
function erNewEvent() {
    ?>
    <h1>Create New Training Event</h1>
    <p>Please make sure that the start time is <strong>always</strong> before the end time. Also, the start time must be in the future to have the system recognize the training as an upcoming training.</p>
    <form id="new-event" name="new-event" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
        <label for="event-name">Training Name:<br></label><input class="er_input" type="text" name="event-name" id="event-name" required/>
        <br><br>
        <label for="open-date">Registration Open Date:<br></label><input class="er_input" type="datetime-local" name="open-date" id="open-date" value="<?php echo date("Y-m-d\TH:i", mktime(0,0)) ?>" required/>
        <br><br>
        <label for="close-date">Registration Close Date:<br></label><input class="er_input" type="datetime-local" name="close-date" id="close-date" value="<?php echo date("Y-m-d\TH:i", strtotime("+31 days" ,mktime(0,0))) ?>" required/>
        <br><br>
        <label for="start-date">Training Start Date:<br></label><input class="er_input" type="datetime-local" name="start-date" id="start-date" value="<?php echo date("Y-m-d\TH:i", strtotime("+31 days" ,mktime(0,0))) ?>" required/>
        <br><br>
        <label for="end-date">Training End Date:<br></label><input class="er_input" type="datetime-local" name="end-date" id="end-date" value="<?php echo date("Y-m-d\TH:i", strtotime("+31 days" ,mktime(0,0))) ?>" required/>
        <br><br>
        <label for="max">Max Registration (optional; leave blank for unlimited):<br></label><input class="er_input" type="number" name="max" id="max" min="0" step="1"/>
        <br><br>
        <label for="max-limit">Limit Maximum registration? &nbsp;&nbsp;&nbsp;</label><input class="er_input" type="checkbox" name="max-limit" id="max-limit" value="1"/>
        <br><br>
        <label for="location">Location:<br></label><input class="er_input" type="text" name="location" id="location" required/>
        <br><br>
        <label for="comment">Information on Training Event<br></label><textarea name="comment" id="comment" cols="45" rows="5"></textarea>
        <br><br>
        <label for="activated">Activated? &nbsp;&nbsp;&nbsp;</label><input class="er_input" type="checkbox" name="activated" id="activated" value="1"/>
        <br><br>
        <input type="submit" name="create_training" id="create_training" value="Create Training" />
    </form>
    <?php
}

// Create new table for the new event and fill first data
if($_POST['create_training']) {
    $event_name     = $_POST['event-name'];
    $location       = $_POST['location'];
    $limit_max      = (int)$_POST['max-limit'];
    $max            = $_POST['max'];

    if($max == 0 && $limit_max == '1') {
        add_action('admin_notices', 'createEventNotAllowed');
    } elseif (isValidEvent($event_name, $location)) { // Check if the training name is valid
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

        if ($success) {add_action('admin_notices', 'tableSuccessCreation');}
        else {add_action('admin_notices', 'tableFailedCreation');}
    } else {
        add_action('admin_notices', 'tableAlreadyExist');
    }
}


/*
VIEW TRAINING PAGE
 */
function erViewEvent() {
    // HTML Information
    global $wpdb;
    $event_table = ER_EVENT_LIST;

    // Only show information if there are trainings
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
                        <td><?php echo spotsOpen($training->max, $training->num_reg) ?></td>
                        <td><?php echo availability($training) ?></td>
                        <td style="text-align: center; vertical-align: middle;">
                            <div style="<?php if ($training->activated == 1) {
                                echo 'background-color: darkgreen';
                            } else {
                                echo 'background-color: darkred';
                            } ?>">&nbsp;
                            </div>
                        </td>
                        <td style="text-align: center; vertical-align: middle; padding-top: 2.75pt"><input type="radio"
                                                                                                           name="select"
                                                                                                           value="<?php echo $training->id ?>"
                                                                                                           required/>
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
            echo 'style="float: right; background-image: linear-gradient(#c62828, #c62828); border-color: #8e0000';
        } else {
            echo 'style="float: right; background-image: linear-gradient(grey, grey); border-color: grey';
        } ?>; color: white" name="remove-1" id="remove-1" value="Remove Training" <?php if ($_POST['remove-1']) {
            echo 'disabled';
        } ?>/>
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
    $name       =   $_POST['update-event-name'];
    $max        =   $_POST['update-max'];
    $limit_max  =   (int)$_POST['update-max-limit'];
    $location   =   $_POST['update-location'];
    $open_time  =   $_POST['update-open-date'];
    $close_time =   $_POST['update-close-date'];
    $start_time =   $_POST['update-start-date'];
    $end_time   =   $_POST['update-end-date'];
    $comment    =   $_POST['update-comment'];
    $event_id   =   $_POST['update-event_id'];
    $event_list_tb = ER_EVENT_LIST;

    // If max is unfilled, set as 9999
    if ($max == 0) {
        $max = -999;
    }

    $wpdb->update($event_list_tb, array(
        "event_name"    =>  $name,
        "max"           =>  $max,
        "open_time"     =>  $open_time,
        "close_time"    =>  $close_time,
        "start_time"    =>  $start_time,
        "end_time"      =>  $end_time,
        "location"      =>  $location,
        "limit_max"     =>  $limit_max,
        "comment"       =>  $comment,
    ), array(
        "id"            =>  $event_id,
    ));

    add_action('admin_notices', 'tableSuccessUpdate');
}


/*
MANAGE REGISTRATION PAGE
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class StaffRegTable extends WP_List_Table {

    private $event_id;  // The Training ID that the table would show


    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'staff',     //singular name of the listed records
            'plural'    => 'staffs',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

    }

    // The column renderer if a custom one is not found
    // Custom renderer looks like: function column_{column_name}()
    function column_default($item, $column_name){
        switch($column_name){
            case 'event_id':
            case 'staff':
            case 'reg_time':
            case 'school':
            case 'comment':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ 'id',  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }

    function column_staff_name($item) {
        $staff_id = $item['staff'];
        global $wpdb;
        $staff_table = ER_STAFF_PROFILE;
        $cn_name = $wpdb->get_var("SELECT `cn_name` FROM $staff_table WHERE `id` = $staff_id");

        // Only show Chinese name when it exists
        if ($cn_name != "") {
            $cn_name = '<br>('.$cn_name.')';
        } else {
            $cn_name = "";
        }

        return idtoName($staff_id).' '.$cn_name;
    }

    function column_staff_position($item) {
        $staff_id = $item['staff'];
        global $wpdb;
        $staff_table = ER_STAFF_PROFILE;
        $staff_profile = $wpdb->get_row("SELECT * FROM $staff_table WHERE `id` = $staff_id");

        // Don't show "Not in LC"
        if ($staff_profile->lc == "Not in LC") {
            $position = $staff_profile->pos;
        } else {
            $position = $staff_profile->pos.' at '.$staff_profile->lc;
        }

        return $position;
    }

    function column_staff_comment($item) {
        $staff_id = $item['staff'];
        global $wpdb;
        $staff_table = ER_STAFF_PROFILE;

        return $wpdb->get_var("SELECT `comment` FROM $staff_table WHERE `id` = $staff_id");
    }

    function column_staff_sex($item) {
        $staff_id = $item['staff'];
        global $wpdb;
        $staff_table = ER_STAFF_PROFILE;

        return $wpdb->get_var("SELECT `sex` FROM $staff_table WHERE `id` = $staff_id");
    }

    function column_staff_age($item) {
        $staff_id = $item['staff'];
        global $wpdb;
        $staff_table = ER_STAFF_PROFILE;

        return $wpdb->get_var("SELECT `age` FROM $staff_table WHERE `id` = $staff_id");
    }

    function column_contact($item) {
        $staff_id = $item['staff'];
        global $wpdb;
        $staff_table = ER_STAFF_PROFILE;
        $staff_profile = $wpdb->get_row("SELECT * FROM $staff_table WHERE `id` = $staff_id");

        return '<b>Phone:</b><br>'.$staff_profile->phone.'<br/><b>Email:</b><br>'.$staff_profile->email;
    }

    function column_school($item) {
        return get_user_by(login,$item['school'])->nickname;
    }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'staff_name'        => 'Name',
            'staff_sex'         => 'Sex',
            'staff_age'         => 'Age',
            'staff_position'    => 'Position',
            'school'            => 'School',
            'contact'           => 'Contact',
            'reg_time'          => 'Reg Time',
            'staff_comment'     => 'Staff Comment',
            'comment'           => 'Reg Comment'
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'school'    => array('school', false),
            'reg_time'  => array('reg_time',false)
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Remove Trainee'
        );
        return $actions;
    }

    function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action()) {
            global $wpdb;
            $reg_table = ER_REGISTRATION_LIST;
            $event_table = ER_EVENT_LIST;

            $record_to_remove = $_GET['id'];

            foreach($record_to_remove as $record) {
                // STEP 1: remove from registration list
                $wpdb->delete($reg_table, array(
                    'event_id' => $this->event_id,
                    'id'    => $record
                ));

                // STEP 2: free up space in event record
                $wpdb->update($event_table, array(
                    'num_reg' => $wpdb->get_var("SELECT `num_reg` FROM $event_table WHERE `id` = $this->event_id") - 1,
                ), array (
                    'id' => $this->event_id,
                ));

            }
        }

    }

    function prepare_items() {
        global $wpdb;
        $reg_table = ER_REGISTRATION_LIST;

        $per_page = 50;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        // Sorting
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'reg_time';
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';

        $data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $reg_table WHERE `event_id` = $this->event_id ORDER BY $orderby $order"),ARRAY_A);

        $current_page = $this->get_pagenum();

        $total_items = count($data);

        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

        $this->items = $data;

        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

    // Set Page id;
    function set_event_id($id) {$this->event_id = $id;}
}


// Main function for MANAGE REGISTRATION PAGE
function erViewEventReg() {
    // HTML Information
    global $wpdb;
    $event_table = ER_EVENT_LIST;

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
                        <td><?php echo spotsOpen($training->max, $training->num_reg) ?></td>
                        <td><?php echo availability($training) ?></td>              
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
        	$reg_table = new StaffRegTable();
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
            <form id="manage-events" name="manage-events" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            	<input type="hidden" name="event-id" value="<?php echo $training->id; ?>">
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




/*
PLUGIN SETTINGS
*/
function erSettings() {
	$show_available = (int)get_option( 'show_availability', 0 );

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
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" class="button button-primary" name="save-settings" id="save-settings" value="Save Settings">
		</p>
	</form>
	<?php
}

if ($_POST['save-settings']) {
		if ($_POST['show-available'] == 1) {
			update_option( 'show_availability', 1);
		} else {
			update_option( 'show_availability', 0);
		}
		
		add_action('admin_notices', 'settingsUpdated');
}

// Download Excel form
// Generate XLS
// Excel export code by Oliver Schwarz <oliver.schwarz@gmail.com>
if ($_POST['download-xls']) {
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


    require_once (ER_PLUGIN_DIR . '/to-excel.php');
    $excel = new Excel_XML;
    $excel->addWorksheet($worksheet_name, $data_array);
    $excel->sendWorkbook('Training_Registration_' . $event_info->location . '_' . date("Y-m-d", strtotime($event_info->start_time)) . '.xls');
}
