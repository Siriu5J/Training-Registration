<?php
/**
 * @var \SOT\TrainingRegistration\Admin\HomeTable $home_table
 * @var array $stats
 */
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Training Overview</h1>
    <a href="<?php echo admin_url('admin.php?page=er_new_event_set'); ?>" class="page-title-action">Add New</a>
    <hr class="wp-header-end">

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            
            <!-- Main Content -->
            <div id="post-body-content">
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">All Trainings</h2>
                    </div>
                    <div class="inside">
                        <form id="training-overview" method="GET">
                            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                            <?php 
                            $home_table->search_box('Search Trainings', 'search_id');
                            $home_table->display(); 
                            ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div id="postbox-container-1" class="postbox-container">
                
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">Quick Stats</h2>
                    </div>
                    <div class="inside">
                        <div class="stats-row">
                            <span class="dashicons dashicons-calendar"></span> 
                            Trainings: <strong><?php echo number_format($stats['total_events']); ?></strong>
                        </div>
                        <div class="stats-row">
                            <span class="dashicons dashicons-clock"></span> 
                            Upcoming: <strong><?php echo number_format($stats['upcoming_events']); ?></strong>
                        </div>
                        <div class="stats-row">
                            <span class="dashicons dashicons-id-alt"></span> 
                            Staff: <strong><?php echo number_format($stats['total_staff']); ?></strong>
                        </div>
                        <div class="stats-row">
                            <span class="dashicons dashicons-edit-large"></span> 
                            Registrations: <strong><?php echo number_format($stats['total_reg']); ?></strong>
                        </div>
                    </div>
                </div>

                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">Quick Actions</h2>
                    </div>
                    <div class="inside">
                        <p>
                            <a href="<?php echo admin_url('admin.php?page=er_new_event_set'); ?>" class="button button-primary" style="width: 100%; text-align: center; margin-bottom: 10px;">
                                <span class="dashicons dashicons-plus" style="vertical-align: middle; margin-top: -3px;"></span> Create Training
                            </a>
                        </p>
                        <p>
                            <a href="<?php echo admin_url('admin.php?page=er_view_reg_set'); ?>" class="button" style="width: 100%; text-align: center; margin-bottom: 10px;">
                                <span class="dashicons dashicons-groups" style="vertical-align: middle; margin-top: -3px;"></span> View Registrations
                            </a>
                        </p>
                        <p>
                            <a href="<?php echo admin_url('admin.php?page=er_settings'); ?>" class="button" style="width: 100%; text-align: center;">
                                <span class="dashicons dashicons-admin-settings" style="vertical-align: middle; margin-top: -3px;"></span> Settings
                            </a>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .stats-row {
        margin-bottom: 12px;
        font-size: 14px;
        display: flex;
        align-items: center;
    }
    .stats-row .dashicons {
        margin-right: 10px;
        color: #646970;
    }
    #post-body-content .postbox .wp-list-table {
        border: none;
    }
    #post-body-content .postbox {
        margin-bottom: 0;
    }
    @media only screen and (max-width: 782px) {
        #post-body.columns-2 #postbox-container-1 {
            margin-top: 20px;
        }
    }
</style>
