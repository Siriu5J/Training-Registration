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
            <table class="sot-tr-table">
                <thead>
                    <tr>
                        <th>Training Name</th>
                        <th>Location</th>
                        <th>Registration Period</th>
                        <th>Training Dates</th>
                        <?php if ($show_available == 1) : ?>
                            <th>Availability</th>
                        <?php endif; ?>
                        <th style="text-align: center;">Register</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trainings_to_show as $training) : ?>
                        <tr>
                            <td style="font-weight: 600;">
                                <?php echo esc_html($training->event_name); ?>
                                <?php if (!empty($training->comment)) : ?>
                                    <div class="sot-tr-training-comment">
                                        <?php echo esc_html($training->comment); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($training->location); ?></td>
                            <td style="font-size: 0.9rem; white-space: nowrap;">
                                <?php echo date("M j", strtotime($training->open_time)); ?> - <?php echo date("M j, Y", strtotime($training->close_time)); ?>
                            </td>
                            <td style="font-size: 0.9rem; white-space: nowrap;">
                                <?php echo date("M j", strtotime($training->start_time)); ?> - <?php echo date("M j, Y", strtotime($training->end_time)); ?>
                            </td>
                            <?php if ($show_available == 1) : ?>
                                <td style="font-size: 0.9rem; font-weight: 500; color: #2f855a;">
                                    <?php echo $tools->spotsOpen($training->id); ?> spots
                                </td>
                            <?php endif; ?>
                            <td style="text-align: center;">
                                <?php 
                                $can_reg = ($training->open_time < $time_now && $training->close_time > $time_now && ($training->limit_max == 0 || $training->max == -999 || $training->max - $training->num_reg > 0));
                                if ($can_reg) : ?>
                                    <button type="button" class="sot-tr-btn-parity state-select" onclick="selectTraining('<?php echo $training->id; ?>')">
                                        <span class="dashicons dashicons-yes"></span> Select
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="sot-tr-btn-parity state-closed" disabled>
                                        <span class="dashicons dashicons-lock"></span> Closed
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <script>
        function selectTraining(id) {
            const select = document.getElementById('training');
            if (select) {
                select.value = id;
                document.querySelector('.sot-tr-reg-form-wrap').scrollIntoView({ behavior: 'smooth' });
                // Optional: add a brief highlight effect
                select.style.borderColor = '#2271b1';
                select.style.boxShadow = '0 0 0 2px rgba(34, 113, 177, 0.2)';
                setTimeout(() => {
                    select.style.borderColor = '';
                    select.style.boxShadow = '';
                }, 2000);
            }
        }
        </script>

        <!-- Registration Form -->
        <div class="sot-tr-form sot-tr-reg-form-wrap" style="background: #f8fafc; padding: 2.5rem; border-radius: 12px; border: 1px solid #e2e8f0;">
            <h2 class="sot-tr-center" style="margin-top: 0;">Complete Registration</h2>
            
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
