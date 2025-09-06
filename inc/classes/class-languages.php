<?php
namespace GetsheildedTheme\Inc\Classes;

use GetsheildedTheme\Inc\Traits\Singleton;

/**
 * Languages Feature
 * 
 * @package GetsheildedTheme\Classes
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Languages {
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
        // AJAX handlers
        add_action('wp_ajax_gst_save_languages', [$this, 'ajax_save_languages']);
        add_action('wp_ajax_gst_get_languages', [$this, 'ajax_get_languages']);
    }
    
    /**
     * Initialize feature
     */
    public function init() {
        // Language-specific initialization
    }
    
    /**
     * AJAX handler for saving languages
     */
    public function ajax_save_languages() {
        check_ajax_referer('gst_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $languages = $_POST['languages'] ?? [];
        $switcher_enabled = $_POST['switcher_enabled'] ?? false;
        
        // Sanitize languages
        $sanitized_languages = [];
        foreach ($languages as $code => $lang) {
            if (isset($lang['name'], $lang['code'], $lang['flag'])) {
                $sanitized_languages[$code] = [
                    'name' => sanitize_text_field($lang['name']),
                    'code' => sanitize_text_field($lang['code']),
                    'flag' => wp_kses_post($lang['flag']),
                    'country' => sanitize_text_field($lang['country'] ?? $lang['name']),
                    'is_default' => isset($lang['is_default']) ? (bool) $lang['is_default'] : false,
                    'active' => isset($lang['active']) ? (bool) $lang['active'] : true
                ];
            }
        }
        
        // Save to database
        $result = update_option('gst_languages', $sanitized_languages);
        if (!$result) {
            $result = add_option('gst_languages', $sanitized_languages);
        }
        
        update_option('gst_language_switcher_enabled', (bool) $switcher_enabled);
        
        wp_send_json_success([
            'languages' => $sanitized_languages,
            'switcher_enabled' => (bool) $switcher_enabled
        ]);
    }
    
    /**
     * AJAX handler for getting languages
     */
    public function ajax_get_languages() {
        check_ajax_referer('gst_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $languages = get_option('gst_languages', []);
        $switcher_enabled = get_option('gst_language_switcher_enabled', false);
        
        wp_send_json_success([
            'languages' => $languages,
            'switcher_enabled' => $switcher_enabled
        ]);
    }
    
    /**
     * Get current language
     */
    public function get_current_language() {
        return get_option('gst_current_language', 'en');
    }
    
    /**
     * Set current language
     */
    public function set_current_language($language_code) {
        update_option('gst_current_language', sanitize_text_field($language_code));
    }
    
    /**
     * Get language URL
     */
    public function get_language_url($language_code, $url = '') {
        if (empty($url)) {
            $url = home_url($_SERVER['REQUEST_URI']);
        }
        
        // Add language parameter
        $separator = strpos($url, '?') !== false ? '&' : '?';
        return $url . $separator . 'lang=' . sanitize_text_field($language_code);
    }
    
    /**
     * Get all languages
     */
    public function get_languages() {
        return get_option('gst_languages', []);
    }
    
    /**
     * Is language switcher enabled
     */
    public function is_switcher_enabled() {
        return get_option('gst_language_switcher_enabled', false);
    }
}
