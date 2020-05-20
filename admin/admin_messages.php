<?php


class admin_messages
{
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
            <p>Settings Updated! (You might need to refresh this page to see the updated settings on this page)</p>
        </div>
        <?php
    }
}