<?php
/**
 * Theme functions and definitions
 * 
 * @package GetsheildedTheme
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load the autoloader FIRST
require_once get_template_directory() . '/inc/helpers/class-autoloader.php';

// Define theme constants
define('GST_THEME_VERSION', '1.0.0');
define('GST_THEME_PATH', get_template_directory());
define('GST_THEME_URL', get_template_directory_uri());

// Basic theme support
add_theme_support('post-thumbnails');
add_theme_support('title-tag');
add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption'));

// Basic theme support (removed Elementor conflicts)
add_theme_support('custom-header');
add_theme_support('custom-logo');

// Load template tags
require_once get_template_directory() . '/inc/helpers/template-tags.php';

// Load the main theme class
require_once get_template_directory() . '/inc/classes/class-get-sheilded-theme.php';

// Initialize the theme
\GetsheildedTheme\Inc\Classes\Get_Sheilded_Theme::get_instance();

// Theme activation hook
function gst_theme_activation() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'gst_theme_activation');

?>
