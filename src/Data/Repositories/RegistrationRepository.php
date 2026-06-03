<?php

namespace SOT\TrainingRegistration\Data\Repositories;

defined('ABSPATH') || exit;

/**
 * Class RegistrationRepository
 *
 * Handles all database operations for the er_event_reg table.
 *
 * @package SOT\TrainingRegistration\Data\Repositories
 */
class RegistrationRepository {
    protected $table;

    public function __construct() {
        global $wpdb;
        $this->table = \ER_REGISTRATION_LIST;
    }

    public function get_by_id($id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id));
    }

    public function get_by_event($event_id) {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$this->table} WHERE `event_id` = %d", $event_id));
    }

    public function get_by_staff($staff_id) {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$this->table} WHERE `staff` = %d", $staff_id));
    }

    public function get_by_event_and_school($event_id, $school_username) {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$this->table} WHERE `event_id` = %d AND `school` = %s", $event_id, $school_username));
    }

    public function check_duplicate($staff_id, $event_id) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->table} WHERE `staff` = %d AND `event_id` = %d", $staff_id, $event_id));
    }

    public function get_total_count() {
        global $wpdb;
        return $wpdb->get_var("SELECT COUNT(*) FROM {$this->table}");
    }

    public function get_total_count_by_school($school_username) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->table} WHERE `school` = %s", $school_username));
    }

    public function get_school_agenda($school_username, $days = 90) {
        global $wpdb;
        $event_table = \ER_EVENT_LIST;
        $now = current_time('mysql');
        $future = date('Y-m-d H:i:s', strtotime("+$days days", current_time('timestamp')));

        $query = $wpdb->prepare("
            SELECT e.id as event_id, e.event_name, e.start_time, e.location, COUNT(r.staff) as staff_count
            FROM {$this->table} r
            JOIN {$event_table} e ON r.event_id = e.id
            WHERE r.school = %s 
            AND e.end_time >= %s 
            AND e.start_time <= %s
            AND e.activated = 1
            GROUP BY e.id
            ORDER BY e.start_time ASC
        ", $school_username, $now, $future);

        return $wpdb->get_results($query);
    }

    public function get_recent_count($days = 30) {
        global $wpdb;
        $date = date('Y-m-d H:i:s', strtotime("-$days days", current_time('timestamp')));
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->table} WHERE `reg_time` >= %s", $date));
    }

    public function insert($data) {
        global $wpdb;
        $result = $wpdb->insert($this->table, $data);
        return $result ? $wpdb->insert_id : false;
    }

    public function delete_by_id($id) {
        global $wpdb;
        return $wpdb->delete($this->table, array('id' => $id));
    }

    public function delete_by_event_and_staff($event_id, $staff_id) {
        global $wpdb;
        return $wpdb->delete($this->table, array(
            'event_id' => $event_id,
            'staff'    => $staff_id,
        ));
    }

    public function delete_by_event($event_id) {
        global $wpdb;
        return $wpdb->delete($this->table, array('event_id' => $event_id));
    }

    public function search($args = array()) {
        global $wpdb;
        $staff_table = \ER_STAFF_PROFILE;
        
        $defaults = array(
            'event_id' => 0,
            'per_page' => 50,
            'offset'   => 0,
            'search'   => '',
            'orderby'  => 'reg_time',
            'order'    => 'desc'
        );
        $args = wp_parse_args($args, $defaults);

        $where = $wpdb->prepare("WHERE r.event_id = %d", $args['event_id']);
        
        if (!empty($args['search'])) {
            $search_val = '%' . $wpdb->esc_like($args['search']) . '%';
            $where .= $wpdb->prepare(" AND (s.first_name LIKE %s OR s.last_name LIKE %s OR r.school LIKE %s)", $search_val, $search_val, $search_val);
        }

        $allowed_orderby = array('id', 'event_id', 'staff', 'reg_time', 'school');
        $orderby = in_array($args['orderby'], $allowed_orderby) ? $args['orderby'] : 'reg_time';
        $order   = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';

        $query = "
            SELECT r.* FROM {$this->table} r
            LEFT JOIN {$staff_table} s ON r.staff = s.id
            $where
            ORDER BY $orderby $order
            LIMIT %d OFFSET %d
        ";

        $items = $wpdb->get_results(
            $wpdb->prepare($query, $args['per_page'], $args['offset']),
            ARRAY_A
        );

        $total_query = "
            SELECT COUNT(*) FROM {$this->table} r
            LEFT JOIN {$staff_table} s ON r.staff = s.id
            $where
        ";
        $total = $wpdb->get_var($total_query);

        return array(
            'items' => $items,
            'total' => $total
        );
    }
}
