<h1>Settings</h1>
<form id="update-settings" name="update-settings" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
    <?php wp_nonce_field('save_settings_nonce', 'settings_nonce_field'); ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th>Show Available Seats</th>
            <td>
                <fieldset>
                    <label for="show-available"><input type="checkbox" name="show-available" value="1" <?php if ($show_available == 1) {echo 'checked';} ?>> Disabling this option will hide the number of seats remaining in a training to schools.</label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th>Enable SOTAM Forms</th>
            <td>
                <fieldset>
                    <label for="enable-my"><input type="checkbox" name="enable-my" value="1" <?php if ($my_enabled == 1) {echo 'checked';} ?>> Enable SOTAM requested form formats.</label>
                </fieldset>
            </td>
        </tr>
        </tbody>
    </table>
    <p class="submit">
        <input type="submit" class="button button-primary" name="save-settings" id="save-settings" value="Save Settings">
    </p>
</form>
<hr>
<form id="create" name="create" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
    <?php wp_nonce_field('create_page_nonce', 'create_page_nonce_field'); ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th>Create Necessary Pages</th>
            <td>
                <input type="submit" class="button button-primary" name="create-page" id="create-page" value="Create Pages">
            </td>
        </tr>
        </tbody>
    </table>
</form>
