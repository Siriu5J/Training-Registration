<?php

namespace SOT\TrainingRegistration\Admin;

defined('ABSPATH') || exit;

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
    public function tableSuccessDeletion() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Training has been deleted successfully!</p>
        </div>
        <?php
    }
    public function registrationDeletion() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Staff Registration has been deleted successfully!</p>
        </div>
        <?php
    }
    public function staffProfileDeletion() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Staff Profile has been deleted successfully!</p>
        </div>
        <?php
    }
    public function staffProfileUpdated() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Staff Profile has been updated successfully!</p>
        </div>
        <?php
    }
    public function staffProfileUpdateFailed() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>Failed to update Staff Profile. Please check the data and try again.</p>
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
