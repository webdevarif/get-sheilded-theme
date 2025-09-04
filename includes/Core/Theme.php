<?php
/**
 * Main Theme Class
 * 
 * @package GetShieldedTheme\Core
 * @since 1.0.0
 */

namespace GetShieldedTheme\Core;

use GetShieldedTheme\Frontend\Scripts as FrontendScripts;
use GetShieldedTheme\Admin\Scripts as AdminScripts;
use GetShieldedTheme\Admin\Templates;
use GetShieldedTheme\Blocks\BlockRegistry;

class Theme {
    
    /**
     * Theme instance
     * 
     * @var Theme
     */
    private static $instance = null;
    
    /**
     * Get theme instance
     * 
     * @return Theme
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
        $this->init_components();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'setup_theme'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_gutenberg_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_gutenberg_frontend_assets'));
    }
    
    /**
     * Initialize theme components
     */
    private function init_components() {
        new FrontendScripts();
        new AdminScripts();
        new Templates();
        new BlockRegistry();
    }
    
    /**
     * Setup theme supports and features
     */
    public function setup_theme() {
        // Add theme support
        add_theme_support('post-thumbnails');
        add_theme_support('title-tag');
        add_theme_support('custom-logo');
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));
        
        // Gutenberg features
        add_theme_support('wp-block-styles');
        add_theme_support('align-wide');
        add_theme_support('responsive-embeds');
        add_theme_support('editor-styles');
        
        // Register navigation menus
        register_nav_menus(array(
            'primary' => __('Primary Menu', 'get-shielded-theme'),
            'footer' => __('Footer Menu', 'get-shielded-theme'),
        ));
        
        // Load text domain
        load_theme_textdomain('get-shielded-theme', GST_THEME_PATH . '/languages');
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Main frontend JS (includes CSS via webpack)
        wp_enqueue_script(
            'gst-frontend-script',
            GST_THEME_URL . '/dist/frontend/main.js',
            array(),
            GST_THEME_VERSION,
            true
        );
        
        // Vendor dependencies
        wp_enqueue_script(
            'gst-frontend-vendors',
            GST_THEME_URL . '/dist/frontend/vendors.js',
            array(),
            GST_THEME_VERSION,
            true
        );
        
        // Components script
        wp_enqueue_script(
            'gst-frontend-components',
            GST_THEME_URL . '/dist/frontend/components.js',
            array('gst-frontend-script'),
            GST_THEME_VERSION,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('gst-frontend-script', 'gstAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gst_nonce'),
        ));
    }
    
    /**
     * Enqueue Gutenberg editor assets
     */
    public function enqueue_gutenberg_assets() {
        // Gutenberg blocks JS
        wp_enqueue_script(
            'gst-gutenberg-blocks',
            GST_THEME_URL . '/dist/gutenberg/blocks.js',
            array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-components'),
            GST_THEME_VERSION,
            true
        );
        
        // Gutenberg blocks CSS for editor
        wp_enqueue_style(
            'gst-gutenberg-editor-style',
            GST_THEME_URL . '/dist/gutenberg/editor.css',
            array('wp-edit-blocks'),
            GST_THEME_VERSION
        );
    }
    
    /**
     * Enqueue Gutenberg frontend assets
     */
    public function enqueue_gutenberg_frontend_assets() {
        // Gutenberg blocks CSS for frontend
        wp_enqueue_style(
            'gst-gutenberg-style',
            GST_THEME_URL . '/dist/gutenberg/style.css',
            array(),
            GST_THEME_VERSION
        );
    }
}
?>
