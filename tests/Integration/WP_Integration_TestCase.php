<?php

use SOT\TrainingRegistration\Core\Activator;

class WP_Integration_TestCase extends WP_UnitTestCase {
    public function setUp(): void {
        parent::setUp();
        
        // Ensure plugin tables are created
        $activator = new Activator();
        $activator->activate_plugin();
    }
}
