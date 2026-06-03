<?php

namespace SOT\TrainingRegistration\Core;

defined('ABSPATH') || exit;

/**
 * Class PageCreator
 *
 * Handles the programmatic creation of WordPress pages required by the plugin.
 *
 * @package SOT\TrainingRegistration\Core
 */
class PageCreator {
    /**
     * Run the page creation/update process.
     */
    public function run() {
        $create_staff_id = $this->upsert_page('Create Staff Profile', '[staff_form]');
        $manage_staff_id = $this->upsert_page('Manage My Staff', '[view_staff]');
        $manage_reg_id = $this->upsert_page('Manage Registrations', '[manage_registrations]');
        $register_training_id = $this->upsert_page('Register for Training', '[register_training]');
        $home_id = $this->upsert_page('Training Registration', '[training_dashboard]');
        
        // Store Page IDs for dynamic link generation
        update_option('er_create_staff_page_id', $create_staff_id);
        update_option('er_manage_staff_page_id', $manage_staff_id);
        update_option('er_manage_registrations_page_id', $manage_reg_id);
        update_option('er_register_training_page_id', $register_training_id);
        update_option('er_dashboard_page_id', $home_id);

        if ($home_id) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $home_id);
        }

        // Flush rewrite rules to ensure the new pages are recognized
        flush_rewrite_rules();
    }

    /**
     * Creates a page if it doesn't exist, or updates the template for an existing one.
     * 
     * @param string $title
     * @param string $shortcode
     * @return int|false Page ID on success, false on failure.
     */
    private function upsert_page($title, $shortcode) {
        if (!function_exists('post_exists')) {
            require_once(ABSPATH . 'wp-admin/includes/post.php');
        }
        $page_id = \post_exists($title, '', '', 'page');
        $content = "<!-- wp:shortcode -->{$shortcode}<!-- /wp:shortcode -->";

        if ($page_id == 0) {
            $page_data = array(
                'post_title'    => $title,
                'post_type'     => 'page',
                'post_content'  => $content,
                'post_status'   => 'publish'
            );
            $page_id = wp_insert_post($page_data);
        } else {
            // Ensure content matches just in case
            $page_data = array(
                'ID'           => $page_id,
                'post_content' => $content,
            );
            wp_update_post($page_data);
        }

        if ($page_id) {
            update_post_meta($page_id, '_wp_page_template', 'app-layout.php');
        }

        return $page_id;
    }
}
