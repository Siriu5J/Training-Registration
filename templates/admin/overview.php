<?php
/**
 * @var \SOT\TrainingRegistration\Admin\HomeTable $home_table
 * @var array $stats
 */
?>
<div class="wrap sot-tr-admin-wrap">
    <h1 class="wp-heading-inline">Training Registration Dashboard</h1>
    <hr class="wp-header-end">

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            
            <!-- Main Content -->
            <div id="post-body-content">
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">Training Events Management</h2>
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
                
                <!-- Dashboard Stats in Sidebar Postbox -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">Quick Stats</h2>
                    </div>
                    <div class="inside sot-tr-padded-inside">
                        <div class="sot-tr-sidebar-stats">
                            <div class="sot-tr-sidebar-stat-item">
                                <span class="dashicons dashicons-groups"></span>
                                <div class="sot-tr-stat-info">
                                    <span class="sot-tr-stat-label">Total Staff</span>
                                    <span class="sot-tr-stat-value"><?php echo number_format($stats['total_staff']); ?></span>
                                </div>
                            </div>
                            <div class="sot-tr-sidebar-stat-item">
                                <span class="dashicons dashicons-calendar-alt"></span>
                                <div class="sot-tr-stat-info">
                                    <span class="sot-tr-stat-label">Upcoming Trainings</span>
                                    <span class="sot-tr-stat-value"><?php echo number_format($stats['upcoming_events']); ?></span>
                                </div>
                            </div>
                            <div class="sot-tr-sidebar-stat-item">
                                <span class="dashicons dashicons-megaphone"></span>
                                <div class="sot-tr-stat-info">
                                    <span class="sot-tr-stat-label">Open for Reg.</span>
                                    <span class="sot-tr-stat-value"><?php echo number_format($stats['open_events']); ?></span>
                                </div>
                            </div>
                            <div class="sot-tr-sidebar-stat-item">
                                <span class="dashicons dashicons-edit-large"></span>
                                <div class="sot-tr-stat-info">
                                    <span class="sot-tr-stat-label">Total Registrations</span>
                                    <span class="sot-tr-stat-value"><?php echo number_format($stats['total_reg']); ?></span>
                                    <small class="sot-tr-recent-reg-stats">
                                        +<?php echo number_format($stats['recent_reg']); ?> in last 30 days
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Access -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">Quick Access</h2>
                    </div>
                    <div class="inside sot-tr-padded-inside">
                        <div class="sot-tr-quick-access-list">
                            <a href="<?php echo admin_url('admin.php?page=er_view_reg_set'); ?>" class="button sot-tr-qa-btn">
                                <span class="dashicons dashicons-list-view"></span> View All Registrations
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=er_new_event_set'); ?>" class="button sot-tr-qa-btn">
                                <span class="dashicons dashicons-plus"></span> Create New Training
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=er_settings'); ?>" class="button sot-tr-qa-btn">
                                <span class="dashicons dashicons-admin-settings"></span> Plugin Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
