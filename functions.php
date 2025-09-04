<?php
/**
 * Theme functions and definitions
 * 
 * @package GetShieldedTheme
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

// Basic theme support (removed Elementor conflicts)
add_theme_support('custom-header');
add_theme_support('custom-logo');

// Autoloader for theme classes
spl_autoload_register(function ($class) {
    $prefix = 'GetShieldedTheme\\';
    $base_dir = GST_THEME_PATH . '/includes/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize theme
function gst_init_theme() {
    new \GetShieldedTheme\Core\Theme();
}
add_action('after_setup_theme', 'gst_init_theme');

// Theme activation hook
function gst_theme_activation() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'gst_theme_activation');

?>
