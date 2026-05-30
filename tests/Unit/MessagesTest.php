<?php

use SOT\TrainingRegistration\Admin\AdminMessages;
use PHPUnit\Framework\TestCase;

class AdminMessagesTest extends TestCase {
    protected $messages;

    public function setUp(): void {
        parent::setUp();
        WP_Mock::setUp();
        
        $this->messages = new AdminMessages();
    }

    public function tearDown(): void {
        WP_Mock::tearDown();
        parent::tearDown();
    }

    public function test_invalidTheme_renders_output_if_transient_exists() {
        WP_Mock::userFunction('get_option', [
            'args' => ['home'],
            'return' => 'http://example.com'
        ]);
        WP_Mock::userFunction('get_transient', [
            'args' => ['invalid_theme_transient'],
            'return' => true
        ]);
        
        $this->expectOutputRegex('/New pages are not create!/');
        $this->messages->invalidTheme();
    }
}
