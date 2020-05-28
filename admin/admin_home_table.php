<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class admin_home_table extends WP_List_Table {
    protected $tools;

    /** Class constructor */
    public function __construct($filter = 1) {
        parent::__construct([
            'singular'  =>  'Training',
            'plural'    =>  'Trainings',
            'ajax'      =>  false
        ]);

        // Initialize tools
        if (!class_exists('training_registration_tools')) {
            require_once(ER_PLUGIN_DIR . '/includes/tools.php');
        }
        $this->tools = new training_registration_tools();
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
        return '<a href="' . get_admin_url(get_current_blog_id(), 'admin.php?page=er_new_event_set') . '&event-id='. $item['id'] . '">' . $item['event_name'] . '</a>' . ( $item['activated'] == 0 ? ' (Deactivated)' : '');
    }

    function column_availability($item) {
        return $this->tools->spotsOpen($item['max'], $item['num_reg']) . '<br />' . $this->tools->availability((object) $item);
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
        $download = '<a href="' . $_SERVER['REQUEST_URI'] . '&id=' . $item['id'] . '&print-excel=true&mode='. $mode . '">Download</a>';
        $view     = '<a href="#">View</a>';
        return $download . '<br />' . $view;
    }

    /** Sortables */
    function get_sortable_columns() {
        return array(
            'event_name'    => array('event_name', true),
            'location'      => array('location', true),
        );
    }

    /** Usort */
    function usort_reorder( $a, $b ) {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'id';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
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
        global $wpdb;
        $time = current_time('mysql');

        $per_page = 20;
        $current_page = $this->get_pagenum();
        if ( 1 < $current_page ) {
            $offset = $per_page * ( $current_page - 1 );
        } else {
            $offset = 0;
        }

        // Search and filter
        $search = '';
        //Retrieve $customvar for use in query to get items.
        $customvar = ( isset($_REQUEST['customvar']) ? $_REQUEST['customvar'] : '');
        if($customvar == '') {
            $search_custom_vars= "AND end_time > '$time'";
        } elseif ($customvar == 'all')	{
            $search_custom_vars = '';
        } else {
            $search_custom_vars = "AND end_time < '$time'";
        }
        if ( ! empty( $_REQUEST['s'] ) ) {
            $search = "AND event_name LIKE '%" . esc_sql( $wpdb->esc_like( $_REQUEST['s'] ) ) . "%' OR location LIKE '%" . esc_sql( $wpdb->esc_like( $_REQUEST['s'] ) ) . "%'";
        }

        $columns = $this->get_columns();
        $sortable = $this->get_sortable_columns();
        $hidden = array();
        $items = $wpdb->get_results( "SELECT * FROM ".ER_EVENT_LIST." WHERE 1=1 {$search} {$search_custom_vars}" . $wpdb->prepare( "ORDER BY id DESC LIMIT %d OFFSET %d;", $per_page, $offset ),ARRAY_A);

        $this->_column_headers = array($columns, $hidden, $sortable);
        usort($items, array(&$this, 'usort_reorder'));

        $count = $wpdb->get_var("SELECT COUNT(*) FROM ".ER_EVENT_LIST." WHERE 1 = 1 {$search} {$search_custom_vars}");

        $this->items = $items;

        // Set pagination
        $this->set_pagination_args( array(
            'total_items'   =>  $count,
            'per_page'      =>  $per_page,
            'total_pages'   =>  ceil($count/$per_page)
        ));
    }
}