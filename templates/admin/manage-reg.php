<?php
/**
 * @var array $trainings
 * @var int $my_mode
 * @var \SOT\TrainingRegistration\Core\Tools $tools
 */
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Manage Registrations</h1>
    <hr class="wp-header-end">

    <?php if (count($trainings) > 0) : ?>
        <p class="description">All registrations for <strong>upcoming and activated trainings</strong> are managed here. Click a training name to jump to its registration list.</p>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-1">
                <div id="post-body-content">
                    <div class="postbox">
                        <h2 class="hndle"><span>Upcoming Trainings Overview</span></h2>
                        <div class="inside">
                            <table class="wp-list-table widefat fixed striped table-view-list">
                                <thead>
                                    <tr>
                                        <th scope="col" class="manage-column column-primary">Training Name</th>
                                        <th scope="col" class="manage-column">Location</th>
                                        <th scope="col" class="manage-column">Dates</th>
                                        <th scope="col" class="manage-column">Available Slots</th>
                                        <th scope="col" class="manage-column">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($trainings as $training) : ?>
                                        <tr>
                                            <td class="column-primary has-row-actions">
                                                <strong><a href="#event-<?php echo esc_attr($training->id); ?>"><?php echo esc_html($training->event_name) ?></a></strong>
                                                <div class="row-actions">
                                                    <span class="view"><a href="<?php echo admin_url('admin.php?page=er_view_reg_set&event-id=' . $training->id); ?>">View Detailed List</a></span>
                                                </div>
                                            </td>
                                            <td><?php echo esc_html($training->location) ?></td>
                                            <td>
                                                <?php echo esc_html(date("M j, Y", strtotime($training->start_time))) ?> - 
                                                <?php echo esc_html(date("M j, Y", strtotime($training->end_time))) ?>
                                            </td>
                                            <td><?php echo esc_html($tools->spotsOpen($training->id)) ?></td>
                                            <td><?php echo esc_html($tools->availability($training)) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <?php foreach ($trainings as $training) : 
                        if ($my_mode == 1) {
                            $reg_table = new \SOT\TrainingRegistration\Admin\StaffRegistrationTableMY($tools);
                        } else {
                            $reg_table = new \SOT\TrainingRegistration\Admin\StaffRegistrationTableCN($tools);
                        }
                        $reg_table->set_event_id($training->id);
                        $reg_table->prepare_items();
                    ?>
                        <div class="postbox" id="event-<?php echo esc_attr($training->id); ?>">
                            <h2 class="hndle">
                                <span>Registrations for <?php echo esc_html($training->event_name); ?></span>
                                <small style="font-weight: normal; margin-left: 10px;">at <?php echo esc_html($training->location); ?></small>
                            </h2>
                            <div class="inside">
                                <form id="staff-reg-<?php echo $training->id ?>" method="GET" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                                    <?php wp_nonce_field('staff_reg_nonce', 'reg_nonce_field'); ?>
                                    <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>" />
                                    <?php $reg_table->display(); ?>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    <?php else : ?>
        <div class="notice notice-info inline">
            <p><strong>No Activated and Upcoming Trainings Found!</strong></p>
            <p>This page only displays registrations for trainings that are currently <strong>activated</strong> and have a <strong>future start date</strong>.</p>
            <p><a href="<?php echo admin_url('admin.php?page=er_new_event_set'); ?>" class="button">Create New Training</a></p>
        </div>
    <?php endif; ?>
</div>
