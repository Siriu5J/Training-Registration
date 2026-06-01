<?php

use SOT\TrainingRegistration\Data\Strategies\DefaultRegistrationMode;
use PHPUnit\Framework\TestCase;

class DefaultRegistrationModeTest extends TestCase {
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
    public function test_handle_staff_creation_detects_duplicate() {
        $staff_repo_mock = Mockery::mock('overload:SOT\TrainingRegistration\Data\Repositories\StaffRepository');
        $staff_repo_mock->shouldReceive('check_duplicate')->once()->andReturn(1);

        WP_Mock::userFunction('sanitize_text_field', [
            'return' => function($val) { return $val; }
        ]);

        $strategy = new DefaultRegistrationMode();
        $result = $strategy->handle_staff_creation([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'school' => 'Test School',
            'phone' => '123456'
        ]);

        $this->assertInstanceOf('WP_Error', $result);
        $this->assertEquals('duplicate', $result->get_error_code());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_handle_staff_creation_inserts_successfully() {
        $staff_repo_mock = Mockery::mock('overload:SOT\TrainingRegistration\Data\Repositories\StaffRepository');
        $staff_repo_mock->shouldReceive('check_duplicate')->once()->andReturn(0);
        $staff_repo_mock->shouldReceive('insert')->once()->andReturn(123);

        WP_Mock::userFunction('sanitize_text_field', [
            'return' => function($val) { return $val; }
        ]);
        WP_Mock::userFunction('sanitize_email', [
            'return' => function($val) { return $val; }
        ]);
        WP_Mock::userFunction('sanitize_textarea_field', [
            'return' => function($val) { return $val; }
        ]);

        $strategy = new DefaultRegistrationMode();
        $result = $strategy->handle_staff_creation([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'school' => 'Test School',
            'phone' => '123456',
            'sex' => 'M',
            'age' => '30',
            'email' => 'john@example.com',
            'position' => 'Teacher',
            'lc' => 'LC1',
            't-exp' => '5 years',
            'cec-exp' => '2 years',
            'degree' => 'Bachelors',
            'grad-year' => '2015',
            'major' => 'CS',
            'institution' => 'University',
            'comment' => 'None'
        ]);

        $this->assertEquals(123, $result);
    }
}
