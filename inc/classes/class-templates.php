<?php
namespace GetsheildedTheme\Inc\Classes;

use GetsheildedTheme\Inc\Traits\Singleton;

/**
 * Templates Feature
 * 
 * @package GetsheildedTheme\Classes
 * @since 1.0.0
 */

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
        $this->setup_hooks();
    }
    
    /**
     * Setup hooks
     */
    private function setup_hooks() {
        add_action('init', [$this, 'register_post_types']);
        add_action('init', [$this, 'register_taxonomies']);
    }
    
    /**
     * Initialize feature
     */
    public function init() {
        // Templates specific initialization
    }
    
    /**
     * Register post types
     */
    public function register_post_types() {
        // Register theme templates post type
        register_post_type('gst_theme_templates', [
            'labels' => [
                'name' => 'Theme Templates',
                'singular_name' => 'Template',
                'add_new' => 'Add New Template',
                'add_new_item' => 'Add New Template',
                'edit_item' => 'Edit Template',
                'new_item' => 'New Template',
                'view_item' => 'View Template',
                'search_items' => 'Search Templates',
                'not_found' => 'No templates found',
                'not_found_in_trash' => 'No templates found in trash',
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'supports' => ['title', 'editor', 'revisions'],
            'has_archive' => false,
            'rewrite' => false,
        ]);
    }
    
    /**
     * Register taxonomies
     */
    public function register_taxonomies() {
        // Register template categories
        register_taxonomy('gst_template_category', 'gst_theme_templates', [
            'labels' => [
                'name' => 'Template Categories',
                'singular_name' => 'Category',
                'search_items' => 'Search Categories',
                'all_items' => 'All Categories',
                'edit_item' => 'Edit Category',
                'update_item' => 'Update Category',
                'add_new_item' => 'Add New Category',
                'new_item_name' => 'New Category Name',
                'menu_name' => 'Categories',
            ],
            'hierarchical' => true,
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'rewrite' => false,
        ]);
    }
    
    /**
     * Get template by type
     * 
     * @param string $type Template type (header, footer, etc.)
     * @return WP_Post|null
     */
    public static function get_template($type) {
        $templates = get_posts([
            'post_type' => 'gst_theme_templates',
            'meta_key' => '_template_type',
            'meta_value' => $type,
            'posts_per_page' => 1,
            'post_status' => 'publish'
        ]);
        
        return !empty($templates) ? $templates[0] : null;
    }
    
    /**
     * Get all templates by type
     * 
     * @param string $type Template type
     * @return array
     */
    public static function get_templates($type) {
        return get_posts([
            'post_type' => 'gst_theme_templates',
            'meta_key' => '_template_type',
            'meta_value' => $type,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    }
}
