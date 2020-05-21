<?php
/**
 * Class training_registration_tools
 *
 * This is the tools library that I built with various functions for the plugin
 *
 * @since 2020-5-19
 * @version 1.0
 *
 * @package training-registration
 */

class training_registration_tools {
    public function isValidEvent($name, $location, $start_date) {
        global $wpdb;
        $table = ER_EVENT_LIST;
        $duplicates = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE `event_name` = $name"));   // Get all trainings that has the same name

        if (empty($duplicates)) {
            return true;    // No duplicated names, training name is valid
        } else {
            foreach ($duplicates as $training) {
                if ($training->location == $location) { // check location
                    if ($training->start_time == $start_date) { // Check time
                        return false;
                    }
                }
            }
            return true;    // Checked through all name duplicates, no location duplicate, training name valid
        }
    }

    // Translate the available slot from number to useful information
    public function spotsOpen ($max, $occupied) {

        // Query for the number of users

        if ($max == -999) {
            return "Unlimited, ".$occupied." registered";
        } elseif ($max-$occupied > 0) {
            return $max-$occupied.'/'.$max.' available';
        } elseif ($max-$occupied == 0) {
            return "0/$max, Full";
        }else {
            $occupied = $occupied-$max; // Can't do operations in between strings
            return "0/$max, $occupied overflow(s)";
        }
    }

    // Returns the status of the training in word
    public function availability($row) {
        $now = current_time('mysql');

        // Time State
        if ($now > $row->close_time) {
            $ts = 'Closed';
        } elseif ($now < $row->open_time) {
            $ts = 'To Be Open';
        } else {
            $ts = 'Open';
        }

        if ($row->num_reg < $row->max || ($row->limit_max == 0 && $row->max == -999)) { // Not full OR not capped AND no expected limit
            return $ts;
        } elseif ($row->limit_max == 0 && $row->max != -999) {  // not capped AND has expected limit
            return $ts.'; Full but not capped';
        } else {
            return 'Full and capped';
        }
    }

// Check to see if the removal list at manage my staff should show
    public function hasRemovables($id) {
        global $wpdb;
        $event_table = ER_EVENT_LIST;
        $reg_table = ER_REGISTRATION_LIST;
        $time_now = current_time('mysql');

        $trainings_registered = $wpdb->get_results($wpdb->prepare("SELECT * FROM $reg_table WHERE `staff` = $id"));
        if (!empty($trainings_registered)) {
            $valid_count = 0;   // Counts the number of trainings the staff registered that are currently upcoming
            foreach ($trainings_registered as $training) {
                // Count increments when the the start time of a training is greater than the current time, hence upcoming
                if ($wpdb->get_var("SELECT `start_time` FROM $event_table WHERE `id` = $training->event_id") > $time_now) {
                    $valid_count++;
                }
            }
            if ($valid_count != 0) {
                return true;
            } else {
                // No upcoming trainings
                return false;
            }
        } else {
            // No trainings registered, no removables
            return false;
        }
    }

    // Tags staff id to name
    public function idtoName($id) {
        global $wpdb;
        $staff_profile = $wpdb->prefix . 'er_staff_profile';
        $row = $wpdb->get_row("SELECT `first_name`, `last_name` FROM $staff_profile WHERE id = $id");

        return $row->first_name . ' ' . $row->last_name;
    }

    // Find a field with the ID, table, and field name given
    public function getFieldById($table, $field_name, $id) {
        global $wpdb;
        return $wpdb->get_var("SELECT `$field_name` FROM $table WHERE `id` = $id");;
    }
}