<?php

use PHPUnit\Framework\TestCase;

class WP_Integration_TestCase extends WP_UnitTestCase {
    public function setUp(): void {
        parent::setUp();
        
        // Ensure plugin tables are created
        require_once ER_PLUGIN_DIR . '/includes/activation.php';
        $activator = new activation();
        $activator->activate_plugin();
    }
}
