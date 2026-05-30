<?php
/**
 * Trait TemplateRenderer
 *
 * Provides a simple method to render PHP templates with data.
 *
 * @package training-registration
 */

trait TemplateRenderer {
    public function render($template, $data = array()) {
        $template_file = ER_PLUGIN_DIR . "/templates/{$template}.php";
        
        if (file_exists($template_file)) {
            extract($data);
            include $template_file;
        } else {
            echo "Template {$template} not found.";
        }
    }
}
