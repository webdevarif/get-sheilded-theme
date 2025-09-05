<?php
/**
 * Simple Language Manager - All-in-one language system
 * 
 * @package GetsheildedTheme\Language
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class LanguageManager {
    
    private $current_language = '';
    private $languages = [];
    private $option_name = 'gst_languages';
    private $instance_id;
    
    public function __construct() {
        $this->instance_id = uniqid('lm_', true);
        error_log('Language Manager - Constructor called (ID: ' . $this->instance_id . ')');
        $this->log_debug('Language Manager - Constructor called (ID: ' . $this->instance_id . ')');
        $this->load_languages();
        $this->detect_language();
        $this->init_hooks();
        $this->init_admin_hooks();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        $this->log_debug('Language Manager - init_hooks called');
        add_action('init', [$this, 'setup_rewrite_rules']);
        add_filter('query_vars', [$this, 'add_query_vars']);
        add_action('wp_loaded', [$this, 'handle_language_switching']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        $this->log_debug('Language Manager - Hooks registered');
    }
    
    /**
     * Load languages from database
     */
    private function load_languages() {
        // Simple approach - just get from options table
        $languages = get_option('gst_languages', []);
        
        if (!is_array($languages)) {
            $languages = [];
        }
        
        $this->languages = $languages;
        $this->log_debug('Language Manager - Constructor loaded languages from gst_languages option: ' . count($languages) . ' languages');
    }
    
    /**
     * Detect current language from URL
     */
    private function detect_language() {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        
        // If no languages configured, don't set a current language
        if (empty($this->languages)) {
            $this->current_language = '';
            return;
        }
        
        // Check for language prefixes
        foreach ($this->languages as $code => $lang) {
            if ($lang['active'] && !$lang['is_default'] && strpos($request_uri, '/' . $code . '/') !== false) {
                $this->current_language = $code;
                return;
            }
        }
        
        // Default to first active language (only if languages are configured)
        foreach ($this->languages as $code => $lang) {
            if ($lang['active']) {
                $this->current_language = $code;
                break;
            }
        }
    }
    
    /**
     * Setup rewrite rules for language URLs
     */
    public function setup_rewrite_rules() {
        foreach ($this->languages as $code => $lang) {
            if ($lang['active'] && !$lang['is_default']) {
                add_rewrite_rule(
                    '^' . $code . '/(.*)/?$',
                    'index.php?lang=' . $code . '&pagename=$matches[1]',
                    'top'
                );
            }
        }
    }
    
    /**
     * Add language query var
     */
    public function add_query_vars($vars) {
        $vars[] = 'lang';
        return $vars;
    }
    
    /**
     * Handle language switching
     */
    public function handle_language_switching() {
        if (isset($_GET['lang']) && isset($this->languages[$_GET['lang']])) {
            $this->current_language = $_GET['lang'];
        }
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        $this->log_debug('Language Manager - Registering REST API routes');
        
        register_rest_route('gst/v1', '/languages', [
            'methods' => 'GET',
            'callback' => [$this, 'get_languages'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
        
        register_rest_route('gst/v1', '/languages', [
            'methods' => 'POST',
            'callback' => [$this, 'save_languages_simple'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
        
        register_rest_route('gst/v1', '/languages/switcher', [
            'methods' => 'POST',
            'callback' => [$this, 'save_switcher_state'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
        
        // Debug endpoint
        register_rest_route('gst/v1', '/languages/debug', [
            'methods' => 'GET',
            'callback' => [$this, 'debug_languages'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
        
        // Simple test endpoint
        register_rest_route('gst/v1', '/languages/test', [
            'methods' => 'GET',
            'callback' => [$this, 'test_endpoint'],
            'permission_callback' => '__return_true'
        ]);
        
        $this->log_debug('Language Manager - REST API routes registered successfully');
    }
    
    /**
     * Simple file-based logging
     */
    private function log_debug($message) {
        $log_file = WP_CONTENT_DIR . '/debug.log';
        $timestamp = current_time('Y-m-d H:i:s');
        file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Test endpoint
     */
    public function test_endpoint() {
        return rest_ensure_response([
            'message' => 'Language Manager REST API is working!',
            'timestamp' => current_time('mysql'),
            'option_name' => $this->option_name,
            'languages_count' => count($this->languages)
        ]);
    }
    
    /**
     * Debug languages endpoint
     */
    public function debug_languages() {
        $languages = get_option($this->option_name, []);
        $all_options = get_option($this->option_name, 'NOT_FOUND');
        
        return rest_ensure_response([
            'option_name' => $this->option_name,
            'languages' => $languages,
            'all_options_raw' => $all_options,
            'option_exists' => $all_options !== 'NOT_FOUND',
            'wp_options_table' => $GLOBALS['wpdb']->get_results("SELECT option_name, option_value FROM {$GLOBALS['wpdb']->options} WHERE option_name LIKE '%gst%'")
        ]);
    }

    /**
     * Get languages via REST API
     */
    public function get_languages() {
        error_log('Language Manager - get_languages method called via REST API (ID: ' . $this->instance_id . ')');
        $this->log_debug('Language Manager - get_languages method called via REST API (ID: ' . $this->instance_id . ')');
        
        // Simple approach - just get from options table
        $languages = get_option('gst_languages', []);
        
        if (!is_array($languages)) {
            $languages = [];
        }
        
        error_log('Language Manager - GET languages from DB: ' . print_r($languages, true));
        error_log('Language Manager - Option name for GET: ' . $this->option_name . ' (ID: ' . $this->instance_id . ')');
        $this->log_debug('Language Manager - GET languages from DB: ' . print_r($languages, true));
        $this->log_debug('Language Manager - Option name for GET: ' . $this->option_name . ' (ID: ' . $this->instance_id . ')');
        
        $response_data = [
            'languages' => $languages,
            'switcher_enabled' => $this->get_switcher_state()
        ];
        
        $this->log_debug('Language Manager - GET response: ' . print_r($response_data, true));
        return rest_ensure_response($response_data);
    }
    
    /**
     * Save languages via REST API
     */
    public function save_languages($request) {
        $this->log_debug('Language Manager - save_languages method called (ID: ' . $this->instance_id . ')');
        $this->log_debug('Language Manager - Request params: ' . print_r($request->get_params(), true));
        
        $languages = $request->get_param('languages');
        $switcher_enabled = $request->get_param('switcher_enabled');
        
        $this->log_debug('Language Manager - Languages param: ' . print_r($languages, true));
        $this->log_debug('Language Manager - Switcher enabled param: ' . $switcher_enabled);
        
        if (!is_array($languages)) {
            return new WP_Error('invalid_data', 'Languages must be an array', ['status' => 400]);
        }
        
        // Validate and sanitize languages
        $sanitized_languages = [];
        $default_language_code = null;
        
        foreach ($languages as $code => $lang) {
            if (isset($lang['name'], $lang['code'], $lang['flag'])) {
                $is_default = isset($lang['is_default']) ? (bool) $lang['is_default'] : false;
                $is_active = isset($lang['active']) ? (bool) $lang['active'] : true;
                
                // Track which language is being set as default
                if ($is_default) {
                    $default_language_code = $code;
                }
                
                $sanitized_flag = $this->sanitize_flag($lang['flag']);
                
                // Debug logging
                $this->log_debug('Language Manager - Original flag: ' . $lang['flag']);
                $this->log_debug('Language Manager - Original flag bytes: ' . bin2hex($lang['flag']));
                $this->log_debug('Language Manager - Sanitized flag: ' . $sanitized_flag);
                $this->log_debug('Language Manager - Sanitized flag bytes: ' . bin2hex($sanitized_flag));
                
                $sanitized_languages[$code] = [
                    'name' => sanitize_text_field($lang['name']),
                    'code' => sanitize_text_field($lang['code']),
                    'flag' => $sanitized_flag, // Custom emoji sanitization
                    'country' => sanitize_text_field($lang['country'] ?? $lang['name']), // Use name as fallback if country not provided
                    'is_default' => $is_default,
                    'active' => $is_active
                ];
            }
        }
        
        // Validate that default language is active
        if ($default_language_code && isset($sanitized_languages[$default_language_code])) {
            if (!$sanitized_languages[$default_language_code]['active']) {
                return new WP_Error('invalid_default', 'Cannot set a disabled language as default', ['status' => 400]);
            }
        }
        
        // Debug logging
        $this->log_debug('Language Manager - Saving languages: ' . print_r($sanitized_languages, true));
        $this->log_debug('Language Manager - Option name: ' . $this->option_name);
        
        // Check data size and serialization
        $serialized_data = serialize($sanitized_languages);
        $this->log_debug('Language Manager - Serialized data length: ' . strlen($serialized_data));
        $this->log_debug('Language Manager - Serialized data: ' . $serialized_data);
        
        // Check if option exists
        $option_exists = get_option($this->option_name, false) !== false;
        
        if ($option_exists) {
            $result = update_option($this->option_name, $sanitized_languages);
            $this->log_debug('Language Manager - update_option called with option: ' . $this->option_name);
        } else {
            // Try update_option first, then add_option as fallback
            $result = update_option($this->option_name, $sanitized_languages);
            $this->log_debug('Language Manager - update_option called with option: ' . $this->option_name . ' (new option)');
            
            if (!$result) {
                $result = add_option($this->option_name, $sanitized_languages);
                $this->log_debug('Language Manager - add_option called with option: ' . $this->option_name . ' (fallback)');
            }
        }
        
        // Check for WordPress errors
        if (is_wp_error($result)) {
            $this->log_debug('Language Manager - WordPress error: ' . $result->get_error_message());
        }
        
        // Test database permissions with a simple option
        $test_result = add_option('gst_test_option', 'test_value');
        $this->log_debug('Language Manager - Test option save result: ' . ($test_result ? 'success' : 'failed'));
        if ($test_result) {
            delete_option('gst_test_option'); // Clean up test
        }
        
        // Test with serialized data similar to languages
        $test_array = ['test' => ['name' => 'Test', 'code' => 'test', 'flag' => '🇪🇸']];
        $test_serialized = serialize($test_array);
        $this->log_debug('Language Manager - Test serialized data length: ' . strlen($test_serialized));
        $test_array_result = add_option('gst_test_array', $test_array);
        $this->log_debug('Language Manager - Test array save result: ' . ($test_array_result ? 'success' : 'failed'));
        if ($test_array_result) {
            delete_option('gst_test_array'); // Clean up test
        }
        
        $this->log_debug('Language Manager - Option exists: ' . ($option_exists ? 'yes' : 'no'));
        $this->log_debug('Language Manager - Save result: ' . ($result ? 'success' : 'failed'));
        
        // Verify what was actually saved
        $saved_languages = get_option($this->option_name, []);
        $this->log_debug('Language Manager - Retrieved after save: ' . print_r($saved_languages, true));
        
        // Also check if the option exists
        $option_exists = get_option($this->option_name, 'NOT_FOUND');
        $this->log_debug('Language Manager - Option exists check: ' . ($option_exists === 'NOT_FOUND' ? 'NOT_FOUND' : 'EXISTS'));
        
        // Force save test - Use WordPress transients instead of options
        if (!$result) {
            $this->log_debug('Language Manager - Both update_option and add_option failed, trying transient storage');
            
            // Test if we can save a simple transient
            $test_transient = set_transient('gst_test_transient_' . time(), 'test_value', 0);
            $this->log_debug('Language Manager - Test transient save: ' . ($test_transient ? 'success' : 'failed'));
            if ($test_transient) {
                delete_transient('gst_test_transient_' . time());
            }
            
            // Store languages as transient
            $transient_name = 'gst_languages_data';
            $transient_result = set_transient($transient_name, $sanitized_languages, 0);
            $this->log_debug('Language Manager - Transient save result: ' . ($transient_result ? 'success' : 'failed'));
            
            if ($transient_result) {
                // Immediately test if we can retrieve the transient
                $test_retrieve = get_transient($transient_name);
                $this->log_debug('Language Manager - Immediate transient retrieve test: ' . ($test_retrieve ? 'success' : 'failed'));
                if ($test_retrieve) {
                    $this->log_debug('Language Manager - Retrieved data type: ' . gettype($test_retrieve));
                    $this->log_debug('Language Manager - Retrieved data count: ' . (is_array($test_retrieve) ? count($test_retrieve) : 'not array'));
                }
                
                $result = true;
                $this->log_debug('Language Manager - Transient storage result: success');
                
                // Also try to save a backup in options with a different approach
                $backup_result = $this->save_languages_backup($sanitized_languages);
                $this->log_debug('Language Manager - Backup save result: ' . ($backup_result ? 'success' : 'failed'));
            } else {
                $this->log_debug('Language Manager - Transient storage result: failed');
            }
        }
        
        $this->languages = $sanitized_languages;
        
        // Save switcher state if provided
        if (isset($switcher_enabled) && is_bool($switcher_enabled)) {
            update_option('gst_language_switcher_enabled', $switcher_enabled);
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        return rest_ensure_response([
            'success' => true, 
            'languages' => $sanitized_languages,
            'switcher_enabled' => $this->get_switcher_state()
        ]);
    }
    
    /**
     * Simple save method - just save to options table directly
     */
    public function save_languages_simple($request) {
        $this->log_debug('Language Manager - save_languages_simple method called');
        $this->log_debug('Language Manager - Request method: ' . $request->get_method());
        $this->log_debug('Language Manager - Request params: ' . print_r($request->get_params(), true));
        
        $languages = $request->get_param('languages');
        $switcher_enabled = $request->get_param('switcher_enabled');
        
        if (!is_array($languages)) {
            return new WP_Error('invalid_data', 'Languages must be an array', ['status' => 400]);
        }
        
        // Simple validation and sanitization
        $sanitized_languages = [];
        foreach ($languages as $code => $lang) {
            if (isset($lang['name'], $lang['code'], $lang['flag'])) {
                $sanitized_languages[$code] = [
                    'name' => sanitize_text_field($lang['name']),
                    'code' => sanitize_text_field($lang['code']),
                    'flag' => sanitize_text_field($lang['flag']),
                    'country' => sanitize_text_field($lang['country'] ?? $lang['name']),
                    'is_default' => isset($lang['is_default']) ? (bool) $lang['is_default'] : false,
                    'active' => isset($lang['active']) ? (bool) $lang['active'] : true
                ];
            }
        }
        
        // Save to options table - simple approach
        $this->log_debug('Language Manager - Simple save: Attempting to save to gst_languages option');
        $this->log_debug('Language Manager - Simple save: Languages to save: ' . print_r($sanitized_languages, true));
        
        // Check if option exists
        $option_exists = get_option('gst_languages', false) !== false;
        $this->log_debug('Language Manager - Simple save: Option exists: ' . ($option_exists ? 'yes' : 'no'));
        
        if ($option_exists) {
            $result = update_option('gst_languages', $sanitized_languages);
            $this->log_debug('Language Manager - Simple save: update_option result: ' . ($result ? 'success' : 'failed'));
        } else {
            $result = add_option('gst_languages', $sanitized_languages);
            $this->log_debug('Language Manager - Simple save: add_option result: ' . ($result ? 'success' : 'failed'));
        }
        
        // Force save using delete + add if both failed
        if (!$result) {
            $this->log_debug('Language Manager - Simple save: Both methods failed, trying delete + add');
            delete_option('gst_languages');
            $result = add_option('gst_languages', $sanitized_languages);
            $this->log_debug('Language Manager - Simple save: delete + add result: ' . ($result ? 'success' : 'failed'));
        }
        
        // Verify the save worked
        $verify = get_option('gst_languages', []);
        $this->log_debug('Language Manager - Simple save: Verification - loaded ' . count($verify) . ' languages after save');
        
        // Save switcher state
        if (isset($switcher_enabled) && is_bool($switcher_enabled)) {
            update_option('gst_language_switcher_enabled', $switcher_enabled);
        }
        
        // Update local instance
        $this->languages = $sanitized_languages;
        
        return rest_ensure_response([
            'success' => $result,
            'languages' => $sanitized_languages,
            'switcher_enabled' => $this->get_switcher_state()
        ]);
    }
    
    /**
     * Backup save method using direct database insertion
     */
    private function save_languages_backup($languages) {
        global $wpdb;
        
        // Try to save as a simple serialized string
        $serialized = maybe_serialize($languages);
        $backup_option = 'gst_languages_backup';
        
        $result = update_option($backup_option, $serialized);
        if (!$result) {
            $result = add_option($backup_option, $serialized);
        }
        
        return $result;
    }
    
    /**
     * Save switcher state via REST API
     */
    public function save_switcher_state($request) {
        $enabled = $request->get_param('enabled');
        
        if (!is_bool($enabled)) {
            return new WP_Error('invalid_data', 'Enabled must be a boolean', ['status' => 400]);
        }
        
        update_option('gst_language_switcher_enabled', $enabled);
        
        return rest_ensure_response(['success' => true, 'enabled' => $enabled]);
    }
    
    /**
     * Get switcher state
     */
    public function get_switcher_state() {
        return get_option('gst_language_switcher_enabled', false);
    }
    
    /**
     * Check permissions for REST API
     */
    public function check_permissions() {
        return current_user_can('manage_options');
    }
    
    /**
     * Get current language
     */
    public function get_current_language() {
        return $this->current_language;
    }

    /**
     * Sanitize flag content while allowing HTML (SVG, img, emoji)
     */
    private function sanitize_flag($flag) {
        // Use WordPress's built-in HTML sanitization
        $flag = wp_kses_post($flag);
        
        // Trim and limit length
        $flag = trim($flag);
        $flag = mb_substr($flag, 0, 1000, 'UTF-8');
        
        error_log('Language Manager - Flag sanitization: Input=' . $flag . ' Output=' . $flag);
        
        return $flag;
    }
    
    /**
     * Get all languages
     */
    public function get_all_languages() {
        return $this->languages;
    }
    
    /**
     * Get active languages
     */
    public function get_active_languages() {
        return array_filter($this->languages, function($lang) {
            return $lang['active'];
        });
    }
    
    /**
     * Get language switcher HTML
     */
    public function render_language_switcher() {
        $active_languages = $this->get_active_languages();
        
        if (count($active_languages) <= 1) {
            return '';
        }
        
        $current_url = $this->get_current_url();
        
        ob_start();
        ?>
        <div class="gst-language-switcher">
            <select onchange="window.location.href=this.value" class="gst-lang-select">
                <?php foreach ($active_languages as $code => $lang): ?>
                    <option value="<?php echo esc_attr($this->get_language_url($code)); ?>" 
                            <?php selected($code, $this->current_language); ?>>
                        <?php echo esc_html($lang['flag'] . ' ' . $lang['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get language URL
     */
    public function get_language_url($lang_code, $path = '') {
        if (!isset($this->languages[$lang_code]) || !$this->languages[$lang_code]['active']) {
            return home_url($path);
        }
        
        $lang = $this->languages[$lang_code];
        
        if ($lang['is_default']) {
            return home_url($path);
        }
        
        return home_url($lang_code . '/' . ltrim($path, '/'));
    }
    
    /**
     * Get current URL
     */
    private function get_current_url() {
        global $wp;
        return home_url($wp->request);
    }
    
    /**
     * Check if language switcher is enabled
     */
    public function is_switcher_enabled() {
        return count($this->get_active_languages()) > 1;
    }
    
    /**
     * Initialize admin hooks
     */
    private function init_admin_hooks() {
        add_action('init', [$this, 'register_language_meta']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_language_sidebar_assets']);
        add_action('init', [$this, 'add_language_post_states']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        
    }
    
    
    /**
     * Register language meta field for REST API
     */
    public function register_language_meta() {
        // Only register if we have languages configured
        if (!empty($this->languages)) {
            register_post_meta('', 'gst_language', array(
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
                // No default value - let it be empty until user selects
            ));
        }
    }
    
    /**
     * Add language post states to admin table
     */
    public function add_language_post_states() {
        add_filter('display_post_states', [$this, 'display_language_post_state'], 10, 2);
    }
    
    /**
     * Display language post state in admin table
     */
    public function display_language_post_state($post_states, $post) {
        // Only show for pages and posts
        if (!in_array($post->post_type, ['post', 'page'])) {
            return $post_states;
        }
        
        // Get the language assigned to this post
        $post_language = get_post_meta($post->ID, 'gst_language', true);
        
        // Only show language post state if a specific language is selected and it exists
        if (!empty($post_language) && isset($this->languages[$post_language])) {
            $language = $this->languages[$post_language];
            
            // Only show language post state if it's NOT the default language and is active
            if (!$language['is_default'] && $language['active']) {
                $language_name = $language['name'];
                $language_flag = $language['flag'];
                
                // Add language post state
                $post_states['gst_language'] = sprintf(
                    '<span class="gst-language-state" title="Language: %s">%s %s</span>',
                    esc_attr($language_name),
                    esc_html($language_flag),
                    esc_html($language_name)
                );
            }
        }
        
        return $post_states;
    }
    
    /**
     * Enqueue language sidebar assets
     */
    public function enqueue_language_sidebar_assets() {
        $screen = get_current_screen();
        if ($screen && in_array($screen->post_type, ['post', 'page'])) {
            wp_enqueue_script(
                'gst-language-sidebar',
                get_template_directory_uri() . '/dist/gutenberg/language-sidebar.js',
                array(
                    'wp-plugins',
                    'wp-edit-post',
                    'wp-element',
                    'wp-components',
                    'wp-data',
                    'wp-editor',
                    'wp-i18n'
                ),
                '1.0.0',
                true
            );
            
            wp_localize_script('gst-language-sidebar', 'gstLanguage', array(
                'nonce' => wp_create_nonce('wp_rest'),
                'apiUrl' => rest_url('wp/v2/'),
                'languages' => $this->get_active_languages(),
            ));
        }
    }
    
    /**
     * Enqueue admin styles for language post states
     */
    public function enqueue_admin_styles() {
        $screen = get_current_screen();
        if ($screen && in_array($screen->id, ['edit-post', 'edit-page'])) {
            wp_add_inline_style('wp-admin', $this->get_language_post_state_css());
        }
    }
    
    /**
     * Get CSS for language post states
     */
    private function get_language_post_state_css() {
        return '
        ';
    }
}

// Initialize the language manager
error_log('GST LanguageManager - About to instantiate LanguageManager');
$GLOBALS['language_manager'] = new LanguageManager();
error_log('GST LanguageManager - LanguageManager instantiated successfully');

/**
 * Helper function to get current language
 */
function gst_get_current_language() {
    global $language_manager;
    return $language_manager->get_current_language();
}

/**
 * Helper function to get language switcher HTML
 */
function gst_language_switcher() {
    global $language_manager;
    return $language_manager->render_language_switcher();
}

/**
 * Helper function to get language URL
 */
function gst_get_language_url($lang_code, $path = '') {
    global $language_manager;
    return $language_manager->get_language_url($lang_code, $path);
}
