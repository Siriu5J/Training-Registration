<?php
/**
 * Data Seeder for Training Registration Plugin
 * Run this script to populate your development environment with test data.
 * 
 * Usage: php scripts/seed-data.php
 */

// Bootstrap WordPress
require_once '/var/www/html/wp-load.php';

use SOT\TrainingRegistration\Data\Repositories\EventRepository;
use SOT\TrainingRegistration\Data\Repositories\StaffRepository;
use SOT\TrainingRegistration\Data\Repositories\RegistrationRepository;

// Ensure we are running from CLI
if (PHP_SAPI !== 'cli') {
    die('This script can only be run from the command line.');
}

echo "Seeding data...\n";

$event_repo = new EventRepository();
$staff_repo = new StaffRepository();
$registration_repo = new RegistrationRepository();

// 1. Create Schools (Users)
$num_schools = 5;
$schools = [];
for ($i = 1; $i <= $num_schools; $i++) {
    $username = "School$i";
    if (!username_exists($username)) {
        $user_id = wp_create_user($username, 'password', "school$i@example.com");
        if (!is_wp_error($user_id)) {
            $user = new WP_User($user_id);
            $user->set_role('subscriber');
            update_user_meta($user_id, 'nickname', "School Name $i");
            echo "Created school: $username\n";
        }
    } else {
        echo "School $username already exists.\n";
    }
    $schools[] = $username;
}

// 2. Create Events
$num_events = 10;
$event_ids = [];
for ($i = 1; $i <= $num_events; $i++) {
    $event_name = "Sample Training Event $i";
    $existing = $event_repo->get_duplicates($event_name);
    if (empty($existing)) {
        $id = $event_repo->insert([
            'event_name' => $event_name,
            'location'   => "Main Hall " . chr(64 + $i),
            'max'        => ($i % 3 == 0 ? -999 : 30),
            'open_time'  => date('Y-m-d H:i:s', strtotime('-5 days')),
            'close_time' => date('Y-m-d H:i:s', strtotime('+15 days')),
            'start_time' => date('Y-m-d H:i:s', strtotime('+20 days')),
            'end_time'   => date('Y-m-d H:i:s', strtotime('+25 days')),
            'activated'  => 1,
            'num_reg'    => 0,
            'comment'    => "This is a sample training event for testing purposes."
        ]);
        $event_ids[] = $id;
        echo "Created event: $event_name\n";
    } else {
        $event_ids[] = $existing[0]->id;
        echo "Event $event_name already exists.\n";
    }
}

// 3. Create Staff and Registrations
foreach ($schools as $school) {
    for ($j = 1; $j <= 3; $j++) {
        $first_name = "Staff{$j}_{$school}";
        $last_name = "Surname";
        $phone = "0123456789$j" . rand(10, 99); // Add randomness to phone to avoid duplicate across schools
        
        if ($staff_repo->check_duplicate($first_name, $last_name, $school, $phone) == 0) {
            $staff_id = $staff_repo->insert([
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'school'     => $school,
                'phone'      => $phone,
                'sex'        => ($j % 2 == 0 ? 'M' : 'F'),
                'pos'        => 'Monitor',
                'lc'         => 'Lower LC',
                'email'      => "staff$j@{$school}.com"
            ]);
            echo "Created staff: $first_name $last_name for $school\n";

            // Randomly register to 1-2 events
            $num_regs = rand(1, 2);
            $random_events = array_rand(array_flip($event_ids), $num_regs);
            if (!is_array($random_events)) $random_events = [$random_events];

            foreach ($random_events as $event_id) {
                $registration_repo->insert([
                    'event_id' => $event_id,
                    'staff'    => $staff_id,
                    'reg_time' => date('Y-m-d H:i:s'),
                    'school'   => $school
                ]);
                $event_repo->increment_registration_count($event_id);
                echo "  Registered to event ID: $event_id\n";
            }
        }
    }
}

echo "Done seeding data!\n";
