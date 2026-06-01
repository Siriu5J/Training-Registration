<?php

require_once __DIR__ . '/WP_Integration_TestCase.php';

use SOT\TrainingRegistration\Data\Repositories\EventRepository;

class EventRepositoryTest extends WP_Integration_TestCase {
    protected $repo;

    public function setUp(): void {
        parent::setUp();
        $this->repo = new EventRepository();
    }

    public function test_insert_and_get_by_id() {
        $data = [
            'event_name' => 'Integration Test Event',
            'location'   => 'Test Lab',
            'max'        => 10,
            'open_time'  => '2026-01-01 00:00:00',
            'close_time' => '2026-12-31 23:59:59',
            'start_time' => '2027-01-01 09:00:00',
            'end_time'   => '2027-01-01 17:00:00',
            'activated'  => 1,
            'num_reg'    => 0
        ];

        $id = $this->repo->insert($data);
        $this->assertNotEmpty($id);

        $event = $this->repo->get_by_id($id);
        $this->assertEquals('Integration Test Event', $event->event_name);
        $this->assertEquals('Test Lab', $event->location);
    }

    public function test_increment_decrement_registration_count() {
        $id = $this->repo->insert([
            'event_name' => 'Counter Test',
            'num_reg'    => 0
        ]);

        $this->repo->increment_registration_count($id);
        $event = $this->repo->get_by_id($id);
        $this->assertEquals(1, $event->num_reg);

        $this->repo->decrement_registration_count($id);
        $event = $this->repo->get_by_id($id);
        $this->assertEquals(0, $event->num_reg);
    }

    public function test_get_all_upcoming() {
        $now = '2026-06-01 12:00:00';
        
        // Future event
        $this->repo->insert([
            'event_name' => 'Future Event',
            'start_time' => '2026-07-01 00:00:00',
            'activated'  => 1
        ]);

        // Past event
        $this->repo->insert([
            'event_name' => 'Past Event',
            'start_time' => '2026-05-01 00:00:00',
            'activated'  => 1
        ]);

        $upcoming = $this->repo->get_all_upcoming($now);
        $this->assertCount(1, $upcoming);
        $this->assertEquals('Future Event', $upcoming[0]->event_name);
    }
}
