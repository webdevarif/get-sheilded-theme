<?php
namespace GetsheildedTheme\Inc\Classes;

use GetsheildedTheme\Inc\Traits\Singleton;

/**
 * Frontend Scripts Feature
 * 
 * @package GetsheildedTheme\Classes
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Frontend_Scripts {
    use Singleton;
    
    /**
     * Constructor
     */
    protected function __construct() {
        $this->setup_hooks();
    }
    
    /**
     * Setup hooks
     */
    private function setup_hooks() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
    }
    
    /**
     * Initialize feature
     */
    public function init() {
        // Frontend scripts specific initialization
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Enqueue main CSS
        wp_enqueue_style(
            'gst-frontend-main',
            GST_THEME_URL . '/dist/frontend/main.css',
            [],
            GST_THEME_VERSION
        );
        
        // Enqueue main JS
        wp_enqueue_script(
            'gst-frontend-main',
            GST_THEME_URL . '/dist/frontend/main.js',
            ['jquery'],
            GST_THEME_VERSION,
            true
        );
        
        // Enqueue components JS
        wp_enqueue_script(
            'gst-frontend-components',
            GST_THEME_URL . '/dist/frontend/components.js',
            ['jquery'],
            GST_THEME_VERSION,
            true
        );
    }
}
