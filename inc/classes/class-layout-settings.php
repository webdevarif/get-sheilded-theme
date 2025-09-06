<?php
namespace GetsheildedTheme\Inc\Classes;

use GetsheildedTheme\Inc\Traits\Singleton;

/**
 * Layout Settings Feature
 * 
 * @package GetsheildedTheme\Classes
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Layout_Settings {
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
        // Layout settings specific initialization
    }
    
    /**
     * Enqueue CSS variables as inline styles in frontend
     */
    public function enqueue_css_variables() {
        try {
            $settings = get_option('gst_theme_settings', []);
            
            // Only generate CSS if settings exist
            if (empty($settings) || empty($settings['layout'])) {
                return;
            }
        
            $css = ":root {\n";
            
            // Layout - Override Tailwind container and spacing
            $container_max_width = $settings['layout']['containerMaxWidth'] ?? '';
            $border_radius = $settings['layout']['borderRadius'] ?? '';
            $spacing = $settings['layout']['spacing'] ?? '';
            
            if ($container_max_width) {
                $css .= "  --max-w-7xl: {$container_max_width};\n";
            }
            if ($border_radius) {
                $css .= "  --rounded-lg: {$border_radius};\n";
            }
            if ($spacing) {
                $css .= "  --gst-spacing: {$spacing};\n";
            }
            
            $css .= "}\n";
            
            // Enqueue inline CSS
            wp_add_inline_style('gst-frontend-main', $css);
        } catch (Exception $e) {
            error_log('GST: Exception in enqueue_css_variables: ' . $e->getMessage());
        }
    }
    
    /**
     * Get layout setting
     */
    public function get_setting($setting_key) {
        $settings = get_option('gst_theme_settings', []);
        return $settings['layout'][$setting_key] ?? '';
    }
    
    /**
     * Get all layout settings
     */
    public function get_settings() {
        $settings = get_option('gst_theme_settings', []);
        return $settings['layout'] ?? [];
    }
}
