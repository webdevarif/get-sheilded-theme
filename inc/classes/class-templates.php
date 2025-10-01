<?php
/**
 * Templates Manager
 * 
 * @package GetsheildedTheme\Admin
 * @since 1.0.0
 */

namespace GetsheildedTheme\Inc\Classes;

use GetsheildedTheme\Inc\Traits\Singleton;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Templates {
    use Singleton;
    
    /**
     * Constructor
     */
    protected function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_meta_fields'));
        // Note: Meta boxes and save_post removed - using Gutenberg REST API instead
        add_action('wp_ajax_gst_get_template', array($this, 'get_template_ajax'));
        add_action('wp_ajax_gst_save_template', array($this, 'save_template_ajax'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));
        
        // Add custom columns to templates list
        add_filter('manage_gst_theme_templates_posts_columns', array($this, 'add_template_columns'));
        add_action('manage_gst_theme_templates_posts_custom_column', array($this, 'display_template_column_content'), 10, 2);
        add_filter('manage_edit-gst_theme_templates_sortable_columns', array($this, 'make_template_columns_sortable'));
    }
    
    /**
     * Register Templates post type
     */
    public function register_post_type() {
        register_post_type('gst_theme_templates', array(
            'labels' => array(
                'name' => __('Templates', 'get-sheilded-theme'),
                'singular_name' => __('Template', 'get-sheilded-theme'),
                'add_new' => __('Add New Template', 'get-sheilded-theme'),
                'add_new_item' => __('Add New Template', 'get-sheilded-theme'),
                'edit_item' => __('Edit Template', 'get-sheilded-theme'),
                'new_item' => __('New Template', 'get-sheilded-theme'),
                'view_item' => __('View Template', 'get-sheilded-theme'),
                'search_items' => __('Search Templates', 'get-sheilded-theme'),
                'not_found' => __('No templates found', 'get-sheilded-theme'),
                'not_found_in_trash' => __('No templates found in trash', 'get-sheilded-theme'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_rest' => true,
            'supports' => array('title', 'editor', 'custom-fields'),
            'capability_type' => 'page',
            'hierarchical' => false,
            'menu_icon' => 'dashicons-admin-page',
        ));
    }
    
    /**
     * Register meta fields for REST API
     */
    public function register_meta_fields() {
        register_post_meta('gst_theme_templates', 'gst_template_type', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'default' => '',
        ));
        
        register_post_meta('gst_theme_templates', 'gst_display_option', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'default' => '',
        ));
        
        register_post_meta('gst_theme_templates', 'gst_selected_pages', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'default' => '',
        ));
        
        register_post_meta('gst_theme_templates', 'gst_exclude_pages', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'default' => '',
        ));
        
        
        register_post_meta('gst_theme_templates', 'gst_priority', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'number',
            'default' => 10,
        ));
    }
    
    
    
    
    /**
     * Enqueue editor assets for the sidebar panel
     */
    public function enqueue_editor_assets() {
        $screen = get_current_screen();
        if ($screen && $screen->post_type === 'gst_theme_templates') {
            wp_enqueue_script(
                'gst-templates-sidebar',
                GST_THEME_URL . '/dist/gutenberg/templates.js',
                array(
                    'wp-plugins',
                    'wp-edit-post',
                    'wp-element',
                    'wp-components',
                    'wp-data',
                    'wp-editor',
                    'wp-i18n'
                ),
                GST_THEME_VERSION,
                true
            );
            
            wp_localize_script('gst-templates-sidebar', 'gstTemplates', array(
                'nonce' => wp_create_nonce('wp_rest'),
                'apiUrl' => rest_url('wp/v2/'),
            ));
        }
    }
    
    /**
     * Get template based on conditions
     */
    public static function get_template($type, $current_page_id = null) {
        if (!$current_page_id) {
            $current_page_id = get_queried_object_id();
        }
        
        $templates = get_posts(array(
            'post_type' => 'gst_theme_templates',
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query' => array(
                array(
                    'key' => 'gst_template_type',
                    'value' => $type,
                    'compare' => '='
                )
            ),
            'orderby' => 'meta_value_num',
            'meta_key' => 'gst_priority',
            'order' => 'ASC'
        ));
        
        // Find the first template that should be displayed
        foreach ($templates as $template) {
            if (self::should_display_template($template->ID, $current_page_id)) {
                return $template;
            }
        }
        
        return null;
    }
    
    /**
     * Check if template should be displayed on current page
     */
    public static function should_display_template($template_id, $current_page_id) {
        $display_option = get_post_meta($template_id, 'gst_display_option', true);
        $selected_pages = get_post_meta($template_id, 'gst_selected_pages', true);
        $exclude_pages = get_post_meta($template_id, 'gst_exclude_pages', true);
        
        // Parse JSON data
        $selected_pages = $selected_pages ? json_decode($selected_pages, true) : array();
        $exclude_pages = $exclude_pages ? json_decode($exclude_pages, true) : array();
        
        switch ($display_option) {
            case 'entire_site':
                // Check if current page is in exclude list
                if (!empty($exclude_pages)) {
                    foreach ($exclude_pages as $exclude_page) {
                        if (isset($exclude_page['value']) && $exclude_page['value'] == $current_page_id) {
                            return false;
                        }
                    }
                }
                return true;
                
            case 'specific_pages':
                // Check if current page is in selected list
                if (!empty($selected_pages)) {
                    foreach ($selected_pages as $selected_page) {
                        if (isset($selected_page['value']) && $selected_page['value'] == $current_page_id) {
                            return true;
                        }
                    }
                }
                return false;
                
            default:
                return false;
        }
    }
    
    /**
     * AJAX handler for getting template
     */
    public function get_template_ajax() {
        check_ajax_referer('wp_rest', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'get-sheilded-theme'));
        }
        
        $template_id = intval($_POST['template_id']);
        $template = get_post($template_id);
        
        if ($template && $template->post_type === 'gst_theme_templates') {
            wp_send_json_success(array(
                'title' => $template->post_title,
                'content' => $template->post_content,
                'template_type' => get_post_meta($template_id, 'gst_template_type', true),
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Template not found', 'get-sheilded-theme')
            ));
        }
    }
    
    /**
     * AJAX handler for saving template
     */
    public function save_template_ajax() {
        check_ajax_referer('wp_rest', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'get-sheilded-theme'));
        }
        
        $template_id = intval($_POST['template_id']);
        $title = sanitize_text_field($_POST['title']);
        $content = wp_kses_post($_POST['content']);
        $template_type = sanitize_text_field($_POST['template_type']);
        
        $post_data = array(
            'ID' => $template_id,
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => 'publish',
            'post_type' => 'gst_templates',
        );
        
        $result = wp_update_post($post_data);
        
        if ($result) {
            update_post_meta($template_id, 'gst_template_type', $template_type);
            
            wp_send_json_success(array(
                'message' => __('Template saved successfully', 'get-sheilded-theme')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to save template', 'get-sheilded-theme')
            ));
        }
    }
    
    /**
     * Add custom columns to templates list
     */
    public function add_template_columns($columns) {
        // Insert template type column after title
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'title') {
                $new_columns['template_type'] = __('Template Type', 'get-sheilded-theme');
                $new_columns['display_on'] = __('Display On', 'get-sheilded-theme');
            }
        }
        return $new_columns;
    }
    
    /**
     * Display content for custom columns
     */
    public function display_template_column_content($column, $post_id) {
        switch ($column) {
            case 'template_type':
                $template_type = get_post_meta($post_id, 'gst_template_type', true);
                if ($template_type) {
                    $type_labels = array(
                        'header' => __('Header', 'get-sheilded-theme'),
                        'footer' => __('Footer', 'get-sheilded-theme')
                    );
                    $label = isset($type_labels[$template_type]) ? $type_labels[$template_type] : ucfirst($template_type);
                    echo '<span class="template-type-badge template-type-' . esc_attr($template_type) . '">';
                    echo esc_html($label);
                    echo '</span>';
                } else {
                    echo '<span class="template-type-badge template-type-none">' . __('Not Set', 'get-sheilded-theme') . '</span>';
                }
                break;
                
            case 'display_on':
                $display_option = get_post_meta($post_id, 'gst_display_option', true);
                $selected_pages = get_post_meta($post_id, 'gst_selected_pages', true);
                $exclude_pages = get_post_meta($post_id, 'gst_exclude_pages', true);
                
                if ($display_option === 'entire_site') {
                    echo '<span class="display-option-badge display-entire-site">';
                    echo __('Entire Site', 'get-sheilded-theme');
                    if ($exclude_pages) {
                        $exclude_data = json_decode($exclude_pages, true);
                        if (is_array($exclude_data) && count($exclude_data) > 0) {
                            echo ' <small>(' . count($exclude_data) . ' ' . __('excluded', 'get-sheilded-theme') . ')</small>';
                        }
                    }
                    echo '</span>';
                } elseif ($display_option === 'specific_pages') {
                    echo '<span class="display-option-badge display-specific-pages">';
                    echo __('Specific Pages', 'get-sheilded-theme');
                    if ($selected_pages) {
                        $selected_data = json_decode($selected_pages, true);
                        if (is_array($selected_data) && count($selected_data) > 0) {
                            echo ' <small>(' . count($selected_data) . ' ' . __('pages', 'get-sheilded-theme') . ')</small>';
                        }
                    }
                    echo '</span>';
                } else {
                    echo '<span class="display-option-badge display-none">' . __('Not Set', 'get-sheilded-theme') . '</span>';
                }
                break;
        }
    }
    
    /**
     * Make custom columns sortable
     */
    public function make_template_columns_sortable($columns) {
        $columns['template_type'] = 'template_type';
        $columns['display_on'] = 'display_on';
        return $columns;
    }
}
