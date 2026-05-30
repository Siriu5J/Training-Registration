<?php

namespace SOT\TrainingRegistration\Data\Repositories;

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
        $this->table = ER_EVENT_LIST;
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
        $current = $wpdb->get_var($wpdb->prepare("SELECT `num_reg` FROM {$this->table} WHERE `id` = %d", $id));
        return $wpdb->update($this->table, array('num_reg' => $current + 1), array('id' => $id));
    }

    public function decrement_registration_count($id) {
        global $wpdb;
        $current = $wpdb->get_var($wpdb->prepare("SELECT `num_reg` FROM {$this->table} WHERE `id` = %d", $id));
        return $wpdb->update($this->table, array('num_reg' => $current - 1), array('id' => $id));
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
            $where .= " AND end_time < '$time'";
        } elseif ($args['customvar'] == 'all') {
            // no filter
        } else {
            $where .= " AND end_time > '$time'";
        }

        if (!empty($args['search'])) {
            $search_val = esc_sql($wpdb->esc_like($args['search']));
            $where .= " AND (event_name LIKE '%$search_val%' OR location LIKE '%$search_val%')";
        }

        $orderby = esc_sql($args['orderby']);
        $order   = esc_sql($args['order']);

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
