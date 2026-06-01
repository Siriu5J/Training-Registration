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
                <table class="sot-tr-table">
                    <thead>
                        <tr>
                            <th>Staff Name</th>
                            <th>Sex</th>
                            <th>Position</th>
                            <?php if ($my_mode == 0) : ?>
                                <th>Email</th>
                            <?php endif; ?>
                            <th>Registered Training(s)</th>
                            <th class="sot-tr-text-center">Actions</th>
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
                                <td class="sot-tr-fw-600"><?php echo esc_html($tools->idtoName($staff->id)); ?></td>
                                <td><?php echo esc_html($staff->sex == 'M' ? 'Male' : 'Female'); ?></td>
                                <td><?php echo esc_html($staff->pos); ?></td>
                                <?php if ($my_mode == 0) : ?>
                                    <td class="sot-tr-fs-09"><?php echo esc_html($staff->email); ?></td>
                                <?php endif; ?>
                                <td>
                                    <?php if (!empty($training_list)) : ?>
                                        <ul class="sot-tr-ul-list">
                                            <?php foreach ($training_list as $t_name) : ?>
                                                <li><?php echo $t_name; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else : ?>
                                        <span class="sot-tr-no-trainings">No upcoming trainings</span>
                                    <?php endif; ?>
                                </td>
                                <td class="sot-tr-text-center">
                                    <div class="sot-tr-action-flex">
                                        <button type="submit" name="edit-profile" value="<?php echo $staff->id; ?>" class="button" title="Edit Profile">

                                            <span class="dashicons dashicons-edit"></span>
                                        </button>
                                        <button type="submit" name="edit-reg" value="<?php echo $staff->id; ?>" class="button" title="Cancel Registration">
                                            <span class="dashicons dashicons-dismiss"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
