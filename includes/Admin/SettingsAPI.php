<?php
/**
 * Settings API Handler
 * 
 * @package GetsheildedTheme\Admin
 * @since 1.0.0
 */

namespace GetsheildedTheme\Admin;

class SettingsAPI {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_google_fonts'), 1);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_css_variables'), 999);
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route('gst/v1', '/settings', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_settings'),
            'permission_callback' => array($this, 'check_permissions'),
        ));
        
        register_rest_route('gst/v1', '/settings', array(
            'methods' => 'POST',
            'callback' => array($this, 'update_settings'),
            'permission_callback' => array($this, 'check_permissions'),
            'args' => array(
                'settings' => array(
                    'required' => true,
                    'type' => 'object',
                    'sanitize_callback' => array($this, 'sanitize_settings'),
                ),
            ),
        ));
    }
    
    /**
     * Check user permissions
     */
    public function check_permissions() {
        return current_user_can('manage_options');
    }
    
    /**
     * Get theme settings
     */
    public function get_settings($request) {
        $default_settings = array(
            'colors' => array(
                'primary' => 'hsl(222.2, 84%, 4.9%)',
                'secondary' => 'hsl(210, 40%, 96%)',
                'accent' => 'hsl(210, 40%, 96%)',
                'background' => 'hsl(0, 0%, 100%)',
                'foreground' => 'hsl(222.2, 84%, 4.9%)',
                'card' => 'hsl(0, 0%, 100%)',
                'cardForeground' => 'hsl(222.2, 84%, 4.9%)',
                'popover' => 'hsl(0, 0%, 100%)',
                'popoverForeground' => 'hsl(222.2, 84%, 4.9%)',
                'muted' => 'hsl(210, 40%, 96%)',
                'mutedForeground' => 'hsl(215.4, 16.3%, 46.9%)',
                'border' => 'hsl(214.3, 31.8%, 91.4%)',
                'input' => 'hsl(214.3, 31.8%, 91.4%)',
                'ring' => 'hsl(222.2, 84%, 4.9%)',
                'destructive' => 'hsl(0, 84.2%, 60.2%)',
                'destructiveForeground' => 'hsl(210, 40%, 98%)',
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
                'lineHeight' => '1.5',
            ),
            'layout' => array(
                'containerMaxWidth' => '1200px',
                'borderRadius' => '0.5rem',
                'spacing' => '1rem',
            ),
            'languages' => array(),
        );
        
        $saved_settings = get_option('gst_theme_settings', array());
        $settings = wp_parse_args($saved_settings, $default_settings);
        
        // Load languages from language manager
        $languages = get_option('gst_simple_languages', array());
        $settings['languages'] = $languages;
        
        return rest_ensure_response(array(
            'success' => true,
            'settings' => $settings,
        ));
    }
    
    /**
     * Update theme settings
     */
    public function update_settings($request) {
        try {
            $settings = $request->get_param('settings');
            
            // Debug: Log what's being saved
            error_log('GST Settings being saved: ' . print_r($settings, true));
            
            if (empty($settings)) {
                error_log('GST: Settings are empty, returning error');
                return new \WP_Error(
                    'invalid_settings',
                    __('Invalid settings data', 'get-sheilded-theme'),
                    array('status' => 400)
                );
            }
            
            // Remove languages from settings as they are handled by dedicated language API
            if (isset($settings['languages'])) {
                unset($settings['languages']);
                error_log('GST: Removed languages from settings save (handled by language API)');
            }
            
            $updated = update_option('gst_theme_settings', $settings);
            
            // update_option returns false if the value hasn't changed, which is not an error
            if ($updated !== false) {
                error_log('GST: Settings saved successfully');
                return rest_ensure_response(array(
                    'success' => true,
                    'message' => __('Settings saved successfully', 'get-sheilded-theme'),
                ));
            } else {
                // Check if the option already exists and has the same value
                $existing_settings = get_option('gst_theme_settings', array());
                if ($existing_settings === $settings) {
                    error_log('GST: Settings unchanged, returning success');
                    return rest_ensure_response(array(
                        'success' => true,
                        'message' => __('Settings saved successfully', 'get-sheilded-theme'),
                    ));
                } else {
                    error_log('GST: Failed to save settings');
                    return new \WP_Error(
                        'save_failed',
                        __('Failed to save settings', 'get-sheilded-theme'),
                        array('status' => 500)
                    );
                }
            }
        } catch (Exception $e) {
            error_log('GST: Exception in update_settings: ' . $e->getMessage());
            error_log('GST: Stack trace: ' . $e->getTraceAsString());
            return new \WP_Error(
                'save_failed',
                __('Failed to save settings: ' . $e->getMessage(), 'get-sheilded-theme'),
                array('status' => 500)
            );
        }
    }
    
    /**
     * Sanitize settings data
     */
    public function sanitize_settings($settings) {
        $sanitized = array();
        
        // Sanitize colors
        if (isset($settings['colors']) && is_array($settings['colors'])) {
            $sanitized['colors'] = array();
            foreach ($settings['colors'] as $key => $value) {
                $sanitized['colors'][sanitize_key($key)] = sanitize_text_field($value);
            }
        }
        
        // Sanitize typography
        if (isset($settings['typography']) && is_array($settings['typography'])) {
            $sanitized['typography'] = array();
            foreach ($settings['typography'] as $key => $value) {
                $sanitized['typography'][sanitize_key($key)] = sanitize_text_field($value);
            }
        }
        
        // Sanitize layout
        if (isset($settings['layout']) && is_array($settings['layout'])) {
            $sanitized['layout'] = array();
            foreach ($settings['layout'] as $key => $value) {
                $sanitized['layout'][sanitize_key($key)] = sanitize_text_field($value);
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Enqueue Google Fonts
     */
    public function enqueue_google_fonts() {
        $settings = get_option('gst_theme_settings', array());
        
        if (empty($settings) || empty($settings['typography'])) {
            return;
        }
        
        $fonts_to_load = array();
        
        // Get body font (handle both old and new format)
        $body_font_family = $settings['typography']['bodyFontFamily'] ?? $settings['typography']['bodyfontfamily'] ?? '';
        if (!empty($body_font_family)) {
            $body_font = $this->extract_google_font($body_font_family);
            if ($body_font) {
                $fonts_to_load[] = $body_font;
            }
        }
        
        // Get heading font (handle both old and new format)
        $heading_font_family = $settings['typography']['headingFontFamily'] ?? $settings['typography']['headingfontfamily'] ?? '';
        if (!empty($heading_font_family)) {
            $heading_font = $this->extract_google_font($heading_font_family);
            if ($heading_font && !in_array($heading_font, $fonts_to_load)) {
                $fonts_to_load[] = $heading_font;
            }
        }
        
        // Load Google Fonts
        if (!empty($fonts_to_load)) {
            $font_families = implode('|', $fonts_to_load);
            $font_url = "https://fonts.googleapis.com/css2?family={$font_families}&display=swap";
            
            wp_enqueue_style(
                'gst-google-fonts',
                $font_url,
                array(),
                GST_THEME_VERSION
            );
        }
    }
    
    /**
     * Extract Google Font name from font family string
     */
    private function extract_google_font($font_family) {
        // Extract the first font name before the comma
        $font_name = explode(',', $font_family)[0];
        $font_name = trim($font_name);
        
        // Check if it's a Google Font (not system fonts)
        $system_fonts = array(
            'system-ui', 'Arial', 'Helvetica', 'Georgia', 'Times New Roman',
            'serif', 'sans-serif', 'monospace', 'cursive', 'fantasy'
        );
        
        if (in_array($font_name, $system_fonts)) {
            return null;
        }
        
        // Replace spaces with + for Google Fonts URL
        return str_replace(' ', '+', $font_name);
    }

    /**
     * Enqueue CSS variables as inline styles in frontend
     */
    public function enqueue_css_variables() {
        try {
            $settings = get_option('gst_theme_settings', array());
            
            
            // Only generate CSS if settings exist
            if (empty($settings)) {
                error_log('GST: No settings found, skipping CSS generation');
                return;
            }
        
        $css = ":root {\n";
        
        // Colors - Override Tailwind color variables
        if (!empty($settings['colors'])) {
            foreach ($settings['colors'] as $key => $value) {
                $css .= "  --gst-{$key}: {$value};\n";
            }
        }
        
        // Typography - Font families and sizes
        if (!empty($settings['typography'])) {
            // Separate font families for body and heading (handle both old and new format)
            $body_font = $settings['typography']['bodyFontFamily'] ?? $settings['typography']['bodyfontfamily'] ?? '';
            $heading_font = $settings['typography']['headingFontFamily'] ?? $settings['typography']['headingfontfamily'] ?? '';
            
            if ($body_font) {
                $css .= "  --gst-font-body: {$body_font};\n";
            }
            if ($heading_font) {
                $css .= "  --gst-font-heading: {$heading_font};\n";
            }
            
            // Font sizes using --gst prefix (handle both old and new format)
            $h1_size = $settings['typography']['h1Size'] ?? $settings['typography']['h1size'] ?? '';
            $h2_size = $settings['typography']['h2Size'] ?? $settings['typography']['h2size'] ?? '';
            $h3_size = $settings['typography']['h3Size'] ?? $settings['typography']['h3size'] ?? '';
            $h4_size = $settings['typography']['h4Size'] ?? $settings['typography']['h4size'] ?? '';
            $h5_size = $settings['typography']['h5Size'] ?? $settings['typography']['h5size'] ?? '';
            $h6_size = $settings['typography']['h6Size'] ?? $settings['typography']['h6size'] ?? '';
            $body_size = $settings['typography']['bodySize'] ?? $settings['typography']['bodysize'] ?? '';
            $line_height = $settings['typography']['lineHeight'] ?? $settings['typography']['lineheight'] ?? '';
            
            if ($h1_size) {
                $css .= "  --gst-text-5xl: {$h1_size};\n";
            }
            if ($h2_size) {
                $css .= "  --gst-text-4xl: {$h2_size};\n";
            }
            if ($h3_size) {
                $css .= "  --gst-text-3xl: {$h3_size};\n";
            }
            if ($h4_size) {
                $css .= "  --gst-text-2xl: {$h4_size};\n";
            }
            if ($h5_size) {
                $css .= "  --gst-text-xl: {$h5_size};\n";
            }
            if ($h6_size) {
                $css .= "  --gst-text-lg: {$h6_size};\n";
            }
            if ($body_size) {
                $css .= "  --gst-text-base: {$body_size};\n";
            }
            if ($line_height) {
                $css .= "  --gst-leading-normal: {$line_height};\n";
            }
        }
        
        // Layout - Override Tailwind container and spacing (handle both old and new format)
        if (!empty($settings['layout'])) {
            $container_max_width = $settings['layout']['containerMaxWidth'] ?? $settings['layout']['containermaxwidth'] ?? '';
            $border_radius = $settings['layout']['borderRadius'] ?? $settings['layout']['borderradius'] ?? '';
            
            if ($container_max_width) {
                $css .= "  --max-w-7xl: {$container_max_width};\n";
            }
            if ($border_radius) {
                $css .= "  --rounded-lg: {$border_radius};\n";
            }
        }
        
        $css .= "}\n";
        
        // Enqueue inline CSS
        wp_add_inline_style('gst-frontend-main', $css);
        } catch (Exception $e) {
            error_log('GST: Exception in enqueue_css_variables: ' . $e->getMessage());
            error_log('GST: Stack trace: ' . $e->getTraceAsString());
        }
    }
    
}
