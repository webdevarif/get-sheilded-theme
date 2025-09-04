<?php
/**
 * Frontend Scripts Handler
 * 
 * @package GetShieldedTheme\Frontend
 * @since 1.0.0
 */

namespace GetShieldedTheme\Frontend;

class Scripts {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'register_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'register_styles'));
    }
    
    /**
     * Register frontend scripts
     */
    public function register_scripts() {
        // Register main frontend script
        wp_register_script(
            'gst-frontend-main',
            GST_THEME_URL . '/dist/frontend/main.js',
            array(),
            GST_THEME_VERSION,
            true
        );
        
        // Register interactive components
        wp_register_script(
            'gst-frontend-components',
            GST_THEME_URL . '/dist/frontend/components.js',
            array('gst-frontend-main'),
            GST_THEME_VERSION,
            true
        );
        
        // Conditionally enqueue scripts
        $this->conditional_enqueue();
    }
    
    /**
     * Register frontend styles
     */
    public function register_styles() {
        // Register main frontend stylesheet
        wp_register_style(
            'gst-frontend-main',
            GST_THEME_URL . '/dist/frontend/main.css',
            array(),
            GST_THEME_VERSION
        );
        
        // Components don't have separate CSS - styles are included in main.css
    }
    
    /**
     * Conditional script enqueuing
     */
    private function conditional_enqueue() {
        // Always enqueue main styles and scripts
        wp_enqueue_style('gst-frontend-main');
        wp_enqueue_script('gst-frontend-main');
        
        // Enqueue components on pages that need them
        if (is_front_page() || is_page() || is_single()) {
            wp_enqueue_script('gst-frontend-components');
        }
        
        // Localize frontend script
        wp_localize_script('gst-frontend-main', 'gstFrontend', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gst_frontend_nonce'),
            'isLoggedIn' => is_user_logged_in(),
            'userId' => get_current_user_id(),
        ));
    }
}
?>
