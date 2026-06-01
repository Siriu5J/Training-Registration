<?php
/**
 * @var \SOT\TrainingRegistration\Core\Tools $tools
 * @var int $id
 * @var \WP_List_Table $reg_table
 */
$event = (new \SOT\TrainingRegistration\Data\Repositories\EventRepository())->get_by_id($id);
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Registrations: <?php echo esc_html($event->event_name); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=er_view_reg_set'); ?>" class="page-title-action">Back to Overview</a>
    <hr class="wp-header-end">

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            
            <!-- Main Content -->
            <div id="post-body-content">
                <div class="postbox">
                    <h2 class="hndle"><span>Registration List</span></h2>
                    <div class="inside sot-tr-padded-inside">
                        <form id="staff-reg" method="GET" action="<?php echo esc_url(add_query_arg(array())); ?>">
                            <?php wp_nonce_field('staff_reg_nonce', 'reg_nonce_field'); ?>
                            <input type="hidden" name="event-id" value="<?php echo esc_attr($id) ?>" />
                            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>" />
                            
                            <?php $reg_table->search_box('Search Trainees', 'search_id'); ?>
                            <?php $reg_table->display(); ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div id="postbox-container-1" class="postbox-container">
                <div class="postbox">
                    <h2 class="hndle"><span>Event Details</span></h2>
                    <div class="inside sot-tr-padded-inside">
                        <div class="misc-pub-section">
                            <strong>Location:</strong> <?php echo esc_html($event->location); ?>
                        </div>
                        <div class="misc-pub-section">
                            <strong>Date:</strong> <?php echo esc_html(date("M j, Y", strtotime($event->start_time))); ?>
                        </div>
                        <div class="misc-pub-section">
                            <strong>Status:</strong> <?php echo $tools->availability($event); ?>
                        </div>
                        <div class="misc-pub-section">
                            <strong>Registrations:</strong> <?php echo esc_html($event->num_reg); ?> / <?php echo ($event->max == -999) ? 'Unlimited' : esc_html($event->max); ?>
                        </div>
                    </div>
                    <div id="major-publishing-actions">
                        <div id="publishing-action">
                            <a href="<?php echo admin_url('admin.php?page=er_new_event_set&view-event=true&event-id=' . $id); ?>" class="button">Edit Training</a>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>

                <div class="postbox">
                    <h2 class="hndle"><span>Actions</span></h2>
                    <div class="inside sot-tr-padded-inside">
                        <?php 
                        $mode = get_option('my_mode');
                        $nonce = wp_create_nonce('excel_export_nonce');
                        $export_url = admin_url('admin.php?page=er_gen_set&id=' . $id . '&print-excel=true&mode='. $mode . '&nonce=' . $nonce);
                        ?>
                        <a href="<?php echo esc_url($export_url); ?>" class="button button-secondary">Download Excel Export</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
