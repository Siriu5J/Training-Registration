<?php

require_once __DIR__ . '/WP_Integration_TestCase.php';

use SOT\TrainingRegistration\UI\TrainingRegistrationUI;
use SOT\TrainingRegistration\Data\Repositories\EventRepository;
use SOT\TrainingRegistration\Data\Repositories\StaffRepository;
use SOT\TrainingRegistration\Data\Repositories\RegistrationRepository;

class RemoveStaffTest extends WP_Integration_TestCase {
    protected $ui;
    protected $event_repo;
    protected $staff_repo;
    protected $registration_repo;

    public function setUp(): void {
        parent::setUp();
        $this->ui = new TrainingRegistrationUI();
        $this->event_repo = new EventRepository();
        $this->staff_repo = new StaffRepository();
        $this->registration_repo = new RegistrationRepository();
        
        // Log in a user
        $user_id = $this->factory->user->create(['role' => 'subscriber', 'user_login' => 'test_school']);
        wp_set_current_user($user_id);
    }

    public function test_remove_staff_with_no_registrations() {
        $staff_id = $this->staff_repo->insert([
            'first_name' => 'No',
            'last_name'  => 'Reg',
            'school'     => 'test_school',
            'phone'      => '12345',
            'sex'        => 'M',
            'pos'        => 'Teacher'
        ]);

        $_POST['remove-staff'] = $staff_id;
        $_POST['staff_nonce_field'] = wp_create_nonce('create_staff_nonce');

        $output = $this->ui->viewEditStaff();

        $this->assertStringContainsString('Staff member successfully removed', $output);
        $this->assertNull($this->staff_repo->get_by_id($staff_id));
    }

    public function test_remove_staff_with_open_registrations() {
        $staff_id = $this->staff_repo->insert([
            'first_name' => 'Open',
            'last_name'  => 'Reg',
            'school'     => 'test_school',
            'phone'      => '67890',
            'sex'        => 'F',
            'pos'        => 'Teacher'
        ]);

        $event_id = $this->event_repo->insert([
            'event_name' => 'Open Training',
            'close_time' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'start_time' => date('Y-m-d H:i:s', strtotime('+2 days')),
            'activated'  => 1,
            'num_reg'    => 1
        ]);

        $this->registration_repo->insert([
            'event_id' => $event_id,
            'staff'    => $staff_id,
            'reg_time' => current_time('mysql'),
            'school'   => 'test_school'
        ]);

        $_POST['remove-staff'] = $staff_id;
        $_POST['staff_nonce_field'] = wp_create_nonce('create_staff_nonce');

        $output = $this->ui->viewEditStaff();

        $this->assertStringContainsString('Staff member successfully removed', $output);
        $this->assertNull($this->staff_repo->get_by_id($staff_id));
        
        $registrations = $this->registration_repo->get_by_staff($staff_id);
        $this->assertEmpty($registrations);

        $event = $this->event_repo->get_by_id($event_id);
        $this->assertEquals(0, $event->num_reg);
    }

    public function test_remove_staff_blocked_by_closed_registration() {
        $staff_id = $this->staff_repo->insert([
            'first_name' => 'Closed',
            'last_name'  => 'Reg',
            'school'     => 'test_school',
            'phone'      => '11111',
            'sex'        => 'M',
            'pos'        => 'Teacher'
        ]);

        $event_id = $this->event_repo->insert([
            'event_name' => 'Closed Training',
            'close_time' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'start_time' => date('Y-m-d H:i:s', strtotime('+2 days')),
            'activated'  => 1,
            'num_reg'    => 1
        ]);

        $this->registration_repo->insert([
            'event_id' => $event_id,
            'staff'    => $staff_id,
            'reg_time' => current_time('mysql'),
            'school'   => 'test_school'
        ]);

        $_POST['remove-staff'] = $staff_id;
        $_POST['staff_nonce_field'] = wp_create_nonce('create_staff_nonce');

        $output = $this->ui->viewEditStaff();

        $this->assertStringContainsString('Cannot remove staff member. The registration period for &quot;Closed Training&quot; has already ended.', $output);
        $this->assertNotNull($this->staff_repo->get_by_id($staff_id));
        
        $registrations = $this->registration_repo->get_by_staff($staff_id);
        $this->assertNotEmpty($registrations);

        $event = $this->event_repo->get_by_id($event_id);
        $this->assertEquals(1, $event->num_reg);
    }
}
