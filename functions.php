<?php
/**
 * Theme functions and definitions - Minimal Working Version
 * 
 * @package GetsheildedTheme
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('GST_THEME_VERSION', '1.0.0');
define('GST_THEME_PATH', get_template_directory());
define('GST_THEME_URL', get_template_directory_uri());

// Basic theme support
add_theme_support('post-thumbnails');
add_theme_support('title-tag');
add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption'));
add_theme_support('custom-header');
add_theme_support('custom-logo');

// Load template tags
require_once get_template_directory() . '/inc/helpers/template-tags.php';

// Simple admin menu setup
add_action('admin_menu', 'gst_add_admin_menu');

function gst_add_admin_menu() {
    add_menu_page(
        __('Theme Settings', 'get-sheilded-theme'),
        __('Theme Settings', 'get-sheilded-theme'),
        'manage_options',
        'gst-theme-settings',
        'gst_render_settings_page',
        'dashicons-admin-customizer',
        30
    );

    add_submenu_page(
        'gst-theme-settings',
        __('General Settings', 'get-sheilded-theme'),
        __('General', 'get-sheilded-theme'),
        'manage_options',
        'gst-theme-settings',
        'gst_render_settings_page'
    );

    add_submenu_page(
        'gst-theme-settings',
        __('Templates', 'get-sheilded-theme'),
        __('Templates', 'get-sheilded-theme'),
        'manage_options',
        'edit.php?post_type=gst_theme_templates'
    );
}

function gst_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <div class="gst-admin-container">
            <div class="gst-admin-header">
                <h2><?php _e('Theme Configuration', 'get-sheilded-theme'); ?></h2>
                <p><?php _e('Configure your theme settings and features.', 'get-sheilded-theme'); ?></p>
            </div>

            <div class="gst-admin-content">
                <div class="gst-admin-card">
                    <h3><?php _e('Quick Actions', 'get-sheilded-theme'); ?></h3>
                    <div class="gst-admin-actions">
                        <a href="<?php echo admin_url('post-new.php?post_type=gst_theme_templates'); ?>" class="button button-primary">
                            <?php _e('Create Template', 'get-sheilded-theme'); ?>
                        </a>
                        <a href="<?php echo admin_url('edit.php?post_type=gst_theme_templates'); ?>" class="button">
                            <?php _e('Manage Templates', 'get-sheilded-theme'); ?>
                        </a>
                    </div>
                </div>

                <div class="gst-admin-card">
                    <h3><?php _e('Theme Information', 'get-sheilded-theme'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Version', 'get-sheilded-theme'); ?></th>
                            <td><?php echo GST_THEME_VERSION; ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Features', 'get-sheilded-theme'); ?></th>
                            <td>
                                <ul>
                                    <li><?php _e('Custom Templates', 'get-sheilded-theme'); ?></li>
                                    <li><?php _e('React Admin Components', 'get-sheilded-theme'); ?></li>
                                    <li><?php _e('Gutenberg Blocks', 'get-sheilded-theme'); ?></li>
                                    <li><?php _e('Modern Build System', 'get-sheilded-theme'); ?></li>
                                </ul>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <style>
        .gst-admin-container {
            max-width: 1200px;
        }
        
        .gst-admin-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        
        .gst-admin-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .gst-admin-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .gst-admin-card h3 {
            margin-top: 0;
            color: #1d2327;
        }
        
        .gst-admin-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .gst-admin-actions .button {
            margin: 0;
        }
        </style>
    </div>
    <?php
}

// Register Templates post type
add_action('init', 'gst_register_templates_post_type');

function gst_register_templates_post_type() {
    register_post_type('gst_theme_templates', array(
        'labels' => array(
            'name' => __('Templates', 'get-sheilded-theme'),
            'singular_name' => __('Template', 'get-sheilded-theme'),
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false, // Don't show in menu automatically
        'show_in_rest' => true, // Enable Gutenberg editor
        'supports' => array('title', 'editor'),
        'menu_icon' => 'dashicons-layout',
    ));
}

// Add Gutenberg support
add_action('enqueue_block_editor_assets', 'gst_enqueue_gutenberg_assets');

function gst_enqueue_gutenberg_assets() {
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'gst_theme_templates') {
        // Load WordPress scripts generated asset file for blocks
        $asset_file = GST_THEME_PATH . '/build/gutenberg/blocks.asset.php';
        $asset = file_exists($asset_file) ? require($asset_file) : ['dependencies' => [], 'version' => GST_THEME_VERSION];

        // Enqueue Gutenberg blocks with proper dependencies
        wp_enqueue_script(
            'gst-gutenberg-blocks',
            GST_THEME_URL . '/build/gutenberg/blocks.js',
            $asset['dependencies'],
            $asset['version'],
            true
        );
        
        // Use enqueue_block_assets for styles to avoid iframe issues
        wp_enqueue_style(
            'gst-gutenberg-blocks-style',
            GST_THEME_URL . '/build/gutenberg/blocks.css',
            array('wp-edit-blocks'),
            GST_THEME_VERSION
        );
    }
}

// Add template settings meta box
add_action('add_meta_boxes', 'gst_add_template_meta_boxes');

function gst_add_template_meta_boxes() {
    add_meta_box(
        'gst_template_settings_meta_box',
        __('T. Settings', 'get-sheilded-theme'),
        'gst_template_settings_meta_box',
        'gst_theme_templates',
        'side',
        'high'
    );
}

function gst_template_settings_meta_box($post) {
    // Add nonce for security
    wp_nonce_field('gst_template_meta_nonce', 'gst_template_meta_nonce');

    // Get current values for hidden fields (React will sync with these)
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

    <!-- Hidden fields for form submission (React will sync with these) -->
    <input type="hidden" id="gst_template_type" name="gst_template_type" value="<?php echo esc_attr($template_type); ?>">
    <input type="hidden" id="gst_display_option" name="gst_display_option" value="<?php echo esc_attr($display_option); ?>">
    <input type="hidden" id="gst_selected_pages" name="gst_selected_pages" value="<?php echo esc_attr(json_encode($selected_pages_array)); ?>">
    <input type="hidden" id="gst_exclude_pages" name="gst_exclude_pages" value="<?php echo esc_attr(json_encode($exclude_pages_array)); ?>">
    <input type="hidden" id="gst_priority" name="gst_priority" value="<?php echo esc_attr($priority ?: 10); ?>">

    <!-- React component container -->
    <div id="gst-template-settings-react"></div>

    <?php
}

// Save template meta data
add_action('save_post', 'gst_save_template_meta');

function gst_save_template_meta($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['gst_template_meta_nonce'])) {
        return $post_id;
    }

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['gst_template_meta_nonce'], 'gst_template_meta_nonce')) {
        return $post_id;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check the user's permissions.
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    // Sanitize and save the data.
    $fields = ['gst_template_type', 'gst_display_option', 'gst_selected_pages', 'gst_exclude_pages', 'gst_priority'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = $_POST[$field];
            if ($field === 'gst_selected_pages' || $field === 'gst_exclude_pages') {
                // These are JSON strings from React, decode, sanitize, then re-encode
                $decoded_value = json_decode(stripslashes($value), true);
                $sanitized_array = array_map('intval', (array) $decoded_value);
                update_post_meta($post_id, $field, json_encode($sanitized_array));
            } elseif ($field === 'gst_priority') {
                update_post_meta($post_id, $field, absint($value));
            } else {
                update_post_meta($post_id, $field, sanitize_text_field($value));
            }
        }
    }
}

// Enqueue React components for template settings
add_action('admin_enqueue_scripts', 'gst_enqueue_template_react_assets');

function gst_enqueue_template_react_assets() {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'gst_theme_templates') {
        return;
    }

    // Load WordPress scripts generated asset file
    $asset_file = GST_THEME_PATH . '/build/admin/template-settings.asset.php';
    $asset = file_exists($asset_file) ? require($asset_file) : ['dependencies' => [], 'version' => GST_THEME_VERSION];

    // Enqueue our built template settings component
    wp_enqueue_script(
        'gst-template-settings',
        GST_THEME_URL . '/build/admin/template-settings.js',
        $asset['dependencies'],
        $asset['version'],
        true
    );
}


// Theme activation hook
function gst_theme_activation() {
    flush_rewrite_rules();
    // Clear any cached autoloader
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
}
add_action('after_switch_theme', 'gst_theme_activation');

// Clear autoloader cache on admin init
add_action('admin_init', function() {
    if (isset($_GET['clear_cache']) && current_user_can('manage_options')) {
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        wp_redirect(admin_url('admin.php?page=gst-theme-settings&cache_cleared=1'));
        exit;
    }
});

?>
