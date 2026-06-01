<?php

use SOT\TrainingRegistration\Data\Strategies\RegistrationModeFactory;
use SOT\TrainingRegistration\Data\Strategies\DefaultRegistrationMode;
use SOT\TrainingRegistration\Data\Strategies\SotamRegistrationMode;
use PHPUnit\Framework\TestCase;

class RegistrationModeFactoryTest extends TestCase {
    public function setUp(): void {
        parent::setUp();
        WP_Mock::setUp();
    }

    public function tearDown(): void {
        WP_Mock::tearDown();
        parent::tearDown();
    }

    public function test_get_current_mode_returns_default_when_option_is_0() {
        WP_Mock::userFunction('get_option', [
            'args' => ['my_mode'],
            'return' => 0
        ]);

        $mode = RegistrationModeFactory::get_current_mode();
        $this->assertInstanceOf(DefaultRegistrationMode::class, $mode);
    }

    public function test_get_current_mode_returns_sotam_when_option_is_1() {
        WP_Mock::userFunction('get_option', [
            'args' => ['my_mode'],
            'return' => 1
        ]);

        $mode = RegistrationModeFactory::get_current_mode();
        $this->assertInstanceOf(SotamRegistrationMode::class, $mode);
    }
}
