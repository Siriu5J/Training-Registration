<div class="wrap">
    <h1 class="wp-heading-inline">Settings</h1>
    <hr class="wp-header-end">

    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php
        settings_fields('er_settings_group');
        do_settings_sections('er_gen_set');
        submit_button('Save Settings');
        ?>
    </form>

    <hr>

    <div class="card">
        <h2>System Actions</h2>
        <p>Use the button below to initialize the mandatory pages required for the plugin to function correctly (Staff Management, Event Registration, etc.).</p>
        <form id="create" name="create" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
            <?php wp_nonce_field('create_page_nonce', 'create_page_nonce_field'); ?>
            <input type="submit" class="button button-secondary" name="create-page" id="create-page" value="Create/Update Necessary Pages">
        </form>
    </div>
</div>
