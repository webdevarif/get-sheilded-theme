<?php
/**
 * Main theme class
 *
 * @package GetsheildedTheme
 */

namespace GetsheildedTheme\Inc\Classes;

use GetsheildedTheme\Inc\Traits\Singleton;
use GetsheildedTheme\Inc\Classes\Settings;
use GetsheildedTheme\Inc\Classes\Languages;
use GetsheildedTheme\Inc\Classes\Color_Palette;
use GetsheildedTheme\Inc\Classes\Layout_Settings;
use GetsheildedTheme\Inc\Classes\Frontend_Scripts;
use GetsheildedTheme\Inc\Classes\Gutenberg_Blocks;
use GetsheildedTheme\Inc\Classes\Templates;
use GetsheildedTheme\Inc\Classes\Block_Registry;
use GetsheildedTheme\Inc\Admin\Menu_Manager;

/**
 * Main theme class
 */
class Get_Sheilded_Theme {

	use Singleton;

	/**
	 * Protected class constructor to prevent direct object creation
	 */
	protected function __construct() {

		// Load class instances
		Frontend_Scripts::get_instance();
		Gutenberg_Blocks::get_instance();
		Templates::get_instance();
		Settings::get_instance();
		Languages::get_instance();
		Color_Palette::get_instance();
		Layout_Settings::get_instance();
		Block_Registry::get_instance();
		Menu_Manager::get_instance();

		$this->setup_hooks();
	}

	/**
	 * Setup hooks
	 */
	private function setup_hooks() {
		// Theme setup
		add_action( 'after_setup_theme', [ $this, 'setup_theme' ] );
		add_action( 'init', [ $this, 'init_theme' ] );
		
		// Admin styles
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );
	}

	/**
	 * Setup theme
	 */
	public function setup_theme() {
		// Add theme support
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'custom-logo' );
		add_theme_support( 'html5', [
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script'
		] );
		
		// Add Gutenberg support
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'responsive-embeds' );
	}


	/**
	 * Initialize theme
	 */
	public function init_theme() {
		// Theme initialization - all classes are already loaded in constructor
	}
	
	/**
	 * Enqueue admin styles
	 */
	public function enqueue_admin_styles() {
		// Enqueue Tailwind CSS for admin
		wp_enqueue_style(
			'gst-admin-tailwind',
			GST_THEME_URL . '/dist/admin/admin.css',
			[],
			GST_THEME_VERSION
		);
		
		// Enqueue Alpine.js for admin
		wp_enqueue_script(
			'alpinejs',
			'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js',
			[],
			'3.x.x',
			true
		);
		
		// No custom JavaScript needed - pure PHP forms
	}
	
}
