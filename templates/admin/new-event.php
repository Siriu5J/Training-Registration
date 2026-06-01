<?php
/**
 * @var object $data
 * @var \SOT\TrainingRegistration\Core\Tools $tools
 */
?>
<?php $show_stat = ($data->id != -1); ?>
<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo $show_stat ? 'Edit Training: ' . esc_html($data->event_name) : 'Create Training'; ?>
    </h1>
    <hr class="wp-header-end">

    <form id="create-training" name="create-training" method="post" action="<?php echo admin_url('admin.php?page=er_new_event_set' . ($show_stat ? '&view-event=true&event-id=' . $data->id : '')); ?>">
        <?php
        if ($show_stat) {
            wp_nonce_field('edit_training_nonce', 'training_nonce_field');
        } else {
            wp_nonce_field('create_training_nonce', 'training_nonce_field');
        }
        ?>
        <input type="hidden" name="event-id" id="event-id" value="<?php echo esc_attr($data->id) ?>" />

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                
                <!-- Left Column -->
                <div id="post-body-content">
                    
                    <!-- Training Details -->
                    <div class="postbox">
                        <h2 class="hndle"><span>Training Details</span></h2>
                        <div class="inside">
                            <table class="form-table" role="presentation">
                                <tbody>
                                    <tr>
                                        <th scope="row"><label for="event-name">Training Name</label></th>
                                        <td><input type="text" class="regular-text" name="event-name" id="event-name" value="<?php echo esc_attr($data->event_name) ?>" required /></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="location">Location</label></th>
                                        <td><input type="text" class="regular-text" name="location" id="location" value="<?php echo esc_attr($data->location) ?>" required /></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="max">Max Number of Trainee</label></th>
                                        <td>
                                            <input type="number" class="small-text" name="max" id="max" min="0" value="<?php echo ($data->max == -999 ? 0 : esc_attr($data->max)) ?>" />
                                            <p class="description">Enter 0 for no limit</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Registration Controls</th>
                                        <td>
                                            <fieldset>
                                                <label for="max-limit">
                                                    <input type="checkbox" name="max-limit" id="max-limit" value="1" <?php checked($data->limit_max, 1); ?> /> 
                                                    Prevent further registrations when the training is full.
                                                </label>
                                                <br>
                                                <label for="activated">
                                                    <input type="checkbox" name="activated" id="activated" value="1" <?php checked($data->activated, 1); ?> /> 
                                                    Activated trainings are visible to schools.
                                                </label>
                                            </fieldset>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Training Times -->
                    <div class="postbox">
                        <h2 class="hndle"><span>Training Times</span></h2>
                        <div class="inside">
                            <table class="form-table" role="presentation">
                                <tbody>
                                    <tr>
                                        <th scope="row"><label for="open-date">Registration Open Time</label></th>
                                        <td><input type="datetime-local" name="open-date" id="open-date" value="<?php echo esc_attr(date("Y-m-d\TH:i", strtotime($data->open_time))) ?>" required /></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="close-date">Registration Close Time</label></th>
                                        <td><input type="datetime-local" name="close-date" id="close-date" value="<?php echo esc_attr(date("Y-m-d\TH:i", strtotime($data->close_time))) ?>" required /></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="start-date">Training Start Time</label></th>
                                        <td><input type="datetime-local" name="start-date" id="start-date" value="<?php echo esc_attr(date("Y-m-d\TH:i", strtotime($data->start_time))) ?>" required /></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="end-date">Training End Time</label></th>
                                        <td><input type="datetime-local" name="end-date" id="end-date" value="<?php echo esc_attr(date("Y-m-d\TH:i", strtotime($data->end_time))) ?>" required /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Comment -->
                    <div class="postbox">
                        <h2 class="hndle"><span>Information / Comment</span></h2>
                        <div class="inside">
                            <textarea name="comment" id="comment" class="large-text" rows="5"><?php echo esc_textarea($data->comment) ?></textarea>
                            <p class="description">This comment will be visible to schools on the registration page.</p>
                        </div>
                    </div>

                    <?php submit_button($show_stat ? 'Update Training' : 'Create Training', 'primary large', $show_stat ? 'submit_edit' : 'create_training'); ?>
                </div>

                <!-- Right Column -->
                <div id="postbox-container-1" class="postbox-container">
                    
                    <!-- Stats Box -->
                    <div class="postbox">
                        <h2 class="hndle"><span>Stats</span></h2>
                        <div class="inside">
                            <div class="misc-pub-section">
                                <strong>Status:</strong> <?php echo $tools->availability($data) ?>
                            </div>
                            <div class="misc-pub-section">
                                <strong>Registered:</strong> <?php echo esc_html($data->num_reg); ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($show_stat) : ?>
                        <!-- Danger Zone -->
                        <div class="postbox sot-tr-danger-postbox">
                            <h2 class="hndle sot-tr-danger-hndle"><span>Danger Zone</span></h2>
                            <div class="inside">
                                <p class="description">
                                    <strong>Remove Training</strong><br>
                                    This will remove the training and all registration records.
                                </p>
                                <p>Type <code>remove training</code> to confirm:</p>
                                <input type="text" id="removal_confirm" class="widefat" placeholder="remove training" autocomplete="off" />
                                <br><br>
                                <button type="button" id="delete_button" class="button button-link-delete sot-tr-danger-button" onclick="if(document.getElementById('removal_confirm').value === 'remove training') { document.getElementById('remove_event_form').submit(); } else { alert('Please type \'remove training\' to confirm.'); }">Remove Training</button>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </form>

    <?php if ($show_stat) : ?>
        <!-- Hidden removal form -->
        <form id="remove_event_form" method="post" action="<?php echo admin_url('admin.php?page=er_gen_set'); ?>" class="sot-tr-hidden-form">
            <?php wp_nonce_field('remove_training_nonce', 'remove_training_nonce_field'); ?>
            <input type="hidden" name="removal-id" value="<?php echo esc_attr($data->id) ?>" />
            <input type="hidden" name="confirm" value="remove training" />
            <input type="hidden" name="confirm_remove" value="1" />
        </form>
    <?php endif; ?>
</div>
