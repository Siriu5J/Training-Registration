<?php

namespace SOT\TrainingRegistration\Core;

use SOT\TrainingRegistration\Data\Repositories\EventRepository;
use SOT\TrainingRegistration\Data\Repositories\RegistrationRepository;
use SOT\TrainingRegistration\Data\Repositories\StaffRepository;

/**
 * Class Tools
 *
 * This is the tools library with various functions for the plugin.
 *
 * @package SOT\TrainingRegistration\Core
 */
class Tools {
    protected $event_repo;
    protected $registration_repo;

    public function __construct() {
        $this->event_repo = new EventRepository();
        $this->registration_repo = new RegistrationRepository();
    }

    public function isValidEvent($name, $location, $start_date, $end_date, $id) {
        $duplicates = $this->event_repo->get_duplicates($name);

        if (empty($duplicates)) {
            return true;    // No duplicated names, training name is valid
        } else {
            foreach ($duplicates as $training) {
                if ($training->location == $location) { // check location
                    if ($training->start_time == $start_date && $training->end_time == $end_date) { // Check times
                        if ($id != $training->id) { // is a different event with same details
                            return false;
                        }
                    }
                }
            }
            return true;
        }
    }

    // Translate the available slot from number to useful information
    public function spotsOpen ($id) {
        // Query for the number of users
        $training = $this->event_repo->get_by_id($id);
        $max = $training->max;
        $occupied = count($this->registration_repo->get_by_event($id));

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
        $time_now = current_time('mysql');

        $trainings_registered = $this->registration_repo->get_by_staff($id);
        if (!empty($trainings_registered)) {
            $valid_count = 0;   // Counts the number of trainings the staff registered that are currently upcoming
            foreach ($trainings_registered as $training) {
                // Count increments when the the start time of a training is greater than the current time, hence upcoming
                $event = $this->event_repo->get_by_id($training->event_id);
                if ($event && $event->start_time > $time_now) {
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
        $staff_repo = new StaffRepository();
        $row = $staff_repo->get_by_id($id);

        return $row ? esc_html($row->first_name . ' ' . $row->last_name) : '';
    }

    // Find a field with the ID, table, and field name given
    public function getFieldById($table, $field_name, $id) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT `$field_name` FROM $table WHERE `id` = %d", $id));;
    }
}
