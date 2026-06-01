<?php

use SOT\TrainingRegistration\Core\Loader;
use PHPUnit\Framework\TestCase;

class LoaderTest extends TestCase {
    public function setUp(): void {
        parent::setUp();
        WP_Mock::setUp();
    }

    public function tearDown(): void {
        WP_Mock::tearDown();
        parent::tearDown();
    }

    public function test_run_registers_actions_and_shortcodes() {
        $loader = new Loader();
        $component = new stdClass();
        
        $loader->er_add_action('wp_enqueue_scripts', $component, 'styles');
        $loader->er_add_shortcode('my_shortcode', $component, 'shortcode_callback');

        WP_Mock::expectActionAdded('wp_enqueue_scripts', [$component, 'styles'], 10);
        
        // WP_Mock doesn't have expectShortcodeAdded, so we use userFunction for that
        WP_Mock::userFunction('add_shortcode', [
            'times' => 1,
            'args' => ['my_shortcode', [$component, 'shortcode_callback']]
        ]);

        $loader->run();
        $this->assertTrue(true);
    }
}
