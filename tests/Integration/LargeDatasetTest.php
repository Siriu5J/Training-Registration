<?php

require_once __DIR__ . '/WP_Integration_TestCase.php';

class LargeDatasetTest extends WP_Integration_TestCase {
    protected $event_repo;
    protected $staff_repo;
    protected $registration_repo;

    public function setUp(): void {
        parent::setUp();
        $this->event_repo = new EventRepository();
        $this->staff_repo = new StaffRepository();
        $this->registration_repo = new RegistrationRepository();
    }

    /**
     * Helper to seed a large dataset
     */
    protected function seed_dataset($num_schools = 10, $num_staff_per_school = 20, $num_events = 50) {
        $schools = [];
        for ($i = 1; $i <= $num_schools; $i++) {
            $username = "school_$i";
            $user_id = $this->factory->user->create(['user_login' => $username, 'role' => 'subscriber']);
            update_user_meta($user_id, 'nickname', "School Nickname $i");
            $schools[] = $username;
        }

        $staff_ids = [];
        foreach ($schools as $school) {
            for ($j = 1; $j <= $num_staff_per_school; $j++) {
                $staff_ids[] = $this->staff_repo->insert([
                    'first_name' => "First_$j",
                    'last_name'  => "Last_$j",
                    'school'     => $school,
                    'phone'      => "12345678$j",
                    'sex'        => ($j % 2 == 0 ? 'M' : 'F'),
                    'pos'        => 'Monitor',
                    'lc'         => 'Lower LC'
                ]);
            }
        }

        $event_ids = [];
        for ($k = 1; $k <= $num_events; $k++) {
            $event_ids[] = $this->event_repo->insert([
                'event_name' => "Training Event $k",
                'location'   => "Location $k",
                'max'        => ($k % 5 == 0 ? -999 : 50),
                'open_time'  => date('Y-m-d H:i:s', strtotime('-10 days')),
                'close_time' => date('Y-m-d H:i:s', strtotime('+10 days')),
                'start_time' => date('Y-m-d H:i:s', strtotime('+15 days')),
                'end_time'   => date('Y-m-d H:i:s', strtotime('+20 days')),
                'activated'  => 1,
                'num_reg'    => 0
            ]);
        }

        // Randomly register staff to events
        foreach ($staff_ids as $staff_id) {
            $num_regs = rand(1, 5);
            $random_events = array_rand(array_flip($event_ids), $num_regs);
            if (!is_array($random_events)) $random_events = [$random_events];
            
            foreach ($random_events as $event_id) {
                $this->registration_repo->insert([
                    'event_id' => $event_id,
                    'staff'    => $staff_id,
                    'reg_time' => date('Y-m-d H:i:s'),
                    'school'   => $this->staff_repo->get_by_id($staff_id)->school
                ]);
                $this->event_repo->increment_registration_count($event_id);
            }
        }
    }

    public function test_large_dataset_queries() {
        // Seed a moderately large dataset
        $this->seed_dataset(5, 20, 20); // 100 staff, 20 events

        $upcoming = $this->event_repo->get_all_upcoming();
        $this->assertCount(20, $upcoming);

        $first_event = $upcoming[0];
        $registrations = $this->registration_repo->get_by_event($first_event->id);
        
        // Assert that num_reg count matches real registration count
        $this->assertEquals(count($registrations), $first_event->num_reg);
    }

    public function test_search_pagination_with_large_data() {
        // Seed a larger dataset: 100 events
        $this->seed_dataset(5, 10, 100);

        // Test pagination
        $args = [
            'per_page' => 15,
            'offset'   => 30,
            'customvar'=> 'all'
        ];
        $results = $this->event_repo->search($args);

        $this->assertCount(15, $results['items']);
        $this->assertGreaterThanOrEqual(100, $results['total']); // Might be more from other tests if DB not wiped
        
        // Test search
        $args_search = [
            'search'    => 'Event 42',
            'customvar' => 'all'
        ];
        $search_results = $this->event_repo->search($args_search);
        $this->assertGreaterThanOrEqual(1, $search_results['total']);
        $this->assertStringContainsString('Event 42', $search_results['items'][0]['event_name']);
    }
}
