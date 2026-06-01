<?php

require_once __DIR__ . '/WP_Integration_TestCase.php';

use SOT\TrainingRegistration\Data\Repositories\StaffRepository;

class StaffRepositoryTest extends WP_Integration_TestCase {
    protected $repo;

    public function setUp(): void {
        parent::setUp();
        $this->repo = new StaffRepository();
    }

    public function test_insert_and_get_by_id() {
        $id = $this->repo->insert([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'school'     => 'test_school',
            'phone'      => '123456'
        ]);
        $this->assertNotEmpty($id);

        $staff = $this->repo->get_by_id($id);
        $this->assertEquals('John', $staff->first_name);
        $this->assertEquals('Doe', $staff->last_name);
    }

    public function test_check_duplicate() {
        $this->repo->insert([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'school'     => 'test_school',
            'phone'      => '123456'
        ]);

        $this->assertEquals(1, $this->repo->check_duplicate('John', 'Doe', 'test_school', '123456'));
        $this->assertEquals(0, $this->repo->check_duplicate('Jane', 'Doe', 'test_school', '123456'));
    }

    public function test_get_all_by_school() {
        $this->repo->insert(['first_name' => 'S1', 'school' => 'school_a']);
        $this->repo->insert(['first_name' => 'S2', 'school' => 'school_a']);
        $this->repo->insert(['first_name' => 'S3', 'school' => 'school_b']);

        $results = $this->repo->get_all_by_school('school_a');
        $this->assertCount(2, $results);
    }
}
