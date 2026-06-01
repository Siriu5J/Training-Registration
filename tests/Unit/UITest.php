<?php

use SOT\TrainingRegistration\UI\TrainingRegistrationUI;
use PHPUnit\Framework\TestCase;

class UITest extends TestCase {
    protected $ui;

    public function setUp(): void {
        parent::setUp();
        WP_Mock::setUp();

        // Mock functions used in constructor or methods
        WP_Mock::userFunction('plugins_url', [
            'return' => 'http://example.com/plugin/url'
        ]);
        WP_Mock::userFunction('is_user_logged_in', [
            'return' => true
        ]);
        WP_Mock::userFunction('wp_enqueue_style', [
            'return' => true
        ]);
        WP_Mock::userFunction('add_action', [
            'return' => true
        ]);
    }

    public function tearDown(): void {
        WP_Mock::tearDown();
        parent::tearDown();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_staffFormCreation_shows_my_form_when_my_mode_is_on() {
        WP_Mock::userFunction('get_option', [
            'args' => ['my_mode'],
            'return' => 1
        ]);
        WP_Mock::userFunction('wp_get_current_user', [
            'return' => (object)['user_login' => 'test_school']
        ]);

        // Mock the UIContent class BEFORE it gets loaded
        $ui_content_mock = Mockery::mock('overload:SOT\TrainingRegistration\UI\UIContent');
        $ui_content_mock->shouldReceive('create_staff_my')->once()->with('test_school');

        $this->ui = new TrainingRegistrationUI();

        $this->ui->staffFormCreation();
        $this->assertTrue(true);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_staffFormCreation_shows_cn_form_when_my_mode_is_off() {
        WP_Mock::userFunction('get_option', [
            'args' => ['my_mode'],
            'return' => 0
        ]);
        WP_Mock::userFunction('wp_get_current_user', [
            'return' => (object)['user_login' => 'test_school']
        ]);

        $ui_content_mock = Mockery::mock('overload:SOT\TrainingRegistration\UI\UIContent');
        $ui_content_mock->shouldReceive('create_staff_cn')->once()->with('test_school');

        $this->ui = new TrainingRegistrationUI();

        $this->ui->staffFormCreation();
        $this->assertTrue(true);
    }
}
