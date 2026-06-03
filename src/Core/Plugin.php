<?php

namespace SOT\TrainingRegistration\Core;

defined('ABSPATH') || exit;

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
        $this->loader->er_add_action('wp_enqueue_scripts', $ui, 'enqueue_ui_css');
        $this->loader->er_add_shortcode('staff_form', $ui, 'staffFormCreation');
        $this->loader->er_add_shortcode('view_staff', $ui, 'viewEditStaff');
        $this->loader->er_add_shortcode('manage_registrations', $ui, 'manageRegistrations');
        $this->loader->er_add_shortcode('register_training', $ui, 'eventRegistration');
        $this->loader->er_add_shortcode('training_dashboard', $ui, 'uiDashboard');

        // Admin Controllers
        $admin = new AdminSettings();
        
        $this->loader->er_add_action('admin_menu', $admin, 'adminSettingsPageRegistration');
        $this->loader->er_add_action('admin_init', $admin, 'registerSettingsFields');
        $this->loader->er_add_action('admin_init', $admin, 'exportRegistrationsToExcel');

        // Template Manager
        $template_manager = new TemplateManager();
        $template_manager->init();
    }

    /**
     * Run the loader to execute all hooks.
     */
    public function run() {
        $this->loader->run();
    }
}
