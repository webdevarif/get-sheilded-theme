<?php
namespace GetsheildedTheme\Inc\Classes;

use GetsheildedTheme\Inc\Traits\Singleton;

/**
 * TranslatePress-like Language System
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
    
    private $current_language = '';
    private $default_language = '';
    private $available_languages = [];
    private $is_editor_mode = false;
    
    /**
     * Constructor
     */
    protected function __construct() {
        $this->setup_hooks();
        $this->init_language_system();
    }
    
    /**
     * Setup hooks
     */
    private function setup_hooks() {
        // Core hooks
        add_action('init', [$this, 'init_language_system'], 5);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_filter('home_url', [$this, 'filter_home_url'], 10, 4);
        
        // Translation filters
        add_filter('gettext', [$this, 'translate_gettext'], 20, 3);
        add_filter('the_content', [$this, 'translate_content']);
        add_filter('the_title', [$this, 'translate_title']);
        add_filter('wp_title', [$this, 'translate_wp_title']);
        
        // AJAX handlers
        add_action('wp_ajax_save_translation', [$this, 'ajax_save_translation']);
        add_action('wp_ajax_auto_translate', [$this, 'ajax_auto_translate']);
        add_action('wp_ajax_get_translations', [$this, 'ajax_get_translations']);
        
        // Admin settings
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        
        // Language switcher
        add_action('wp_footer', [$this, 'add_language_switcher']);
        
        // Editor mode
        add_action('wp_footer', [$this, 'add_editor_toggle']);
        add_action('wp_footer', [$this, 'add_translation_app_container']);
        
        // Handle language URLs with rewrite rules
        add_action('init', [$this, 'add_language_rewrite_rules']);
        add_filter('query_vars', [$this, 'add_language_query_vars']);
        // Prevent WP from stripping language slug via canonical redirect
        add_filter('redirect_canonical', [$this, 'bypass_canonical_redirect_for_language'], 10, 2);
        // Map language-prefixed requests to underlying content before main query
        add_filter('request', [$this, 'map_language_request_to_query']);
    }
    
    /**
     * Initialize language system
     */
    public function init_language_system() {
        // Create database table
        $this->create_translations_table();
        
        // Load settings
        $this->load_language_settings();
        
        // Detect current language
        $this->detect_current_language();
        
        // Set editor mode
        $this->is_editor_mode = isset($_GET['edit_trans']) && current_user_can('edit_posts');
        
        // Redirect to frontend if in admin/editor and translation mode
        // But not if this is being loaded in an iframe
        if ($this->is_editor_mode && is_admin() && !$this->is_iframe_request()) {
            // Build the full URL properly
            $protocol = is_ssl() ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            $uri = $_SERVER['REQUEST_URI'];
            $frontend_url = $protocol . $host . $uri;
            
            // Remove edit_trans parameter and add it back to ensure it's in the right place
            $frontend_url = remove_query_arg('edit_trans', $frontend_url);
            $frontend_url = add_query_arg('edit_trans', '1', $frontend_url);
            
            wp_redirect($frontend_url);
            exit;
        }
        
        // Define global constant
        if (!defined('GST_CURRENT_LANGUAGE')) {
            define('GST_CURRENT_LANGUAGE', $this->current_language);
        }
    }
    
    /**
     * Create translations database table
     */
    private function create_translations_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gst_translations';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            original_text text NOT NULL,
            translation text NOT NULL,
            language varchar(10) NOT NULL,
            context varchar(255) DEFAULT '',
            element_type varchar(50) DEFAULT 'text',
            element_id varchar(100) DEFAULT '',
            status varchar(20) DEFAULT 'approved',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY unique_translation (original_text(191), language, context(50))
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Load language settings
     */
    private function load_language_settings() {
        $settings = get_option('gst_theme_settings', []);
        $languages = $settings['languages'] ?? [];
        
        $this->default_language = $languages['default_language'] ?? '';
        $this->available_languages = $languages['available'] ?? [];
        
        // Fallback: If no languages are set, create some default ones
        if (empty($this->available_languages)) {
            $this->available_languages = [
                'en' => [
                    'name' => 'English',
                    'code' => 'en',
                    'flag' => '🇺🇸',
                    'country' => 'United States',
                    'is_default' => false,
                    'active' => true
                ],
                'bn' => [
                    'name' => 'Bengali',
                    'code' => 'bn',
                    'flag' => '🇧🇩',
                    'country' => 'Bangladesh',
                    'is_default' => true,
                    'active' => true
                ]
            ];
            
            // Save the default languages
            $settings['languages'] = [
                'switcher_enabled' => true,
                'available' => $this->available_languages
            ];
            update_option('gst_theme_settings', $settings);
            
            error_log('GST Language Settings - Created default languages');
        }
    }
    
    /**
     * Detect current language
     */
    private function detect_current_language() {
        // Debug logging
        error_log('GST Language Detection - REQUEST_URI: ' . ($_SERVER['REQUEST_URI'] ?? 'N/A'));
        error_log('GST Language Detection - Query vars: ' . print_r($_GET, true));
        error_log('GST Language Detection - Available languages: ' . print_r($this->available_languages, true));
        
        // Check rewrite query vars first (from URL rewriting)
        $gst_language = get_query_var('gst_language');
        if ($gst_language) {
            error_log('GST Language Detection - Found in query var: ' . $gst_language);
            $this->current_language = sanitize_text_field($gst_language);
            $this->set_language_cookie($this->current_language);
            return;
        }
        
        // Check URL path segment (/en/, /bn/)
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $path_parts = explode('/', trim($request_uri, '/'));
        
        if (!empty($path_parts[0]) && isset($this->available_languages[$path_parts[0]])) {
            error_log('GST Language Detection - Found in path: ' . $path_parts[0]);
            $this->current_language = sanitize_text_field($path_parts[0]);
            $this->set_language_cookie($this->current_language);
            return;
        }
        
        // Check URL parameter as fallback
        if (isset($_GET['lang'])) {
            error_log('GST Language Detection - Found in GET param: ' . $_GET['lang']);
            $this->current_language = sanitize_text_field($_GET['lang']);
            $this->set_language_cookie($this->current_language);
            return;
        }
        
        // Check cookie
        if (isset($_COOKIE['gst_current_language'])) {
            error_log('GST Language Detection - Found in cookie: ' . $_COOKIE['gst_current_language']);
            $this->current_language = sanitize_text_field($_COOKIE['gst_current_language']);
            return;
        }
        
        // Use default (empty if no language is set)
        error_log('GST Language Detection - Using default: ' . ($this->default_language ?: 'empty'));
        $this->current_language = $this->default_language;
    }
    
    /**
     * Set language cookie
     */
    private function set_language_cookie($language) {
        setcookie('gst_current_language', $language, time() + (30 * 24 * 60 * 60), '/');
    }

    /**
     * Add language slug to home_url similar to TranslatePress
     */
    public function filter_home_url($url, $path, $orig_scheme, $blog_id) {
        // Only add when a current language is set and not default empty
        if (empty($this->current_language)) {
            return $url;
        }
        // Respect admin and file/sitemap contexts
        if (is_admin() || strpos($path, 'sitemap') !== false || preg_match('/\\.xml($|\\?)/', $path)) {
            return $url;
        }
        $url_slug = $this->current_language; // we use raw code as slug for now
        if (empty($url_slug)) {
            return $url;
        }
        // Work only with the provided $url to avoid recursion
        $parsed = wp_parse_url($url);
        if (empty($parsed) || empty($parsed['host'])) {
            return $url;
        }
        $path_current = isset($parsed['path']) ? $parsed['path'] : '/';
        // Avoid double-prefixing
        $segments = explode('/', ltrim($path_current, '/'));
        if (!empty($segments[0]) && $segments[0] === $url_slug) {
            return $url;
        }
        // Insert language slug after leading slash
        $new_path = '/' . $url_slug . '/' . ltrim($path_current, '/');
        // Normalize multiple slashes
        $new_path = preg_replace('#/+#', '/', $new_path);
        // Rebuild URL
        $rebuilt = (isset($parsed['scheme']) ? $parsed['scheme'] : (is_ssl() ? 'https' : 'http')) . '://' . $parsed['host'];
        if (isset($parsed['port'])) {
            $rebuilt .= ':' . $parsed['port'];
        }
        $rebuilt .= $new_path;
        if (isset($parsed['query'])) {
            $rebuilt .= '?' . $parsed['query'];
        }
        if (isset($parsed['fragment'])) {
            $rebuilt .= '#' . $parsed['fragment'];
        }
        return $rebuilt;
    }
    
    /**
     * Check if this is an iframe request
     */
    private function is_iframe_request() {
        // Check if the request is coming from an iframe
        $is_iframe = isset($_GET['iframe']) || 
                     (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'edit_trans=1') !== false) ||
                     (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
        
        // Debug logging
        error_log('GST Iframe Detection: ' . ($is_iframe ? 'TRUE' : 'FALSE'));
        error_log('GST GET iframe: ' . (isset($_GET['iframe']) ? 'YES' : 'NO'));
        error_log('GST Referer: ' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'NONE'));
        
        return $is_iframe;
    }
    
    // Old language meta field method removed - using TranslatePress-like system
    
    // Old language meta field fallback method removed - using TranslatePress-like system
    
    // Old language meta field simple method removed - using TranslatePress-like system
    
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
        
        // Get current settings
        $settings = get_option('gst_theme_settings', []);
        
        // Update language settings
        $settings['languages'] = [
            'switcher_enabled' => (bool) $switcher_enabled,
            'available' => $sanitized_languages
        ];
        
        // Save to database
        $result = update_option('gst_theme_settings', $settings);
        if (!$result) {
            $result = add_option('gst_theme_settings', $settings);
        }
        
        // Language settings saved successfully
        
        // Flush rewrite rules to activate new language rules
        flush_rewrite_rules();
        
        // Debug: Log the saved settings
        error_log('GST Language Settings Saved: ' . print_r($settings, true));
        
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
        
        $settings = get_option('gst_theme_settings', []);
        $languages = $settings['languages']['available'] ?? [];
        $switcher_enabled = $settings['languages']['switcher_enabled'] ?? false;
        
        wp_send_json_success([
            'languages' => $languages,
            'switcher_enabled' => $switcher_enabled
        ]);
    }
    
    /**
     * Get current language
     */
    public static function get_current_language() {
        $instance = self::get_instance();
        return $instance->current_language;
    }
    
    /**
     * Set current language
     */
    public static function set_current_language($language_code) {
        $instance = self::get_instance();
        $instance->current_language = sanitize_text_field($language_code);
        $instance->set_language_cookie($language_code);
    }
    
    /**
     * Translate gettext strings
     */
    public function translate_gettext($translated, $original, $domain) {
        // Only translate if we have a current language and it's not empty
        if (empty($this->current_language)) {
            return $translated;
        }
        
        $translation = $this->get_translation($original, 'gettext');
        return $translation ? $translation : $translated;
    }
    
    /**
     * Translate content
     */
    public function translate_content($content) {
        // Only translate if we have a current language and it's not empty
        if (empty($this->current_language)) {
            return $content;
        }
        
        // Wrap translatable content for editor
        if ($this->is_editor_mode) {
            $content = $this->wrap_translatable_content($content, 'content');
        }
        
        return $this->translate_text($content, 'content');
    }
    
    /**
     * Translate title
     */
    public function translate_title($title) {
        // Only translate if we have a current language and it's not empty
        if (empty($this->current_language)) {
            return $title;
        }
        
        if ($this->is_editor_mode) {
            $title = '<span class="gst-translatable" data-original="' . esc_attr($title) . '" data-context="title">' . $title . '</span>';
        }
        
        return $this->translate_text($title, 'title');
    }
    
    /**
     * Translate wp_title
     */
    public function translate_wp_title($title) {
        // Only translate if we have a current language and it's not empty
        if (empty($this->current_language)) {
            return $title;
        }
        
        return $this->translate_text($title, 'wp_title');
    }
    
    /**
     * Wrap translatable content for editor
     */
    private function wrap_translatable_content($content, $context) {
        // Skip wrapping if we're in Elementor code view
        if (isset($_GET['edit_trans']) && (isset($_GET['elementor-preview']) || strpos($_SERVER['REQUEST_URI'], 'elementor') !== false)) {
            return $content;
        }
        
        // Skip wrapping if content is empty or contains only whitespace
        if (empty(trim($content))) {
            return $content;
        }
        
        // More conservative approach - only wrap text that's clearly visible content
        $content = preg_replace_callback(
            '/(?<=>)([^<>]+?)(?=<)/',
            function($matches) use ($context) {
                $text = trim($matches[1]);
                
                // Skip if text is too short, is only numbers/special chars, or contains script/style content
                if (strlen($text) < 4 || 
                    preg_match('/^[0-9\s\-_\.]+$/', $text) ||
                    strpos($text, '<script') !== false ||
                    strpos($text, '<style') !== false ||
                    strpos($text, 'javascript:') !== false ||
                    strpos($text, 'onclick=') !== false ||
                    strpos($text, 'onload=') !== false) {
                    return $matches[0];
                }
                
                // Only wrap if it looks like actual content text and hasn't been wrapped already
                if (preg_match('/[a-zA-Z]/', $text) && strpos($text, 'gst-translatable') === false) {
                    return '<span class="gst-translatable" data-original="' . esc_attr($text) . '" data-context="' . esc_attr($context) . '">' . $text . '</span>';
                }
                
                return $matches[0];
            },
            $content
        );
        
        return $content;
    }
    
    /**
     * Translate text
     */
    private function translate_text($text, $context = '') {
        // Only translate if we have a current language and it's not empty
        if (empty($this->current_language)) {
            return $text;
        }
        
        // Extract text from HTML
        $plain_text = wp_strip_all_tags($text);
        if (empty($plain_text)) {
            return $text;
        }
        
        $translation = $this->get_translation($plain_text, $context);
        if ($translation) {
            return str_replace($plain_text, $translation, $text);
        }
        
        // Fallback: Auto-translate on the fly if enabled and AI configured
        $auto_enabled = (bool) get_option('gst_auto_translate');
        $ai_model = get_option('gst_ai_model', '');
        $api_key = get_option('gst_ai_api_key', '');
        
        // Debug logging
        error_log('GST Translation Debug - Current language: ' . $this->current_language);
        error_log('GST Translation Debug - Auto translate enabled: ' . ($auto_enabled ? 'YES' : 'NO'));
        error_log('GST Translation Debug - AI model: ' . $ai_model);
        error_log('GST Translation Debug - Text to translate: ' . $plain_text);
        
        if ($auto_enabled && !empty($ai_model) && !empty($api_key)) {
            $auto = $this->auto_translate($plain_text, $this->current_language);
            if (!empty($auto) && $auto !== $plain_text) {
                // Cache to DB
                $this->save_translation($plain_text, $auto, $this->current_language, $context);
                error_log('GST Translation Debug - Auto-translated: ' . $auto);
                return str_replace($plain_text, $auto, $text);
            }
        }
        
        return $text;
    }
    
    /**
     * Get translation from database
     */
    private function get_translation($original, $context = '') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gst_translations';
        $translation = $wpdb->get_var($wpdb->prepare(
            "SELECT translation FROM $table_name WHERE original_text = %s AND language = %s AND context = %s",
            $original,
            $this->current_language,
            $context
        ));
        
        return $translation;
    }
    
    /**
     * Save translation
     */
    public function save_translation($original, $translation, $language, $context = '') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gst_translations';
        
        $result = $wpdb->replace(
            $table_name,
            [
                'original_text' => $original,
                'translation' => $translation,
                'language' => $language,
                'context' => $context,
                'status' => 'approved'
            ],
            ['%s', '%s', '%s', '%s', '%s']
        );
        
        return $result !== false;
    }
    
    /**
     * Auto translate using API
     */
    public function auto_translate($text, $target_language) {
        $model = get_option('gst_ai_model', '');
        $api_key = get_option('gst_ai_api_key', '');
        $text = (string) $text;
        $target_language = sanitize_text_field($target_language);
        if (empty($model) || empty($api_key) || empty($text) || empty($target_language)) {
            // Legacy fallback to Google Translate API key if set
            $legacy_key = get_option('gst_google_translate_key', '');
            if (!empty($legacy_key)) {
                $response = wp_remote_get(
                    'https://translation.googleapis.com/language/translate/v2?key=' . $legacy_key . '&q=' . rawurlencode($text) . '&target=' . $target_language
                );
                if (!is_wp_error($response)) {
                    $body = json_decode(wp_remote_retrieve_body($response), true);
                    if (isset($body['data']['translations'][0]['translatedText'])) {
                        return (string) $body['data']['translations'][0]['translatedText'];
                    }
                }
            }
            return $text;
        }

        if ($model === 'openai') {
            // OpenAI Chat Completions simple translate prompt
            $endpoint = 'https://api.openai.com/v1/chat/completions';
            $payload = [
                'model' => 'gpt-4o-mini',
                'temperature' => 0,
                'messages' => [
                    [ 'role' => 'system', 'content' => 'You are a translator. Respond ONLY with the translation text.' ],
                    [ 'role' => 'user', 'content' => 'Translate to language code ' . $target_language . ': ' . $text ]
                ]
            ];
            $response = wp_remote_post($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type' => 'application/json'
                ],
                'body' => wp_json_encode($payload),
                'timeout' => 20
            ]);
            if (!is_wp_error($response)) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($body['choices'][0]['message']['content'])) {
                    return (string) $body['choices'][0]['message']['content'];
                }
            }
            return $text;
        }

        if ($model === 'gemini') {
            // Google Generative Language (Gemini) simple translate prompt
            $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . rawurlencode($api_key);
            $payload = [
                'contents' => [[
                    'parts' => [[ 'text' => 'Translate to language code ' . $target_language . ': ' . $text . "\nRespond with translation only." ]]
                ]]
            ];
            $response = wp_remote_post($endpoint, [
                'headers' => [ 'Content-Type' => 'application/json' ],
                'body' => wp_json_encode($payload),
                'timeout' => 20
            ]);
            if (!is_wp_error($response)) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                if (!empty($body['candidates'][0]['content']['parts'][0]['text'])) {
                    return (string) $body['candidates'][0]['content']['parts'][0]['text'];
                }
            }
            return $text;
        }

        return $text;
    }
    
    
    /**
     * Get language URL
     */
    public function get_language_url($language_code, $url = '') {
        if (empty($url)) {
            // Build the full URL properly
            $protocol = is_ssl() ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            $uri = $_SERVER['REQUEST_URI'];
            $url = $protocol . $host . $uri;
        }
        
        // Parse the URL
        $parsed_url = parse_url($url);
        $path = $parsed_url['path'] ?? '/';
        $query = $parsed_url['query'] ?? '';
        
        // Remove existing language from path
        $path_parts = explode('/', trim($path, '/'));
        if (!empty($path_parts[0]) && isset($this->available_languages[$path_parts[0]])) {
            // Remove the first part if it's a language code
            array_shift($path_parts);
        }
        
        // Remove lang parameter from query string
        if (!empty($query)) {
            parse_str($query, $query_params);
            unset($query_params['lang']);
            $query = http_build_query($query_params);
        }
        
        // Build new path
        $new_path = '/';
        if (!empty($path_parts)) {
            $new_path .= implode('/', $path_parts) . '/';
        }
        
        // Add language code to path (except for default language)
        // If default language is empty, all languages get a path prefix
        if ($language_code !== $this->default_language && !empty($language_code)) {
            $new_path = '/' . $language_code . $new_path;
        }
        
        // Build final URL
        $final_url = $parsed_url['scheme'] . '://' . $parsed_url['host'] . $new_path;
        if (!empty($query)) {
            $final_url .= '?' . $query;
        }
        
        // Debug logging
        error_log('GST Language URL - Language: ' . $language_code);
        error_log('GST Language URL - Original URL: ' . $url);
        error_log('GST Language URL - New path: ' . $new_path);
        error_log('GST Language URL - Final URL: ' . $final_url);
        
        return $final_url;
    }
    
    /**
     * Get all languages
     */
    public function get_languages() {
        $settings = get_option('gst_theme_settings', []);
        return $settings['languages']['available'] ?? [];
    }
    
    /**
     * Is language switcher enabled
     */
    public function is_switcher_enabled() {
        $settings = get_option('gst_theme_settings', []);
        return $settings['languages']['switcher_enabled'] ?? false;
    }
    
    // REST API methods removed - using pure PHP approach
    
    /**
     * Add language rewrite rules
     */
    public function add_language_rewrite_rules() {
        $this->load_language_settings();
        
        if (empty($this->available_languages)) {
            return;
        }
        
        $language_codes = implode('|', array_keys($this->available_languages));
        
        // Add rewrite rules for language URLs
        add_rewrite_rule(
            '^(' . $language_codes . ')/(.*)/?$',
            'index.php?gst_language=$matches[1]&gst_path=$matches[2]',
            'top'
        );
        
        // Add rewrite rule for language root
        add_rewrite_rule(
            '^(' . $language_codes . ')/?$',
            'index.php?gst_language=$matches[1]&gst_path=',
            'top'
        );
    }
    
    /**
     * Add language query vars
     */
    public function add_language_query_vars($vars) {
        $vars[] = 'gst_language';
        $vars[] = 'gst_path';
        return $vars;
    }

    /**
     * Map language-prefixed requests to WP query vars without redirect
     */
    public function map_language_request_to_query($query_vars) {
        $this->load_language_settings();
        if (empty($this->available_languages)) {
            return $query_vars;
        }
        // If rewrite vars are present, use them directly
        if (!empty($query_vars['gst_language'])) {
            $lang = sanitize_text_field($query_vars['gst_language']);
            if (isset($this->available_languages[$lang])) {
                $this->current_language = $lang;
                $this->set_language_cookie($this->current_language);
                if (!empty($query_vars['gst_path'])) {
                    $query_vars['pagename'] = trim($query_vars['gst_path'], '/');
                }
                unset($query_vars['gst_language'], $query_vars['gst_path']);
                return $query_vars;
            }
        }
        // Fallback: derive from request URI
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $path_only = parse_url($request_uri, PHP_URL_PATH);
        $parts = explode('/', trim((string) $path_only, '/'));
        if (empty($parts[0]) || !isset($this->available_languages[$parts[0]])) {
            return $query_vars;
        }
        $language_code = sanitize_text_field(array_shift($parts));
        $this->current_language = $language_code;
        $this->set_language_cookie($this->current_language);
        $path_without_lang = implode('/', $parts);
        if ($path_without_lang !== '') {
            $query_vars['pagename'] = $path_without_lang;
        }
        return $query_vars;
    }

    /**
     * Bypass canonical redirect when a recognized language slug is present
     */
    public function bypass_canonical_redirect_for_language($redirect_url, $requested_url) {
        $this->load_language_settings();
        if (empty($this->available_languages)) {
            return $redirect_url;
        }
        $req = $requested_url ?: ((is_ssl() ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? ''));
        $parsed = wp_parse_url($req);
        $path = isset($parsed['path']) ? trim($parsed['path'], '/') : '';
        $first = $path !== '' ? explode('/', $path)[0] : '';
        if ($first && isset($this->available_languages[$first])) {
            // Set current language from slug
            $this->current_language = sanitize_text_field($first);
            $this->set_language_cookie($this->current_language);
            // Do not let WP redirect to the non-prefixed URL
            return false;
        }
        return $redirect_url;
    }

    /**
     * Resolve language-prefixed requests during 404 handling without redirect
     */
    public function resolve_language_prefixed_request($preempt, $wp_query) {
        // Only intervene on 404
        if (!is_404()) {
            return $preempt;
        }
        $this->load_language_settings();
        if (empty($this->available_languages)) {
            return $preempt;
        }
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $parts = explode('/', trim($request_uri, '/'));
        if (empty($parts[0]) || !isset($this->available_languages[$parts[0]])) {
            return $preempt;
        }
        // Extract language and rebuild path without it
        $language_code = sanitize_text_field(array_shift($parts));
        $this->current_language = $language_code;
        $this->set_language_cookie($this->current_language);
        $new_path = '/' . implode('/', $parts) . '/';
        if ($new_path === '//' || $new_path === '/') {
            // It's the homepage in a language. Serve homepage without redirect.
            status_header(200);
            // Let WP continue rendering as front page.
            add_filter('request', function($vars) { return $vars; }, 1);
            return true; // stop 404
        }
        // Try to query the underlying content
        $q = new \WP_Query(['pagename' => trim($new_path, '/')]);
        if ($q->have_posts()) {
            // Instruct WP to use this query
            $GLOBALS['wp_query'] = $q;
            status_header(200);
            return true; // handled, no 404
        }
        return $preempt;
    }
    
    /**
     * Handle language template redirect
     */
    public function handle_language_template_redirect() {
        $language = get_query_var('gst_language');
        $path = get_query_var('gst_path');
        
        if (!empty($language)) {
            error_log('GST Language Template Redirect - Language: ' . $language . ', Path: ' . $path);
            
            // Set the current language
            $this->current_language = sanitize_text_field($language);
            $this->set_language_cookie($this->current_language);
            
            // If there's a path, redirect to it with the language set
            if (!empty($path)) {
                $redirect_url = home_url('/' . $path . '/');
                error_log('GST Language Template Redirect - Redirecting to: ' . $redirect_url);
                wp_redirect($redirect_url, 302);
                exit;
            }
        }
    }
    
    // Old meta box methods removed - using TranslatePress-like system
    
    // Old save_post_language method removed - using TranslatePress-like system
    
    /**
     * Add language state to post states
     */
    public function add_language_state($post_states, $post) {
        $settings = get_option('gst_theme_settings', []);
        $languages = $settings['languages']['available'] ?? [];
        
        // Only show if language switcher is enabled
        if (!($settings['languages']['switcher_enabled'] ?? false)) {
            return $post_states;
        }
        
        $post_language = get_post_meta($post->ID, '_post_language', true);
        
        if ($post_language && isset($languages[$post_language])) {
            $lang = $languages[$post_language];
            $post_states[] = $lang['flag'] . ' ' . $lang['name'];
        }
        
        return $post_states;
    }
    
    // Old post language helper methods removed - using TranslatePress-like system
    
    /**
     * Get language switcher HTML
     */
    public static function get_language_switcher($args = []) {
        $settings = get_option('gst_theme_settings', []);
        $languages = $settings['languages'] ?? [];
        
        // Only show if language switcher is enabled
        if (!($languages['switcher_enabled'] ?? false)) {
            return '';
        }
        
        $available_languages = $languages['available'] ?? [];
        $current_language = self::get_current_language();
        
        if (empty($available_languages)) {
            return '';
        }
        
        $defaults = [
            'show_flags' => true,
            'show_names' => true,
            'current_language' => $current_language,
            'class' => 'gst-language-switcher',
            'format' => 'dropdown' // dropdown, list, buttons
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        ob_start();
        ?>
        <div class="<?php echo esc_attr($args['class']); ?>">
            <?php if ($args['format'] === 'dropdown'): ?>
                <select onchange="window.location.href=this.value">
                    <?php foreach ($available_languages as $code => $lang): ?>
                        <?php if ($lang['active']): ?>
                            <option value="<?php echo esc_url(self::get_language_url($code)); ?>">
                                <?php if ($args['show_flags']): ?>
                                    <?php echo wp_kses_post($lang['flag']); ?>
                                <?php endif; ?>
                                <?php if ($args['show_names']): ?>
                                    <?php echo esc_html($lang['name']); ?>
                                <?php endif; ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            <?php elseif ($args['format'] === 'list'): ?>
                <ul>
                    <?php foreach ($available_languages as $code => $lang): ?>
                        <?php if ($lang['active']): ?>
                            <li class="<?php echo $current_language === $code ? 'current' : ''; ?>">
                                <a href="<?php echo esc_url(self::get_language_url($code)); ?>">
                                    <?php if ($args['show_flags']): ?>
                                        <?php echo wp_kses_post($lang['flag']); ?>
                                    <?php endif; ?>
                                    <?php if ($args['show_names']): ?>
                                        <?php echo esc_html($lang['name']); ?>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        echo '<script>console.log("Enqueuing frontend assets, editor mode: ' . ($this->is_editor_mode ? 'true' : 'false') . '");</script>';
        
        wp_enqueue_style(
            'gst-translate-css',
            GST_THEME_URL . '/dist/frontend/translate.css',
            [],
            GST_THEME_VERSION
        );
        
        wp_enqueue_style(
            'gst-translation-editor-css',
            GST_THEME_URL . '/dist/frontend/translation-editor.css',
            [],
            GST_THEME_VERSION
        );
        
        if ($this->is_editor_mode) {
            // Always load the script, but with different localization based on mode
            $script_handle = $this->is_iframe_request() ? 'gst-translate-iframe' : 'gst-translate-editor';
            
            echo '<script>console.log("Enqueuing translation script: ' . $script_handle . '");</script>';
            
            wp_enqueue_script(
                $script_handle,
                GST_THEME_URL . '/dist/frontend/translate-editor-simple.js',
                ['jquery'],
                GST_THEME_VERSION,
                true
            );
            
            
            $localize_data = [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('gst_translate_nonce'),
                'current_language' => $this->current_language,
                'available_languages' => $this->available_languages,
                'default_language' => $this->default_language,
                'switcher_enabled' => $this->is_switcher_enabled()
            ];
            
            if ($this->is_iframe_request()) {
                $localize_data['iframe_mode'] = true;
            }
            
            wp_localize_script($script_handle, 'gstTranslate', $localize_data);
        }
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets() {
        wp_enqueue_style(
            'gst-translate-admin-css',
            GST_THEME_URL . '/dist/admin/translate-admin.css',
            [],
            GST_THEME_VERSION
        );
        
        wp_enqueue_script(
            'gst-translate-admin-js',
            GST_THEME_URL . '/dist/admin/translate-admin.js',
            ['jquery'],
            GST_THEME_VERSION,
            true
        );
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'get-sheilded-settings',
            'TranslatePress',
            'TranslatePress',
            'manage_options',
            'gst-translatepress',
            [$this, 'admin_page']
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        // Backward-compat Google key (optional)
        // AI settings (single model + single API key)
        register_setting('gst_translate_settings', 'gst_ai_model'); // gemini | openai
        register_setting('gst_translate_settings', 'gst_ai_api_key');
        register_setting('gst_translate_settings', 'gst_auto_translate');
    }
    
    /**
     * Admin page
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>TranslatePress Settings</h1>
            
            <form method="post" action="options.php">
                <?php settings_fields('gst_translate_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">AI Translation Model</th>
                        <td>
                            <select name="gst_ai_model">
                                <?php $ai_model = get_option('gst_ai_model', 'gemini'); ?>
                                <option value="gemini" <?php selected($ai_model, 'gemini'); ?>>GEMINI</option>
                                <option value="openai" <?php selected($ai_model, 'openai'); ?>>OPENAI</option>
                            </select>
                            <p class="description">Choose the AI provider for translations.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">AI API Key</th>
                        <td>
                            <input type="text" name="gst_ai_api_key" value="<?php echo esc_attr(get_option('gst_ai_api_key')); ?>" class="regular-text" />
                            <p class="description">Enter the API key for the selected model.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Auto Translate</th>
                        <td>
                            <label>
                                <input type="checkbox" name="gst_auto_translate" value="1" <?php checked(get_option('gst_auto_translate'), 1); ?> />
                                Enable automatic translation for new content
                            </label>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <div class="gst-translate-actions">
                <h2>Quick Actions</h2>
                <p>
                    <a href="<?php echo home_url('?edit_trans=1'); ?>" class="button button-primary" target="_blank">
                        Open Live Editor (Homepage)
                    </a>
                    <span class="description">Click to edit translations on your homepage</span>
                </p>
                <p>
                    <a href="<?php echo admin_url('edit.php?post_type=page'); ?>" class="button button-secondary">
                        Select Page to Translate
                    </a>
                    <span class="description">Choose a specific page to translate</span>
                </p>
                
                <h3>Language URL Test</h3>
                <p>
                    <strong>Current Language:</strong> <?php echo esc_html($this->current_language); ?><br>
                    <strong>Available Languages:</strong> <?php echo esc_html(implode(', ', array_keys($this->available_languages))); ?><br>
                    <strong>Default Language:</strong> <?php echo esc_html($this->default_language); ?>
                </p>
                <p>
                    <a href="<?php echo $this->get_language_url('bn'); ?>" class="button" target="_blank">Test Bengali URL</a>
                </p>
                
                <h3>Debug Information</h3>
                <p>
                    <strong>Settings:</strong> <?php echo esc_html(print_r(get_option('gst_theme_settings'), true)); ?>
                </p>
                
                <h3>Manual Test</h3>
                <p>
                    <a href="<?php echo home_url('/en/about/'); ?>" class="button" target="_blank">Test /en/about/</a>
                    <a href="<?php echo home_url('/bn/about/'); ?>" class="button" target="_blank">Test /bn/about/</a>
                </p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Add language switcher
     */
    public function add_language_switcher() {
        // Only show if language switcher is enabled
        if (!$this->is_switcher_enabled()) {
            return;
        }
        
        if (empty($this->available_languages)) {
            return;
        }
        
        echo '<div class="gst-language-switcher">';
        echo '<select onchange="window.location.href=this.value">';
        echo '<option value="">Select Language</option>';
        
        foreach ($this->available_languages as $code => $lang) {
            if ($lang['active']) {
                $url = $this->get_language_url($code);
                echo '<option value="' . esc_url($url) . '">';
                echo esc_html($lang['flag'] . ' ' . $lang['name']);
                echo '</option>';
            }
        }
        
        echo '</select>';
        echo '</div>';
    }
    
    /**
     * Add editor toggle
     */
    public function add_translation_app_container() {
        if ($this->is_editor_mode) {
            echo '<div id="gst-translation-app"></div>';
            echo '<script>console.log("Translation app container added");</script>';
            
            // Add inline script to show sidebar immediately
            echo "<script>
            console.log(\"Adding inline translation sidebar...\");
            document.addEventListener(\"DOMContentLoaded\", function() {
                console.log(\"DOM loaded, creating sidebar...\");
                
                // Create simple sidebar
                const sidebar = document.createElement(\"div\");
                sidebar.id = \"gst-simple-sidebar\";
                sidebar.style.cssText = \"position: fixed !important; top: 0 !important; right: 0 !important; width: 400px !important; height: 100vh !important; background: white !important; border-left: 1px solid #ddd !important; box-shadow: -2px 0 10px rgba(0,0,0,0.1) !important; z-index: 99999 !important; padding: 20px !important; overflow-y: auto !important;\";
                
                sidebar.innerHTML = \"<h3>Translation Editor</h3><p>Click on any text to translate it</p><div id=\\\"gst-translation-sections\\\" style=\\\"margin: 20px 0;\\\"><!-- Translation sections will be added here dynamically --></div><div style=\\\"margin: 20px 0;\\\"><button onclick=\\\"autoTranslateAll()\\\" style=\\\"background: #28a745; color: white; padding: 8px 15px; border: none; border-radius: 3px; cursor: pointer; margin-right: 10px;\\\">Auto Translate All</button><button onclick=\\\"saveAllTranslations()\\\" style=\\\"background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer;\\\">Save All Translations</button></div><button onclick=\\\"document.getElementById('gst-simple-sidebar').remove()\\\" style=\\\"background: #ccc; color: black; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; width: 100%;\\\">Close Editor</button>\";
                
                document.body.appendChild(sidebar);
                console.log(\"Simple sidebar created and added to page\");
                
                // Create translation sections for all languages
                const translationSections = document.getElementById(\"gst-translation-sections\");
                
                // Get languages from WordPress settings
                const availableLanguages = window.gstTranslate ? window.gstTranslate.available_languages : {};
                const currentLanguage = window.gstTranslate ? window.gstTranslate.current_language : \"bn\";
                const defaultLanguage = window.gstTranslate ? window.gstTranslate.default_language : \"en\";
                
                // Fallback if no data is available
                if (!availableLanguages || Object.keys(availableLanguages).length === 0) {
                    console.warn(\"No language data available, using fallback\");
                    const fallbackLanguages = {
                        \"en\": { name: \"English\", flag: \"🇺🇸\", active: true },
                        \"bn\": { name: \"Bengali\", flag: \"🇧🇩\", active: true }
                    };
                    Object.assign(availableLanguages, fallbackLanguages);
                }
                
                console.log(\"Available languages:\", availableLanguages);
                console.log(\"Current language:\", currentLanguage);
                console.log(\"Default language:\", defaultLanguage);
                
                // First, add default language at the top (read-only)
                if (defaultLanguage && availableLanguages[defaultLanguage]) {
                    const defaultLang = availableLanguages[defaultLanguage];
                    const defaultSection = document.createElement(\"div\");
                    defaultSection.className = \"gst-translation-section gst-default-language\";
                    defaultSection.style.cssText = \"margin: 20px 0; padding: 0;\";
                    
                    defaultSection.innerHTML = \"<div style=\\\"margin-bottom: 8px;\\\"><span style=\\\"font-size: 16px; margin-right: 6px;\\\">\" + defaultLang.flag + \"</span><label style=\\\"font-weight: 600; margin: 0; font-size: 14px;\\\">\" + defaultLang.name + \" (Default - Source Language)</label></div><textarea id=\\\"gst-translation-\" + defaultLanguage + \"\\\" readonly style=\\\"width: 100%; padding: 10px; margin: 0; height: 60px; border: 1px solid #ddd; background: #f8f8f8; color: #666; font-size: 13px; resize: none;\\\">This is the original text that will be translated to other languages</textarea>\";
                    
                    translationSections.appendChild(defaultSection);
                    console.log(\"Added default language section:\", defaultLanguage);
                }
                
                // Then add all other active languages (translatable)
                Object.keys(availableLanguages).forEach(function(langCode) {
                    const lang = availableLanguages[langCode];
                    console.log(\"Processing language:\", langCode, \"Active:\", lang.active);
                    
                    if (lang.active && langCode !== defaultLanguage) {
                        const section = document.createElement(\"div\");
                        section.className = \"gst-translation-section\";
                        section.style.cssText = \"margin: 20px 0; padding: 0;\";
                        
                        const isCurrent = langCode === currentLanguage;
                        
                        section.innerHTML = \"<div style=\\\"margin-bottom: 8px;\\\"><span style=\\\"font-size: 16px; margin-right: 6px;\\\">\" + lang.flag + \"</span><label style=\\\"font-weight: 600; margin: 0; font-size: 14px;\\\">To \" + lang.name + (isCurrent ? \" (Current)\" : \"\") + \":</label></div><textarea id=\\\"gst-translation-\" + langCode + \"\\\" placeholder=\\\"Enter translation for \" + lang.name + \"...\\\" style=\\\"width: 100%; padding: 10px; margin: 0; height: 60px; border: 1px solid #ddd; font-size: 13px; resize: vertical;\\\"></textarea>\";
                        
                        translationSections.appendChild(section);
                        console.log(\"Added translation section for:\", langCode);
                    }
                });
                
                // Function to load existing translations
                window.loadExistingTranslations = function(originalText) {
                    if (!originalText) return;
                    
                    console.log(\"Loading existing translations for:\", originalText);
                    
                    // Load translations for each language
                    Object.keys(availableLanguages).forEach(function(langCode) {
                        const lang = availableLanguages[langCode];
                        if (lang.active && langCode !== defaultLanguage) {
                            const textarea = document.getElementById(\"gst-translation-\" + langCode);
                            if (textarea) {
                                // Load translation via AJAX
                                const formData = new FormData();
                                formData.append(\"action\", \"get_translations\");
                                formData.append(\"nonce\", window.gstTranslate ? window.gstTranslate.nonce : \"\");
                                formData.append(\"language\", langCode);
                                
                                fetch(window.gstTranslate ? window.gstTranslate.ajax_url : \"\", {
                                    method: \"POST\",
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success && data.data.translations) {
                                        // Find translation for this specific text
                                        const translation = data.data.translations.find(t => t.original_text === originalText);
                                        if (translation) {
                                            textarea.value = translation.translation;
                                            console.log(\"Loaded existing translation for\", langCode, \":\", translation.translation);
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error(\"Error loading translation for\", langCode, \":\", error);
                                });
                            }
                        }
                    });
                };
                
                // Make text elements clickable (simplified approach)
                const textSelectors = [
                    \"p\", \"h1\", \"h2\", \"h3\", \"h4\", \"h5\", \"h6\", 
                    \"span\", \"a\", \"strong\", \"em\", \"b\", \"i\",
                    \"li\", \"td\", \"th\", \"label\"
                ];
                
                textSelectors.forEach(function(selector) {
                    document.querySelectorAll(selector).forEach(function(element) {
                        const text = element.textContent.trim();
                        
                        // Skip if already processed or too short
                        if (element.classList.contains(\"gst-translatable\") || 
                            text.length < 3) {
                            return;
                        }
                        
                        // Skip if inside sidebar or navigation
                        if (element.closest(\".gst-simple-sidebar\") ||
                            element.closest(\".gst-editor-toggle\") ||
                            element.closest(\".gst-translation-notice\") ||
                            element.closest(\"nav\") ||
                            element.closest(\"header\") ||
                            element.closest(\"footer\")) {
                            return;
                        }
                        
                        // Skip UI text
                        if (text.includes(\"Click on any text\") ||
                            text.includes(\"Translation Mode\") ||
                            text.includes(\"Edit Translations\") ||
                            text.includes(\"Close Editor\") ||
                            text.includes(\"Auto Translate\") ||
                            text.includes(\"Save Translation\") ||
                            text.includes(\"Select text to translate\") ||
                            text.includes(\"Enter translation\") ||
                            text.includes(\"Default - Source Language\") ||
                            text.includes(\"Current)\") ||
                            text.includes(\"(Default)\")) {
                            return;
                        }
                        
                        // Make element clickable
                        element.classList.add(\"gst-translatable\");
                        element.style.cursor = \"pointer\";
                        element.style.border = \"1px dashed transparent\";
                        element.style.transition = \"all 0.2s ease\";
                        
                        element.addEventListener(\"mouseenter\", function() {
                            if (!this.classList.contains(\"gst-translatable\")) return;
                            this.style.border = \"1px dashed #666\";
                            this.style.backgroundColor = \"rgba(0, 0, 0, 0.05)\";
                        });
                        
                        element.addEventListener(\"mouseleave\", function() {
                            if (!this.classList.contains(\"gst-translatable\")) return;
                            this.style.border = \"1px dashed transparent\";
                            this.style.backgroundColor = \"transparent\";
                        });
                        
                        element.addEventListener(\"click\", function(e) {
                            if (!this.classList.contains(\"gst-translatable\")) return;
                            e.preventDefault();
                            e.stopPropagation();
                            const text = this.textContent.trim();
                            if (text) {
                                // Update default language field with original text
                                const defaultLanguage = window.gstTranslate ? window.gstTranslate.default_language : \"en\";
                                const defaultTextarea = document.getElementById(\"gst-translation-\" + defaultLanguage);
                                if (defaultTextarea) {
                                    defaultTextarea.value = text;
                                }
                                
                                console.log(\"Text selected:\", text);
                                console.log(\"Updated default language field:\", defaultLanguage);
                                
                                // Load existing translations for this text
                                if (window.loadExistingTranslations) {
                                    window.loadExistingTranslations(text);
                                }
                                
                                // Visual feedback
                                this.style.backgroundColor = \"rgba(0, 0, 0, 0.1)\";
                                setTimeout(() => {
                                    this.style.backgroundColor = \"transparent\";
                                }, 1000);
                            }
                        });
                    });
                });
            });
            
            // Global functions for buttons
            window.autoTranslateAll = function() {
                // Get original text from default language field
                const defaultLanguage = window.gstTranslate ? window.gstTranslate.default_language : \"en\";
                const defaultTextarea = document.getElementById(\"gst-translation-\" + defaultLanguage);
                
                if (!defaultTextarea) {
                    alert(\"Default language field not found!\");
                    return;
                }
                
                const originalText = defaultTextarea.value ? defaultTextarea.value.trim() : \"\";
                
                if (!originalText) {
                    alert(\"Please select some text first!\");
                    return;
                }
                
                console.log(\"Auto translating to all languages:\", originalText);
                
                // Get all translation textareas (excluding default language)
                const textareas = document.querySelectorAll(\"[id^=\\\"gst-translation-\\\"]:not(.gst-default-language textarea)\");
                textareas.forEach(function(textarea) {
                    const langCode = textarea.id.replace(\"gst-translation-\", \"\");
                    textarea.value = \"[Auto-translated: \" + originalText + \" in \" + langCode + \"]\";
                });
                
                alert(\"Auto-translated to all languages!\");
            };
            
            window.saveAllTranslations = function() {
                // Get original text from default language field
                const defaultLanguage = window.gstTranslate ? window.gstTranslate.default_language : \"en\";
                const defaultTextarea = document.getElementById(\"gst-translation-\" + defaultLanguage);
                
                if (!defaultTextarea) {
                    alert(\"Default language field not found!\");
                    return;
                }
                
                const originalText = defaultTextarea.value ? defaultTextarea.value.trim() : \"\";
                
                if (!originalText) {
                    alert(\"Please select some text first!\");
                    return;
                }
                
                console.log(\"Saving all translations for:\", originalText);
                
                // Get all translation textareas and save each one
                const textareas = document.querySelectorAll(\"[id^=\\\"gst-translation-\\\"]\");
                let savedCount = 0;
                let totalCount = 0;
                
                console.log(\"Found textareas:\", textareas.length);
                
                // First, count how many translations we have (excluding default language)
                textareas.forEach(function(textarea) {
                    if (textarea && textarea.value) {
                        const translation = textarea.value.trim();
                        const langCode = textarea.id.replace(\"gst-translation-\", \"\");
                        
                        // Only count non-default language textareas
                        if (translation && langCode !== defaultLanguage) {
                            totalCount++;
                        }
                    }
                });
                
                console.log(\"Total translations to save:\", totalCount);
                console.log(\"Default language:\", defaultLanguage);
                
                if (totalCount === 0) {
                    alert(\"No translations to save!\");
                    return;
                }
                
                // Save each translation via AJAX
                textareas.forEach(function(textarea) {
                    if (textarea && textarea.value) {
                        const translation = textarea.value.trim();
                        const langCode = textarea.id.replace(\"gst-translation-\", \"\");
                        
                        if (translation) {
                            // Skip default language (it\'s the source, not a translation)
                            if (langCode === defaultLanguage) {
                                return;
                            }
                            
                            console.log(\"Saving translation for\", langCode, \":\", translation);
                            
                            // Save via AJAX
                            const formData = new FormData();
                            formData.append(\"action\", \"save_translation\");
                            formData.append(\"nonce\", window.gstTranslate ? window.gstTranslate.nonce : \"\");
                            formData.append(\"original\", originalText);
                            formData.append(\"translation\", translation);
                            formData.append(\"language\", langCode);
                            formData.append(\"context\", \"content\");
                            
                            fetch(window.gstTranslate ? window.gstTranslate.ajax_url : \"\", {
                                method: \"POST\",
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    savedCount++;
                                    console.log(\"Successfully saved translation for\", langCode);
                                    
                                    // Check if all translations are saved
                                    if (savedCount === totalCount) {
                                        alert(\"Successfully saved \" + savedCount + \" translations!\");
                                    }
                                } else {
                                    console.error(\"Failed to save translation for\", langCode, \":\", data);
                                }
                            })
                            .catch(error => {
                                console.error(\"Error saving translation for\", langCode, \":\", error);
                            });
                        }
                    }
                });
                
                if (savedCount === 0) {
                    alert(\"No translations to save!\");
                }
            };
            </script>";
        }
    }

    public function add_editor_toggle() {
        if (!current_user_can("edit_posts")) {
            return;
        }
        
        // Only show editor toggle if language switcher is enabled
        if (!$this->is_switcher_enabled()) {
            return;
        }
        
        // Use current page URL instead of home URL
        $current_url = (is_ssl() ? "https://" : "http://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        $editor_url = add_query_arg("edit_trans", "1", $current_url);
        $normal_url = remove_query_arg("edit_trans", $current_url);
        
        if ($this->is_editor_mode) {
            echo "<div class=\"gst-editor-toggle\">";
            echo "<a href=\"" . esc_url($normal_url) . "\" class=\"button\">Exit Editor</a>";
            echo "</div>";
            
            // Add instruction notice
            echo "<div class=\"gst-translation-notice\" style=\"
                position: fixed;
                top: 60px;
                left: 50%;
                transform: translateX(-50%);
                background: #0073aa;
                color: white;
                padding: 15px 20px;
                border-radius: 4px;
                z-index: 9999;
                font-size: 14px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            \">";
            echo "<strong>Translation Mode Active!</strong> Click on any text to translate it.";
            echo "</div>";
        } else {
            echo "<div class=\"gst-editor-toggle\">";
            echo "<a href=\"" . esc_url($editor_url) . "\" class=\"button button-primary\">Edit Translations</a>";
            echo "</div>";
        }
        
        // Add JavaScript to handle editor toggle from any page
        if (!wp_script_is("gst-editor-toggle", "enqueued")) {
            wp_enqueue_script("gst-editor-toggle", "", [], "1.0.0", true);
            wp_add_inline_script("gst-editor-toggle", "
                document.addEventListener(\"DOMContentLoaded\", function() {
                    // Add editor toggle to any page for logged-in users
                    // Only show if language switcher is enabled
                    if (document.body.classList.contains(\"logged-in\") && 
                        !document.querySelector(\".gst-editor-toggle\") &&
                        window.gstTranslate && 
                        window.gstTranslate.switcher_enabled) {
                        const editorToggle = document.createElement(\"div\");
                        editorToggle.className = \"gst-editor-toggle\";
                        editorToggle.style.cssText = \"
                            position: fixed;
                            top: 20px;
                            right: 20px;
                            z-index: 9999;
                        \";
                        
                        const currentUrl = window.location.href;
                        const editorUrl = currentUrl.includes(\"?\") ? 
                            currentUrl + \"&edit_trans=1\" : 
                            currentUrl + \"?edit_trans=1\";
                        
                        editorToggle.innerHTML = \"<a href=\\\"\" + editorUrl + \"\\\" class=\\\"button button-primary\\\" style=\\\"background: #0073aa; color: white; padding: 8px 16px; text-decoration: none; border-radius: 3px; font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);\\\">Edit Translations</a>\";
                        
                        document.body.appendChild(editorToggle);
                    }
                });
            ");
        }
    }
    
    
    /**
     * AJAX: Save translation
     */
    public function ajax_save_translation() {
        check_ajax_referer("gst_translate_nonce", "nonce");
        
        if (!current_user_can("edit_posts")) {
            wp_die("Unauthorized");
        }
        
        $original = sanitize_text_field($_POST["original"] ?? "");
        $translation = wp_kses_post($_POST["translation"] ?? "");
        $language = sanitize_text_field($_POST["language"] ?? "");
        $context = sanitize_text_field($_POST["context"] ?? "");
        
        $result = $this->save_translation($original, $translation, $language, $context);
        
        wp_send_json_success(["saved" => $result]);
    }
    
    /**
     * AJAX: Auto translate
     */
    public function ajax_auto_translate() {
        check_ajax_referer("gst_translate_nonce", "nonce");
        
        if (!current_user_can("edit_posts")) {
            wp_die("Unauthorized");
        }
        
        $text = sanitize_text_field($_POST["text"] ?? "");
        $language = sanitize_text_field($_POST["language"] ?? "");
        
        $translation = $this->auto_translate($text, $language);
        
        wp_send_json_success(["translation" => $translation]);
    }
    
    /**
     * AJAX: Get translations
     */
    public function ajax_get_translations() {
        check_ajax_referer("gst_translate_nonce", "nonce");
        
        if (!current_user_can("edit_posts")) {
            wp_die("Unauthorized");
        }
        
        $language = sanitize_text_field($_POST["language"] ?? "");
        
        global $wpdb;
        $table_name = $wpdb->prefix . "gst_translations";
        $translations = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE language = %s ORDER BY updated_at DESC",
            $language
        ));
        
        wp_send_json_success(["translations" => $translations]);
    }
    
    /**
     * Get all available languages
     */
    public function get_available_languages() {
        return $this->available_languages;
    }
    
}
