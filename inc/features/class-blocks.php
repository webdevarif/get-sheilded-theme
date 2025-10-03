<?php
/**
 * Blocks Feature - Streamlined
 *
 * @package GetsheildedTheme
 */

namespace GetsheildedTheme\Inc\Features;

use GetsheildedTheme\Inc\Traits\Singleton;

/**
 * Blocks feature
 */
class Blocks {

    use Singleton;

    /**
     * Constructor
     */
    protected function __construct() {
        $this->init();
    }

    /**
     * Initialize blocks feature
     */
    private function init() {
        add_action('init', [$this, 'register_blocks']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_assets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
    }

    /**
     * Register Gutenberg blocks
     */
    public function register_blocks() {
        // Register block styles
        register_block_style('core/heading', [
            'name' => 'gst-gradient',
            'label' => __('Gradient Text', 'get-sheilded-theme'),
        ]);

        register_block_style('core/button', [
            'name' => 'gst-outline',
            'label' => __('Outline Button', 'get-sheilded-theme'),
        ]);

        register_block_style('core/group', [
            'name' => 'gst-card',
            'label' => __('Card Container', 'get-sheilded-theme'),
        ]);
    }

    /**
     * Enqueue block editor assets
     */
    public function enqueue_block_assets() {
        wp_enqueue_style(
            'gst-editor',
            GST_THEME_URL . '/dist/gutenberg/editor.css',
            ['wp-edit-blocks'],
            GST_THEME_VERSION
        );

        wp_enqueue_script(
            'gst-blocks',
            GST_THEME_URL . '/dist/gutenberg/blocks.js',
            ['wp-blocks', 'wp-element', 'wp-editor'],
            GST_THEME_VERSION,
            true
        );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'gst-blocks',
            GST_THEME_URL . '/dist/gutenberg/blocks.css',
            [],
            GST_THEME_VERSION
        );
    }
}
