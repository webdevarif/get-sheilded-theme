<?php
namespace GetsheildedTheme\Inc\Classes;

use GetsheildedTheme\Inc\Traits\Singleton;

/**
 * Gutenberg Blocks Feature
 * 
 * @package GetsheildedTheme\Classes
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Gutenberg_Blocks {
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
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_gutenberg_assets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_gutenberg_frontend_assets']);
    }
    
    /**
     * Initialize feature
     */
    public function init() {
        // Gutenberg blocks specific initialization
    }
    
    /**
     * Enqueue Gutenberg assets
     */
    public function enqueue_gutenberg_assets() {
        // Enqueue editor styles
        wp_enqueue_style(
            'gst-gutenberg-editor',
            GST_THEME_URL . '/dist/gutenberg/editor.css',
            [],
            GST_THEME_VERSION
        );
        
        // Enqueue editor scripts
        wp_enqueue_script(
            'gst-gutenberg-blocks',
            GST_THEME_URL . '/dist/gutenberg/blocks.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'],
            GST_THEME_VERSION,
            true
        );
    }
    
    /**
     * Enqueue Gutenberg frontend assets
     */
    public function enqueue_gutenberg_frontend_assets() {
        // Enqueue frontend styles
        wp_enqueue_style(
            'gst-gutenberg-style',
            GST_THEME_URL . '/dist/gutenberg/style.css',
            [],
            GST_THEME_VERSION
        );
    }
}
