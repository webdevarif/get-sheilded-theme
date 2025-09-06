<?php
namespace GetsheildedTheme\Inc\Classes;

use GetsheildedTheme\Inc\Traits\Singleton;

/**
 * Settings Feature
 * 
 * @package GetsheildedTheme\Classes
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Settings {
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
        // Admin styles are now loaded globally in the main theme class
        add_action('wp_enqueue_scripts', [$this, 'enqueue_google_fonts'], 1);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_css_variables'], 999);
        
        // AJAX handlers
        add_action('wp_ajax_gst_save_settings', [$this, 'ajax_save_settings']);
        add_action('wp_ajax_gst_get_settings', [$this, 'ajax_get_settings']);
    }
    
    
    /**
     * Render settings page
     */
    public function render_page() {
        $default_settings = $this->get_default_settings();
        $saved_settings = get_option('gst_theme_settings', []);
        
        // Debug: Log what we're merging
        error_log('Default settings languages: ' . print_r($default_settings['languages'] ?? 'No default languages', true));
        error_log('Saved settings from DB: ' . print_r($saved_settings, true));
        error_log('Saved settings languages: ' . print_r($saved_settings['languages'] ?? 'No saved languages', true));
        
        // Merge settings properly for nested arrays
        $settings = $this->merge_settings($default_settings, $saved_settings);
        
        // Auto-set first language as default ONLY if no default is set and languages exist
        // This should only happen on initial setup, not after user has made a choice
        if (empty($settings['languages']['default_language']) && !empty($settings['languages']['available'])) {
            $first_language_code = array_key_first($settings['languages']['available']);
            $settings['languages']['default_language'] = $first_language_code;
            error_log('Auto-setting first language as default: ' . $first_language_code);
        } else {
            error_log('Default language already set: ' . ($settings['languages']['default_language'] ?? 'empty'));
        }
        
        // Debug: Log what was loaded
        error_log('Language settings loaded: ' . print_r($settings['languages'] ?? 'No languages', true));
        
        // Handle form submissions
        if (isset($_POST['action'])) {
            $this->handle_form_submission();
        }
        
        // Get current tab from URL or default to colors
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'colors';
        ?>
        <div class="wrap">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h1 class="text-2xl font-bold text-gray-900">Theme Settings</h1>
                        <p class="text-gray-600">Customize your theme's appearance and functionality</p>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                            <a href="<?php echo admin_url('admin.php?page=get-sheilded-settings&tab=colors'); ?>" 
                               class="<?php echo $current_tab === 'colors' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                </svg>
                                Theme
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=get-sheilded-settings&tab=layout'); ?>" 
                               class="<?php echo $current_tab === 'layout' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                                </svg>
                                Layout
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=get-sheilded-settings&tab=languages'); ?>" 
                               class="<?php echo $current_tab === 'languages' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                </svg>
                                Languages
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="bg-white shadow rounded-lg">
                    <form method="POST" action="">
                        <?php wp_nonce_field('gst_save_settings', 'gst_settings_nonce'); ?>
                        <input type="hidden" name="action" value="gst_save_settings">
                        
                        <?php if ($current_tab === 'colors'): ?>
                    <!-- Color Palette Tab -->
                        <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Color Palette</h2>
                            
                            <!-- Side by Side Layout -->
                            <div class="flex gap-8">
                                <!-- Light Mode Colors -->
                                <div class="flex-1">
                                    <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                                        <span class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></span>
                                        Light Mode Colors
                                    </h3>
                                    <div class="space-y-6">
                                        <!-- Primary Colors -->
                                    <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-3">Primary Colors</h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Primary</label>
                                                    <input type="color" name="settings[colors][light][primary]" value="<?php echo esc_attr($settings['colors']['light']['primary'] ?? '#1e293b'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                    </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Primary Foreground</label>
                                                    <input type="color" name="settings[colors][light][primary-foreground]" value="<?php echo esc_attr($settings['colors']['light']['primary-foreground'] ?? '#f8fafc'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                    </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Secondary</label>
                                                    <input type="color" name="settings[colors][light][secondary]" value="<?php echo esc_attr($settings['colors']['light']['secondary'] ?? '#f1f5f9'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Secondary Foreground</label>
                                                    <input type="color" name="settings[colors][light][secondary-foreground]" value="<?php echo esc_attr($settings['colors']['light']['secondary-foreground'] ?? '#1e293b'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Background Colors -->
                                    <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-3">Background Colors</h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Background</label>
                                                    <input type="color" name="settings[colors][light][background]" value="<?php echo esc_attr($settings['colors']['light']['background'] ?? '#ffffff'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                    </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Foreground</label>
                                                    <input type="color" name="settings[colors][light][foreground]" value="<?php echo esc_attr($settings['colors']['light']['foreground'] ?? '#0f172a'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Card</label>
                                                    <input type="color" name="settings[colors][light][card]" value="<?php echo esc_attr($settings['colors']['light']['card'] ?? '#ffffff'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Card Foreground</label>
                                                    <input type="color" name="settings[colors][light][card-foreground]" value="<?php echo esc_attr($settings['colors']['light']['card-foreground'] ?? '#0f172a'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                </div>
                            </div>
                            
                                        <!-- Interactive Colors -->
                                    <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-3">Interactive Colors</h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Accent</label>
                                                    <input type="color" name="settings[colors][light][accent]" value="<?php echo esc_attr($settings['colors']['light']['accent'] ?? '#f1f5f9'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                    </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Accent Foreground</label>
                                                    <input type="color" name="settings[colors][light][accent-foreground]" value="<?php echo esc_attr($settings['colors']['light']['accent-foreground'] ?? '#1e293b'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Muted</label>
                                                    <input type="color" name="settings[colors][light][muted]" value="<?php echo esc_attr($settings['colors']['light']['muted'] ?? '#f1f5f9'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Muted Foreground</label>
                                                    <input type="color" name="settings[colors][light][muted-foreground]" value="<?php echo esc_attr($settings['colors']['light']['muted-foreground'] ?? '#64748b'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Popover Colors -->
                                    <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-3">Popover Colors</h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Popover</label>
                                                    <input type="color" name="settings[colors][light][popover]" value="<?php echo esc_attr($settings['colors']['light']['popover'] ?? '#ffffff'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                    </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Popover Foreground</label>
                                                    <input type="color" name="settings[colors][light][popover-foreground]" value="<?php echo esc_attr($settings['colors']['light']['popover-foreground'] ?? '#0f172a'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Destructive Colors -->
                                    <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-3">Destructive Colors</h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Destructive</label>
                                                    <input type="color" name="settings[colors][light][destructive]" value="<?php echo esc_attr($settings['colors']['light']['destructive'] ?? '#ef4444'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Destructive Foreground</label>
                                                    <input type="color" name="settings[colors][light][destructive-foreground]" value="<?php echo esc_attr($settings['colors']['light']['destructive-foreground'] ?? '#f8fafc'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                    </div>
                                </div>
                            </div>
                            
                                        <!-- Border & Input Colors -->
                                    <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-3">Border & Input Colors</h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Border</label>
                                                    <input type="color" name="settings[colors][light][border]" value="<?php echo esc_attr($settings['colors']['light']['border'] ?? '#e2e8f0'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                    </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Input</label>
                                                    <input type="color" name="settings[colors][light][input]" value="<?php echo esc_attr($settings['colors']['light']['input'] ?? '#e2e8f0'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Ring</label>
                                                    <input type="color" name="settings[colors][light][ring]" value="<?php echo esc_attr($settings['colors']['light']['ring'] ?? '#0f172a'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Dark Mode Colors -->
                                <div class="flex-1">
                                    <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                                        <span class="w-3 h-3 bg-gray-800 rounded-full mr-2"></span>
                                        Dark Mode Colors
                                    </h3>
                                    <div class="space-y-6">
                                        <!-- Primary Colors -->
                                    <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-3">Primary Colors</h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Primary</label>
                                                    <input type="color" name="settings[colors][dark][primary]" value="<?php echo esc_attr($settings['colors']['dark']['primary'] ?? '#f8fafc'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                    </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Primary Foreground</label>
                                                    <input type="color" name="settings[colors][dark][primary-foreground]" value="<?php echo esc_attr($settings['colors']['dark']['primary-foreground'] ?? '#1e293b'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Secondary</label>
                                                    <input type="color" name="settings[colors][dark][secondary]" value="<?php echo esc_attr($settings['colors']['dark']['secondary'] ?? '#2d3748'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Secondary Foreground</label>
                                                    <input type="color" name="settings[colors][dark][secondary-foreground]" value="<?php echo esc_attr($settings['colors']['dark']['secondary-foreground'] ?? '#f8fafc'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Background Colors -->
                                    <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-3">Background Colors</h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Background</label>
                                                    <input type="color" name="settings[colors][dark][background]" value="<?php echo esc_attr($settings['colors']['dark']['background'] ?? '#0f172a'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                    </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Foreground</label>
                                                    <input type="color" name="settings[colors][dark][foreground]" value="<?php echo esc_attr($settings['colors']['dark']['foreground'] ?? '#f8fafc'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Card</label>
                                                    <input type="color" name="settings[colors][dark][card]" value="<?php echo esc_attr($settings['colors']['dark']['card'] ?? '#0f172a'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Card Foreground</label>
                                                    <input type="color" name="settings[colors][dark][card-foreground]" value="<?php echo esc_attr($settings['colors']['dark']['card-foreground'] ?? '#f8fafc'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                            </div>
                        </div>
                        
                                        <!-- Interactive Colors -->
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-3">Interactive Colors</h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Accent</label>
                                                    <input type="color" name="settings[colors][dark][accent]" value="<?php echo esc_attr($settings['colors']['dark']['accent'] ?? '#2d3748'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Accent Foreground</label>
                                                    <input type="color" name="settings[colors][dark][accent-foreground]" value="<?php echo esc_attr($settings['colors']['dark']['accent-foreground'] ?? '#f8fafc'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Muted</label>
                                                    <input type="color" name="settings[colors][dark][muted]" value="<?php echo esc_attr($settings['colors']['dark']['muted'] ?? '#2d3748'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Muted Foreground</label>
                                                    <input type="color" name="settings[colors][dark][muted-foreground]" value="<?php echo esc_attr($settings['colors']['dark']['muted-foreground'] ?? '#a0aec0'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                        </div>
                    </div>

                                        <!-- Popover Colors -->
                                <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-3">Popover Colors</h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Popover</label>
                                                    <input type="color" name="settings[colors][dark][popover]" value="<?php echo esc_attr($settings['colors']['dark']['popover'] ?? '#0f172a'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Popover Foreground</label>
                                                    <input type="color" name="settings[colors][dark][popover-foreground]" value="<?php echo esc_attr($settings['colors']['dark']['popover-foreground'] ?? '#f8fafc'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Destructive Colors -->
                                <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-3">Destructive Colors</h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Destructive</label>
                                                    <input type="color" name="settings[colors][dark][destructive]" value="<?php echo esc_attr($settings['colors']['dark']['destructive'] ?? '#dc2626'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Destructive Foreground</label>
                                                    <input type="color" name="settings[colors][dark][destructive-foreground]" value="<?php echo esc_attr($settings['colors']['dark']['destructive-foreground'] ?? '#f8fafc'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Border & Input Colors -->
                                <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-3">Border & Input Colors</h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Border</label>
                                                    <input type="color" name="settings[colors][dark][border]" value="<?php echo esc_attr($settings['colors']['dark']['border'] ?? '#2d3748'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Input</label>
                                                    <input type="color" name="settings[colors][dark][input]" value="<?php echo esc_attr($settings['colors']['dark']['input'] ?? '#2d3748'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <label class="text-sm font-medium text-gray-700">Ring</label>
                                                    <input type="color" name="settings[colors][dark][ring]" value="<?php echo esc_attr($settings['colors']['dark']['ring'] ?? '#d1d5db'); ?>" class="w-[35px] h-[35px] border border-gray-300 rounded-lg">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            
                            <!-- Save Button for Colors Tab -->
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 mt-6">
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        Save Settings
                                    </button>
                    </div>
                </div>
                        </div>
                        <?php elseif ($current_tab === 'layout'): ?>
                        <!-- Layout Tab -->
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-6">Layout Settings</h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Container Max Width</label>
                                    <input type="text" name="settings[layout][containerMaxWidth]" value="<?php echo esc_attr($settings['layout']['containerMaxWidth']); ?>" placeholder="1200px" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Border Radius</label>
                                    <input type="text" name="settings[layout][borderRadius]" value="<?php echo esc_attr($settings['layout']['borderRadius']); ?>" placeholder="0.5rem" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Spacing</label>
                                    <input type="text" name="settings[layout][spacing]" value="<?php echo esc_attr($settings['layout']['spacing']); ?>" placeholder="1rem" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                            <!-- Save Button for Layout Tab -->
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 mt-6">
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        Save Settings
                            </button>
                        </div>
                    </div>
                        </div>
                        <?php elseif ($current_tab === 'languages'): ?>
                        <!-- Languages Tab -->
                        <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Language Settings</h2>
                        
                            <!-- Language Switcher Toggle -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                                <div class="flex items-center justify-between">
                            <div>
                                        <h3 class="text-lg font-medium text-gray-900">Language Switcher</h3>
                                        <p class="text-sm text-gray-600">Enable or disable the language switcher on your website</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="settings[languages][switcher_enabled]" value="1" 
                                               <?php checked($settings['languages']['switcher_enabled'] ?? false, true); ?>
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                                </div>
                        </div>

                            <!-- Available Languages -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Available Languages</h3>
                                    <button type="button" onclick="addLanguage()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Language
                                </button>
                            </div>
                            
                                <div id="languages-list" class="space-y-3">
                                    <?php 
                                    $languages = $settings['languages']['available'] ?? [];
                                    
                                    if (empty($languages)): ?>
                                    <div class="text-center py-8 text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-900 mb-2">No languages added yet</p>
                                        <p class="text-sm text-gray-600">Click "Add Language" to get started</p>
                                    </div>
                                    <?php else:
                                    foreach ($languages as $code => $lang): ?>
                                    <div class="language-item border border-gray-200 rounded-lg p-4" data-code="<?php echo esc_attr($code); ?>">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="text-2xl"><?php echo esc_html($lang['flag']); ?></div>
                                            <div>
                                                    <h4 class="font-medium text-gray-900"><?php echo esc_html($lang['name']); ?></h4>
                                                    <p class="text-sm text-gray-600"><?php echo esc_html(strtoupper($code)); ?></p>
                                            </div>
                                        </div>
                                            <div class="flex items-center space-x-4">
                                                <label class="flex items-center">
                                                    <input type="radio" name="settings[languages][default_language]" value="<?php echo esc_attr($code); ?>" 
                                                           <?php checked($settings['languages']['default_language'] ?? '', $code); ?>
                                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                                    <span class="ml-2 text-sm text-gray-700">Default</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="settings[languages][available][<?php echo esc_attr($code); ?>][active]" value="1" 
                                                           <?php checked($lang['active'], true); ?>
                                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                                </label>
                                                <button type="button" onclick="editLanguage('<?php echo esc_attr($code); ?>')" class="text-blue-600 hover:text-blue-800" title="Edit Language">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                                <button type="button" onclick="removeLanguage('<?php echo esc_attr($code); ?>')" class="text-red-600 hover:text-red-800" title="Remove Language">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                        <input type="hidden" name="settings[languages][available][<?php echo esc_attr($code); ?>][name]" value="<?php echo esc_attr($lang['name']); ?>">
                                        <input type="hidden" name="settings[languages][available][<?php echo esc_attr($code); ?>][code]" value="<?php echo esc_attr($lang['code']); ?>">
                                        <input type="hidden" name="settings[languages][available][<?php echo esc_attr($code); ?>][flag]" value="<?php echo esc_attr($lang['flag']); ?>">
                                </div>
                                    <?php 
                                    endforeach;
                                    endif; ?>
                    </div>
                </div>

                            <!-- Add Language Form (Hidden by default) -->
                            <div id="add-language-form" class="bg-white border border-gray-200 rounded-lg p-6 mb-6 hidden">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Language</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Language Name</label>
                                        <input type="text" id="new-language-name" placeholder="e.g., Spanish" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                                <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Language Code</label>
                                        <input type="text" id="new-language-code" placeholder="e.g., es" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Flag Emoji</label>
                                        <input type="text" id="new-language-flag" placeholder="e.g., 🇪🇸" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                    <div class="flex items-end">
                                        <button type="button" onclick="saveNewLanguage()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 mr-2">
                                            Save Language
                                </button>
                                        <button type="button" onclick="cancelAddLanguage()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                            
                            <!-- Save Button for Languages Tab -->
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 mt-6">
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        Save Settings
                                    </button>
                </div>
            </div>
        </div>

                        <!-- JavaScript for Language Management -->
                        <script>
                        function addLanguage() {
                            document.getElementById('add-language-form').classList.remove('hidden');
                        }
                        
                        function cancelAddLanguage() {
                            document.getElementById('add-language-form').classList.add('hidden');
                            document.getElementById('new-language-name').value = '';
                            document.getElementById('new-language-code').value = '';
                            document.getElementById('new-language-flag').value = '';
                            
                            // Reset form title and button
                            document.querySelector('#add-language-form h3').textContent = 'Add New Language';
                            const saveBtn = document.querySelector('#add-language-form button[onclick*="saveNewLanguage"]');
                            if (saveBtn) {
                                saveBtn.textContent = 'Save Language';
                                saveBtn.onclick = function() {
                                    saveNewLanguage();
                                };
                            }
                        }
                        
                        function saveNewLanguage() {
                            const name = document.getElementById('new-language-name').value.trim();
                            const code = document.getElementById('new-language-code').value.trim().toLowerCase();
                            const flag = document.getElementById('new-language-flag').value.trim();
                            
                            if (!name || !code || !flag) {
                                alert('Please fill in all fields');
                                return;
                            }
                            
                            // Check if this is the first language (should be default)
                            const existingLanguages = document.querySelectorAll('.language-item');
                            const isFirstLanguage = existingLanguages.length === 0;
                            
                            // Create new language item
                            const languagesList = document.getElementById('languages-list');
                            const newLanguageHtml = `
                                <div class="language-item border border-gray-200 rounded-lg p-4" data-code="${code}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="text-2xl">${flag}</div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">${name}</h4>
                                                <p class="text-sm text-gray-600">${code.toUpperCase()}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <label class="flex items-center">
                                                <input type="radio" name="settings[languages][default_language]" value="${code}" ${isFirstLanguage ? 'checked' : ''} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-gray-700">Default</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="checkbox" name="settings[languages][available][${code}][active]" value="1" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-gray-700">Active</span>
                                            </label>
                                            <button type="button" onclick="editLanguage('${code}')" class="text-blue-600 hover:text-blue-800" title="Edit Language">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button type="button" onclick="removeLanguage('${code}')" class="text-red-600 hover:text-red-800" title="Remove Language">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="settings[languages][available][${code}][name]" value="${name}">
                                    <input type="hidden" name="settings[languages][available][${code}][code]" value="${code}">
                                    <input type="hidden" name="settings[languages][available][${code}][flag]" value="${flag}">
                                </div>
                            `;
                            
                            languagesList.insertAdjacentHTML('beforeend', newLanguageHtml);
                            
                            // Update default language section
                            updateDefaultLanguageSection();
                            
                            cancelAddLanguage();
                        }
                        
                        function editLanguage(code) {
                            const languageItem = document.querySelector(`[data-code="${code}"]`);
                            if (languageItem) {
                                const name = languageItem.querySelector('input[name*="[name]"]').value;
                                const languageCode = languageItem.querySelector('input[name*="[code]"]').value;
                                const flag = languageItem.querySelector('input[name*="[flag]"]').value;
                                
                                // Fill the add language form with existing data
                                document.getElementById('new-language-name').value = name;
                                document.getElementById('new-language-code').value = languageCode;
                                document.getElementById('new-language-flag').value = flag;
                                
                                // Show the form
                                document.getElementById('add-language-form').classList.remove('hidden');
                                
                                // Remove the original language item
                                languageItem.remove();
                                
                                // Update the form title
                                document.querySelector('#add-language-form h3').textContent = 'Edit Language';
                                
                                // Update the save button
                                const saveBtn = document.querySelector('#add-language-form button[onclick="saveNewLanguage()"]');
                                saveBtn.textContent = 'Update Language';
                                saveBtn.onclick = function() {
                                    updateLanguage(code);
                                };
                            }
                        }
                        
                        function updateLanguage(oldCode) {
                            const name = document.getElementById('new-language-name').value.trim();
                            const code = document.getElementById('new-language-code').value.trim().toLowerCase();
                            const flag = document.getElementById('new-language-flag').value.trim();
                            
                            if (!name || !code || !flag) {
                                alert('Please fill in all fields');
                                return;
                            }
                            
                            // Create updated language item
                            const languagesList = document.getElementById('languages-list');
                            const updatedLanguageHtml = `
                                <div class="language-item border border-gray-200 rounded-lg p-4" data-code="${code}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="text-2xl">${flag}</div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">${name}</h4>
                                                <p class="text-sm text-gray-600">${code.toUpperCase()}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <label class="flex items-center">
                                                <input type="radio" name="settings[languages][default_language]" value="${code}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-gray-700">Default</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="checkbox" name="settings[languages][available][${code}][active]" value="1" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-gray-700">Active</span>
                                            </label>
                                            <button type="button" onclick="editLanguage('${code}')" class="text-blue-600 hover:text-blue-800" title="Edit Language">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button type="button" onclick="removeLanguage('${code}')" class="text-red-600 hover:text-red-800" title="Remove Language">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="settings[languages][available][${code}][name]" value="${name}">
                                    <input type="hidden" name="settings[languages][available][${code}][code]" value="${code}">
                                    <input type="hidden" name="settings[languages][available][${code}][flag]" value="${flag}">
                                </div>
                            `;
                            
                            languagesList.insertAdjacentHTML('beforeend', updatedLanguageHtml);
                            cancelAddLanguage();
                        }
                        
                        function removeLanguage(code) {
                            if (confirm('Are you sure you want to remove this language?')) {
                                const languageItem = document.querySelector(`[data-code="${code}"]`);
                                if (languageItem) {
                                    // Check if this was the default language
                                    const defaultRadio = document.querySelector(`input[name="settings[languages][default_language]"][value="${code}"]`);
                                    const wasDefault = defaultRadio && defaultRadio.checked;
                                    
                                    languageItem.remove();
                                    
                                    // If this was the default language, select the first remaining language as default
                                    if (wasDefault) {
                                        const remainingLanguages = document.querySelectorAll('.language-item');
                                        if (remainingLanguages.length > 0) {
                                            const firstLanguage = remainingLanguages[0];
                                            const firstLanguageCode = firstLanguage.getAttribute('data-code');
                                            const newDefaultRadio = document.querySelector(`input[name="settings[languages][default_language]"][value="${firstLanguageCode}"]`);
                                            if (newDefaultRadio) {
                                                newDefaultRadio.checked = true;
                                            }
                                        }
                                    }
                                    
                                    // Update default language section
                                    updateDefaultLanguageSection();
                                }
                            }
                        }
                        
                        function updateDefaultLanguageSection() {
                            // This function will be called when languages are added/removed
                            // The default language section will be updated on page refresh
                            // For now, we'll just trigger a page refresh to show the updated section
                            // In a more advanced implementation, we could dynamically update the HTML
                        }
                        </script>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Handle form submission
     */
    private function handle_form_submission() {
        if (!wp_verify_nonce($_POST['gst_settings_nonce'], 'gst_save_settings')) {
            wp_die('Security check failed');
        }
        
        if (isset($_POST['settings'])) {
            $settings = $_POST['settings'];
            
            // Debug: Log raw POST data
            error_log('Raw POST data: ' . print_r($_POST, true));
            error_log('Raw settings data: ' . print_r($settings, true));
            
            // Get existing settings first
            $existing_settings = get_option('gst_theme_settings', $this->get_default_settings());
            
            // Merge with existing settings to preserve other data
            $merged_settings = $this->merge_settings($existing_settings, $settings);
            
            // Sanitize the data properly for nested arrays
            $sanitized_settings = $this->sanitize_settings($merged_settings);
            
            // Debug: Log sanitized data
            error_log('Sanitized settings: ' . print_r($sanitized_settings, true));
            
            // Save to database - ensure proper serialization
            $result = update_option('gst_theme_settings', $sanitized_settings);
            
            // Debug: Log save result
            error_log('Update option result: ' . ($result ? 'SUCCESS' : 'FAILED'));
            
            // If update failed, try add_option
            if (!$result) {
                $add_result = add_option('gst_theme_settings', $sanitized_settings);
                error_log('Add option result: ' . ($add_result ? 'SUCCESS' : 'FAILED'));
            }
            
            // Verify what was actually saved
            $verify_saved = get_option('gst_theme_settings', []);
            error_log('Verification - what was actually saved: ' . print_r($verify_saved, true));
            
            // Debug: Log what was saved
            error_log('Language settings saved: ' . print_r($sanitized_settings['languages'] ?? 'No languages', true));
            error_log('Default language being saved: ' . ($sanitized_settings['languages']['default_language'] ?? 'NOT SET'));
            
            // Test: Check if languages exist
            if (isset($sanitized_settings['languages']['available']) && !empty($sanitized_settings['languages']['available'])) {
                $lang_count = count($sanitized_settings['languages']['available']);
                echo '<div class="notice notice-success"><p>Settings saved successfully! Languages: ' . $lang_count . '</p></div>';
            } else {
                echo '<div class="notice notice-warning"><p>Settings saved but no languages found in data.</p></div>';
            }
        }
    }
    
    /**
     * Convert HSL to Hex
     */
    private function hsl_to_hex($h, $s, $l) {
        $h = floatval($h);
        $s = floatval($s) / 100;
        $l = floatval($l) / 100;
        
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod(($h / 60), 2) - 1));
        $m = $l - ($c / 2);
        
        if ($h < 60) {
            $r = $c; $g = $x; $b = 0;
        } elseif ($h < 120) {
            $r = $x; $g = $c; $b = 0;
        } elseif ($h < 180) {
            $r = 0; $g = $c; $b = $x;
        } elseif ($h < 240) {
            $r = 0; $g = $x; $b = $c;
        } elseif ($h < 300) {
            $r = $x; $g = 0; $b = $c;
        } else {
            $r = $c; $g = 0; $b = $x;
        }
        
        $r = round(($r + $m) * 255);
        $g = round(($g + $m) * 255);
        $b = round(($b + $m) * 255);
        
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
    
    /**
     * Merge settings arrays recursively
     */
    private function merge_settings($default, $saved) {
        if (!is_array($default)) {
            return $saved;
        }
        
        if (!is_array($saved)) {
            return $default;
        }
        
        $merged = $default;
        foreach ($saved as $key => $value) {
            if (isset($merged[$key]) && is_array($merged[$key]) && is_array($value)) {
                $merged[$key] = $this->merge_settings($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        
        // Debug: Log merge result for languages
        if (isset($merged['languages'])) {
            error_log('Merge result for languages: ' . print_r($merged['languages'], true));
        }
        
        return $merged;
    }
    
    /**
     * Sanitize settings data recursively
     */
    private function sanitize_settings($data) {
        if (is_array($data)) {
            $sanitized = array();
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $sanitized[sanitize_key($key)] = $this->sanitize_settings($value);
                } else {
                    $sanitized[sanitize_key($key)] = sanitize_text_field($value);
                }
            }
            return $sanitized;
        }
        return sanitize_text_field($data);
    }
    
    /**
     * Get default settings
     */
    private function get_default_settings() {
        return array(
            'colors' => array(
                'light' => array(
                    'background' => '0 0% 100%',
                    'foreground' => '222.2 84% 4.9%',
                    'card' => '0 0% 100%',
                    'card-foreground' => '222.2 84% 4.9%',
                    'popover' => '0 0% 100%',
                    'popover-foreground' => '222.2 84% 4.9%',
                    'primary' => '222.2 47.4% 11.2%',
                    'primary-foreground' => '210 40% 98%',
                    'secondary' => '210 40% 96%',
                    'secondary-foreground' => '222.2 47.4% 11.2%',
                    'muted' => '210 40% 96%',
                    'muted-foreground' => '215.4 16.3% 46.9%',
                    'accent' => '210 40% 96%',
                    'accent-foreground' => '222.2 47.4% 11.2%',
                    'destructive' => '0 84.2% 60.2%',
                    'destructive-foreground' => '210 40% 98%',
                    'border' => '214.3 31.8% 91.4%',
                    'input' => '214.3 31.8% 91.4%',
                    'ring' => '222.2 84% 4.9%'
                ),
                'dark' => array(
                    'background' => '222.2 84% 4.9%',
                    'foreground' => '210 40% 98%',
                    'card' => '222.2 84% 4.9%',
                    'card-foreground' => '210 40% 98%',
                    'popover' => '222.2 84% 4.9%',
                    'popover-foreground' => '210 40% 98%',
                    'primary' => '210 40% 98%',
                    'primary-foreground' => '222.2 47.4% 11.2%',
                    'secondary' => '217.2 32.6% 17.5%',
                    'secondary-foreground' => '210 40% 98%',
                    'muted' => '217.2 32.6% 17.5%',
                    'muted-foreground' => '215 20.2% 65.1%',
                    'accent' => '217.2 32.6% 17.5%',
                    'accent-foreground' => '210 40% 98%',
                    'destructive' => '0 62.8% 30.6%',
                    'destructive-foreground' => '210 40% 98%',
                    'border' => '217.2 32.6% 17.5%',
                    'input' => '217.2 32.6% 17.5%',
                    'ring' => '212.7 26.8% 83.9%'
                )
            ),
            'typography' => array(
                'bodyFontFamily' => 'Inter, system-ui, sans-serif',
                'headingFontFamily' => 'Inter, system-ui, sans-serif',
                'h1Size' => '2.25rem',
                'h2Size' => '1.875rem',
                'h3Size' => '1.5rem',
                'h4Size' => '1.25rem',
                'h5Size' => '1.125rem',
                'h6Size' => '1rem',
                'bodySize' => '0.875rem',
                'lineHeight' => '1.5'
            ),
            'layout' => array(
                'containerMaxWidth' => '1300px',
                'borderRadius' => '0.5rem',
                'spacing' => '1rem'
            ),
                'languages' => array(
                    'switcher_enabled' => false,
                    'default_language' => '',
                    'available' => array()
                )
        );
    }
    
    /**
     * Enqueue Google Fonts
     */
    public function enqueue_google_fonts() {
        $settings = get_option('gst_theme_settings', $this->get_default_settings());
        $body_font = $settings['typography']['bodyFontFamily'] ?? 'Inter, system-ui, sans-serif';
        $heading_font = $settings['typography']['headingFontFamily'] ?? 'Inter, system-ui, sans-serif';
        
        // Extract font names
        $body_font_name = explode(',', $body_font)[0];
        $heading_font_name = explode(',', $heading_font)[0];
        
        // Create Google Fonts URL
        $fonts = array_unique(array($body_font_name, $heading_font_name));
        $font_url = 'https://fonts.googleapis.com/css2?family=' . implode('&family=', array_map('urlencode', $fonts)) . '&display=swap';
        
        wp_enqueue_style('gst-google-fonts', $font_url, array(), null);
    }
    
    /**
     * Enqueue CSS variables
     */
    public function enqueue_css_variables() {
        $settings = get_option('gst_theme_settings', $this->get_default_settings());
        
        $css = ':root {';
        
        // Light mode colors - strip hsl() wrapper to get just the values
        if (isset($settings['colors']['light'])) {
            foreach ($settings['colors']['light'] as $key => $value) {
                // Remove hsl() wrapper if present
                $clean_value = str_replace(['hsl(', ')'], '', $value);
                $css .= '--' . $key . ': ' . $clean_value . ';';
            }
        }
        
        // Typography
        if (isset($settings['typography']) && is_array($settings['typography'])) {
            foreach ($settings['typography'] as $key => $value) {
                $css .= '--' . $key . ': ' . $value . ';';
            }
        }
        
        // Layout
        if (isset($settings['layout']) && is_array($settings['layout'])) {
            foreach ($settings['layout'] as $key => $value) {
                $css .= '--' . $key . ': ' . $value . ';';
            }
        }
        
        $css .= '}';
        
        // Dark mode colors - strip hsl() wrapper to get just the values
        if (isset($settings['colors']['dark'])) {
            $css .= '.dark {';
            foreach ($settings['colors']['dark'] as $key => $value) {
                // Remove hsl() wrapper if present
                $clean_value = str_replace(['hsl(', ')'], '', $value);
                $css .= '--' . $key . ': ' . $clean_value . ';';
            }
            $css .= '}';
        }
        
        wp_add_inline_style('gst-frontend-main', $css);
    }
    
    /**
     * AJAX save settings
     */
    public function ajax_save_settings() {
        // This method is kept for compatibility but not used in pure PHP approach
        wp_die('This method is not used in pure PHP approach');
    }
    
    /**
     * AJAX get settings
     */
    public function ajax_get_settings() {
        // This method is kept for compatibility but not used in pure PHP approach
        wp_die('This method is not used in pure PHP approach');
    }
}