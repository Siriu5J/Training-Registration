<?php

namespace SOT\TrainingRegistration\Admin;

use WP_List_Table;
use SOT\TrainingRegistration\Data\Repositories\StaffRepository;
use SOT\TrainingRegistration\Data\Repositories\EventRepository;
use SOT\TrainingRegistration\Data\Repositories\RegistrationRepository;

if(!class_exists('WP_List_Table')){
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Class StaffRegistrationTableCN
 *
 * Extends WP_List_Table to display training registrations for the default (CN) mode.
 *
 * @package SOT\TrainingRegistration\Admin
 */
class StaffRegistrationTableCN extends WP_List_Table {

    protected $tools;
    private $event_id;  // The Training ID that the table would show

    protected $staff_repo;
    protected $event_repo;
    protected $registration_repo;


    function __construct($tools){
        global $status, $page;
        $this->tools = $tools;

        $this->staff_repo = new StaffRepository();
        $this->event_repo = new EventRepository();
        $this->registration_repo = new RegistrationRepository();

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'staff',     //singular name of the listed records
            'plural'    => 'staffs',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

    }

    /** No Items */
    public function no_items() {
        _e( 'No trainings found.' );
    }

    function get_columns(){
        $columns = array(
            'cb'                => '<input type="checkbox" />',
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

    function column_default($item, $column_name){
        switch($column_name){
            case 'event_id':
            case 'staff':
            case 'reg_time':
            case 'school':
            case 'comment':
                return $item[$column_name];
            default:
                return print_r($item,true);
        }
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            'id',
            $item['id']
        );
    }

    function column_staff_name($item) {
        $staff_id = $item['staff'];
        $staff_profile = $this->staff_repo->get_by_id($staff_id);
        $cn_name = $staff_profile ? $staff_profile->cn_name : "";

        if ($cn_name != "") {
            $cn_name = '<br>('.$cn_name.')';
        } else {
            $cn_name = "";
        }

        return $this->tools->idtoName($staff_id).' '.$cn_name;
    }

    function column_staff_position($item) {
        $staff_id = $item['staff'];
        $staff_profile = $this->staff_repo->get_by_id($staff_id);

        if (!$staff_profile) return '';

        if ($staff_profile->lc == "Not in LC") {
            $position = $staff_profile->pos;
        } else {
            $position = $staff_profile->pos.' at '.$staff_profile->lc;
        }

        return $position;
    }

    function column_staff_comment($item) {
        $staff_id = $item['staff'];
        $staff_profile = $this->staff_repo->get_by_id($staff_id);

        return $staff_profile ? $staff_profile->comment : '';
    }

    function column_staff_sex($item) {
        $staff_id = $item['staff'];
        $staff_profile = $this->staff_repo->get_by_id($staff_id);

        return $staff_profile ? $staff_profile->sex : '';
    }

    function column_staff_age($item) {
        $staff_id = $item['staff'];
        $staff_profile = $this->staff_repo->get_by_id($staff_id);

        return $staff_profile ? $staff_profile->age : '';
    }

    function column_contact($item) {
        $staff_id = $item['staff'];
        $staff_profile = $this->staff_repo->get_by_id($staff_id);

        if (!$staff_profile) return '';

        return '<b>Phone:</b><br>'.$staff_profile->phone.'<br/><b>Email:</b><br>'.$staff_profile->email;
    }

    function column_school($item) {
        $user = get_user_by('login',$item['school']);
        return $user ? $user->nickname : $item['school'];
    }

    function get_sortable_columns() {
        return array(
            'staff_name'=> array('staff_name', false),
            'school'    => array('school', false),
            'reg_time'  => array('reg_time',true)
        );
    }

    function get_bulk_actions() {
        return array(
            'delete'    => 'Remove Trainee'
        );
    }

    function process_bulk_action() {
        if( 'delete'===$this->current_action()) {
            $record_to_remove = $_GET['id'];

            foreach($record_to_remove as $record) {
                global $wpdb;
                $wpdb->delete(ER_REGISTRATION_LIST, array(
                    'event_id' => $this->event_id,
                    'id'    => $record
                ));
                $this->event_repo->decrement_registration_count($this->event_id);
            }
        }
    }

    function prepare_items() {
        $per_page = 50;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'reg_time';
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';

        global $wpdb;
        $data = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".ER_REGISTRATION_LIST." WHERE `event_id` = %d ORDER BY $orderby $order", $this->event_id),ARRAY_A);

        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

        $this->items = $data;

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items/$per_page)
        ) );
    }

    function set_event_id($id) {$this->event_id = $id;}
}
