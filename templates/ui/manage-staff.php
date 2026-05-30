<?php
/**
 * @var array $all_staff
 * @var int $my_mode
 * @var string $time_now
 * @var \SOT\TrainingRegistration\Core\Tools $tools
 * @var \SOT\TrainingRegistration\Data\Repositories\EventRepository $event_repo
 * @var \SOT\TrainingRegistration\Data\Repositories\RegistrationRepository $registration_repo
 */
?>
<div class="sot-tr-container">
    <?php if (count($all_staff) != 0) : ?>
        <form id="select-staff" name="select-staff" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']);?>">
            <?php wp_nonce_field('create_staff_nonce', 'staff_nonce_field'); ?>
            
            <div class="sot-tr-table-container">
                <table class="sot-tr-table wp-block-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Name</th>
                            <th>Sex</th>
                            <th>Position</th>
                            <?php if ($my_mode == 0) : ?>
                                <th>Email</th>
                            <?php endif; ?>
                            <th>Registered Training(s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_staff as $staff) : ?>
                            <?php
                            $trainings = $registration_repo->get_by_staff($staff->id);
                            $training_list = array();
                            foreach ($trainings as $training) {
                                $event = $event_repo->get_by_id($training->event_id);
                                if ($event && $time_now < $event->start_time) {
                                    $training_list[] = esc_html($event->event_name);
                                }
                            }
                            ?>
                            <tr>
                                <td>
                                    <input type="radio" name="select" value="<?php echo $staff->id ?>" required/>
                                </td>
                                <td><?php echo esc_html($tools->idtoName($staff->id)); ?></td>
                                <td><?php echo esc_html($staff->sex); ?></td>
                                <td><?php echo esc_html($staff->pos); ?></td>
                                <?php if ($my_mode == 0) : ?>
                                    <td><?php echo esc_html($staff->email); ?></td>
                                <?php endif; ?>
                                <td>
                                    <?php if (!empty($training_list)) : ?>
                                        <ul style="margin: 0; padding-left: 1.2rem;">
                                            <?php foreach ($training_list as $t_name) : ?>
                                                <li><?php echo $t_name; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else : ?>
                                        <span style="color: #a0aec0; font-style: italic;">No Trainings Registered</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="sot-tr-actions">
                <input type="submit" name="edit-profile" id="edit-profile" value="Edit Staff Profile" class="button" />
                <input type="submit" name="edit-reg" id="edit-reg" value="Cancel Staff Registration" class="button" />
            </div>
        </form>
    <?php else : ?>
        <div class="sot-tr-empty">
            <h3>No Staff Found</h3>
            <p>You haven't added any staff members yet.</p>
        </div>
    <?php endif; ?>
    <div class="sot-tr-spacer"></div>
</div>
