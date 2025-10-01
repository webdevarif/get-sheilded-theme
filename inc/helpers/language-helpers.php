<?php
/**
 * Language Helper Functions
 * 
 * @package GetsheildedTheme\Helpers
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get current language
 */
function gst_get_current_language() {
    return \GetsheildedTheme\Inc\Classes\Languages::get_current_language();
}

/**
 * Set current language
 */
function gst_set_current_language($language_code) {
    return \GetsheildedTheme\Inc\Classes\Languages::set_current_language($language_code);
}

/**
 * Get language switcher HTML
 */
function gst_language_switcher($args = []) {
    echo \GetsheildedTheme\Inc\Classes\Languages::get_language_switcher($args);
}

/**
 * Get posts by current language
 */
function gst_get_posts_by_current_language($post_type = 'post') {
    return \GetsheildedTheme\Inc\Classes\Languages::get_posts_by_current_language($post_type);
}

/**
 * Get posts by language
 */
function gst_get_posts_by_language($language_code, $post_type = 'post') {
    return \GetsheildedTheme\Inc\Classes\Languages::get_posts_by_language($language_code, $post_type);
}

/**
 * Get post language
 */
function gst_get_post_language($post_id) {
    return \GetsheildedTheme\Inc\Classes\Languages::get_post_language($post_id);
}

/**
 * Check if language switcher is enabled
 */
function gst_is_language_switcher_enabled() {
    return \GetsheildedTheme\Inc\Classes\Languages::is_switcher_enabled();
}

/**
 * Get all available languages
 */
function gst_get_available_languages() {
    return \GetsheildedTheme\Inc\Classes\Languages::get_languages();
}

/**
 * Get language URL
 */
function gst_get_language_url($language_code, $url = '') {
    return \GetsheildedTheme\Inc\Classes\Languages::get_language_url($language_code, $url);
}

/**
 * Check if post type supports language
 */
function gst_post_type_supports_language($post_type) {
    return \GetsheildedTheme\Inc\Classes\Languages::post_type_supports_language($post_type);
}

/**
 * Get language supported post types
 */
function gst_get_language_supported_post_types() {
    return \GetsheildedTheme\Inc\Classes\Languages::get_language_supported_post_types();
}
