<?php

require_once __DIR__ . '/WP_Integration_TestCase.php';

use SOT\TrainingRegistration\Data\Repositories\RegistrationRepository;
use SOT\TrainingRegistration\Data\Repositories\EventRepository;

class RegistrationRepositoryTest extends WP_Integration_TestCase {
    protected $repo;
    protected $event_repo;

    public function setUp(): void {
        parent::setUp();
        $this->repo = new RegistrationRepository();
        $this->event_repo = new EventRepository();
    }

    public function test_insert_and_get_by_id() {
        $id = $this->repo->insert([
            'event_id' => 1,
            'staff'    => 10,
            'school'   => 'test_school',
            'reg_time' => current_time('mysql')
        ]);
        $this->assertNotEmpty($id);

        $reg = $this->repo->get_by_id($id);
        $this->assertEquals(1, $reg->event_id);
        $this->assertEquals(10, $reg->staff);
    }

    public function test_check_duplicate() {
        $this->repo->insert(['event_id' => 1, 'staff' => 10]);
        
        $this->assertEquals(1, $this->repo->check_duplicate(10, 1));
        $this->assertEquals(0, $this->repo->check_duplicate(11, 1));
    }

    public function test_get_school_agenda() {
        $event_id = $this->event_repo->insert([
            'event_name' => 'Agenda Event',
            'start_time' => date('Y-m-d H:i:s', strtotime('+1 day', current_time('timestamp'))),
            'end_time'   => date('Y-m-d H:i:s', strtotime('+2 days', current_time('timestamp'))),
            'activated'  => 1
        ]);

        $this->repo->insert([
            'event_id' => $event_id,
            'staff'    => 10,
            'school'   => 'my_school'
        ]);

        $agenda = $this->repo->get_school_agenda('my_school');
        $this->assertCount(1, $agenda);
        $this->assertEquals('Agenda Event', $agenda[0]->event_name);
    }

    public function test_delete_by_event_and_staff() {
        $this->repo->insert(['event_id' => 1, 'staff' => 10]);
        $this->assertEquals(1, $this->repo->check_duplicate(10, 1));

        $this->repo->delete_by_event_and_staff(1, 10);
        $this->assertEquals(0, $this->repo->check_duplicate(10, 1));
    }
}
