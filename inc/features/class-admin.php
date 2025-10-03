<?php
/**
 * Admin Feature - Streamlined
 *
 * @package GetsheildedTheme
 */

namespace GetsheildedTheme\Inc\Features;

use GetsheildedTheme\Inc\Traits\Singleton;

/**
 * Admin feature
 */
class Admin {

    use Singleton;

    /**
     * Constructor
     */
    protected function __construct() {
        $this->init();
    }

    /**
     * Initialize admin feature
     */
    private function init() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Theme Settings', 'get-sheilded-theme'),
            __('Theme Settings', 'get-sheilded-theme'),
            'manage_options',
            'gst-theme-settings',
            [$this, 'render_settings_page'],
            'dashicons-admin-customizer',
            30
        );

        // Add submenu pages
        add_submenu_page(
            'gst-theme-settings',
            __('General Settings', 'get-sheilded-theme'),
            __('General', 'get-sheilded-theme'),
            'manage_options',
            'gst-theme-settings',
            [$this, 'render_settings_page']
        );

        add_submenu_page(
            'gst-theme-settings',
            __('Templates', 'get-sheilded-theme'),
            __('Templates', 'get-sheilded-theme'),
            'manage_options',
            'edit.php?post_type=gst_theme_templates'
        );

        add_submenu_page(
            'gst-theme-settings',
            __('Blocks', 'get-sheilded-theme'),
            __('Blocks', 'get-sheilded-theme'),
            'manage_options',
            'gst-blocks',
            [$this, 'render_blocks_page']
        );
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
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
        <?php
    }

    /**
     * Render blocks page
     */
    public function render_blocks_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="gst-admin-container">
                <div class="gst-admin-header">
                    <h2><?php _e('Gutenberg Blocks', 'get-sheilded-theme'); ?></h2>
                    <p><?php _e('Manage and configure your custom Gutenberg blocks.', 'get-sheilded-theme'); ?></p>
                </div>

                <div class="gst-admin-content">
                    <div class="gst-admin-card">
                        <h3><?php _e('Available Blocks', 'get-sheilded-theme'); ?></h3>
                        <div class="gst-blocks-list">
                            <div class="gst-block-item">
                                <h4><?php _e('Header Block', 'get-sheilded-theme'); ?></h4>
                                <p><?php _e('Custom header block with advanced styling options.', 'get-sheilded-theme'); ?></p>
                                <span class="gst-block-status active"><?php _e('Active', 'get-sheilded-theme'); ?></span>
                            </div>
                            
                            <div class="gst-block-item">
                                <h4><?php _e('Hero Section', 'get-sheilded-theme'); ?></h4>
                                <p><?php _e('Full-width hero section with background image support.', 'get-sheilded-theme'); ?></p>
                                <span class="gst-block-status active"><?php _e('Active', 'get-sheilded-theme'); ?></span>
                            </div>
                            
                            <div class="gst-block-item">
                                <h4><?php _e('Feature Card', 'get-sheilded-theme'); ?></h4>
                                <p><?php _e('Card component with icon, title, and description.', 'get-sheilded-theme'); ?></p>
                                <span class="gst-block-status active"><?php _e('Active', 'get-sheilded-theme'); ?></span>
                            </div>
                            
                            <div class="gst-block-item">
                                <h4><?php _e('Testimonial', 'get-sheilded-theme'); ?></h4>
                                <p><?php _e('Quote block with author attribution.', 'get-sheilded-theme'); ?></p>
                                <span class="gst-block-status active"><?php _e('Active', 'get-sheilded-theme'); ?></span>
                            </div>
                            
                            <div class="gst-block-item">
                                <h4><?php _e('Call to Action', 'get-sheilded-theme'); ?></h4>
                                <p><?php _e('Centered CTA section with customizable styling.', 'get-sheilded-theme'); ?></p>
                                <span class="gst-block-status active"><?php _e('Active', 'get-sheilded-theme'); ?></span>
                            </div>
                            
                            <div class="gst-block-item">
                                <h4><?php _e('Pricing Table', 'get-sheilded-theme'); ?></h4>
                                <p><?php _e('Responsive pricing grid with multiple columns.', 'get-sheilded-theme'); ?></p>
                                <span class="gst-block-status active"><?php _e('Active', 'get-sheilded-theme'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="gst-admin-card">
                        <h3><?php _e('Block Styles', 'get-sheilded-theme'); ?></h3>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('Gradient Text', 'get-sheilded-theme'); ?></th>
                                <td><?php _e('Available for Heading blocks', 'get-sheilded-theme'); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Outline Button', 'get-sheilded-theme'); ?></th>
                                <td><?php _e('Available for Button blocks', 'get-sheilded-theme'); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Card Container', 'get-sheilded-theme'); ?></th>
                                <td><?php _e('Available for Group blocks', 'get-sheilded-theme'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <style>
        .gst-blocks-list {
            display: grid;
            gap: 15px;
        }
        
        .gst-block-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .gst-block-item h4 {
            margin: 0 0 5px 0;
            color: #1d2327;
        }
        
        .gst-block-item p {
            margin: 0;
            color: #646970;
            font-size: 13px;
        }
        
        .gst-block-status {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .gst-block-status.active {
            background: #d4edda;
            color: #155724;
        }
        </style>
        <?php
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets() {
        wp_enqueue_style(
            'gst-admin',
            GST_THEME_URL . '/dist/admin/admin.css',
            [],
            GST_THEME_VERSION
        );
    }
}
