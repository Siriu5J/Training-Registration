<?php
/**
 * IDE Helper for WordPress Unit Tests
 * 
 * This file is not loaded by the plugin but is parsed by the IDE (Intelephense)
 * to resolve the inheritance chain of WordPress test classes.
 */

namespace PHPUnit\Framework {
    if (!class_exists('PHPUnit\Framework\TestCase')) {
        class TestCase {}
    }
}

namespace {
    /**
     * Stubs for WP Test Factories
     */
    class WP_UnitTest_Factory {
        /** @var WP_UnitTest_Factory_For_User */
        public $user;
        /** @var WP_UnitTest_Factory_For_Post */
        public $post;
    }

    class WP_UnitTest_Factory_For_User {
        public function create($args = array(), $generation_args = null) { return 0; }
        public function create_and_get($args = array(), $generation_args = null) { return new \stdClass(); }
        public function create_many($count, $args = array(), $generation_args = null) { return array(); }
    }

    class WP_UnitTest_Factory_For_Post {
        public function create($args = array(), $generation_args = null) { return 0; }
        public function create_and_get($args = array(), $generation_args = null) { return new \stdClass(); }
        public function create_many($count, $args = array(), $generation_args = null) { return array(); }
    }

    if (!class_exists('WP_UnitTestCase')) {
        /**
         * @property WP_UnitTest_Factory $factory
         * 
         * @method void setUp()
         * @method void tearDown()
         * @method void assertGreaterThanOrEqual($expected, $actual, string $message = '')
         * @method void assertCount($expectedCount, $haystack, string $message = '')
         * @method void assertNotEmpty($actual, string $message = '')
         * @method void assertEquals($expected, $actual, string $message = '')
         * @method void assertTrue($actual, string $message = '')
         * @method void assertFalse($actual, string $message = '')
         * @method void assertStringContainsString(string $needle, string $haystack, string $message = '')
         */
        abstract class WP_UnitTestCase extends \PHPUnit\Framework\TestCase {}
    }

    /**
     * Fallback for built-in PHP functions if stubs fail to load
     */
    if (!function_exists('rand')) {
        function rand(int $min, int $max): int { return 0; }
    }
}
