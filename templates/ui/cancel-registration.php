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
    
    <?php if ($tools->hasRemovables($staff_id)) : ?>
        <h3>Cancel Registrations for <?php echo esc_html($tools->idtoName($staff_id)) ?></h3>
        
        <div class="er-notice er-notice-red">
            <div>
                <strong>Important Notice:</strong><br>
                Although it is possible to withdraw from a training here even after the training registration is closed, please <strong>ALWAYS</strong> notify the training organizer before doing so.
            </div>
        </div>

        <div class="sot-tr-form">
            <form id="staff-profile-cancel" name="staff-profile-cancel" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']);?>">
                <?php wp_nonce_field('create_staff_nonce', 'staff_nonce_field'); ?>
                
                <div class="sot-tr-form-group">
                    <label for="training-id">Select training(s) to withdraw from:</label>
                    <select name="training-id[]" id="training-id" required multiple="multiple" style="height: auto; min-height: 150px;">
                        <?php
                        foreach ($trainings_registered as $training) {
                            $event = $event_repo->get_by_id($training->event_id);
                            if ($event && $event->start_time > $time_now) {
                                echo '<option value="' . esc_attr($training->event_id) . '">' . esc_html($event->event_name . ' at ' . $event->location) . '</option>';
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
            
            <form id="cancel-form" name="cancel-form" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']);?>" style="display:none;"></form>
        </div>
    <?php else : ?>
        <div class="sot-tr-empty">
            <h3>No registrations available for cancellation</h3>
            <p>There are no upcoming trainings registered for <?php echo esc_html($tools->idtoName($staff_id)); ?>.</p>
        </div>
    <?php endif; ?>
</div>
