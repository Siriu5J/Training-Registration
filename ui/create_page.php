<?php


class create_page {
    public function create_staff_profile() {
        $content = '<!-- wp:shortcode -->[staff_form]<!-- /wp:shortcode -->';
        $staff_profile_content = array(
            'post_title'    =>  "Create Staff Profile",
            'post_type'     =>  'page',
            'page_template' =>  'Default Template',
            'post_content'  =>  $content,
            'post_status'   =>  'publish'
        );

        wp_insert_post($staff_profile_content);
    }

    public function create_manage_my_staff() {
        $content = '<!-- wp:shortcode -->[view_staff]<!-- /wp:shortcode -->';
        $manage_staff_content = array(
            'post_title'    =>  "Manage My Staff",
            'post_type'     =>  'page',
            'page_template' =>  'Default Template',
            'post_content'  =>  $content,
            'post_status'   =>  'publish'
        );

        wp_insert_post($manage_staff_content);
    }

    public function create_register_to_training() {
        $content = '<!-- wp:shortcode -->[register_training]<!-- /wp:shortcode -->';
        $create_register_content = array(
            'post_title'    =>  "Register to Training",
            'post_type'     =>  'page',
            'page_template' =>  'Default Template',
            'post_content'  =>  $content,
            'post_status'   =>  'publish'
        );

        wp_insert_post($create_register_content);
    }

    public function create_home() {
        $site_home = (string)get_option('home');
        $content = '
        <!-- wp:button {"className":"aligncenter"} -->
        <div class="wp-block-button aligncenter"><a class="wp-block-button__link" href="'.$site_home.'/index.php/create-staff-profile/">Create Staff Profile</a></div>
        <!-- /wp:button -->

        <!-- wp:button {"className":"aligncenter"} -->
        <div class="wp-block-button aligncenter"><a class="wp-block-button__link" href="'.$site_home.'/index.php/register-to-training/">Register to Training</a></div>
        <!-- /wp:button -->

        <!-- wp:button {"className":"aligncenter"} -->
        <div class="wp-block-button aligncenter"><a class="wp-block-button__link" href="'.$site_home.'/index.php/manage-my-staff/">Manage my Staff</a></div>
        <!-- /wp:button -->
        ';
        $create_home = array(
            'post_title'    =>  "Training Registration",
            'post_type'     =>  'page',
            'page_template' =>  'Default Template',
            'post_content'  =>  $content,
            'post_status'   =>  'publish'
        );

        $id = wp_insert_post($create_home);

        // Set as home
        update_option('show_on_front', 'page');
        update_option('page_on_front', $id);
    }
}