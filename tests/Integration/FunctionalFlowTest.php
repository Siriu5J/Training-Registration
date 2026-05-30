<?php

require_once __DIR__ . '/WP_Integration_TestCase.php';

class FunctionalFlowTest extends WP_Integration_TestCase {
    protected $event_repo;
    protected $staff_repo;
    protected $registration_repo;
    protected $tools;

    public function setUp(): void {
        parent::setUp();
        $this->event_repo = new EventRepository();
        $this->staff_repo = new StaffRepository();
        $this->registration_repo = new RegistrationRepository();
        $this->tools = new training_registration_tools();
    }

    public function test_full_registration_lifecycle() {
        // 1. Create a school user
        $school_user = 'test_school';
        $user_id = $this->factory->user->create(['user_login' => $school_user, 'role' => 'subscriber']);
        
        // 2. Create a staff member
        $staff_id = $this->staff_repo->insert([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'school'     => $school_user,
            'phone'      => '555-1234',
            'sex'        => 'M',
            'pos'        => 'Monitor'
        ]);
        $this->assertNotEmpty($staff_id);

        // 3. Create a training event
        $event_id = $this->event_repo->insert([
            'event_name' => 'Advanced Training',
            'location'   => 'Room A',
            'max'        => 5,
            'open_time'  => date('Y-m-d H:i:s', strtotime('-1 day')),
            'close_time' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'start_time' => date('Y-m-d H:i:s', strtotime('+5 days')),
            'end_time'   => date('Y-m-d H:i:s', strtotime('+6 days')),
            'activated'  => 1,
            'num_reg'    => 0
        ]);
        $this->assertNotEmpty($event_id);

        // 4. Verify initial availability
        $event = $this->event_repo->get_by_id($event_id);
        $this->assertEquals('Open', $this->tools->availability($event));
        $this->assertEquals('5/5 available', $this->tools->spotsOpen($event_id));

        // 5. Register the staff member
        $reg_id = $this->registration_repo->insert([
            'event_id' => $event_id,
            'staff'    => $staff_id,
            'reg_time' => current_time('mysql'),
            'school'   => $school_user
        ]);
        $this->assertNotEmpty($reg_id);
        $this->event_repo->increment_registration_count($event_id);

        // 6. Verify updated availability
        $event = $this->event_repo->get_by_id($event_id);
        $this->assertEquals(1, $event->num_reg);
        $this->assertEquals('4/5 available', $this->tools->spotsOpen($event_id));

        // 7. Verify hasRemovables
        $this->assertTrue($this->tools->hasRemovables($staff_id));

        // 8. Cancel registration
        $this->registration_repo->delete_by_event_and_staff($event_id, $staff_id);
        $this->event_repo->decrement_registration_count($event_id);

        // 9. Final verification
        $event = $this->event_repo->get_by_id($event_id);
        $this->assertEquals(0, $event->num_reg);
        $this->assertEquals('5/5 available', $this->tools->spotsOpen($event_id));
        $this->assertFalse($this->tools->hasRemovables($staff_id));
    }

    public function test_capacity_limits() {
        // Create an event with small capacity
        $event_id = $this->event_repo->insert([
            'event_name' => 'Small Seminar',
            'location'   => 'Room B',
            'max'        => 2,
            'open_time'  => date('Y-m-d H:i:s', strtotime('-1 day')),
            'close_time' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'start_time' => date('Y-m-d H:i:s', strtotime('+5 days')),
            'end_time'   => date('Y-m-d H:i:s', strtotime('+6 days')),
            'activated'  => 1,
            'num_reg'    => 0,
            'limit_max'  => 1 // Capped
        ]);

        // Register 2 staff members
        for ($i = 1; $i <= 2; $i++) {
            $staff_id = $this->staff_repo->insert(['first_name' => "Staff_$i", 'last_name' => 'Test', 'school' => 'school_1']);
            $this->registration_repo->insert(['event_id' => $event_id, 'staff' => $staff_id, 'reg_time' => current_time('mysql')]);
            $this->event_repo->increment_registration_count($event_id);
        }

        $event = $this->event_repo->get_by_id($event_id);
        $this->assertEquals('Full and capped', $this->tools->availability($event));
        $this->assertEquals('0/2, Full', $this->tools->spotsOpen($event_id));

        // Attempt one more (it shouldn't be allowed by business logic in ui, but here we just check if state is correct)
        $staff_id_3 = $this->staff_repo->insert(['first_name' => 'Staff_3', 'last_name' => 'Test', 'school' => 'school_1']);
        $this->registration_repo->insert(['event_id' => $event_id, 'staff' => $staff_id_3, 'reg_time' => current_time('mysql')]);
        $this->event_repo->increment_registration_count($event_id);

        $event = $this->event_repo->get_by_id($event_id);
        $this->assertEquals('0/2, 1 overflow(s)', $this->tools->spotsOpen($event_id));
    }
}
