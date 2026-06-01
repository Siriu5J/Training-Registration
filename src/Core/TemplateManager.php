<?php

namespace SOT\TrainingRegistration\Core;

/**
 * Class TemplateManager
 *
 * Handles registering and loading custom page templates from the plugin.
 */
class TemplateManager {
    /**
     * The template slug used to identify our custom template.
     */
    private $template_slug = 'app-layout.php';

    /**
     * Initialize the manager and register hooks.
     */
    public function init() {
        add_filter('theme_page_templates', [$this, 'register_template']);
        add_filter('template_include', [$this, 'load_template']);
    }

    /**
     * Register the custom template in the WordPress Page editor.
     */
    public function register_template($templates) {
        $templates[$this->template_slug] = __('Training App Template', 'training-registration');
        return $templates;
    }

    /**
     * Intercept template loading and serve our plugin file if the slug matches.
     */
    public function load_template($template) {
        if (is_page()) {
            $current_template = get_post_meta(get_the_ID(), '_wp_page_template', true);
            
            if ($current_template === $this->template_slug) {
                $plugin_template = ER_PLUGIN_DIR . '/templates/' . $this->template_slug;
                
                if (file_exists($plugin_template)) {
                    return $plugin_template;
                }
            }
        }
        
        return $template;
    }
}
