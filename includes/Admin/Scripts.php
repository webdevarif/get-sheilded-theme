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
        // Only load on our admin pages
        if (strpos($hook, 'get-shielded') === false) {
            return;
        }
        
        // Admin React app styles (built with Vite)
        wp_enqueue_style(
            'gst-admin-app',
            GST_THEME_URL . '/dist/admin/index.css',
            array(),
            GST_THEME_VERSION
        );
        
        // Admin React app script (built with Vite)
        wp_enqueue_script(
            'gst-admin-app',
            GST_THEME_URL . '/dist/admin/index.js',
            array(),
            GST_THEME_VERSION,
            true
        );
        
        // Pass WordPress data to React app
        wp_localize_script('gst-admin-app', 'gstAdminData', array(
            'apiUrl' => rest_url('wp/v2/'),
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
        // Main theme settings page
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
            __('Theme Settings', 'get-shielded-theme'),
            __('Settings', 'get-shielded-theme'),
            'manage_options',
            'get-shielded-settings',
            array($this, 'render_admin_page')
        );
        
        add_submenu_page(
            'get-shielded-settings',
            __('Block Manager', 'get-shielded-theme'),
            __('Blocks', 'get-shielded-theme'),
            'manage_options',
            'get-shielded-blocks',
            array($this, 'render_admin_page')
        );
        
        add_submenu_page(
            'get-shielded-settings',
            __('Templates', 'get-shielded-theme'),
            __('Templates', 'get-shielded-theme'),
            'manage_options',
            'edit.php?post_type=gst_theme_templates'
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
