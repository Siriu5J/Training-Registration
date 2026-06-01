<?php

defined('ABSPATH') || exit;

/*
Plugin Name: Event Registration
Plugin URI: https://github.com/Siriu5J/Training-Registration
Description: A robust training management system for WordPress that enables organizers to create events and Learning Centers to register staff. Features include automated page creation, staff profile management, and comprehensive data export capabilities.
Version: 4.0.0-beta1
Author: Samuel Jiang
Author URI: https://github.com/Siriu5J/Training-Registration
License: Apache-2.0
License URI: https://www.apache.org/licenses/LICENSE-2.0
Requires at least: 6.0
Requires PHP: 8.1
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
