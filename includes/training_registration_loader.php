<?php
/**
 * Class training_registration_loader
 *
 * This class handles all the shortcode and action hooks. Add those actions in the main php
 *
 * @since 2020-5-19
 * @version 1.0
 *
 * @package Training-Registration
 */

class training_registration_loader {
    // Two arrays to store all actions and shortcodes
    protected $actions;
    protected $shortcodes;

    // Constructor
    public function __construct() {
        $this->actions = array();
        $this->shortcodes = array();
    }

    // Fill the actions array
    public function er_add_action($hook, $component, $callback) {
        $this->actions[] = array(
            'hook'      => $hook,
            'component' => $component,
            'callback'  => $callback
        );
    }

    // Fill the shortcodes array
    public function er_add_shortcode($tag, $component, $callback) {
        $this->shortcodes[] = array(
            'tag'      => $tag,
            'component' => $component,
            'callback'  => $callback
        );
    }

    // Register all the hooks to WordPress
    public function run() {
        foreach ($this->actions as $action) {
            add_action($action['hook'], array($action['component'], $action['callback']));
        }

        foreach ($this->shortcodes as $shortcode) {
            add_shortcode($shortcode['tag'], array($shortcode['component'], $shortcode['callback']));
        }
    }


}