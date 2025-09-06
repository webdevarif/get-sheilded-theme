<?php
namespace GetsheildedTheme\Inc\Traits;

/**
 * Singleton Trait
 * 
 * @package GetsheildedTheme\Traits
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

trait Singleton {
    
    /**
     * Instance of the class
     * 
     * @var object
     */
    private static $instance = null;
    
    /**
     * Get instance of the class
     * 
     * @return object
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup() {}
}
