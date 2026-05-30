<?php
/**
 * @var int $staff_id
 * @var array $trainings_registered
 * @var string $time_now
 * @var \SOT\TrainingRegistration\Core\Tools $tools
 * @var \SOT\TrainingRegistration\Data\Repositories\EventRepository $event_repo
 */
?>
<br><hr>
<?php if ($tools->hasRemovables($staff_id)) : ?>
    <h4>Cancel Registrations for <?php echo $tools->idtoName($staff_id) ?>:</h4>
    <p><b>Important Notice:</b><br>Although it is possible to withdraw from a training here even after the training registration is closed, please <b>ALWAYS</b> notify the training organizer before doing so. To withdraw from a training, select the training(s) and click withdraw.</p>
    <div style="text-align: center;">
        <form id="staff-profile-cancel" name="staff-profile-cancel" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']);?>">
            <?php wp_nonce_field('create_staff_nonce', 'staff_nonce_field'); ?>
            <label for="training-id">Select from list</label>
            <select name="training-id[]" id="training-id" required multiple="multiple">
                <?php
                foreach ($trainings_registered as $training) {
                    $event = $event_repo->get_by_id($training->event_id);
                    if ($event && $event->start_time > $time_now) {
                        echo '<option value="' . esc_attr($training->event_id) . '">' . esc_html($event->event_name . ' at ' . $event->location) . '</option>';
                    }
                }
                ?>
            </select>
            <br>
            <input type="hidden" name="staff_id" value="<?php echo esc_attr($staff_id) ?>">
            <input type="submit" name="confirm-remove" id="confirm-remove" value="Withdraw" /><br><br>
        </form>
        <form id="cancel-form" name="cancel-form" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']);?>">
            <input type="submit" name="cancel" id="cancel" value="Cancel" />
        </form>
    </div>
<?php else : ?>
    <h4>No registrations available for cancelling for <?php echo $tools->idtoName($staff_id) ?></h4>
<?php endif; ?>
