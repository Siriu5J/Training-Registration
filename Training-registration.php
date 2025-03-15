<?php

/*
Plugin Name: Event Registration
Plugin URI: https://github.com/Siriu5J/Training-Registration
Description: This WordPress plugin allows Training coordinators and managers to create training events where schools could register their staffs to events that are available. V2 is re-written from the the original unreleased plugin with some visual update. Version 2.2 is a clean up update that rewrites the code in object oriented manner.
Version: 3.0.2
Author: Samuel Jiang
Author URI: https://github.com/Siriu5J/Training-Registration
License: A "Slug" license name e.g. GPL2
*/

// Defined Values
define('ER_PLUGIN_DIR', dirname(__FILE__));
define('ER_STAFF_PROFILE', $wpdb->prefix . 'er_staff_profile');
define('ER_EVENT_LIST', $wpdb->prefix . 'er_event_list');
define('ER_REGISTRATION_LIST', $wpdb->prefix . 'er_event_reg');

// Including Extra PHP Files
require_once(ER_PLUGIN_DIR . '/includes/training_registration_main.php');

// Activation Hook
register_activation_hook(__FILE__, 'erActivation');
function erActivation() {
    require_once(ER_PLUGIN_DIR . '/includes/activation.php');
    $activator = new activation();
    $activator->activate_plugin();
}

// Run the plugin
function run_training_registration_main() {
    $run_main = new training_registration_main();

    $run_main->run();
}

run_training_registration_main();


