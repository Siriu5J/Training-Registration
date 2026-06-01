<?php

namespace SOT\TrainingRegistration\Data\Strategies;

use SOT\TrainingRegistration\Data\Repositories\StaffRepository;
use WP_Error;

/**
 * Class SotamRegistrationMode
 *
 * Implementation for the SOTAM (MY) registration mode.
 *
 * @package SOT\TrainingRegistration\Data\Strategies
 */
class SotamRegistrationMode implements RegistrationModeInterface {
    protected $staff_repo;

    public function __construct() {
        $this->staff_repo = new StaffRepository();
    }

    public function get_name() {
        return 'SOTAM';
    }

    public function get_staff_fields($trainee_data, $reg_time, $school_nick) {
        return [
            $reg_time,
            $trainee_data->first_name,
            $trainee_data->last_name,
            $trainee_data->mid_name,
            $trainee_data->sex,
            $trainee_data->cn_name,
            $school_nick,
            $trainee_data->school,
            $trainee_data->phone,
            $trainee_data->pos,
            $trainee_data->grad_year,
            stripslashes($trainee_data->lc),
            stripslashes($trainee_data->degree),
            $trainee_data->comment
        ];
    }

    public function handle_staff_creation($post_data) {
        $first_name = sanitize_text_field($post_data['first_name']);
        $last_name = sanitize_text_field($post_data['last_name']);
        $school = sanitize_text_field($post_data['school']);
        $phone = sanitize_text_field($post_data['phone']);

        if ($this->staff_repo->check_duplicate($first_name, $last_name, $school, $phone) > 0) {
            return new WP_Error('duplicate', "Staff, {$first_name} {$last_name}, already exist in record!");
        }

        return $this->staff_repo->insert([
            "first_name" => $first_name,
            "last_name"  => $last_name,
            "cn_name"    => sanitize_text_field($post_data['cn_name']),
            "mid_name"   => sanitize_text_field($post_data['mid_name']),
            "sex"        => sanitize_text_field($post_data['sex']),
            "school"     => $school,
            "phone"      => $phone,
            "pos"        => sanitize_text_field($post_data['position']),
            "lc"         => sanitize_text_field($post_data['lc']),
            "grad_year"  => sanitize_text_field($post_data['t-exp']),
            "degree"     => sanitize_text_field($post_data['degree']),
            "comment"    => sanitize_textarea_field($post_data['comment']),
        ]);
    }

    public function handle_staff_update($staff_id, $post_data) {
        return $this->staff_repo->update($staff_id, [
            "first_name" => sanitize_text_field($post_data['first_name']),
            "last_name"  => sanitize_text_field($post_data['last_name']),
            "mid_name"   => sanitize_text_field($post_data['mid_name']),
            "cn_name"    => sanitize_text_field($post_data['cn_name']),
            "sex"        => sanitize_text_field($post_data['sex']),
            "school"     => sanitize_text_field($post_data['school']),
            "phone"      => sanitize_text_field($post_data['phone']),
            "pos"        => sanitize_text_field($post_data['position']),
            "lc"         => sanitize_text_field($post_data['lc']),
            "degree"     => sanitize_text_field($post_data['degree']),
            "grad_year"  => sanitize_text_field($post_data['t-exp']),
            "comment"    => sanitize_textarea_field($post_data['comment']),
        ]);
    }

    public function render_staff_creation_form($username, $ui_content) {
        $ui_content->create_staff_my($username);
    }

    public function render_staff_edit_form($username, $profile, $staff_id, $ui_content) {
        $ui_content->edit_staff_my($username, $profile, $staff_id);
    }
}
