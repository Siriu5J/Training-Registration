<?php
/**
 * Class RegistrationRepository
 *
 * Handles all database operations for the er_event_reg table.
 *
 * @package training-registration
 */

class RegistrationRepository {
    protected $table;

    public function __construct() {
        global $wpdb;
        $this->table = ER_REGISTRATION_LIST;
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

    public function check_duplicate($staff_id, $event_id) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->table} WHERE `staff` = %d AND `event_id` = %d", $staff_id, $event_id));
    }

    public function insert($data) {
        global $wpdb;
        $result = $wpdb->insert($this->table, $data);
        return $result ? $wpdb->insert_id : false;
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
}
