<?php
/**
 * class StaffRegTable
 *
 * This class extends the WP_List_Table to show custom training registrations. This is called by admin settings.
 * This is derived from a GitHub project that I lost
 *
 * @since 2019-12
 * @version 2.0
 *
 * @package training-registration
 */

if(!class_exists('WP_List_Table')){
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class StaffRegTableCN extends WP_List_Table {

    protected $tools;
    private $event_id;  // The Training ID that the table would show


    function __construct($tools){
        global $status, $page;
        $this->tools = $tools;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'staff',     //singular name of the listed records
            'plural'    => 'staffs',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

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

        return $this->tools->idtoName($staff_id).' '.$cn_name;
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
        return get_user_by('login',$item['school'])->nickname;
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


class StaffRegTableMY extends WP_List_Table {

    private $event_id;  // The Training ID that the table would show
    protected $tools;

    function __construct($tools){
        global $status, $page;
        $this->tools = $tools;

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

        return $this->tools->idtoName($staff_id);
    }

    function column_staff_position($item) {
        $staff_id = $item['staff'];
        global $wpdb;
        $staff_table = ER_STAFF_PROFILE;
        $staff_profile = $wpdb->get_row("SELECT * FROM $staff_table WHERE `id` = $staff_id");

        return $staff_profile->pos;
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

    function column_contact($item) {
        $staff_id = $item['staff'];
        global $wpdb;
        $staff_table = ER_STAFF_PROFILE;
        $staff_profile = $wpdb->get_row("SELECT * FROM $staff_table WHERE `id` = $staff_id");

        return '<b>Phone:</b><br>'.$staff_profile->phone;
    }

    function column_school($item) {
        return get_user_by('login',$item['school'])->nickname;
    }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'staff_name'        => 'Name',
            'staff_sex'         => 'Sex',
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