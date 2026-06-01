<?php

namespace SOT\TrainingRegistration\Admin;

defined('ABSPATH') || exit;

use WP_List_Table;
use SOT\TrainingRegistration\Data\Repositories\StaffRepository;
use SOT\TrainingRegistration\Data\Repositories\EventRepository;
use SOT\TrainingRegistration\Data\Repositories\RegistrationRepository;

if(!class_exists('WP_List_Table')){
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Class StaffRegistrationTableMY
 *
 * Extends WP_List_Table to display training registrations for the SOTAM (MY) mode.
 *
 * @package SOT\TrainingRegistration\Admin
 */
class StaffRegistrationTableMY extends WP_List_Table {

    private $event_id;  // The Training ID that the table would show
    protected $tools;

    /** @var \SOT\TrainingRegistration\Data\Repositories\StaffRepository */
    protected $staff_repo;
    /** @var \SOT\TrainingRegistration\Data\Repositories\EventRepository */
    protected $event_repo;
    /** @var \SOT\TrainingRegistration\Data\Repositories\RegistrationRepository */
    protected $registration_repo;

    function __construct($tools){
        global $status, $page;
        $this->tools = $tools;

        $this->staff_repo = new StaffRepository();
        $this->event_repo = new EventRepository();
        $this->registration_repo = new RegistrationRepository();

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'staff',
            'plural'    => 'staffs',
            'ajax'      => false
        ) );
    }

    function column_default($item, $column_name){
        switch($column_name){
            case 'event_id':
            case 'staff':
            case 'reg_time':
            case 'school':
                return esc_html($item[$column_name]);
            case 'comment':
                return esc_html($item[$column_name]);
            default:
                return esc_html(print_r($item, true));
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
        return $this->tools->idtoName($staff_id);
    }

    function column_staff_position($item) {
        $staff_id = $item['staff'];
        $staff_profile = $this->staff_repo->get_by_id($staff_id);

        return $staff_profile ? esc_html($staff_profile->pos) : '';
    }

    function column_staff_comment($item) {
        $staff_id = $item['staff'];
        $staff_profile = $this->staff_repo->get_by_id($staff_id);

        return $staff_profile ? esc_html($staff_profile->comment) : '';
    }

    function column_staff_sex($item) {
        $staff_id = $item['staff'];
        $staff_profile = $this->staff_repo->get_by_id($staff_id);

        return $staff_profile ? esc_html($staff_profile->sex) : '';
    }

    function column_contact($item) {
        $staff_id = $item['staff'];
        $staff_profile = $this->staff_repo->get_by_id($staff_id);

        return $staff_profile ? '<b>Phone:</b><br>' . esc_html($staff_profile->phone) : '';
    }

    function column_school($item) {
        $user = get_user_by('login', $item['school']);
        $school = $user ? $user->nickname : $item['school'];
        return esc_html($school);
    }

    function get_columns(){
        $columns = array(
            'cb'                => '<input type="checkbox" />',
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
        return array(
            'school'    => array('school', false),
            'reg_time'  => array('reg_time',false)
        );
    }

    function get_bulk_actions() {
        return array(
            'delete'    => 'Remove Trainee'
        );
    }

    function process_bulk_action() {
        if( 'delete'===$this->current_action()) {
            check_admin_referer('bulk-staffs');

            $records_to_remove = isset($_GET['id']) ? (array)$_GET['id'] : array();

            foreach($records_to_remove as $record_id) {
                $this->registration_repo->delete_by_id(intval($record_id));
                $this->event_repo->decrement_registration_count($this->event_id);
            }
        }
    }

    function prepare_items() {
        $per_page = 50;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        $args = array(
            'event_id' => $this->event_id,
            'per_page' => $per_page,
            'offset'   => $offset,
            'search'   => ( ! empty( $_REQUEST['s'] ) ? $_REQUEST['s'] : '' ),
            'orderby'  => ( ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'reg_time' ),
            'order'    => ( ! empty( $_GET['order'] ) ? $_GET['order'] : 'desc' )
        );

        $results = $this->registration_repo->search($args);

        $this->items = $results['items'];

        $this->set_pagination_args( array(
            'total_items' => $results['total'],
            'per_page'    => $per_page,
            'total_pages' => ceil($results['total']/$per_page)
        ) );
    }

    function set_event_id($id) {$this->event_id = $id;}
}
