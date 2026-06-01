<?php

namespace SOT\TrainingRegistration\Data\Repositories;

defined('ABSPATH') || exit;

/**
 * Class StaffRepository
 *
 * Handles all database operations for the er_staff_profile table.
 *
 * @package SOT\TrainingRegistration\Data\Repositories
 */
class StaffRepository {
    protected $table;

    public function __construct() {
        global $wpdb;
        $this->table = \ER_STAFF_PROFILE;
    }

    public function get_by_id($id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id));
    }

    public function get_all_by_school($school_username) {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$this->table} WHERE school = %s", $school_username));
    }

    public function get_count_by_school($school_username) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->table} WHERE `school` = %s", $school_username));
    }

    public function get_total_count() {
        global $wpdb;
        return $wpdb->get_var("SELECT COUNT(*) FROM {$this->table}");
    }

    public function check_duplicate($first_name, $last_name, $school, $phone) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->table} WHERE `first_name` = %s AND `last_name` = %s AND `school` = %s AND `phone` = %s", $first_name, $last_name, $school, $phone));
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
}
