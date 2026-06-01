<?php

namespace SOT\TrainingRegistration\Traits;

/**
 * Trait TemplateRenderer
 *
 * Provides a simple method to render PHP templates with data.
 *
 * @package SOT\TrainingRegistration\Traits
 */
trait TemplateRenderer {
    /**
     * Renders a template file.
     * 
     * @param string $template The template name (path relative to templates directory without .php).
     * @param array $data The data to extract into the template scope.
     * @param bool $echo Whether to echo the output or return it as a string.
     * @return string|null The rendered content if $echo is false, otherwise null.
     */
    public function render($template, $data = array(), $echo = true) {
        $template_file = ER_PLUGIN_DIR . "/templates/{$template}.php";
        
        if (file_exists($template_file)) {
            extract($data);
            if (!$echo) {
                ob_start();
            }
            include $template_file;
            if (!$echo) {
                return ob_get_clean();
            }
        } else {
            $error_message = "Template {$template} not found.";
            if ($echo) {
                echo $error_message;
            } else {
                return $error_message;
            }
        }
        return null;
    }
}
