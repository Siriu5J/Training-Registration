<?php

namespace SOT\TrainingRegistration\Core;

defined('ABSPATH') || exit;

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
    protected $staff_repo;

    public function __construct() {
        $this->event_repo = new EventRepository();
        $this->registration_repo = new RegistrationRepository();
    }

    public function isValidEvent($name, $location, $start_date, $end_date, $id) {
        $duplicates = $this->event_repo->get_duplicates($name);

        if (empty($duplicates)) {
            return true;
        }

        foreach ($duplicates as $training) {
            if ($training->location !== $location) {
                continue;
            }

            if ($training->start_time !== $start_date || $training->end_time !== $end_date) {
                continue;
            }

            if ($id != $training->id) {
                return false;
            }
        }

        return true;
    }

    public function spotsOpen($id) {
        $training = $this->event_repo->get_by_id($id);
        $max = (int)$training->max;
        $occupied = count($this->registration_repo->get_by_event($id));
        $diff = $max - $occupied;

        return match (true) {
            $max === -999 => "Unlimited, {$occupied} registered",
            $diff > 0 => "{$diff}/{$max} available",
            $diff === 0 => "0/{$max}, Full",
            default => "0/{$max}, " . ($occupied - $max) . " overflow(s)",
        };
    }

    public function availability($row) {
        $now = current_time('mysql');

        $ts = match (true) {
            $now > $row->close_time => 'Closed',
            $now < $row->open_time => 'To Be Open',
            default => 'Open',
        };

        $isFull = $row->num_reg >= $row->max && $row->max != -999;
        $isCapped = $row->limit_max == 1;

        return match (true) {
            !$isFull => $ts,
            !$isCapped => "{$ts}; Full but not capped",
            default => 'Full and capped',
        };
    }

    public function hasRemovables($id) {
        $time_now = current_time('mysql');
        $trainings_registered = $this->registration_repo->get_by_staff($id);

        if (empty($trainings_registered)) {
            return false;
        }

        foreach ($trainings_registered as $training) {
            $event = $this->event_repo->get_by_id($training->event_id);
            if ($event && $event->close_time > $time_now) {
                return true;
            }
        }

        return false;
    }

    // Tags staff id to name
    public function idtoName($id) {
        if (!isset($this->staff_repo)) {
            $this->staff_repo = new StaffRepository();
        }
        $row = $this->staff_repo->get_by_id($id);

        return $row ? esc_html($row->first_name . ' ' . $row->last_name) : '';
    }

    // Find a field with the ID, table, and field name given
    public function getFieldById($table, $field_name, $id) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT `$field_name` FROM $table WHERE `id` = %d", $id));
    }
}
