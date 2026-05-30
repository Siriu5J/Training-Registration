<?php
/**
 * @var \SOT\TrainingRegistration\Admin\HomeTable $home_table
 * @var array $stats
 */
?>
<div class="wrap sot-tr-admin-wrap">
    <h1 class="wp-heading-inline">Training Registration Dashboard</h1>
    <a href="<?php echo admin_url('admin.php?page=er_new_event_set'); ?>" class="page-title-action">Create New Training</a>
    <hr class="wp-header-end">

    <!-- Dashboard Cards -->
    <div class="sot-tr-dashboard-grid">
        <div class="sot-tr-dashboard-card">
            <div class="sot-tr-card-icon color-blue">
                <span class="dashicons dashicons-groups"></span>
            </div>
            <div class="sot-tr-card-content">
                <span class="sot-tr-card-label">Total Staff</span>
                <span class="sot-tr-card-value"><?php echo number_format($stats['total_staff']); ?></span>
            </div>
        </div>

        <div class="sot-tr-dashboard-card">
            <div class="sot-tr-card-icon color-green">
                <span class="dashicons dashicons-calendar-alt"></span>
            </div>
            <div class="sot-tr-card-content">
                <span class="sot-tr-card-label">Upcoming Trainings</span>
                <span class="sot-tr-card-value"><?php echo number_format($stats['upcoming_events']); ?></span>
            </div>
        </div>

        <div class="sot-tr-dashboard-card">
            <div class="sot-tr-card-icon color-orange">
                <span class="dashicons dashicons-megaphone"></span>
            </div>
            <div class="sot-tr-card-content">
                <span class="sot-tr-card-label">Open for Registration</span>
                <span class="sot-tr-card-value"><?php echo number_format($stats['open_events']); ?></span>
            </div>
        </div>

        <div class="sot-tr-dashboard-card">
            <div class="sot-tr-card-icon color-purple">
                <span class="dashicons dashicons-edit-large"></span>
            </div>
            <div class="sot-tr-card-content">
                <span class="sot-tr-card-label">Total Registrations</span>
                <span class="sot-tr-card-value"><?php echo number_format($stats['total_reg']); ?></span>
            </div>
            <div class="sot-tr-card-footer">
                <span class="dashicons dashicons-chart-line"></span>
                <?php echo number_format($stats['recent_reg']); ?> in last 30 days
            </div>
        </div>
    </div>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">Training Events Management</h2>
                        <div class="handle-actions">
                            <a href="<?php echo admin_url('admin.php?page=er_view_reg_set'); ?>" class="button">
                                <span class="dashicons dashicons-list-view"></span> View All Registrations
                            </a>
                        </div>
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
        </div>
    </div>
</div>
