<?php
/**
 * Admin Scripts Handler
 * 
 * @package GetShieldedTheme\Admin
 * @since 1.0.0
 */

namespace GetShieldedTheme\Admin;

class Scripts {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_filter('script_loader_tag', array($this, 'add_module_to_script'), 10, 3);
    }
    
    /**
     * Register admin scripts and styles
     */
    public function register_admin_scripts($hook) {
        // Debug: Log all hooks
        error_log('GST: Admin script hook called: ' . $hook);
        
        // Only load on our admin pages
        if (strpos($hook, 'get-shielded') === false) {
            error_log('GST: Hook does not contain get-shielded, skipping');
            return;
        }
        
        error_log('GST: Hook contains get-shielded, loading scripts');
        
        $css_url = GST_THEME_URL . '/dist/admin/index.css';
        $js_url = GST_THEME_URL . '/dist/admin/index.js';
        
        // Debug: Log the URLs being generated
        error_log('GST: CSS URL: ' . $css_url);
        error_log('GST: JS URL: ' . $js_url);
        error_log('GST: Theme URL: ' . GST_THEME_URL);
        error_log('GST: Hook: ' . $hook);
        
        // Admin React app styles (built with Vite)
        wp_enqueue_style(
            'gst-admin-app',
            $css_url,
            array(),
            GST_THEME_VERSION
        );
        
        // Admin React app script (built with Vite)
        wp_enqueue_script(
            'gst-admin-app',
            $js_url,
            array(),
            GST_THEME_VERSION,
            true
        );
        
        // Pass WordPress data to React app
        wp_localize_script('gst-admin-app', 'gstAdminData', array(
            'apiUrl' => rest_url('gst/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'currentUser' => wp_get_current_user(),
            'adminUrl' => admin_url(),
            'themeUrl' => GST_THEME_URL,
        ));
    }
    
    /**
     * Add admin menu pages
     */
    public function add_admin_menu() {
        // Main theme settings page - directly shows the React app
        add_menu_page(
            __('Get Shielded Theme', 'get-shielded-theme'),
            __('Get Shielded', 'get-shielded-theme'),
            'manage_options',
            'get-shielded-settings',
            array($this, 'render_admin_page'),
            'dashicons-shield',
            30
        );
        
        // Sub-menu pages
        add_submenu_page(
            'get-shielded-settings',
            __('Welcome', 'get-shielded-theme'),
            __('Welcome', 'get-shielded-theme'),
            'manage_options',
            'get-shielded-settings',
            array($this, 'render_admin_page')
        );
        
        add_submenu_page(
            'get-shielded-settings',
            __('Theme Settings', 'get-shielded-theme'),
            __('Theme', 'get-shielded-theme'),
            'manage_options',
            'get-shielded-theme',
            array($this, 'render_theme_redirect')
        );
        
        add_submenu_page(
            'get-shielded-settings',
            __('Block Manager', 'get-shielded-theme'),
            __('Blocks', 'get-shielded-theme'),
            'manage_options',
            'get-shielded-blocks',
            array($this, 'render_blocks_redirect')
        );
        
        // Add Templates submenu manually at the end
        add_submenu_page(
            'get-shielded-settings',
            __('Templates', 'get-shielded-theme'),
            __('Templates', 'get-shielded-theme'),
            'manage_options',
            'edit.php?post_type=gst_theme_templates',
            null
        );
        
    }
    
    /**
     * Render admin page container for React app
     */
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <div id="gst-admin-app"></div>
        </div>
        <?php
    }
    
    /**
     * Render theme redirect - redirects to main page with theme tab
     */
    public function render_theme_redirect() {
        ?>
        <script>
            // Redirect to main page with theme tab
            window.location.href = '<?php echo admin_url('admin.php?page=get-shielded-settings&tab=theme'); ?>';
        </script>
        <div class="wrap">
            <p>Redirecting to theme settings...</p>
        </div>
        <?php
    }
    
    /**
     * Render blocks redirect - redirects to main page with blocks tab
     */
    public function render_blocks_redirect() {
        ?>
        <script>
            // Redirect to main page with blocks tab
            window.location.href = '<?php echo admin_url('admin.php?page=get-shielded-settings&tab=blocks'); ?>';
        </script>
        <div class="wrap">
            <p>Redirecting to blocks...</p>
        </div>
        <?php
    }
    
    /**
     * Add type="module" to admin script
     */
    public function add_module_to_script($tag, $handle, $src) {
        if ('gst-admin-app' === $handle) {
            $tag = str_replace('<script ', '<script type="module" ', $tag);
        }
        return $tag;
    }
}
?>
