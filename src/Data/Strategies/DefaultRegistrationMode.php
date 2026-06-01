<?php

namespace SOT\TrainingRegistration\Data\Strategies;

use SOT\TrainingRegistration\Data\Repositories\StaffRepository;
use WP_Error;

/**
 * Class DefaultRegistrationMode
 *
 * Implementation for the default (CN) registration mode.
 *
 * @package SOT\TrainingRegistration\Data\Strategies
 */
class DefaultRegistrationMode implements RegistrationModeInterface {
    protected $staff_repo;

    public function __construct() {
        $this->staff_repo = new StaffRepository();
    }

    public function get_name() {
        return 'Default';
    }

    public function get_staff_fields($trainee_data, $reg_time, $school_nick) {
        return [
            $reg_time,
            $trainee_data->first_name,
            $trainee_data->last_name,
            $trainee_data->cn_name,
            $trainee_data->sex,
            $trainee_data->age,
            $school_nick,
            $trainee_data->school,
            $trainee_data->email,
            $trainee_data->phone,
            $trainee_data->pos,
            $trainee_data->lc,
            $trainee_data->training_exp,
            $trainee_data->cec_exp,
            $trainee_data->degree,
            $trainee_data->grad_year,
            $trainee_data->major,
            $trainee_data->minor,
            $trainee_data->institution,
            $trainee_data->comment
        ];
    }

    public function get_excel_template() {
        return ER_PLUGIN_DIR . '/files/Default_Excel_Template.xlsx';
    }

    public function get_excel_column_format() {
        return 'A2:T2';
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
            "first_name"    => $first_name,
            "last_name"     => $last_name,
            "cn_name"       => sanitize_text_field($post_data['cn_name'] ?? ''),
            "sex"           => sanitize_text_field($post_data['sex']),
            "age"           => intval($post_data['age']),
            "school"        => $school,
            "email"         => sanitize_email($post_data['email']),
            "phone"         => $phone,
            "pos"           => sanitize_text_field($post_data['position']),
            "lc"            => sanitize_text_field($post_data['lc']),
            "training_exp"  => sanitize_text_field($post_data['t-exp']),
            "cec_exp"       => sanitize_text_field($post_data['cec-exp']),
            "degree"        => sanitize_text_field($post_data['degree']),
            "grad_year"     => sanitize_text_field($post_data['grad-year']),
            "major"         => sanitize_text_field($post_data['major']),
            "minor"         => sanitize_text_field($post_data['minor'] ?? ''),
            "institution"   => sanitize_text_field($post_data['institution']),
            "comment"       => sanitize_textarea_field($post_data['comment']),
        ]);
    }

    public function handle_staff_update($staff_id, $post_data) {
        return $this->staff_repo->update($staff_id, [
            "first_name"    => sanitize_text_field($post_data['first_name']),
            "last_name"     => sanitize_text_field($post_data['last_name']),
            "cn_name"       => sanitize_text_field($post_data['cn_name'] ?? ''),
            "sex"           => sanitize_text_field($post_data['sex']),
            "age"           => intval($post_data['age']),
            "school"        => sanitize_text_field($post_data['school']),
            "email"         => sanitize_email($post_data['email']),
            "phone"         => sanitize_text_field($post_data['phone']),
            "pos"           => sanitize_text_field($post_data['position']),
            "lc"            => sanitize_text_field($post_data['lc']),
            "training_exp"  => sanitize_text_field($post_data['t-exp']),
            "cec_exp"       => sanitize_text_field($post_data['cec-exp']),
            "degree"        => sanitize_text_field($post_data['degree']),
            "grad_year"     => sanitize_text_field($post_data['grad-year']),
            "major"         => sanitize_text_field($post_data['major']),
            "minor"         => sanitize_text_field($post_data['minor'] ?? ''),
            "institution"   => sanitize_text_field($post_data['institution']),
            "comment"       => sanitize_textarea_field($post_data['comment']),
        ]);
    }

    public function render_staff_creation_form($username, $ui_content) {
        $ui_content->create_staff_cn($username);
    }

    public function render_staff_edit_form($username, $profile, $staff_id, $ui_content) {
        $ui_content->edit_staff_cn($username, $profile, $staff_id);
    }
}
