<?php

use SOT\TrainingRegistration\Data\Strategies\SotamRegistrationMode;
use PHPUnit\Framework\TestCase;

class SotamRegistrationModeTest extends TestCase {
    public function setUp(): void {
        parent::setUp();
        WP_Mock::setUp();
    }

    public function tearDown(): void {
        WP_Mock::tearDown();
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_handle_staff_creation_inserts_successfully() {
        $staff_repo_mock = Mockery::mock('overload:SOT\TrainingRegistration\Data\Repositories\StaffRepository');
        $staff_repo_mock->shouldReceive('check_duplicate')->once()->andReturn(0);
        $staff_repo_mock->shouldReceive('insert')->once()->andReturn(456);

        WP_Mock::userFunction('sanitize_text_field', [
            'return' => function($val) { return $val; }
        ]);
        WP_Mock::userFunction('sanitize_textarea_field', [
            'return' => function($val) { return $val; }
        ]);

        $strategy = new SotamRegistrationMode();
        $result = $strategy->handle_staff_creation([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'school' => 'SOTAM School',
            'phone' => '654321',
            'cn_name' => 'JaneCN',
            'mid_name' => 'M',
            'sex' => 'F',
            'position' => 'Principal',
            'lc' => 'LC2',
            't-exp' => '2020', // grad_year mapped to t-exp in SotamRegistrationMode
            'degree' => 'Masters',
            'comment' => 'Experienced'
        ]);

        $this->assertEquals(456, $result);
    }
}
