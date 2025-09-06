<?php
namespace GetsheildedTheme\Inc\Admin;

use GetsheildedTheme\Inc\Traits\Singleton;

/**
 * Dynamic Menu Manager
 * 
 * @package GetsheildedTheme\Admin
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Menu_Manager {
    use Singleton;
    
    /**
     * Menu configuration
     * 
     * @var array
     */
    private $menus = [
        'main' => [
            'page_title' => 'Get sheilded Theme',
            'menu_title' => 'Get sheilded',
            'capability' => 'manage_options',
            'menu_slug' => 'get-sheilded-welcome',
            'icon' => 'dashicons-shield',
            'position' => 30,
            'callback' => 'render_welcome_page',
            'submenus' => [
                'welcome' => [
                    'page_title' => 'Welcome',
                    'menu_title' => 'Welcome',
                    'capability' => 'manage_options',
                    'menu_slug' => 'get-sheilded-welcome',
                    'callback' => 'render_welcome_page',
                ],
                'blocks' => [
                    'page_title' => 'Block Manager',
                    'menu_title' => 'Blocks',
                    'capability' => 'manage_options',
                    'menu_slug' => 'get-sheilded-blocks',
                    'callback' => 'render_blocks_page',
                ],
                'templates' => [
                    'page_title' => 'Templates',
                    'menu_title' => 'Templates',
                    'capability' => 'manage_options',
                    'menu_slug' => 'edit.php?post_type=gst_theme_templates',
                    'callback' => null,
                ],
                'settings' => [
                    'page_title' => 'Settings',
                    'menu_title' => 'Settings',
                    'capability' => 'manage_options',
                    'menu_slug' => 'get-sheilded-settings',
                    'callback' => 'render_settings_page',
                ],
            ]
        ]
    ];
    
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
        add_action('admin_menu', [$this, 'register_menus']);
    }
    
    /**
     * Register all menus
     */
    public function register_menus() {
        foreach ($this->menus as $menu_key => $menu_config) {
            $this->register_menu($menu_key, $menu_config);
        }
    }
    
    /**
     * Register a single menu
     * 
     * @param string $menu_key
     * @param array $menu_config
     */
    private function register_menu($menu_key, $menu_config) {
        // Register main menu
        add_menu_page(
            $menu_config['page_title'],
            $menu_config['menu_title'],
            $menu_config['capability'],
            $menu_config['menu_slug'],
            [$this, $menu_config['callback']],
            $menu_config['icon'],
            $menu_config['position']
        );
        
        // Register submenus
        if (isset($menu_config['submenus']) && is_array($menu_config['submenus'])) {
            foreach ($menu_config['submenus'] as $submenu_key => $submenu_config) {
                // Handle direct URL submenus (like post type pages)
                if (strpos($submenu_config['menu_slug'], 'edit.php') === 0) {
                    add_submenu_page(
                        $menu_config['menu_slug'],
                        $submenu_config['page_title'],
                        $submenu_config['menu_title'],
                        $submenu_config['capability'],
                        $submenu_config['menu_slug'],
                        null
                    );
                } else {
                    add_submenu_page(
                        $menu_config['menu_slug'],
                        $submenu_config['page_title'],
                        $submenu_config['menu_title'],
                        $submenu_config['capability'],
                        $submenu_config['menu_slug'],
                        [$this, $submenu_config['callback']]
                    );
                }
            }
        }
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (class_exists('GetsheildedTheme\Inc\Classes\Settings')) {
            \GetsheildedTheme\Inc\Classes\Settings::get_instance()->render_page();
        }
    }
    
    /**
     * Render welcome page
     */
    public function render_welcome_page() {
        ?>
        <div class="wrap">
            <h1>Welcome to Get Sheilded Theme</h1>
            <p>This is the welcome page for the Get Sheilded Theme.</p>
        </div>
        <?php
    }
    
    /**
     * Render blocks page
     */
    public function render_blocks_page() {
        if (class_exists('GetsheildedTheme\Inc\Classes\Block_Registry')) {
            // \GetsheildedTheme\Inc\Classes\Block_Registry::get_instance()->render_admin_page();
            ?>
            <div class="wrap">
                <h1>Blocks</h1>
                <p>This is the blocks page for the Get Sheilded Theme.</p>
            </div>
            <?php
        }
    }
    
    
    /**
     * Add menu dynamically
     * 
     * @param string $menu_key
     * @param array $menu_config
     */
    public function add_menu($menu_key, $menu_config) {
        $this->menus[$menu_key] = $menu_config;
    }
    
    /**
     * Add submenu dynamically
     * 
     * @param string $parent_menu
     * @param string $submenu_key
     * @param array $submenu_config
     */
    public function add_submenu($parent_menu, $submenu_key, $submenu_config) {
        if (isset($this->menus[$parent_menu])) {
            $this->menus[$parent_menu]['submenus'][$submenu_key] = $submenu_config;
        }
    }
    
    /**
     * Remove menu
     * 
     * @param string $menu_key
     */
    public function remove_menu($menu_key) {
        unset($this->menus[$menu_key]);
    }
    
    /**
     * Remove submenu
     * 
     * @param string $parent_menu
     * @param string $submenu_key
     */
    public function remove_submenu($parent_menu, $submenu_key) {
        if (isset($this->menus[$parent_menu]['submenus'][$submenu_key])) {
            unset($this->menus[$parent_menu]['submenus'][$submenu_key]);
        }
    }
}
