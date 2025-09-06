<?php
/**
 * Simple Language API
 * 
 * @package GetsheildedTheme\Rest
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class LanguageAPI {
    
    private $option_name = 'gst_languages';
    
    public function __construct() {
        error_log('GST LanguageAPI - Constructor called');
        add_action('rest_api_init', [$this, 'register_routes']);
        error_log('GST LanguageAPI - REST API hook registered');
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        error_log('GST LanguageAPI - register_routes called');
        
        // Get languages
        register_rest_route('gst/v1', '/languages', [
            'methods' => 'GET',
            'callback' => [$this, 'get_languages'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
        
        // Save languages
        register_rest_route('gst/v1', '/languages', [
            'methods' => 'POST',
            'callback' => [$this, 'save_languages'],
            'permission_callback' => [$this, 'check_permissions'],
            'args' => [
                'languages' => [
                    'required' => true,
                    'type' => 'object',
                    'sanitize_callback' => [$this, 'sanitize_languages']
                ],
                'switcher_enabled' => [
                    'required' => false,
                    'type' => 'boolean',
                    'default' => false
                ]
            ]
        ]);
        
        error_log('GST LanguageAPI - Routes registered successfully');
    }
    
    /**
     * Get languages
     */
    public function get_languages() {
        error_log('GST LanguageAPI - get_languages called');
        
        $languages = get_option($this->option_name, []);
        $switcher_enabled = get_option('gst_language_switcher_enabled', false);
        
        error_log('GST LanguageAPI - Languages from DB: ' . print_r($languages, true));
        error_log('GST LanguageAPI - Switcher enabled: ' . ($switcher_enabled ? 'true' : 'false'));
        
        return rest_ensure_response([
            'success' => true,
            'languages' => $languages,
            'switcher_enabled' => $switcher_enabled
        ]);
    }
    
    /**
     * Save languages
     */
    public function save_languages($request) {
        $languages = $request->get_param('languages');
        $switcher_enabled = $request->get_param('switcher_enabled');
        
        // Save languages
        $result = update_option($this->option_name, $languages);
        if (!$result) {
            $result = add_option($this->option_name, $languages);
        }
        
        // Save switcher state
        update_option('gst_language_switcher_enabled', $switcher_enabled);
        
        if ($result) {
            return rest_ensure_response([
                'success' => true,
                'languages' => $languages,
                'switcher_enabled' => $switcher_enabled
            ]);
        } else {
            return new WP_Error('save_failed', 'Failed to save languages', ['status' => 500]);
        }
    }
    
    /**
     * Sanitize languages data
     */
    public function sanitize_languages($languages) {
        if (!is_array($languages)) {
            return [];
        }
        
        $sanitized = [];
        foreach ($languages as $code => $lang) {
            if (isset($lang['name'], $lang['code'], $lang['flag'])) {
                $sanitized[$code] = [
                    'name' => sanitize_text_field($lang['name']),
                    'code' => sanitize_text_field($lang['code']),
                    'flag' => wp_kses_post($lang['flag']), // Allow HTML for flags
                    'country' => sanitize_text_field($lang['country'] ?? $lang['name']),
                    'is_default' => isset($lang['is_default']) ? (bool) $lang['is_default'] : false,
                    'active' => isset($lang['active']) ? (bool) $lang['active'] : true
                ];
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Check permissions
     */
    public function check_permissions() {
        return current_user_can('manage_options');
    }
}

// Initialize the API
new LanguageAPI();
