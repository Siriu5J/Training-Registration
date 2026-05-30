<?php

use PHPUnit\Framework\TestCase;

class AdminMessagesTest extends TestCase {
    protected $messages;

    public function setUp(): void {
        parent::setUp();
        WP_Mock::setUp();
        
        require_once ER_PLUGIN_DIR . '/admin/admin_messages.php';
        $this->messages = new admin_messages();
    }

    public function tearDown(): void {
        WP_Mock::tearDown();
        parent::tearDown();
    }

    public function test_invalidTheme_adds_action_if_transient_exists() {
        WP_Mock::userFunction('get_transient', [
            'args' => ['invalid_theme_transient'],
            'return' => true
        ]);
        
        // We expect it to call add_action('admin_notices', ...)
        WP_Mock::userFunction('add_action', [
            'times' => 1,
            'args' => ['admin_notices', Mockery::type('callable')]
        ]);

        $this->messages->invalidTheme();
        $this->assertTrue(true);
    }
}
