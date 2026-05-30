<?php

namespace SOT\TrainingRegistration\Admin;

use WP_List_Table;
use SOT\TrainingRegistration\Core\Tools;
use SOT\TrainingRegistration\Data\Repositories\EventRepository;

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class HomeTable
 *
 * Handles the display of the training events list in the WordPress admin area.
 *
 * @package SOT\TrainingRegistration\Admin
 */
class HomeTable extends WP_List_Table {
    protected $tools;
    /** @var \SOT\TrainingRegistration\Data\Repositories\EventRepository */
    protected $event_repo;

    /** Class constructor */
    public function __construct($filter = 1) {
        parent::__construct([
            'singular'  =>  'Training',
            'plural'    =>  'Trainings',
            'ajax'      =>  false
        ]);

        $this->tools = new Tools();
        $this->event_repo = new EventRepository();
    }

    /** No Items */
    public function no_items() {
        _e( 'No trainings found.' );
    }

    /** Columns */
    function get_columns() {
        $columns = array(
            'event_name'    =>  'Training',
            'location'      =>  'Location',
            'availability'  =>  'Availability',
            'reg_dates'     =>  'Registration Dates',
            'training_dates'=>  'Training Dates',
            'print_sheet'   =>  'Registrations'
        );

        return $columns;
    }

    /** Default Column Renderer */
    function column_default($item, $column_name) {
        switch($column_name) {
            case 'event_name':
            case 'location':
            return $item[$column_name];
            default:
                return print_r($item, true);    // For debug purpose
        }
    }

    function column_event_name($item) {
        return '<a href="' . get_admin_url(get_current_blog_id(), 'admin.php?page=er_new_event_set') . '&event-id='. $item['id'] . '&view-event">' . $item['event_name'] . '</a>' . ( $item['activated'] == 0 ? ' (Deactivated)' : '');
    }

    function column_availability($item) {
        return $this->tools->spotsOpen($item['id']) . '<br />' . $this->tools->availability((object) $item);
    }

    function column_reg_dates($item) {
        $reg_start = date("Y-m-d", strtotime($item['open_time']));
        $reg_end = date("Y-m-d", strtotime($item['close_time']));

        return '<b>Open: </b>' . $reg_start . '<br /><b>Close: </b>' . $reg_end;
    }

    function column_training_dates($item) {
        $training_start = date("Y-m-d", strtotime($item['start_time']));
        $training_end = date("Y-m-d", strtotime($item['end_time']));

        return '<b>Start: </b>' . $training_start . '<br /><b>End: </b>' . $training_end;
    }

    function column_print_sheet($item) {
        $mode = get_option('my_mode');
        $nonce = wp_create_nonce('excel_export_nonce');
        $download = '<a href="' . esc_url($_SERVER['REQUEST_URI'] . '&id=' . $item['id'] . '&print-excel=true&mode='. $mode . '&nonce=' . $nonce) . '">Download</a>';
        $view     = '<a href="'. get_admin_url(get_current_blog_id(), 'admin.php?page=er_view_reg_set') . '&event-id=' . $item['id'] .'">View</a>';
        return $download . '<br />' . $view;
    }

    /** Sortables */
    function get_sortable_columns() {
        return array(
            'event_name'    => array('event_name', true),
            'location'      => array('location', true),
        );
    }

    /** Filters */
    protected function get_views() {
        $views = array();
        $current = ( !empty($_REQUEST['customvar']) ? $_REQUEST['customvar'] : 'current');

        //All link
        $class = ($current == 'current' ? ' class="current"' :'');
        $all_url = remove_query_arg('customvar');
        $views['current'] = "<a href='{$all_url }' {$class} >Upcoming</a>";

        //All link
        $foo_url = add_query_arg('customvar','all');
        $class = ($current == 'all' ? ' class="current"' :'');
        $views['all'] = "<a href='{$foo_url}' {$class} >All</a>";

        //Past
        $bar_url = add_query_arg('customvar','past');
        $class = ($current == 'past' ? ' class="current"' :'');
        $views['past'] = "<a href='{$bar_url}' {$class} >Past</a>";

        return $views;
    }

    public function prepare_items() {
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $offset = ( $current_page - 1 ) * $per_page;

        $args = array(
            'per_page'  => $per_page,
            'offset'    => $offset,
            'search'    => ( ! empty( $_REQUEST['s'] ) ? $_REQUEST['s'] : '' ),
            'customvar' => ( ! empty( $_REQUEST['customvar'] ) ? $_REQUEST['customvar'] : 'current' ),
            'orderby'   => ( ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'id' ),
            'order'     => ( ! empty( $_GET['order'] ) ? $_GET['order'] : 'desc' )
        );

        $results = $this->event_repo->search($args);

        $columns = $this->get_columns();
        $sortable = $this->get_sortable_columns();
        $hidden = array();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->items = $results['items'];

        // Set pagination
        $this->set_pagination_args( array(
            'total_items'   =>  $results['total'],
            'per_page'      =>  $per_page,
            'total_pages'   =>  ceil($results['total']/$per_page)
        ));
    }
}
