<?php


class create_page {
    public function run() {
        if (post_exists('Create Staff Profile', '', '', 'page') == 0) {
            $this->create_staff_profile();
        }
        if (post_exists('Manage My Staff', '', '', 'page') == 0) {
            $this->create_manage_my_staff();
        }
        if (post_exists('Register for Training', '', '', 'page') == 0) {
            $this->create_register_to_training();
        }
        if (post_exists('Training Registration', '', '', 'page') == 0) {
            $this->create_home();
        }
    }

    private function create_staff_profile() {
        $content = '<!-- wp:shortcode -->[staff_form]<!-- /wp:shortcode -->';
        $staff_profile_content = array(
            'post_title'    =>  "Create Staff Profile",
            'post_type'     =>  'page',
            'page_template' =>  'templates/template-full-width.php',
            'post_content'  =>  $content,
            'post_status'   =>  'publish'
        );

        wp_insert_post($staff_profile_content);
    }

    private function create_manage_my_staff() {
        $content = '<!-- wp:shortcode -->[view_staff]<!-- /wp:shortcode -->';
        $manage_staff_content = array(
            'post_title'    =>  "Manage My Staff",
            'post_type'     =>  'page',
            'page_template' =>  'templates/template-full-width.php',
            'post_content'  =>  $content,
            'post_status'   =>  'publish'
        );

        wp_insert_post($manage_staff_content);
    }

    private function create_register_to_training() {
        $content = '<!-- wp:shortcode -->[register_training]<!-- /wp:shortcode -->';
        $create_register_content = array(
            'post_title'    =>  "Register for Training",
            'post_type'     =>  'page',
            'page_template' =>  'templates/template-full-width.php',
            'post_content'  =>  $content,
            'post_status'   =>  'publish'
        );

        wp_insert_post($create_register_content);
    }

    private function create_home() {
        $site_home = (string)get_option('home');
        $content = '
        <!-- wp:group -->
        <div class="wp-block-group"><div class="wp-block-group__inner-container">
            <!-- wp:buttons {"align":"center","className":".container { width: 80%; }"} -->
            <div class="wp-block-buttons aligncenter .container { width: 80%; }">
                <!-- wp:button {"className":"aligncenter"} -->
                <div class="wp-block-button aligncenter"><a class="wp-block-button__link" href="'.$site_home.'/create-staff-profile/">Create Staff Profile</a></div>
                <!-- /wp:button -->
        
                <!-- wp:button {"className":"aligncenter"} -->
                <div class="wp-block-button aligncenter"><a class="wp-block-button__link" href="'.$site_home.'/register-for-training/">Register for Training</a></div>
                <!-- /wp:button -->
        
                <!-- wp:button {"className":"aligncenter"} -->
                <div class="wp-block-button aligncenter"><a class="wp-block-button__link" href="'.$site_home.'/manage-my-staff/">Manage my Staff</a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->
        </div></div>
        <!-- /wp:group -->
        ';
        $create_home = array(
            'post_title'    =>  "Training Registration",
            'post_type'     =>  'page',
            'page_template' =>  'templates/template-full-width.php',
            'post_content'  =>  $content,
            'post_status'   =>  'publish'
        );

        $id = wp_insert_post($create_home);

        // Set as home
        update_option('show_on_front', 'page');
        update_option('page_on_front', $id);
    }
}