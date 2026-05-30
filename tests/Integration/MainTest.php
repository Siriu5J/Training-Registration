<?php

use SOT\TrainingRegistration\Core\Plugin;
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
        
        $main = new Plugin();
        $this->assertInstanceOf(Plugin::class, $main);
    }
}
