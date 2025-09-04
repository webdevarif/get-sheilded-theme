<?php
/**
 * Gutenberg Blocks Registry
 * 
 * @package GetsheildedTheme\Blocks
 * @since 1.0.0
 */

namespace GetsheildedTheme\Blocks;

class BlockRegistry {
    
    /**
     * Blocks to register
     * 
     * @var array
     */
    private $blocks = array(
        'header-1',
    );
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'register_blocks'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_assets'));
        add_filter('block_categories_all', array($this, 'add_custom_block_category'), 10, 2);
        
        // Include PHP block files
        $this->include_block_files();
    }
    
    /**
     * Include PHP block files
     */
    private function include_block_files() {
        $block_files = array(
            'header-1/block.php',
        );
        
        foreach ($block_files as $block_file) {
            $file_path = GST_THEME_PATH . '/src/gutenberg/blocks/' . $block_file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }
    
    /**
     * Add custom block category
     */
    public function add_custom_block_category($categories, $post) {
        return array_merge(
            $categories,
            array(
                array(
                    'slug'  => 'get-sheilded',
                    'title' => __('Get sheilded Blocks', 'get-sheilded-theme'),
                    'icon'  => 'shield',
                ),
            )
        );
    }
    
    /**
     * Register all blocks
     */
    public function register_blocks() {
        // Blocks are registered via JavaScript - no PHP registration needed
        // This ensures the JS blocks are properly loaded
    }
    
    /**
     * Enqueue block editor assets
     */
    public function enqueue_block_assets() {
        // Main blocks script
        wp_enqueue_script(
            'gst-blocks-editor',
            GST_THEME_URL . '/dist/gutenberg/blocks.js',
            array(
                'wp-blocks',
                'wp-i18n',
                'wp-element',
                'wp-components',
                'wp-block-editor',
                'wp-compose',
                'wp-data',
                'wp-api-fetch',
            ),
            GST_THEME_VERSION,
            true
        );
        
        // Block editor styles
        wp_enqueue_style(
            'gst-blocks-editor-style',
            GST_THEME_URL . '/dist/gutenberg/editor.css',
            array('wp-edit-blocks'),
            GST_THEME_VERSION
        );
        
        // Pass theme data to blocks
        wp_localize_script('gst-blocks-editor', 'gstBlocks', array(
            'themeUrl' => GST_THEME_URL,
            'apiUrl' => rest_url('wp/v2/'),
            'nonce' => wp_create_nonce('wp_rest'),
        ));
    }
}
?>
