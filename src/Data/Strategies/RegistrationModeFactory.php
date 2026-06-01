<?php

namespace SOT\TrainingRegistration\Data\Strategies;

defined('ABSPATH') || exit;

/**
 * Class RegistrationModeFactory
 *
 * Factory to create the appropriate registration mode strategy.
 *
 * @package SOT\TrainingRegistration\Data\Strategies
 */
class RegistrationModeFactory {
    public static function get_current_mode() {
        return match ((int)get_option('my_mode')) {
            1 => new SotamRegistrationMode(),
            default => new DefaultRegistrationMode(),
        };
    }
}
