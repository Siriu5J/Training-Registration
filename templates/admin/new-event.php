<?php $show_stat = ($data->id != -1); ?>
<div class="wrap">
    <?php
    if ($show_stat) {
        echo '<h1 class="wp-heading-inline">Edit Training: ' . esc_html($data->event_name) . '</h1>';
    } else {
        echo '<h1 class="wp-heading-inline">Create Training</h1>';
    }
    ?>
    <hr class="wp-header-end">
    <div id="er-main-content">
        <div class="er-left-column">
            <form id="create-training" name="create-training" method="post" action="<?php echo get_site_url() . '/wp-admin/admin.php?page=er_gen_set';?>">
                <?php
                if ($show_stat) {
                    wp_nonce_field('edit_training_nonce', 'training_nonce_field');
                } else {
                    wp_nonce_field('create_training_nonce', 'training_nonce_field');
                }
                ?>
                <input type="hidden" name="event-id" id="event-id" value="<?php echo esc_attr($data->id) ?>" />
                <div class="er-block">
                    <div class="er-block-title">
                        <h3>Training Details</h3>
                    </div>
                    <div class="er-block-content">
                        <table class="form-table er-table">
                            <tbody>
                            <tr>
                                <th scope="row"><label for="event-name">Training Name</label></th>
                                <td><input type="text" class="er_input" name="event-name" id="event-name" value="<?php echo esc_attr($data->event_name) ?>" required /></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="location">Location</label></th>
                                <td><input type="text" class="er_input" name="location" id="location" value="<?php echo esc_attr($data->location) ?>" required /></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="max">Max Number of Trainee</label></th>
                                <td><input type="number" class="er_input" name="max" id="max" value="<?php echo ($data->max == -999 ? 0 : esc_attr($data->max)) ?>" /><br />
                                    <p class="description">Enter 0 for no limit</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Cap Registration</th>
                                <td>
                                    <fieldset>
                                        <label for="max-limit"><input type="checkbox" name="max-limit" id="max-limit" value="1" <?php if ($data->limit_max == 1) {echo 'checked';} ?> /> Prevent further registrations when the training is full.</label>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Activate Training</th>
                                <td>
                                    <fieldset>
                                        <label for="activated"><input type="checkbox" name="activated" id="activated" value="1" <?php if ($data->activated == 1) {echo 'checked';} ?> /> Activated trainings are visible to schools.</label>
                                    </fieldset>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="er-block">
                    <div class="er-block-title">
                        <h3>Training Times</h3>
                    </div>
                    <div class="er-block-content">
                        <table class="form-table er-table">
                            <tbody>
                            <tr>
                                <th scope="row"><label for="open-date">Registration Open Time</label></th>
                                <td><input type="datetime-local" class="er_input" name="open-date" id="open-date" value="<?php echo esc_attr(date("Y-m-d\TH:i", strtotime($data->open_time))) ?>" required /></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="close-date">Registration Close Time</label></th>
                                <td><input type="datetime-local" class="er_input" name="close-date" id="close-date" value="<?php echo esc_attr(date("Y-m-d\TH:i", strtotime($data->close_time))) ?>" required /></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="start-date">Training Start Time</label></th>
                                <td><input type="datetime-local" class="er_input" name="start-date" id="start-date" value="<?php echo esc_attr(date("Y-m-d\TH:i", strtotime($data->start_time))) ?>" required /></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="end-date">Training End Time</label></th>
                                <td><input type="datetime-local" class="er_input" name="end-date" id="end-date" value="<?php echo esc_attr(date("Y-m-d\TH:i", strtotime($data->end_time))) ?>" required /></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="er-block">
                    <div class="er-block-title">
                        <h3>Comment</h3>
                    </div>
                    <div class="er-block-content">
                        <textarea name="comment" id="comment" class="er_input" style="height: 100pt"><?php echo esc_textarea($data->comment) ?></textarea>
                        <p class="description">This comment will be visible to schools on the registration page.</p>
                    </div>
                </div>

                <p class="submit">
                    <?php
                    if ($show_stat) {
                        echo '<input type="submit" class="button button-primary" name="submit_edit" id="submit_edit" value="Update Training">';
                    } else {
                        echo '<input type="submit" class="button button-primary" name="create_training" id="create_training" value="Create Training">';
                    }
                    ?>
                </p>
        </div>

        <div class="er-right-column">
            <div class="er-sidebar-block">
                <div class="er-block-title">
                    <h3>Stats</h3>
                </div>
                <div class="er-block-content">
                    <table class="er-table">
                        <tbody>
                        <tr>
                            <th>Status</th>
                            <td><?php echo $tools->availability($data) ?></td>
                        </tr>
                        <tr>
                            <th>Registered</th>
                            <td><?php echo $data->num_reg ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </form>

            <?php
            // Show danger zone
            if ($show_stat) {
                ?>
                <div class="er-sidebar-block">
                    <div class="er-block-title">
                        <h3>Danger Zone</h3>
                    </div>
                    <div class="er-block-content">
                        <p><b>Remove Training</b><br />
                            Warning! Doing this will remove the training event and registration records from the database. Type "remove training" in the box below and click remove to remove the training.
                        </p>
                        <form id="confirm-remove-event" name="confirm-remove-event" method="post" action="<?php echo get_site_url() . '/wp-admin/admin.php?page=er_gen_set';?>">
                            <?php wp_nonce_field('remove_training_nonce', 'remove_training_nonce_field'); ?>
                            <input type="hidden" name="removal-id" id="removal-id" value="<?php echo esc_attr($data->id) ?>" />
                            <br />
                            <input type="text" class="er_input" name="confirm" required pattern="remove training" autocomplete="off" />
                            <br />
                            <br />
                            <input type="submit" id="confirm_remove_button" name="confirm_remove" value="Remove Training">
                        </form>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <br />
</div>
