<?php
/**
 * PHPUnit bootstrap for WordPress Integration Tests
 */

// Load composer
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Path to the WordPress tests library
$_tests_dir = getenv('WP_TESTS_DIR');
if (!$_tests_dir) {
    $_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Load the PHPUnit Polyfills
 */
if ( file_exists( dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php' ) ) {
    require_once dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';
}

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
    require dirname(__DIR__) . '/Training-registration.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

// Define plugin constants if not already defined (though Training-registration.php should handle it)
if (!defined('ER_PLUGIN_DIR')) {
    define('ER_PLUGIN_DIR', dirname(__DIR__));
}
