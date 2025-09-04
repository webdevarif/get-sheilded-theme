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
    private $option_name = 'gst_simple_languages';
    
    public function __construct() {
        $this->load_languages();
        $this->detect_language();
        $this->init_hooks();
        $this->init_admin_hooks();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('init', [$this, 'setup_rewrite_rules']);
        add_filter('query_vars', [$this, 'add_query_vars']);
        add_action('wp_loaded', [$this, 'handle_language_switching']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }
    
    /**
     * Load languages from database
     */
    private function load_languages() {
        $this->languages = get_option($this->option_name, []);
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
        register_rest_route('gst/v1', '/languages', [
            'methods' => 'GET',
            'callback' => [$this, 'get_languages'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
        
        register_rest_route('gst/v1', '/languages', [
            'methods' => 'POST',
            'callback' => [$this, 'save_languages'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
        
        register_rest_route('gst/v1', '/languages/switcher', [
            'methods' => 'POST',
            'callback' => [$this, 'save_switcher_state'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
    }
    
    /**
     * Get languages via REST API
     */
    public function get_languages() {
        $response_data = [
            'languages' => $this->languages,
            'switcher_enabled' => $this->get_switcher_state()
        ];
        return rest_ensure_response($response_data);
    }
    
    /**
     * Save languages via REST API
     */
    public function save_languages($request) {
        $languages = $request->get_param('languages');
        $switcher_enabled = $request->get_param('switcher_enabled');
        
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
                
                $sanitized_languages[$code] = [
                    'name' => sanitize_text_field($lang['name']),
                    'code' => sanitize_text_field($lang['code']),
                    'flag' => sanitize_text_field($lang['flag']),
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
        
        update_option($this->option_name, $sanitized_languages);
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
        .gst-language-state {
            display: inline-block;
            margin-left: 8px;
            padding: 2px 6px;
            background: #f0f0f1;
            border: 1px solid #c3c4c7;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 500;
            color: #50575e;
            text-decoration: none;
            vertical-align: middle;
        }
        .gst-language-state:hover {
            background: #e0e0e1;
            border-color: #8c8f94;
        }
        .gst-language-state:before {
            content: "🌐 ";
            margin-right: 2px;
        }
        /* Make it look more like WordPress post states */
        .post-state .gst-language-state {
            background: #f0f0f1;
            border: 1px solid #c3c4c7;
            color: #50575e;
        }
        .post-state .gst-language-state:hover {
            background: #e0e0e1;
            border-color: #8c8f94;
        }
        ';
    }
}

// Initialize the language manager
$GLOBALS['language_manager'] = new LanguageManager();

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
