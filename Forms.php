<?php


class Forms {

    // private variables for all the table names
    static private $staff_profile = "";
    static private $event_list = "";
    static private $reg_list = "";

    // Constructor
    function __construct($prefix) {
        self::$staff_profile = $prefix . 'er_staff_profile';
        self::$event_list    = $prefix . 'er_event_list';
        self::$reg_list      = $prefix . 'er_event_reg';
    }

    function activate_plugin() {
        global $wpdb;
        $staff = $this->staff_profile;
        $event = $this->event_list;
        $registration = $this->reg_list;

        $charset_collate = $wpdb->get_charset_collate();

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
}