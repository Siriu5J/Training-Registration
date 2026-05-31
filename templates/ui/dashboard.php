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
    <div class="sot-tr-actions-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2.5rem;">
        <a href="<?php echo $site_home; ?>/create-staff-profile/" class="button button-primary" style="text-align: center; justify-content: center; display: flex; align-items: center; gap: 8px;">
            <span class="dashicons dashicons-plus"></span> Create Staff Profile
        </a>
        <a href="<?php echo $site_home; ?>/register-for-training/" class="button button-primary" style="text-align: center; justify-content: center; display: flex; align-items: center; gap: 8px;">
            <span class="dashicons dashicons-edit"></span> Register for Training
        </a>
        <a href="<?php echo $site_home; ?>/manage-my-staff/" class="button button-primary" style="text-align: center; justify-content: center; display: flex; align-items: center; gap: 8px;">
            <span class="dashicons dashicons-admin-users"></span> Manage My Staff
        </a>
    </div>

    <!-- Agenda View -->
    <div class="sot-tr-card" style="margin-top: 2rem;">
        <h3>Upcoming Agenda (Next 90 Days)</h3>
        <hr>
        <?php if (empty($stats['agenda'])): ?>
            <p class="sot-tr-empty">You have no staff registered for trainings in the next 90 days.</p>
        <?php else: ?>
            <ul class="sot-tr-agenda-list" style="list-style: none; padding: 0;">
                <?php foreach ($stats['agenda'] as $item): ?>
                    <li style="padding: 1rem; border-bottom: 1px solid #edf2f7; display: flex; align-items: center; gap: 1rem;">
                        <div class="sot-tr-agenda-icon" style="background: #f0f4f8; padding: 0.75rem; border-radius: 8px; color: #2271b1;">
                            <span class="dashicons dashicons-clock"></span>
                        </div>
                        <div class="sot-tr-agenda-text" style="font-size: 1.1rem; line-height: 1.4;">
                            <strong><?php echo number_format($item->staff_count); ?></strong> staff<?php echo $item->staff_count > 1 ? 's' : ''; ?> 
                            <?php echo $item->staff_count == 1 ? 'is' : 'are'; ?> going to <strong><?php echo esc_html($item->event_name); ?></strong> 
                            at <strong><?php echo date('F j, Y', strtotime($item->start_time)); ?></strong> 
                            in <em><?php echo esc_html($item->location); ?></em>.
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<style>
/* Reusing admin card styles for UI frontend consistency */
.sot-tr-dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin: 20px 0 30px 0;
}

.sot-tr-dashboard-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    padding: 20px;
    display: flex;
    align-items: center;
    border-radius: 8px;
}

.sot-tr-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.sot-tr-card-icon .dashicons {
    font-size: 24px;
    width: 24px;
    height: 24px;
    color: #fff;
}

.color-blue { background-color: #2271b1; }
.color-green { background-color: #38a169; }
.color-purple { background-color: #805ad5; }

.sot-tr-card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.sot-tr-card-label {
    font-size: 0.8rem;
    color: #718096;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.sot-tr-card-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1a202c;
    line-height: 1.2;
}
</style>
