<?php
/**
 * Class training_registration_main
 *
 * This is the main class of the plugin. It requires all the necessary php classes and registers all the actions and shortcodes.
 *
 * @since 2020-5-19
 * @version 1.0
 *
 * @package training-registration
 */


class training_registration_main {

    protected $loader;

    // Constructor
    public function __construct() {
        $this->load_dependencies();
        $this->define_hooks();
    }

    // Requires all the classes and files. It also instantiates $loader
    private function load_dependencies() {

        // UI
        require_once(ER_PLUGIN_DIR . '/ui/ui.php');

        // Admin Menu
        require_once(ER_PLUGIN_DIR . '/admin/admin_settings.php');

        // Admin Message
        require_once(ER_PLUGIN_DIR . '/admin/admin_messages.php');

        // Loader
        require_once(ER_PLUGIN_DIR . '/includes/training_registration_loader.php');
        $this->loader = new training_registration_loader();
    }

    // Insert the details for the hooks
    private function define_hooks() {
        // UI Shortcodes
        $ui = new training_registration_ui();
        $this->loader->er_add_shortcode('staff_form', $ui, 'staffFormCreation');
        $this->loader->er_add_shortcode('view_staff', $ui, 'viewEditStaff');
        $this->loader->er_add_shortcode('register_training', $ui, 'eventRegistration');

        // Admin menu
        $admin = new training_registration_acp();
        $messages = new admin_messages();
        $this->loader->er_add_action('admin_notices', $messages, 'invalidTheme');
        $this->loader->er_add_action('admin_menu', $admin, 'adminSettingsPageRegistration');
    }

    public function run() {
        $this->loader->run();
    }
}