<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

WP_Mock::bootstrap();

// Define constants that the plugin expects
if (!defined('ER_PLUGIN_DIR')) {
    define('ER_PLUGIN_DIR', dirname(__DIR__));
}

// Mock $wpdb for constant definitions if needed, or just define them with a mock prefix
if (!defined('ER_STAFF_PROFILE')) {
    define('ER_STAFF_PROFILE', 'wp_er_staff_profile');
}
if (!defined('ER_EVENT_LIST')) {
    define('ER_EVENT_LIST', 'wp_er_event_list');
}
if (!defined('ER_REGISTRATION_LIST')) {
    define('ER_REGISTRATION_LIST', 'wp_er_event_reg');
}

// Mock some common WP functions if WP_Mock doesn't handle them automatically
if (!function_exists('esc_html')) {
    function esc_html($text) { return $text; }
}
if (!function_exists('esc_attr')) {
    function esc_attr($text) { return $text; }
}
if (!function_exists('esc_url')) {
    function esc_url($url) { return $url; }
}
if (!function_exists('__')) {
    function __($text, $domain = 'default') { return $text; }
}
if (!function_exists('_e')) {
    function _e($text, $domain = 'default') { echo $text; }
}

if (!class_exists('WP_Error')) {
    class WP_Error {
        public $code;
        public $message;
        public function __construct($code = '', $message = '') {
            $this->code = $code;
            $this->message = $message;
        }
        public function get_error_code() { return $this->code; }
    }
}
