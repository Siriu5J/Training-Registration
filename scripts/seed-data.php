<?php
/**
 * Data Seeder for Training Registration Plugin
 * Run this script to populate your development environment with test data.
 * 
 * Usage: php scripts/seed-data.php [--schools=N] [--events=N] [--staff-per-school=N]
 * 
 * Options:
 *   --schools=N          Number of schools to create (default: 5)
 *   --events=N           Number of events to create (default: 10)
 *   --staff-per-school=N Number of staff per school (default: 3)
 *   -h, --help           Show usage information
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

/**
 * Parse command-line arguments
 * @return array Parsed arguments with defaults
 */
function parse_arguments() {
    $args = $_SERVER['argv'];
    $defaults = [
        'schools' => 5,
        'events' => 10,
        'staff-per-school' => 3
    ];
    $parsed = $defaults;
    
    // Check for help flag
    if (in_array('-h', $args) || in_array('--help', $args)) {
        show_usage();
        exit(0);
    }
    
    // Parse arguments
    for ($i = 1; $i < count($args); $i++) {
        if (preg_match('/^--(\w+)=([0-9]+)$/', $args[$i], $matches)) {
            switch ($matches[1]) {
                case 'schools':
                    $parsed['schools'] = (int)$matches[2];
                    break;
                case 'events':
                    $parsed['events'] = (int)$matches[2];
                    break;
                case 'staff-per-school':
                    $parsed['staff-per-school'] = (int)$matches[2];
                    break;
                default:
                    // Unknown argument, ignore
                    break;
            }
        }
    }
    
    return $parsed;
}

/**
 * Show usage message and exit
 */
function show_usage() {
    echo "Usage: php scripts/seed-data.php [--schools=N] [--events=N] [--staff-per-school=N]\n\n";
    echo "Options:\n";
    echo "  --schools=N          Number of schools to create (default: 5)\n";
    echo "  --events=N           Number of events to create (default: 10)\n";
    echo "  --staff-per-school=N Number of staff per school (default: 3)\n";
    echo "  -h, --help           Show this help message\n\n";
    echo "Examples:\n";
    echo "  php scripts/seed-data.php\n";
    echo "  php scripts/seed-data.php --schools=10 --events=20\n";
    echo "  php scripts/seed-data.php --staff-per-school=5\n";
    echo "  php scripts/seed-data.php --schools=8 --events=15 --staff-per-school=4\n";
}

// Parse arguments
$arguments = parse_arguments();

echo "Seeding data with parameters:\n";
echo "  Schools: " . $arguments['schools'] . "\n";
echo "  Events: " . $arguments['events'] . "\n";
echo "  Staff per school: " . $arguments['staff-per-school'] . "\n\n";

$event_repo = new EventRepository();
$staff_repo = new StaffRepository();
$registration_repo = new RegistrationRepository();

// 1. Create Schools (Users)
$num_schools = $arguments['schools'];
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
$num_events = $arguments['events'];
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
    for ($j = 1; $j <= $arguments['staff-per-school']; $j++) {
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
