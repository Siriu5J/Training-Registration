<?php
/**
 * @var object $event
 * @var array $registrations
 * @var string $time_now
 * @var \SOT\TrainingRegistration\Core\Tools $tools
 * @var \SOT\TrainingRegistration\Data\Repositories\StaffRepository $staff_repo
 * @var string $dashboard_url
 */
?>
<div class="sot-tr-container">
    <div class="sot-tr-header-row">
        <h2>Manage Registrations for: <?php echo esc_html($event->event_name); ?></h2>
        <a href="<?php echo esc_url($dashboard_url); ?>" class="button">Back to Dashboard</a>
    </div>
    
    <div class="sot-tr-event-summary card">
        <p><strong>Location:</strong> <?php echo esc_html($event->location); ?></p>
        <p><strong>Training Date:</strong> <?php echo esc_html(wp_date('F j, Y', strtotime($event->start_time))); ?></p>
        <p><strong>Registration Deadline:</strong> <?php echo esc_html(wp_date('F j, Y g:i a', strtotime($event->close_time))); ?></p>
    </div>

    <?php if (empty($registrations)) : ?>
        <div class="sot-tr-empty">
            <h3>No Registrations Found</h3>
            <p>You haven't registered any staff for this training yet.</p>
        </div>
    <?php else : ?>
        <div class="sot-tr-table-container">
            <table class="sot-tr-table">
                <thead>
                    <tr>
                        <th>Staff Name</th>
                        <th>Sex</th>
                        <th>Position</th>
                        <th>Registration Date</th>
                        <th class="sot-tr-text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrations as $reg) : ?>
                        <?php $staff = $staff_repo->get_by_id($reg->staff); ?>
                        <tr>
                            <td class="sot-tr-fw-600"><?php echo esc_html($tools->idtoName($reg->staff)); ?></td>
                            <td><?php echo esc_html($staff->sex == 'M' ? 'Male' : 'Female'); ?></td>
                            <td><?php echo esc_html($staff->pos); ?></td>
                            <td><?php echo esc_html(wp_date('M j, Y', strtotime($reg->reg_time))); ?></td>
                            <td class="sot-tr-text-center">
                                <?php if ($time_now > $event->close_time) : ?>
                                    <button type="button" class="button button-disabled" title="Registration Closed" onclick="alert('Cannot withdraw staff. The registration period for this training has ended. Please contact the training organizer.');">
                                        <span class="dashicons dashicons-lock"></span> Closed
                                    </button>
                                <?php else : ?>
                                    <form method="post" action="<?php echo esc_url(add_query_arg(array())); ?>" onsubmit="return confirm('Are you sure you want to withdraw <?php echo esc_js($tools->idtoName($reg->staff)); ?> from this training?');">
                                        <?php wp_nonce_field('withdraw_staff_nonce', 'reg_nonce_field'); ?>
                                        <input type="hidden" name="staff_id" value="<?php echo esc_attr($reg->staff); ?>">
                                        <button type="submit" name="withdraw-staff" class="button button-link color-red" title="Withdraw Staff">
                                            <span class="dashicons dashicons-no-alt"></span> Withdraw
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
