<?php
namespace GetsheildedTheme\Inc\Classes;

use GetsheildedTheme\Inc\Traits\Singleton;

/**
 * Color Palette Feature
 * 
 * @package GetsheildedTheme\Classes
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Color_Palette {
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
        add_action('wp_enqueue_scripts', [$this, 'enqueue_css_variables'], 999);
    }
    
    /**
     * Initialize feature
     */
    public function init() {
        // Color palette specific initialization
    }
    
    /**
     * Enqueue CSS variables as inline styles in frontend
     */
    public function enqueue_css_variables() {
        try {
            $settings = get_option('gst_theme_settings', []);
            
            // Only generate CSS if settings exist
            if (empty($settings) || empty($settings['colors'])) {
                return;
            }
        
            $css = ":root {\n";
            
            // Colors - Override Tailwind color variables
            foreach ($settings['colors'] as $key => $value) {
                $css .= "  --gst-{$key}: {$value};\n";
            }
            
            $css .= "}\n";
            
            // Enqueue inline CSS
            wp_add_inline_style('gst-frontend-main', $css);
        } catch (Exception $e) {
            error_log('GST: Exception in enqueue_css_variables: ' . $e->getMessage());
        }
    }
    
    /**
     * Get color value
     */
    public function get_color($color_key) {
        $settings = get_option('gst_theme_settings', []);
        return $settings['colors'][$color_key] ?? '';
    }
    
    /**
     * Get all colors
     */
    public function get_colors() {
        $settings = get_option('gst_theme_settings', []);
        return $settings['colors'] ?? [];
    }
}
