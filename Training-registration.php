<?php

/*
Plugin Name: Event Registration
Plugin URI: https://github.com/Siriu5J/Training-Registration
Description: This WordPress plugin allows Training coordinators and managers to create training events where schools could register their staffs to events that are available. V3 is re-written from the original unreleased plugin with some visual update. Version 2.2 is a cleanup update that rewrites the code in object oriented manner.
Version: 4.0.0-beta1
Author: Samuel Jiang
Author URI: https://github.com/Siriu5J/Training-Registration
License: A "Slug" license name e.g. GPL2
*/

// Defined Values
define('ER_PLUGIN_DIR', dirname(__FILE__));

// Autoload Dependencies
if (file_exists(ER_PLUGIN_DIR . '/vendor/autoload.php')) {
    require_once(ER_PLUGIN_DIR . '/vendor/autoload.php');
}

// Database Constants
global $wpdb;
if (!defined('ER_STAFF_PROFILE')) {
    define('ER_STAFF_PROFILE', $wpdb->prefix . 'er_staff_profile');
}
if (!defined('ER_EVENT_LIST')) {
    define('ER_EVENT_LIST', $wpdb->prefix . 'er_event_list');
}
if (!defined('ER_REGISTRATION_LIST')) {
    define('ER_REGISTRATION_LIST', $wpdb->prefix . 'er_event_reg');
}

// Activation Hook
register_activation_hook(__FILE__, function() {
    $activator = new \SOT\TrainingRegistration\Core\Activator();
    $activator->activate_plugin();
});

// Run the plugin
add_action('plugins_loaded', function() {
    $plugin = new \SOT\TrainingRegistration\Core\Plugin();
    $plugin->run();
});
