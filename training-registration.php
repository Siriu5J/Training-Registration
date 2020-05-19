<?php

/*
Plugin Name: Event Registration
Plugin URI: https://github.com/Siriu5J/Training-Registration
Description: This WordPress plugin allows Training coordinators and managers to create training events where schools could register their staffs to events that are available. V2 is re-written from the the original unreleased plugin with some visual update.
Version: 2.1.2
Author: Samuel Jiang
Author URI: https://github.com/Siriu5J/Training-Registration
License: A "Slug" license name e.g. GPL2
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

// Defined Values
define('ER_VERSION', '2.0');
define('ER_PLUGIN_DIR', dirname(__FILE__));
define('ER_STAFF_PROFILE', $wpdb->prefix . 'er_staff_profile');
define('ER_EVENT_LIST', $wpdb->prefix . 'er_event_list');
define('ER_REGISTRATION_LIST', $wpdb->prefix . 'er_event_reg');

// Activation Hook
register_activation_hook(__FILE__, 'erActivation');

// Including Extra PHP Files
require_once(ER_PLUGIN_DIR . '/admin_settings.php');    // Settings in ACP
require_once(ER_PLUGIN_DIR . '/ui.php');                // User interfaces


/*
ACTIVATION FUNCTION

NOTE: The small messages related to the creation of database tables are removed since I am fairly confident that the code would handle the activation well.
*/
function erActivation() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    // Use the full name for the table rather than constants because WordPress seems to have the bug of not recognizing the right prefix here
    $staff = $wpdb->prefix . 'er_staff_profile';
    $event = $wpdb->prefix . 'er_event_list';
    $registration = $wpdb->prefix . 'er_event_reg';

    // Create the Staff Profile table if not created
    if ($wpdb->get_var("SHOW TABLES LIKE '$staff'") != $staff) {
        $sql = "CREATE TABLE $staff (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            first_name tinytext NOT NULL,
            mid_name tinytext,
            last_name tinytext NOT NULL,
            cn_name tinytext NULL,
            sex char(1) NOT NULL,
            age tinytext NOT NULL,
            school tinytext NOT NULL,
            email tinytext NOT NULL,
            phone bigint,
            pos tinytext NOT NULL,
            lc tinytext NOT NULL,
            training_exp tinyint,
            cec_exp tinyint,
            degree tinytext NOT NULL,
            grad_year mediumint(4) NOT NULL,
            major tinytext,
            minor tinytext,
            institution mediumtext NOT NULL,
            comment text,
            
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }


    // Create the Training List table if not created
    if ($wpdb->get_var("SHOW TABLES LIKE '$event'") != $event) {
        $sql = "CREATE TABLE $event (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            event_name mediumtext NOT NULL,
            max MEDIUMINT,
            open_time DATETIME NOT NULL,
            close_time DATETIME NOT NULL,
            start_time DATETIME NOT NULL,
            end_time DATETIME NOT NULL,
            location tinytext NOT NULL,
            limit_max bool,
            activated bool NOT NULL,
            comment text,
            num_reg SMALLINT,
            
            PRIMARY KEY (id)
        )$charset_collate";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Create the Registration table if not created
    if ($wpdb->get_var("SHOW TABLES LIKE '$registration'") != $registration) {
        $sql = " CREATE TABLE $registration (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            event_id mediumint(9) NOT NULL,
            staff mediumint(9) NOT NULL,
            reg_time DATETIME NOT NULL,
            school tinytext NOT NULL,
            comment text,
        
            PRIMARY KEY (id)
        ) $charset_collate";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }



}


/*
SOME HELPFUL FUNCTIONS
 */
// Check to see if this training name / location combination is valid
function isValidEvent($name, $location) {
    global $wpdb;
    $table = ER_EVENT_LIST;
    $duplicates = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE `event_name` = $name"));   // Get all trainings that has the same name

    if (empty($duplicates)) {
        return true;    // No duplicated names, training name is valid
    } else {
        foreach ($duplicates as $training) {
            if ($training->location == $location) {
                return false;   // Same name and location, not valid
            }
        }
        return true;    // Checked through all name duplicates, no location duplicate, training name valid
    }
}

// Translate the available slot from number to useful information
function spotsOpen ($max, $occupied) {

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
function availability($row) {
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
function hasRemovables($id) {
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
function idtoName($id) {
    global $wpdb;
    $staff_profile = $wpdb->prefix . 'er_staff_profile';
    $row = $wpdb->get_row("SELECT `first_name`, `last_name` FROM $staff_profile WHERE id = $id");

    return $row->first_name . ' ' . $row->last_name;
}

// Find a field with the ID, table, and field name given
function getFieldById($table, $field_name, $id) {
    global $wpdb;
    return $wpdb->get_var("SELECT `$field_name` FROM $table WHERE `id` = $id");;
}

