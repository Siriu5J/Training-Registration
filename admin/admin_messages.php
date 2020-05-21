<?php


/**
 * Class admin_messages
 *
 * This class contains all the callback functions for the admin messages. training_registration_acp uses this class exclusively to send admin messages.
 *
 * @since 2020-5-19
 * @version 1.0
 *
 * @package training-registration
 */

class admin_messages {
    // Admin Message Boxes
    public function createEventNotAllowed() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>You cannot limit training registration number with an unlimited registration!</p>
        </div>
        <?php
    }
    public function tableSuccessCreation() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Training has been created successfully! Click <a href="../wp-admin/admin.php?page=er_event_view_set">here</a> to see the training you created.</p>
        </div>
        <?php
    }
    public function tableSuccessUpdate() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Training has been updated successfully!</p>
        </div>
        <?php
    }
    public function tableFailedCreation() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>Failed to create training!</p>
        </div>
        <?php
    }

    public function tableFailedUpdate() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>Failed to update training!</p>
        </div>
        <?php
    }

    public function tableAlreadyExist() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>Event already exist!</p>
        </div>
        <?php
    }

    public function settingsUpdated() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Settings Updated!</p>
        </div>
        <?php
    }

    public function invalidTheme() {
        $admin_site_url = (string)get_option('home') . '/wp-admin/admin.php?page=er_settings';
        if (get_transient('invalid_theme_transient')) {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><b>New pages are not create!</b> Please make sure you are using the built-in Twenty Twenty theme!<br>
                    Go to Training Registration <a href="<?php echo $admin_site_url ?>">Settings</a> to create pages after you have changed the theme.</p>
            </div>
            <?php
        }
    }

}