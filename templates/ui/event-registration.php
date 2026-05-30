<?php
/**
 * @var array $trainings_to_show
 * @var \SOT\TrainingRegistration\Core\Tools $tools
 * @var int $show_available
 * @var string $time_now
 * @var array $staff_available
 * @var string $username
 */
?>
<?php if (count($trainings_to_show) != 0) : ?>
    <div>
        <!-- Show all trainings that has not yet been started -->
        <table>
            <?php foreach ($trainings_to_show as $training) : ?>
                <tr>
                    <th colspan="3"><?php echo $training->event_name.' ('.$tools->availability($training).')' ?></th>
                    <th style="width: 20%">Location</th>
                    <td style="width: fit-content" colspan="2"><?php echo $training->location ?></td>
                </tr>
                <tr>
                    <td></td>
                    <th>Registration Date</th>
                    <td><?php echo date("Y-m-d", strtotime($training->open_time)).' to '. date("Y-m-d", strtotime($training->close_time))?></td>
                    <th style="width: 20%">Training Date</th>
                    <td><?php echo date("Y-m-d", strtotime($training->start_time)).' to '. date("Y-m-d", strtotime($training->end_time))?></td>
                </tr>
                <?php if ($show_available == 1) : ?>
                    <tr>
                        <td></td>
                        <th>Available Seats</th>
                        <td colspan="3"><?php echo $tools->spotsOpen($training->id); ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td></td>
                    <th>Information</th>
                    <td colspan="3"><?php echo esc_html($training->comment); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Form for registering into training -->
    <form id="reg-event" name="reg-event" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']);?>">
        <?php wp_nonce_field('reg_nonce', 'reg_nonce_field'); ?>
        <div style="display: flex; align-items: center; margin-bottom: 10px;">
            <label for="training" style="width: 20%;">Select Training:</label>
            <select id="training" name="training" required>
                <option selected disabled>Select a training</option>
                <?php
                foreach($trainings_to_show as $training) {
                    if ($training->open_time < $time_now && $training->close_time > $time_now && ($training->limit_max == 0 || $training->max == -999 || $training->max - $training->num_reg > 0)) {
                        echo '<option value="'.$training->id.'">'.$training->event_name.' at '.$training->location.'</option>';
                    }
                }
                ?>
            </select>
        </div>
        <br>
        <div style="display: flex; align-items: center; margin-bottom: 10px;">
            <label for="staff" style="width: 20%;">Staff List:</label>
            <select id="staff" name="staff">
                <option selected disabled>Select a staff</option>
                <?php
                foreach ($staff_available as $staff) {
                    echo '<option value="'.$staff->id.'">'. esc_html($tools->idtoName($staff->id)).'</option>';
                }
                ?>
            </select>
        </div>
        <br>
        <div style="display: flex; margin-bottom: 10px;">
            <label for="comment" style="width: 20%">Comment: </label><textarea name="comment" id="comment"></textarea>
        </div>
        <input type="hidden" name="school" value="<?php echo esc_attr($username); ?>">
        <br><br><input type="submit" name="reg-training" id="reg-training" value="Register"/><br><br>
    </form>
<?php else : ?>
    <div style="text-align: center;">
        <h3 style="text-align: center;">No trainings are available now. Check again later.</h3>
    </div>
<?php endif; ?>
