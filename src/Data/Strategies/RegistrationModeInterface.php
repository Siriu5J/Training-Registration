<?php

namespace SOT\TrainingRegistration\Data\Strategies;

defined('ABSPATH') || exit;

/**
 * Interface RegistrationModeInterface
 *
 * Defines the contract for different registration modes (e.g., Default CN vs SOTAM MY).
 *
 * @package SOT\TrainingRegistration\Data\Strategies
 */
interface RegistrationModeInterface {
    public function get_name();
    
    public function get_staff_fields($trainee_data, $reg_time, $school_nick);
    
    public function get_excel_template();
    
    public function get_excel_column_format();
    
    public function handle_staff_creation($post_data);
    
    public function handle_staff_update($staff_id, $post_data);
    
    public function render_staff_creation_form($username, $ui_content);
    
    public function render_staff_edit_form($username, $profile, $staff_id, $ui_content);
}
