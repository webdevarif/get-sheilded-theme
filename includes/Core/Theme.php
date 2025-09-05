<?php
/**
 * Main Theme Class
 * 
 * @package GetsheildedTheme\Core
 * @since 1.0.0
 */

namespace GetsheildedTheme\Core;

use GetsheildedTheme\Frontend\Scripts as FrontendScripts;
use GetsheildedTheme\Admin\Scripts as AdminScripts;
use GetsheildedTheme\Admin\Templates;
use GetsheildedTheme\Admin\SettingsAPI;
use GetsheildedTheme\Blocks\BlockRegistry;

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
        $this->init_language();
    }
    
    /**
     * Initialize language system
     */
    private function init_language() {
        // Load only the language manager
        error_log('GST Theme - Loading LanguageManager from: ' . GST_THEME_PATH . '/includes/Language/LanguageManager.php');
        require_once GST_THEME_PATH . '/includes/Language/LanguageManager.php';
        error_log('GST Theme - LanguageManager loaded successfully');
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
        new SettingsAPI();
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
            'primary' => __('Primary Menu', 'get-sheilded-theme'),
            'footer' => __('Footer Menu', 'get-sheilded-theme'),
        ));
        
        // Load text domain
        load_theme_textdomain('get-sheilded-theme', GST_THEME_PATH . '/languages');
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
