<?php

namespace SOT\TrainingRegistration\Admin;

/**
 * Class AdminMessages
 *
 * This class contains all the callback functions for the admin messages.
 *
 * @package SOT\TrainingRegistration\Admin
 */
class AdminMessages {
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
            <p>Training has been created successfully!</p>
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

    public function tableSuccessDeletion() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Training and all associated registrations have been removed.</p>
        </div>
        <?php
    }

    public function tableAlreadyExist() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>Event already exists!</p>
        </div>
        <?php
    }

    public function invalidTimeOrder() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>Invalid time range! Ensure end times are later than start times.</p>
        </div>
        <?php
    }

    public function invalidMaxTrainee() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>Max number of trainees cannot be negative!</p>
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

    public function pagesCreated() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Necessary plugin pages have been created successfully!</p>
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
