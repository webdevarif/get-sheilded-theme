<?php
/**
 * Core Theme Class - Streamlined and Reusable
 *
 * @package GetsheildedTheme
 */

namespace GetsheildedTheme\Inc\Core;

use GetsheildedTheme\Inc\Traits\Singleton;

/**
 * Core theme functionality
 */
class Theme_Core {

    use Singleton;

    /**
     * Theme features registry
     */
    private $features = [];

    /**
     * Theme components registry
     */
    private $components = [];

    /**
     * Protected constructor
     */
    protected function __construct() {
        $this->init();
    }

    /**
     * Initialize theme core
     */
    private function init() {
        $this->setup_hooks();
        $this->load_core_features();
    }

    /**
     * Setup WordPress hooks
     */
    private function setup_hooks() {
        add_action('after_setup_theme', [$this, 'setup_theme']);
        add_action('init', [$this, 'init_theme']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    /**
     * Setup theme support
     */
    public function setup_theme() {
        // Core WordPress features
        add_theme_support('post-thumbnails');
        add_theme_support('title-tag');
        add_theme_support('custom-logo');
        add_theme_support('html5', [
            'search-form', 'comment-form', 'comment-list', 
            'gallery', 'caption', 'style', 'script'
        ]);

        // Gutenberg support
        add_theme_support('wp-block-styles');
        add_theme_support('align-wide');
        add_theme_support('editor-styles');
        add_theme_support('responsive-embeds');
    }

    /**
     * Initialize theme
     */
    public function init_theme() {
        // Initialize registered features
        foreach ($this->features as $feature) {
            if (method_exists($feature, 'init')) {
                $feature->init();
            }
        }
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'gst-main',
            GST_THEME_URL . '/dist/frontend/main.css',
            [],
            GST_THEME_VERSION
        );

        wp_enqueue_script(
            'gst-main',
            GST_THEME_URL . '/dist/frontend/main.js',
            [],
            GST_THEME_VERSION,
            true
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets() {
        wp_enqueue_style(
            'gst-admin',
            GST_THEME_URL . '/dist/admin/admin.css',
            [],
            GST_THEME_VERSION
        );
    }

    /**
     * Register a theme feature
     */
    public function register_feature($name, $feature_class) {
        if (class_exists($feature_class)) {
            $this->features[$name] = $feature_class::get_instance();
        }
    }

    /**
     * Register a component
     */
    public function register_component($name, $component_class) {
        if (class_exists($component_class)) {
            $this->components[$name] = $component_class::get_instance();
        }
    }

    /**
     * Get a registered feature
     */
    public function get_feature($name) {
        return $this->features[$name] ?? null;
    }

    /**
     * Get a registered component
     */
    public function get_component($name) {
        return $this->components[$name] ?? null;
    }

    /**
     * Load core features
     */
    private function load_core_features() {
        // Core features that are always loaded
        $this->register_feature('templates', \GetsheildedTheme\Inc\Features\Templates::class);
        $this->register_feature('admin', \GetsheildedTheme\Inc\Features\Admin::class);
        $this->register_feature('blocks', \GetsheildedTheme\Inc\Features\Blocks::class);
    }
}
