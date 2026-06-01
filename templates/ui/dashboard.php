<?php
/**
 * @var array $stats
 */
$site_home = (string)get_option('home');
?>
<div class="sot-tr-container">
    <h2>Welcome to Your Training Dashboard</h2>
    <p>Manage your staff profiles and training registrations from one central location.</p>

    <!-- Dashboard Cards -->
    <div class="sot-tr-dashboard-grid">
        <div class="sot-tr-dashboard-card">
            <div class="sot-tr-card-icon color-blue">
                <span class="dashicons dashicons-groups"></span>
            </div>
            <div class="sot-tr-card-content">
                <span class="sot-tr-card-label">My Staff</span>
                <span class="sot-tr-card-value"><?php echo number_format($stats['total_staff']); ?></span>
            </div>
        </div>

        <div class="sot-tr-dashboard-card">
            <div class="sot-tr-card-icon color-green">
                <span class="dashicons dashicons-calendar-alt"></span>
            </div>
            <div class="sot-tr-card-content">
                <span class="sot-tr-card-label">Upcoming Trainings</span>
                <span class="sot-tr-card-value"><?php echo number_format($stats['upcoming_trainings']); ?></span>
            </div>
        </div>

        <div class="sot-tr-dashboard-card">
            <div class="sot-tr-card-icon color-purple">
                <span class="dashicons dashicons-edit-large"></span>
            </div>
            <div class="sot-tr-card-content">
                <span class="sot-tr-card-label">My Registrations</span>
                <span class="sot-tr-card-value"><?php echo number_format($stats['my_registrations']); ?></span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="sot-tr-actions-grid">
        <a href="<?php echo $site_home; ?>/create-staff-profile/" class="button button-primary sot-tr-action-btn">
            <span class="dashicons dashicons-plus"></span> Create Staff Profile
        </a>
        <a href="<?php echo $site_home; ?>/register-for-training/" class="button button-primary sot-tr-action-btn">
            <span class="dashicons dashicons-edit"></span> Register for Training
        </a>
        <a href="<?php echo $site_home; ?>/manage-my-staff/" class="button button-primary sot-tr-action-btn">
            <span class="dashicons dashicons-admin-users"></span> Manage My Staff
        </a>
    </div>

    <!-- Agenda View -->
    <div class="sot-tr-card sot-tr-agenda-card">
        <h3>Upcoming Agenda (Next 90 Days)</h3>
        <hr>
        <?php if (empty($stats['agenda'])): ?>
            <p class="sot-tr-empty">You have no staff registered for trainings in the next 90 days.</p>
        <?php else: ?>
            <ul class="sot-tr-agenda-list">
                <?php foreach ($stats['agenda'] as $item): ?>
                    <li class="sot-tr-agenda-item">
                        <div class="sot-tr-agenda-icon-wrap">
                            <span class="dashicons dashicons-clock"></span>
                        </div>
                        <div class="sot-tr-agenda-text">
                            <strong><?php echo number_format($item->staff_count); ?></strong> staff<?php echo $item->staff_count > 1 ? 's' : ''; ?> 
                            <?php echo $item->staff_count == 1 ? 'is' : 'are'; ?> going to <strong><?php echo esc_html($item->event_name); ?></strong> 
                            at <strong><?php echo esc_html(wp_date('F j, Y', strtotime($item->start_time))); ?></strong> 
                            in <em><?php echo esc_html($item->location); ?></em>.
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
