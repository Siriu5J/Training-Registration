<?php

require_once __DIR__ . '/WP_Integration_TestCase.php';

/**
 * Class ActivationTest
 * 
 * Tests the plugin activation logic, including page creation and permalink settings.
 */
class ActivationTest extends WP_Integration_TestCase {

    public function test_permalink_structure_is_post_name() {
        // Activator::activate_plugin() is already called in WP_Integration_TestCase::setUp()
        $this->assertEquals('/%postname%/', get_option('permalink_structure'));
    }

    public function test_required_pages_are_created() {
        $pages = [
            'Create Staff Profile',
            'Manage My Staff',
            'Register for Training',
            'Training Registration'
        ];

        foreach ($pages as $title) {
            $page = get_page_by_title($title);
            $this->assertNotNull($page, "Page '$title' should exist.");
            $this->assertEquals('publish', $page->post_status);
            $this->assertEquals('app-layout.php', get_post_meta($page->ID, '_wp_page_template', true));
        }
    }

    public function test_home_page_is_set() {
        $home_page = get_page_by_title('Training Registration');
        $this->assertEquals('page', get_option('show_on_front'));
        $this->assertEquals($home_page->ID, get_option('page_on_front'));
    }
}
