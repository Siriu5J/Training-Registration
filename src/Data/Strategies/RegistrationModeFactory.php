<?php

namespace SOT\TrainingRegistration\Data\Strategies;

/**
 * Class RegistrationModeFactory
 *
 * Factory to create the appropriate registration mode strategy.
 *
 * @package SOT\TrainingRegistration\Data\Strategies
 */
class RegistrationModeFactory {
    public static function get_current_mode() {
        $my_mode = get_option('my_mode');
        
        if ($my_mode == 1) {
            return new SotamRegistrationMode();
        }
        
        return new DefaultRegistrationMode();
    }
}
