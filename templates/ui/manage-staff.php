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
<?php if (count($all_staff) != 0) : ?>
    <form id="select-staff" name="select-staff" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']);?>">
        <?php wp_nonce_field('create_staff_nonce', 'staff_nonce_field'); ?>
        <table>
            <tr>
                <th style="width: fit-content"></th>
                <th style="width: fit-content">Name</th>
                <th style="width: fit-content">Sex</th>
                <th style="width: fit-content">Position</th>
                <?php if ($my_mode == 0) {echo "<th style=\"width: fit-content\">Email</th>";} ?>
                <th style="width: max-content">Upcoming Training(s) Registered</th>
            </tr>
            <?php foreach ($all_staff as $staff) : ?>
                <?php
                $trainings = $registration_repo->get_by_staff($staff->id);
                $training_registered = "<ul style='margin:0'>";
                foreach ($trainings as $training) {
                    $event = $event_repo->get_by_id($training->event_id);
                    if ($event && $time_now < $event->start_time) {
                        $training_registered .= '<li>'.esc_html($event->event_name).'</li>';
                    }
                }
                $training_registered .= '</ul>';
                ?>
                <tr>
                    <td><label><input type="radio" name="select" value="<?php echo $staff->id ?>" required/></label></td>
                    <td><?php echo esc_html($tools->idtoName($staff->id)); ?></td>
                    <td><?php echo $staff->sex; ?></td>
                    <td><?php echo $staff->pos; ?></td>
                    <?php if ($my_mode == 0) {echo "<td>$staff->email</td>";} ?>
                    <td><?php if ($training_registered != "<ul style='margin:0'></ul>") {echo $training_registered;} else {echo "No Trainings Registered";} ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <div style="text-align: center;">
            <input style="float: left; width: fit-content" type="submit" name="edit-reg" id="edit-reg" value="Cancel Staff Registration" />
            <input style="float: right; width: fit-content" type="submit" name="edit-profile" id="edit-profile" value="Edit Staff Profile" />
        </div>
    </form>
<?php else : ?>
    <div style="text-align: center;">
        <h3 style="text-align: center;">No Staff Found</h3>
    </div>
<?php endif; ?>
