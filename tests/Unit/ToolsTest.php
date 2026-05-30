<?php

use SOT\TrainingRegistration\Core\Tools;
use PHPUnit\Framework\TestCase;

class ToolsTest extends TestCase {
    protected $tools;

    public function setUp(): void {
        parent::setUp();
        WP_Mock::setUp();
        
        $this->tools = new Tools();
    }

    public function tearDown(): void {
        WP_Mock::tearDown();
        parent::tearDown();
    }

    public function test_isValidEvent_returns_true_if_no_duplicates() {
        global $wpdb;
        $wpdb = Mockery::mock('stdClass');
        $wpdb->shouldReceive('get_results')->andReturn([]);
        $wpdb->shouldReceive('prepare')->andReturnUsing(function($query, ...$args) {
            return sprintf(str_replace('%s', "'%s'", $query), ...$args);
        });

        $result = $this->tools->isValidEvent('Test Event', 'Location', '2026-01-01', 0);
        $this->assertTrue($result);
    }

    public function test_idtoName_returns_formatted_name() {
        global $wpdb;
        $wpdb = Mockery::mock('stdClass');
        $wpdb->prefix = 'wp_';
        $row = new stdClass();
        $row->first_name = 'John';
        $row->last_name = 'Doe';
        
        $wpdb->shouldReceive('get_row')->andReturn($row);
        $wpdb->shouldReceive('prepare')->andReturn('query');

        $result = $this->tools->idtoName(1);
        $this->assertEquals('John Doe', $result);
    }

    public function test_spotsOpen_unlimited() {
        global $wpdb;
        $wpdb = Mockery::mock('stdClass');
        $training = new stdClass();
        $training->max = -999;
        
        $wpdb->shouldReceive('get_row')->andReturn($training);
        $wpdb->shouldReceive('get_results')->andReturn(array_fill(0, 5, new stdClass())); // 5 registrations
        $wpdb->shouldReceive('prepare')->andReturn('query');

        $result = $this->tools->spotsOpen(1);
        $this->assertEquals('Unlimited, 5 registered', $result);
    }

    public function test_availability_closed() {
        WP_Mock::userFunction('current_time', [
            'args' => ['mysql'],
            'return' => '2026-05-30 12:00:00'
        ]);
        
        $row = new stdClass();
        $row->close_time = '2026-05-29 12:00:00';
        $row->open_time = '2026-05-20 12:00:00';
        $row->num_reg = 0;
        $row->max = 10;
        $row->limit_max = 1;

        $result = $this->tools->availability($row);
        $this->assertEquals('Closed', $result);
    }
}
