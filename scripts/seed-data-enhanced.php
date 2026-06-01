<?php

/**
 * Enhanced Seed Data Generator for Training Registration Plugin v3
 *
 * This script creates comprehensive sample data for testing and development purposes,
 * including realistic staff members, training events, and registrations.
 *
 * USAGE:
 * 1. Place this file in the scripts directory of your Training Registration plugin
 * 2. Access it via command line: php seed-data-enhanced.php
 * 3. Or access it via web browser (ensure WordPress is loaded)
 *
 * ARGUMENTS:
 * --schools=[number]    Number of schools to generate (default: 10)
 * --events=[number]   Number of events to generate (default: 8)
 * --users=[number]  Number of staff users to generate (default: 50)
 * --no-clear          Do not clear existing data before seeding
 * --force             Skip confirmation when clearing data
 *
 * REQUIREMENTS:
 * - WordPress environment with the Training Registration plugin installed
 * - Database connection configured
 * - Appropriate permissions to modify database tables
 *
 * OUTPUT:
 * - Configurable number of staff profiles across configurable number of schools
 * - Configurable number of training events with realistic scheduling
 * - Sample registrations between staff and events
 *
 * WARNING:
 * This script will DELETE all existing data in the plugin's tables before generating new data.
 * Use with caution in production environments.
 */

// Robust WordPress loading
if (!defined('ABSPATH')) {
    $path = __DIR__;
    while ($path !== '/' && !file_exists($path . '/wp-load.php')) {
        $path = dirname($path);
    }

    if (file_exists($path . '/wp-load.php')) {
        require_once $path . '/wp-load.php';
    } else {
        // Fallback to searching for wp-config.php if wp-config.php is not found
        $path = __DIR__;
        while ($path !== '/' && !file_exists($path . '/wp-config.php')) {
            $path = dirname($path);
        }
        if (file_exists($path . '/wp-config.php')) {
            require_once $path . '/wp-config.php';
        } else {
            die("Error: Could not find WordPress environment (wp-load.php or wp-config.php).\n");
        }
    }
}

// Parse command-line arguments
$schools_count = 10;
$events_count = 8;
$users_count = 50;
$clear_data = true;
$force = false;

if (php_sapi_name() === 'cli') {
    $options = getopt("", ["schools:", "events:", "users:", "no-clear", "force", "help"]);

    if (isset($options["schools"])) {
        $schools_count = (int)$options["schools"];
        if ($schools_count < 1) $schools_count = 1;
    }

    if (isset($options["events"])) {
        $events_count = (int)$options["events"];
        if ($events_count < 1) $events_count = 1;
    }

    if (isset($options["users"])) {
        $users_count = (int)$options["users"];
        if ($users_count < 1) $users_count = 1;
    }

    if (isset($options["no-clear"])) {
        $clear_data = false;
    }

    if (isset($options["force"])) {
        $force = true;
    }

    // Check for help argument
    if (isset($options["help"]) || in_array("--help", $argv) || in_array("-h", $argv)) {
        echo "Usage: php seed-data-enhanced.php [OPTIONS]\n\n";
        echo "OPTIONS:\n";
        echo "  --schools=[number]  Number of schools to generate (default: 10)\n";
        echo "  --events=[number]   Number of events to generate (default: 8)\n";
        echo "  --users=[number]    Number of staff users to generate (default: 50)\n";
        echo "  --no-clear          Do not clear existing data before seeding\n";
        echo "  --force             Skip confirmation when clearing data\n";
        echo "  --help              Show this help message\n\n";
        echo "Examples:\n";
        echo "  php seed-data-enhanced.php --schools=5 --events=3 --users=25\n";
        echo "  php seed-data-enhanced.php --no-clear\n";
        exit(0);
    }
}

// Require the main plugin file to ensure constants are defined
require_once dirname(__DIR__) . '/Training-registration.php';

// Get database instance
global $wpdb;

// Ensure tables exist by running activator
if (class_exists('SOT\TrainingRegistration\Core\Activator')) {
    $activator = new SOT\TrainingRegistration\Core\Activator();
    $activator->activate_plugin();
}

// Define table names
$staff_table = ER_STAFF_PROFILE;
$event_table = ER_EVENT_LIST;
$registration_table = ER_REGISTRATION_LIST;

class EnhancedSeedDataGenerator {

    private static $first_names = [
        'John', 'Jane', 'Robert', 'Emily', 'Michael', 'Sarah', 'William', 'Jessica',
        'David', 'Lisa', 'James', 'Jennifer', 'Thomas', 'Patricia', 'Christopher',
        'Linda', 'Daniel', 'Elizabeth', 'Matthew', 'Barbara', 'Christopher', 'Amanda',
        'Matthew', 'Nicole', 'Andrew', 'Ashley', 'Jason', 'Samantha', 'Ryan', 'Melissa',
        'Kevin', 'Kimberly', 'Nicholas', 'Stephanie', 'Eric', 'Amy', 'Timothy', 'Rachel',
        'Steven', 'Lauren', 'Adam', 'Jennifer', 'Brandon', 'Michelle',
        'Jeffrey', 'Jessica', 'Scott', 'Amanda', 'Brian', 'Sarah', 'Richard', 'Elizabeth'
    ];

    private static $last_names = [
        'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis',
        'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Wilson', 'Anderson', 'Taylor',
        'Thomas', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez',
        'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Walker', 'Young', 'Allen', 'King',
        'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill', 'Flores', 'Green', 'Adams', 'Nelson',
        'Baker', 'Hall', 'Rivera', 'Campbell', 'Mitchell', 'Carter', 'Roberts', 'Gomez'
    ];

    private static $cn_names = [
        '张伟', '王芳', '李秀英', '刘伟', '张秀英', '李军', '王静', '张静',
        '李强', '王强', '刘洋', '张敏', '李敏', '王敏', '郭明', '赵军',
        '钱伟', '孙芳', '周秀英', '吴军', '郑静', '王强', '冯洋', '陈敏',
        '褚明', '卫军', '蒋静', '沈强', '韩洋', '杨敏', '朱明', '秦军',
        '许静', '何强', '吕洋', '施敏', '蔡明', '魏军', '丁静', '邓强'
    ];

    private static $positions = ['Administrator', 'Principal', 'Supervisor', 'Monitor', 'Others'];

    private static $locations = [
        'Learning Center A', 'Learning Center B', 'Central Conference Hall', 'School District Office',
        'Regional Educational Hub', 'Virtual Online Platform', 'Community Learning Center',
        'Innovation Lab', 'Professional Development Center', 'Educational Resource Center',
        'Main Auditorium', 'Science Wing', 'Arts Studio', 'Technology Center',
        'Library Annex', 'Cafeteria Learning Area', 'Outdoor Classroom', 'Media Center',
        'Student Lounge', 'Staff Break Room', 'Administration Building', 'Curriculum Office',
        'Parent Meeting Room', 'Staff Training Room', 'Conference Center', 'Instructional Support',
        'Special Education Resource', 'STEM Lab', 'Language Arts Center', 'Mathematics Wing'
    ];

    private static $event_names = [
        'Digital Literacy Workshop', 'Leadership Development Program', 'STEM Conference',
        'Math Pedagogy Seminar', 'Language Arts Curriculum Design', 'Science Inquiry Methods',
        'Social Studies Integration', 'Technology in Classroom Management', 'Special Education Strategies',
        'Student Assessment Techniques', 'Classroom Management Techniques', 'Curriculum Mapping Workshop',
        'Educational Technology Integration', 'Differentiated Instruction Strategies', 'Formative Assessment Practices',
        'Student Engagement Techniques', 'Inclusive Education Methods', 'Educational Research Methods',
        'Professional Learning Communities', 'Instructional Coaching', 'School Improvement Planning',
        'Parent Engagement Strategies', 'Student Mental Health Awareness', 'Cultural Competency Training',
        'STEM Education Innovation', 'Arts Integration in Curriculum', 'Literacy Development',
        'Mathematics Education Research', 'Science Education Best Practices', 'Social Emotional Learning',
        'Educational Leadership Development', 'School Safety and Security', 'Digital Citizenship',
        'Career and Technical Education', 'Early Childhood Education', 'Adult Learning Theory',
        'Educational Policy and Practice', 'Innovation in Education', 'Global Education Perspectives',
        'Educational Equity and Inclusion', 'Student Success Strategies', 'Educational Assessment',
        'Teaching and Learning Research', 'Educational Technology Trends', 'School Culture and Climate',
        'Educational Data Analysis', 'Student Achievement Improvement', 'Educational Program Evaluation',
        'Inclusive Teaching Practices', 'Educational Innovation', 'Learning Analytics',
        'Educational Leadership', 'School Reform', 'Educational Change Management'
    ];

    private static $degrees = ['Bachelor', 'Master', 'Doctorate', 'Associate', 'Certificate', 'PhD'];

    private static $majors = ['Education', 'Psychology', 'Business', 'Computer Science', 'Biology', 'Chemistry', 'Physics', 'Mathematics', 'English', 'History', 'Political Science', 'Sociology', 'Art', 'Music', 'Physical Education', 'Nursing', 'Engineering', 'Communications', 'Marketing', 'Finance'];

    /**
     * Generate a random staff member record
     */
    public static function generateStaffMember($school_id) {
        $first = self::$first_names[array_rand(self::$first_names)];
        $last = self::$last_names[array_rand(self::$last_names)];

        return [
            'first_name' => $first,
            'mid_name'   => '',
            'last_name'  => $last,
            'cn_name'    => self::$cn_names[array_rand(self::$cn_names)],
            'sex'        => rand(0, 1) ? 'M' : 'F',
            'age'        => rand(25, 65),
            'school'     => $school_id,
            'email'      => strtolower($first . '.' . $last) . '@example.org',
            'phone'      => rand(1000000000, 9999999999),
            'pos'        => self::$positions[array_rand(self::$positions)],
            'lc'         => ['Kindergarten','Lower LC','Upper LC','Not in LC'][array_rand(['Kindergarten','Lower LC','Upper LC','Not in LC'])],
            'training_exp' => rand(0, 10),
            'cec_exp'    => rand(0, 5),
            'degree'     => self::$degrees[array_rand(self::$degrees)],
            'grad_year'  => rand(1980, 2024),
            'major'      => self::$majors[array_rand(self::$majors)],
            'minor'      => self::$majors[array_rand(self::$majors)],
            'institution'=> 'University of ' . ['Toronto','Ottawa','Vancouver','Montreal','Calgary','Edmonton','Quebec City','Winnipeg','Halifax','Victoria'][rand(0,9)] . ' School of Education',
            'comment'    => ''
        ];
    }

    /**
     * Generate a training event with realistic timing and capacity data
     */
    public static function generateEvent() {
        $start_date_ts = strtotime('+'.rand(1, 60).' days');
        $start_date = date('Y-m-d H:i:s', $start_date_ts);
        $duration_days = rand(1, 3);
        $end_date = date('Y-m-d H:i:s', strtotime("+$duration_days days", $start_date_ts));

        // Registration open/close times based on event start time
        $open_time = date('Y-m-d H:i:s', strtotime('-14 days', $start_date_ts));
        $close_time = date('Y-m-d H:i:s', strtotime('-2 days', $start_date_ts));

        return [
            'event_name' => self::$event_names[array_rand(self::$event_names)],
            'max'          => rand(20, 100),
            'open_time'    => $open_time,
            'close_time'   => $close_time,
            'start_time'   => $start_date,
            'end_time'     => $end_date,
            'location'     => self::$locations[array_rand(self::$locations)],
            'limit_max'    => (bool)rand(0, 1),
            'activated'    => true,
            'comment'      => 'Sample event generated by seeder.',
            'num_reg'      => 0
        ];
    }

    /**
     * Generate an event registration record
     */
    public static function generateRegistration($staff_id, $event_id, $school_name) {
        return [
            'event_id'   => $event_id,
            'staff'      => $staff_id,
            'reg_time'   => date('Y-m-d H:i:s'),
            'school'     => $school_name,
            'comment'    => 'Automated registration.'
        ];
    }
}

/**
 * Main seeding logic
 */

if ($clear_data) {
    if (!$force && php_sapi_name() === 'cli') {
        echo "WARNING: This will DELETE all existing data in the plugin's tables.\n";
        echo "Are you sure you want to continue? (y/N): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (trim(strtolower($line)) != 'y') {
            echo "Operation cancelled.\n";
            exit;
        }
    }

    echo "Clearing existing database tables...\n";
    $wpdb->query("TRUNCATE TABLE {$staff_table}");
    $wpdb->query("TRUNCATE TABLE {$event_table}");
    $wpdb->query("TRUNCATE TABLE {$registration_table}");

    // Clear existing school users
    $existing_school_users = $wpdb->get_results("SELECT ID FROM $wpdb->users WHERE user_login LIKE 'SCHOOL_%'");
    if (!empty($existing_school_users)) {
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        foreach ($existing_school_users as $u) {
            wp_delete_user($u->ID);
        }
        echo "Cleared existing school user accounts.\n";
    }
}

echo "\n=== SEEDING SCHOOLS AND USERS ===\n";

$school_ids = [];
for ($i = 1; $i <= $schools_count; $i++) {
    $school_login = 'SCHOOL_' . str_pad($i, 3, '0', STR_PAD_LEFT);
    $school_ids[] = $school_login;
    
    if (!username_exists($school_login)) {
        $user_id = wp_create_user($school_login, 'password', $school_login . '@example.org');
        if (!is_wp_error($user_id)) {
            update_user_meta($user_id, 'nickname', "School " . str_pad($i, 3, '0', STR_PAD_LEFT));
            // Set role to subscriber as seen in tests
            $u = new WP_User($user_id);
            $u->set_role('subscriber');
            echo "Created user account: {$school_login}\n";
        } else {
            echo "Error creating user {$school_login}: " . $user_id->get_error_message() . "\n";
        }
    } else {
        echo "User account {$school_login} already exists, skipping creation.\n";
    }
}

echo "\n=== SEEDING STAFF PROFILES ===\n";

for ($index = 1; $index <= $users_count; $index++) {
    $current_school = $school_ids[array_rand($school_ids)];
    $staff_data = EnhancedSeedDataGenerator::generateStaffMember($current_school);

    if ($wpdb->insert($staff_table, $staff_data)) {
        echo "Added staff member #" . sprintf('%03d', $index) . ": {$staff_data['first_name']} {$staff_data['last_name']} ({$current_school})\n";
    }
}

echo "\nGenerated {$users_count} staff profiles across {$schools_count} schools\n";

echo "\n=== SEEDING TRAINING EVENTS ===\n";

$events_created = [];
for ($index = 1; $index <= $events_count; $index++) {
    $event_data = EnhancedSeedDataGenerator::generateEvent();

    if ($wpdb->insert($event_table, $event_data)) {
        $event_id = $wpdb->insert_id;
        $events_created[] = [
            'id' => $event_id, 
            'name' => $event_data['event_name'],
            'max' => $event_data['max']
        ];
        echo "Created event #" . sprintf('%02d', $index) . ": {$event_data['event_name']}\n";
    }
}

echo "\nGenerated {$events_count} training events\n";

echo "\n=== CREATING EVENT REGISTRATIONS ===\n";

if (!empty($events_created)) {
    // Get all staff records with their schools
    $all_staff = $wpdb->get_results("SELECT id, school FROM {$staff_table}");

    foreach ($events_created as $event_info) {
        $event_id = $event_info['id'];
        // Randomly decide how many people register, up to event max or available staff
        $registrations_to_create = rand(min(5, count($all_staff)), min($event_info['max'], count($all_staff)));

        echo "Registering {$registrations_to_create} staff members for event: {$event_info['name']}...\n";

        shuffle($all_staff);
        $count = 0;
        for ($j = 0; $j < $registrations_to_create; $j++) {
            $staff_member = $all_staff[$j];
            $reg_data = EnhancedSeedDataGenerator::generateRegistration(
                $staff_member->id,
                $event_id,
                $staff_member->school
            );

            if ($wpdb->insert($registration_table, $reg_data)) {
                $count++;
            }
        }

        // Update the event's num_reg field once per event
        $wpdb->update($event_table, ['num_reg' => $count], ['id' => $event_id]);
        echo "  Done. {$count} registrations created.\n";
    }
}

echo "\n=== SEED DATA GENERATION COMPLETE ===\n";
echo "Summary:\n";
echo "  • Staff Profiles: {$users_count}\n";
echo "  • Training Events: {$events_count}\n";
echo "  • Registrations Created: " . count($wpdb->get_results("SELECT * FROM {$registration_table}")) . "\n";
?>
