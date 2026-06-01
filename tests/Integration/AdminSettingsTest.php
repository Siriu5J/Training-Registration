<?php

namespace SOT\TrainingRegistration\Tests\Integration;

require_once __DIR__ . '/WP_Integration_TestCase.php';

use SOT\TrainingRegistration\Admin\AdminSettings;
use WP_Integration_TestCase;

class AdminSettingsTest extends WP_Integration_TestCase {
    protected $admin_settings;

    public function setUp(): void {
        parent::setUp();
        $this->admin_settings = new AdminSettings();
        
        // Ensure user has permissions
        wp_set_current_user($this->factory->user->create(['role' => 'administrator']));
    }

    public function test_registerSettingsFields_registers_options() {
        global $wp_settings_sections, $wp_settings_fields;
        
        $this->admin_settings->registerSettingsFields();
        
        $this->assertArrayHasKey('er_gen_set', $wp_settings_sections);
        $this->assertArrayHasKey('er_general_settings_section', $wp_settings_sections['er_gen_set']);
        
        $this->assertArrayHasKey('er_gen_set', $wp_settings_fields);
        $this->assertArrayHasKey('er_general_settings_section', $wp_settings_fields['er_gen_set']);
        $this->assertArrayHasKey('show_availability', $wp_settings_fields['er_gen_set']['er_general_settings_section']);
        $this->assertArrayHasKey('my_mode', $wp_settings_fields['er_gen_set']['er_general_settings_section']);
    }

    public function test_handleSettingsPageActions_creates_pages() {
        $_POST['create-page'] = '1';
        $_POST['create_page_nonce_field'] = wp_create_nonce('create_page_nonce');
        
        // Mock redirect using an exception to jump out before exit
        add_filter('wp_redirect', function($location) {
            throw new \Exception("REDIRECT_TO: " . $location);
        });

        try {
            $this->admin_settings->handleSettingsPageActions();
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), "REDIRECT_TO: ") !== 0) {
                throw $e;
            }
            $this->assertStringContainsString('pages-created=true', $e->getMessage());
        }

        $page = get_page_by_path(sanitize_title('Training Registration'));
        $this->assertNotNull($page);
        
        unset($_POST['create-page']);
        unset($_POST['create_page_nonce_field']);
    }

    public function test_handleEventSubmission_creates_event() {
        $_POST['create_training'] = '1';
        $_POST['training_nonce_field'] = wp_create_nonce('create_training_nonce');
        $_POST['event-name'] = 'Integration Test Event';
        $_POST['max'] = '20';
        $_POST['open-date'] = '2026-06-01T10:00';
        $_POST['close-date'] = '2026-06-10T10:00';
        $_POST['start-date'] = '2026-06-15T10:00';
        $_POST['end-date'] = '2026-06-20T10:00';
        $_POST['location'] = 'Virtual';
        $_POST['event-id'] = '-1';
        $_POST['comment'] = 'Test Comment';

        add_filter('wp_redirect', function($location) {
            throw new \Exception("REDIRECT_TO: " . $location);
        }, 10, 1);

        try {
            $this->admin_settings->handleEventSubmission();
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), "REDIRECT_TO: ") !== 0) {
                throw $e;
            }
            $this->assertStringContainsString('created=true', $e->getMessage());
        }

        global $wpdb;
        $event = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}er_event_list WHERE event_name = 'Integration Test Event'");
        $this->assertNotNull($event);
        $this->assertEquals(20, $event->max);

        unset($_POST['create_training']);
    }
}
