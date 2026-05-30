<?php

use PHPUnit\Framework\TestCase;

class MainTest extends TestCase {
    public function setUp(): void {
        parent::setUp();
        WP_Mock::setUp();
    }

    public function tearDown(): void {
        WP_Mock::tearDown();
        parent::tearDown();
    }

    public function test_main_initialization() {
        // Mock requirements in constructor
        WP_Mock::userFunction('add_shortcode', ['return' => true]);
        WP_Mock::userFunction('add_action', ['return' => true]);
        
        // Mock dependencies that are required in load_dependencies()
        // We use Patchwork or just Mockery to prevent real file loading if needed,
        // but here we can just let them load if they don't have side effects.
        
        require_once ER_PLUGIN_DIR . '/includes/training_registration_main.php';
        
        // We need to mock the classes that main instantiates
        // Since it uses 'new', we'd need overload if they are already loaded.
        
        $main = new training_registration_main();
        $this->assertInstanceOf('training_registration_main', $main);
    }
}
