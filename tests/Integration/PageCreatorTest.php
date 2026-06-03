<?php

namespace SOT\TrainingRegistration\Tests\Integration;

require_once __DIR__ . '/WP_Integration_TestCase.php';

use SOT\TrainingRegistration\Core\PageCreator;
use WP_Integration_TestCase;

class PageCreatorTest extends WP_Integration_TestCase {
    protected $page_creator;

    public function setUp(): void {
        parent::setUp();
        $this->page_creator = new PageCreator();
    }

    public function test_run_creates_pages() {
        $this->page_creator->run();

        $pages = [
            'Create Staff Profile',
            'Manage My Staff',
            'Register for Training',
            'Training Registration'
        ];

        foreach ($pages as $title) {
            $page = get_page_by_path(sanitize_title($title));
            $this->assertNotNull($page, "Page '$title' should exist");
            $this->assertEquals('publish', $page->post_status);
            $this->assertEquals('app-layout.php', get_post_meta($page->ID, '_wp_page_template', true));
        }

        $home_id = get_page_by_path(sanitize_title('Training Registration'))->ID;
        $this->assertEquals('page', get_option('show_on_front'));
        $this->assertEquals($home_id, get_option('page_on_front'));

        // Assert Page IDs are stored in options
        $this->assertEquals(get_page_by_path(sanitize_title('Create Staff Profile'))->ID, get_option('er_create_staff_page_id'));
        $this->assertEquals(get_page_by_path(sanitize_title('Manage My Staff'))->ID, get_option('er_manage_staff_page_id'));
        $this->assertEquals(get_page_by_path(sanitize_title('Register for Training'))->ID, get_option('er_register_training_page_id'));
        $this->assertEquals($home_id, get_option('er_dashboard_page_id'));
    }

    public function test_run_does_not_duplicate_pages() {
        // Run once
        $this->page_creator->run();
        $count1 = count(get_pages());

        // Run again
        $this->page_creator->run();
        $count2 = count(get_pages());

        $this->assertEquals($count1, $count2, "Pages should not be duplicated on multiple runs");
    }
}
