<?php

use SOT\TrainingRegistration\Core\TemplateManager;
use PHPUnit\Framework\TestCase;

class TemplateManagerTest extends TestCase {
    protected $manager;

    public function setUp(): void {
        parent::setUp();
        WP_Mock::setUp();
        $this->manager = new TemplateManager();
    }

    public function tearDown(): void {
        WP_Mock::tearDown();
        parent::tearDown();
    }

    public function test_init_registers_filters() {
        WP_Mock::expectFilterAdded('theme_page_templates', [$this->manager, 'register_template']);
        WP_Mock::expectFilterAdded('template_include', [$this->manager, 'load_template']);
        
        $this->manager->init();
        $this->assertTrue(true);
    }

    public function test_register_template_adds_slug_to_array() {
        $templates = ['existing.php' => 'Existing Template'];
        $result = $this->manager->register_template($templates);
        
        $this->assertArrayHasKey('app-layout.php', $result);
        $this->assertEquals('Training App Template', $result['app-layout.php']);
    }

    public function test_load_template_returns_plugin_template_on_match() {
        WP_Mock::userFunction('is_page', [
            'return' => true
        ]);
        WP_Mock::userFunction('get_the_ID', [
            'return' => 123
        ]);
        WP_Mock::userFunction('get_post_meta', [
            'args' => [123, '_wp_page_template', true],
            'return' => 'app-layout.php'
        ]);

        // ER_PLUGIN_DIR is defined in bootstrap
        $expected = ER_PLUGIN_DIR . '/templates/app-layout.php';
        
        // We assume the file exists since it's in the repo
        $result = $this->manager->load_template('original.php');
        $this->assertEquals($expected, $result);
    }

    public function test_load_template_returns_original_when_no_match() {
        WP_Mock::userFunction('is_page', [
            'return' => true
        ]);
        WP_Mock::userFunction('get_the_ID', [
            'return' => 123
        ]);
        WP_Mock::userFunction('get_post_meta', [
            'args' => [123, '_wp_page_template', true],
            'return' => 'default'
        ]);

        $result = $this->manager->load_template('original.php');
        $this->assertEquals('original.php', $result);
    }
}
