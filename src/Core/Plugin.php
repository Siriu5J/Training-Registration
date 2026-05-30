<?php

namespace SOT\TrainingRegistration\Core;

use SOT\TrainingRegistration\UI\TrainingRegistrationUI;
use SOT\TrainingRegistration\Admin\AdminSettings;
use SOT\TrainingRegistration\Admin\AdminMessages;

/**
 * Class Plugin
 *
 * The main orchestrator of the plugin. Handles initialization and hook registration.
 *
 * @package SOT\TrainingRegistration\Core
 */
class Plugin {

    protected $loader;

    public function __construct() {
        $this->loader = new Loader();
        $this->define_hooks();
    }

    /**
     * Register all hooks and shortcodes.
     */
    private function define_hooks() {
        // UI Controllers
        $ui = new TrainingRegistrationUI();
        $this->loader->er_add_shortcode('staff_form', $ui, 'staffFormCreation');
        $this->loader->er_add_shortcode('view_staff', $ui, 'viewEditStaff');
        $this->loader->er_add_shortcode('register_training', $ui, 'eventRegistration');

        // Admin Controllers
        $admin = new AdminSettings();
        $messages = new AdminMessages();
        
        $this->loader->er_add_action('admin_notices', $messages, 'invalidTheme');
        $this->loader->er_add_action('admin_menu', $admin, 'adminSettingsPageRegistration');
        $this->loader->er_add_action('admin_init', $admin, 'exportRegistrationsToExcel');
    }

    /**
     * Run the loader to execute all hooks.
     */
    public function run() {
        $this->loader->run();
    }
}
