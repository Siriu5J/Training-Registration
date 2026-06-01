<?php

namespace SOT\TrainingRegistration\Data\Repositories;

defined('ABSPATH') || exit;

/**
 * Class EventRepository
 *
 * Handles all database operations for the er_event_list table.
 *
 * @package SOT\TrainingRegistration\Data\Repositories
 */
class EventRepository {
    protected $table;

    public function __construct() {
        global $wpdb;
        $this->table = \ER_EVENT_LIST;
    }

    public function get_by_id($id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id));
    }

    public function get_all_upcoming($time_now = null) {
        global $wpdb;
        if (!$time_now) {
            $time_now = current_time('mysql');
        }
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$this->table} WHERE `start_time` > %s AND `activated` = 1", $time_now));
    }

    public function get_count_upcoming($time_now = null) {
        global $wpdb;
        if (!$time_now) {
            $time_now = current_time('mysql');
        }
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->table} WHERE `activated` = 1 AND `start_time` > %s", $time_now));
    }

    public function get_count_open($time_now = null) {
        global $wpdb;
        if (!$time_now) {
            $time_now = current_time('mysql');
        }
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->table} WHERE `activated` = 1 AND `open_time` <= %s AND `close_time` >= %s", $time_now, $time_now));
    }

    /**
     * Get summary stats for events starting in the next X days
     */
    public function get_agenda_stats($days = 14) {
        global $wpdb;
        $now = current_time('mysql');
        $future = date('Y-m-d H:i:s', strtotime("+$days days", current_time('timestamp')));
        
        $query = $wpdb->prepare(
            "SELECT COUNT(*) as event_count, SUM(num_reg) as staff_count 
             FROM {$this->table} 
             WHERE `activated` = 1 
             AND `start_time` >= %s 
             AND `start_time` <= %s",
            $now, $future
        );
        
        return $wpdb->get_row($query);
    }

    /**
     * Get list of events starting in the next X days
     */
    public function get_upcoming_agenda($days = 14) {
        global $wpdb;
        $now = current_time('mysql');
        $future = date('Y-m-d H:i:s', strtotime("+$days days", current_time('timestamp')));
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table} 
             WHERE `activated` = 1 
             AND `start_time` >= %s 
             AND `start_time` <= %s 
             ORDER BY `start_time` ASC",
            $now, $future
        ));
    }

    public function get_total_count() {
        global $wpdb;
        return $wpdb->get_var("SELECT COUNT(*) FROM {$this->table}");
    }

    public function insert($data) {
        global $wpdb;
        $result = $wpdb->insert($this->table, $data);
        return $result ? $wpdb->insert_id : false;
    }

    public function update($id, $data) {
        global $wpdb;
        return $wpdb->update($this->table, $data, array('id' => $id));
    }

    public function delete($id) {
        global $wpdb;
        return $wpdb->delete($this->table, array('id' => $id));
    }

    public function get_duplicates($name) {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$this->table} WHERE `event_name` = %s", $name));
    }

    public function increment_registration_count($id) {
        global $wpdb;
        return $wpdb->query($wpdb->prepare("UPDATE {$this->table} SET `num_reg` = `num_reg` + 1 WHERE `id` = %d", $id));
    }

    public function decrement_registration_count($id) {
        global $wpdb;
        return $wpdb->query($wpdb->prepare("UPDATE {$this->table} SET `num_reg` = `num_reg` - 1 WHERE `id` = %d", $id));
    }

    public function search($args = array()) {
        global $wpdb;
        $defaults = array(
            'per_page' => 20,
            'offset'   => 0,
            'search'   => '',
            'customvar'=> 'current',
            'orderby'  => 'id',
            'order'    => 'desc'
        );
        $args = wp_parse_args($args, $defaults);
        $time = current_time('mysql');

        $where = "WHERE 1=1";
        if ($args['customvar'] == 'past') {
            $where .= $wpdb->prepare(" AND end_time < %s", $time);
        } elseif ($args['customvar'] == 'all') {
            // no filter
        } else {
            $where .= $wpdb->prepare(" AND end_time > %s", $time);
        }

        if (!empty($args['search'])) {
            $search_val = '%' . $wpdb->esc_like($args['search']) . '%';
            $where .= $wpdb->prepare(" AND (event_name LIKE %s OR location LIKE %s)", $search_val, $search_val);
        }

        $allowed_orderby = array('id', 'event_name', 'open_time', 'close_time', 'start_time', 'end_time', 'location', 'num_reg');
        $orderby = in_array($args['orderby'], $allowed_orderby) ? $args['orderby'] : 'id';
        $order   = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';

        $items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} $where ORDER BY $orderby $order LIMIT %d OFFSET %d",
                $args['per_page'],
                $args['offset']
            ),
            ARRAY_A
        );

        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table} $where");

        return array(
            'items' => $items,
            'total' => $total
        );
    }
}
