<?php
/**
 * @var int $staff_id
 * @var array $trainings_registered
 * @var string $time_now
 * @var \SOT\TrainingRegistration\Core\Tools $tools
 * @var \SOT\TrainingRegistration\Data\Repositories\EventRepository $event_repo
 */
?>
<div class="sot-tr-container">
    <hr>
    
    <?php if (!empty($trainings_registered)) : ?>
        <h3>Withdraw Registrations for <?php echo esc_html($tools->idtoName($staff_id)) ?></h3>
        
        <div class="er-notice er-notice-red">
            <div>
                <strong>Notice:</strong><br>
                If a training registration period has ended, it will be disabled in the list below. To withdraw from a closed training, please <strong>ALWAYS</strong> contact the training organizer.
            </div>
        </div>

        <div class="sot-tr-form">
            <form id="staff-profile-cancel" name="staff-profile-cancel" method="post" action="<?php echo esc_url(add_query_arg(array()));?>" onsubmit="return confirm('Are you sure you want to withdraw from the selected training(s)? This action cannot be undone.');">
                <?php wp_nonce_field('create_staff_nonce', 'staff_nonce_field'); ?>
                
                <div class="sot-tr-form-group">
                    <label for="training-id">Select training(s) to withdraw from:</label>
                    <select name="training-id[]" id="training-id" required multiple="multiple" class="sot-tr-cancel-select">
                        <?php
                        foreach ($trainings_registered as $training) {
                            $event = $event_repo->get_by_id($training->event_id);
                            if ($event) {
                                $is_closed = $time_now > $event->close_time;
                                $disabled = $is_closed ? ' disabled="disabled"' : '';
                                $label_suffix = $is_closed ? ' (Registration Closed)' : '';
                                
                                echo '<option value="' . esc_attr($training->event_id) . '"' . $disabled . '>' . 
                                     esc_html($event->event_name . ' at ' . $event->location . $label_suffix) . 
                                     '</option>';
                            }
                        }
                        ?>
                    </select>
                    <p>Hold Ctrl (Windows) or Command (Mac) to select multiple trainings.</p>
                </div>

                <input type="hidden" name="staff_id" value="<?php echo esc_attr($staff_id) ?>">
                
                <div class="sot-tr-submit-row">
                    <input type="submit" name="confirm-remove" id="confirm-remove" value="Confirm Withdrawal" class="button button-primary" />
                    
                    <button type="submit" name="cancel" id="cancel" class="button" form="cancel-form">Back to List</button>
                </div>
            </form>
            
            <form id="cancel-form" name="cancel-form" method="post" action="<?php echo esc_url(add_query_arg(array()));?>" class="sot-tr-hidden-form"></form>
        </div>
    <?php else : ?>
        <div class="sot-tr-empty">
            <h3>No registrations found</h3>
            <p>There are no trainings registered for <?php echo esc_html($tools->idtoName($staff_id)); ?>.</p>
        </div>
    <?php endif; ?>
</div>
