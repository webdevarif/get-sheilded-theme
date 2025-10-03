<?php
/**
 * Templates Feature - Streamlined
 *
 * @package GetsheildedTheme
 */

namespace GetsheildedTheme\Inc\Features;

use GetsheildedTheme\Inc\Traits\Singleton;

/**
 * Templates feature
 */
class Templates {

    use Singleton;

    /**
     * Constructor
     */
    protected function __construct() {
        $this->init();
    }

    /**
     * Initialize templates feature
     */
    private function init() {
        add_action('init', [$this, 'register_post_type']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_template_meta']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_editor_assets']);
    }

    /**
     * Register custom post type
     */
    public function register_post_type() {
        register_post_type('gst_theme_templates', [
            'labels' => [
                'name' => __('Templates', 'get-sheilded-theme'),
                'singular_name' => __('Template', 'get-sheilded-theme'),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'gst-theme-settings',
            'supports' => ['title'],
            'menu_icon' => 'dashicons-layout',
        ]);
    }

    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'gst_template_settings',
            __('T. Settings', 'get-sheilded-theme'),
            [$this, 'template_settings_meta_box'],
            'gst_theme_templates',
            'normal',
            'high'
        );
    }

    /**
     * Template settings meta box
     */
    public function template_settings_meta_box($post) {
        wp_nonce_field('gst_template_meta_nonce', 'gst_template_meta_nonce');
        
        // Get current values
        $template_type = get_post_meta($post->ID, 'gst_template_type', true);
        $display_option = get_post_meta($post->ID, 'gst_display_option', true);
        $selected_pages = get_post_meta($post->ID, 'gst_selected_pages', true);
        $exclude_pages = get_post_meta($post->ID, 'gst_exclude_pages', true);
        $priority = get_post_meta($post->ID, 'gst_priority', true);
        
        // Parse JSON data
        $selected_pages_array = is_string($selected_pages) ? json_decode($selected_pages, true) : [];
        $exclude_pages_array = is_string($exclude_pages) ? json_decode($exclude_pages, true) : [];
        
        if (!is_array($selected_pages_array)) $selected_pages_array = [];
        if (!is_array($exclude_pages_array)) $exclude_pages_array = [];
        ?>
        
        <!-- Hidden fields for form submission -->
        <input type="hidden" id="gst_template_type" name="gst_template_type" value="<?php echo esc_attr($template_type); ?>">
        <input type="hidden" id="gst_display_option" name="gst_display_option" value="<?php echo esc_attr($display_option); ?>">
        <input type="hidden" id="gst_selected_pages" name="gst_selected_pages" value="<?php echo esc_attr(json_encode($selected_pages_array)); ?>">
        <input type="hidden" id="gst_exclude_pages" name="gst_exclude_pages" value="<?php echo esc_attr(json_encode($exclude_pages_array)); ?>">
        <input type="hidden" id="gst_priority" name="gst_priority" value="<?php echo esc_attr($priority ?: 10); ?>">
        
        <!-- React component container -->
        <div id="gst-template-settings-react"></div>
        
        <?php
    }

    /**
     * Save template meta data
     */
    public function save_template_meta($post_id) {
        if (!isset($_POST['gst_template_meta_nonce']) || 
            !wp_verify_nonce($_POST['gst_template_meta_nonce'], 'gst_template_meta_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save meta fields
        $fields = ['gst_template_type', 'gst_display_option', 'gst_selected_pages', 'gst_exclude_pages', 'gst_priority'];
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }

    /**
     * Enqueue editor assets
     */
    public function enqueue_editor_assets() {
        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'gst_theme_templates') {
            return;
        }
        
        // Load WordPress scripts generated asset file
        $asset_file = GST_THEME_PATH . '/dist/admin/admin-react.asset.php';
        $asset = file_exists($asset_file) ? require($asset_file) : ['dependencies' => [], 'version' => GST_THEME_VERSION];

        // Enqueue our bundled React component
        wp_enqueue_script(
            'gst-admin-react',
            GST_THEME_URL . '/dist/admin/admin-react.js',
            $asset['dependencies'],
            $asset['version'],
            true
        );
    }

    /**
     * Get template based on conditions
     */
    public static function get_template($type, $current_page_id = null) {
        $args = [
            'post_type' => 'gst_theme_templates',
            'meta_query' => [
                [
                    'key' => 'gst_template_type',
                    'value' => $type,
                    'compare' => '='
                ]
            ],
            'meta_key' => 'gst_priority',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'posts_per_page' => 1
        ];

        $templates = get_posts($args);
        
        if (empty($templates)) {
            return null;
        }

        $template = $templates[0];
        $display_option = get_post_meta($template->ID, 'gst_display_option', true);

        // Check display conditions
        if ($display_option === 'entire_site') {
            $exclude_pages = get_post_meta($template->ID, 'gst_exclude_pages', true);
            $exclude_pages_array = is_string($exclude_pages) ? json_decode($exclude_pages, true) : [];
            
            if (in_array($current_page_id, $exclude_pages_array)) {
                return null;
            }
        } elseif ($display_option === 'specific_pages') {
            $selected_pages = get_post_meta($template->ID, 'gst_selected_pages', true);
            $selected_pages_array = is_string($selected_pages) ? json_decode($selected_pages, true) : [];
            
            if (!in_array($current_page_id, $selected_pages_array)) {
                return null;
            }
        }

        return $template;
    }
}
