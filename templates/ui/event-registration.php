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
<div class="sot-tr-container">
    <?php if (count($trainings_to_show) != 0) : ?>
        <!-- Training Overview Table -->
        <div class="sot-tr-table-container">
            <table class="sot-tr-table wp-block-table">
                <thead>
                    <tr>
                        <th>Training</th>
                        <th>Location</th>
                        <th>Registration Dates</th>
                        <th>Training Dates</th>
                        <?php if ($show_available == 1) : ?>
                            <th>Available</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trainings_to_show as $training) : ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html($training->event_name); ?></strong>
                                <?php if (!empty($training->comment)) : ?>
                                    <div class="sot-tr-training-comment">
                                        <?php echo esc_html($training->comment); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($training->location); ?></td>
                            <td>
                                <?php echo date("Y-m-d", strtotime($training->open_time)); ?> to<br>
                                <?php echo date("Y-m-d", strtotime($training->close_time)); ?>
                            </td>
                            <td>
                                <?php echo date("Y-m-d", strtotime($training->start_time)); ?> to<br>
                                <?php echo date("Y-m-d", strtotime($training->end_time)); ?>
                            </td>
                            <?php if ($show_available == 1) : ?>
                                <td><?php echo $tools->spotsOpen($training->id); ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Registration Form -->
        <div class="sot-tr-form sot-tr-reg-form-wrap">
            <h2 class="sot-tr-center">Register for Training</h2>
            
            <form id="reg-event" name="reg-event" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']);?>">
                <?php wp_nonce_field('reg_nonce', 'reg_nonce_field'); ?>
                
                <div class="sot-tr-form-group">
                    <label for="training">Select Training</label>
                    <select id="training" name="training" required>
                        <option value="" selected disabled>-- Choose a training --</option>
                        <?php
                        foreach($trainings_to_show as $training) {
                            if ($training->open_time < $time_now && $training->close_time > $time_now && ($training->limit_max == 0 || $training->max == -999 || $training->max - $training->num_reg > 0)) {
                                echo '<option value="'.$training->id.'">'.esc_html($training->event_name).' at '.esc_html($training->location).'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="sot-tr-form-group">
                    <label for="staff">Select Staff</label>
                    <select id="staff" name="staff" required>
                        <option value="" selected disabled>-- Choose a staff member --</option>
                        <?php
                        foreach ($staff_available as $staff) {
                            echo '<option value="'.$staff->id.'">'. esc_html($tools->idtoName($staff->id)).'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="sot-tr-form-group">
                    <label for="comment">Comment (Optional)</label>
                    <textarea name="comment" id="comment" placeholder="Any special requests or notes..."></textarea>
                </div>

                <input type="hidden" name="school" value="<?php echo esc_attr($username); ?>">
                
                <div class="sot-tr-submit-row">
                    <input type="submit" name="reg-training" id="reg-training" value="Register Now" class="button button-primary"/>
                </div>
            </form>
        </div>

    <?php else : ?>
        <div class="sot-tr-empty">
            <h3>No trainings are available at this time.</h3>
            <p>Please check back later for upcoming schedules.</p>
        </div>
    <?php endif; ?>
    <div class="sot-tr-spacer"></div>
</div>
