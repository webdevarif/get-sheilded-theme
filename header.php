<?php
/**
 * The header for our theme
 *
 * @package GetsheildedTheme
 */

use GetsheildedTheme\Admin\Templates;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <header id="masthead" class="site-header">
        <?php
        // Get custom header template
        $header_template = Templates::get_template('header');
        
        
        if ($header_template) {
            // Display custom header content
            ?>
            <div class="gst-custom-header" data-template-id="<?php echo esc_attr($header_template->ID); ?>">
                <?php
                // Process blocks directly without interfering with main content
                echo do_blocks($header_template->post_content);
                ?>
            </div>
            <?php
        } else {
            // Fallback to default header - Modern Tailwind design
            ?>
            <!-- Default header is loading -->
            <div class="gst-default-header">
                <nav class="bg-gradient-to-br from-indigo-500 to-purple-600 py-4 shadow-lg sticky top-0 z-50" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; min-height: 60px;">
                    <div class="container mx-auto px-4 flex justify-between items-center max-w-7xl">
                        <div class="site-branding">
                            <?php if (has_custom_logo()) : ?>
                                <div class="max-w-48"><?php the_custom_logo(); ?></div>
                            <?php else : ?>
                                <a href="<?php echo esc_url(home_url('/')); ?>" 
                                   class="site-logo text-white no-underline text-3xl font-bold flex items-center gap-2" 
                                   rel="home">
                                    <span class="bg-white/20 backdrop-blur p-2 rounded-lg text-2xl">🛡️</span>
                                    <?php bloginfo('name'); ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="nav-menu-wrapper flex items-center gap-8">
                            <div class="desktop-menu hidden md:flex gap-8">
                                <?php
                                $menu_items = wp_get_nav_menu_items('primary');
                                if ($menu_items) {
                                    foreach ($menu_items as $item) {
                                        echo '<a href="' . esc_url($item->url) . '" class="text-white no-underline px-4 py-2 rounded-md transition-all duration-300 font-medium hover:bg-white/20">' . esc_html($item->title) . '</a>';
                                    }
                                } else {
                                    // Default menu items if no menu is set
                                    echo '<a href="' . esc_url(home_url('/')) . '" class="text-white no-underline px-4 py-2 rounded-md transition-all duration-300 font-medium hover:bg-white/20">' . __('Home', 'get-sheilded-theme') . '</a>';
                                    echo '<a href="#" class="text-white no-underline px-4 py-2 rounded-md transition-all duration-300 font-medium hover:bg-white/20">' . __('About', 'get-sheilded-theme') . '</a>';
                                    echo '<a href="#" class="text-white no-underline px-4 py-2 rounded-md transition-all duration-300 font-medium hover:bg-white/20">' . __('Services', 'get-sheilded-theme') . '</a>';
                                    echo '<a href="#" class="text-white no-underline px-4 py-2 rounded-md transition-all duration-300 font-medium hover:bg-white/20">' . __('Contact', 'get-sheilded-theme') . '</a>';
                                }
                                ?>
                            </div>
                            
                            <button class="mobile-menu-toggle md:hidden bg-white/20 backdrop-blur border-0 text-white p-2 rounded-md text-xl cursor-pointer"
                                    aria-controls="primary-menu" 
                                    aria-expanded="false"
                                    onclick="toggleMobileMenu()">
                                <span class="sr-only"><?php esc_html_e('Toggle navigation', 'get-sheilded-theme'); ?></span>
                                ☰
                            </button>
                        </div>
                    </div>
                </nav>

                <div class="mobile-menu bg-gradient-to-br from-indigo-500 to-purple-600 border-t border-white/10 hidden" id="mobileMenu">
                    <div class="container mx-auto px-4 max-w-7xl">
                        <div class="flex flex-col gap-2 py-4">
                            <?php
                            $menu_items = wp_get_nav_menu_items('primary');
                            if ($menu_items) {
                                foreach ($menu_items as $item) {
                                    echo '<a href="' . esc_url($item->url) . '" class="text-white no-underline p-4 rounded-md transition-all duration-300 font-medium hover:bg-white/20">' . esc_html($item->title) . '</a>';
                                }
                            } else {
                                echo '<a href="' . esc_url(home_url('/')) . '" class="text-white no-underline p-4 rounded-md transition-all duration-300 font-medium hover:bg-white/20">' . __('Home', 'get-sheilded-theme') . '</a>';
                                echo '<a href="#" class="text-white no-underline p-4 rounded-md transition-all duration-300 font-medium hover:bg-white/20">' . __('About', 'get-sheilded-theme') . '</a>';
                                echo '<a href="#" class="text-white no-underline p-4 rounded-md transition-all duration-300 font-medium hover:bg-white/20">' . __('Services', 'get-sheilded-theme') . '</a>';
                                echo '<a href="#" class="text-white no-underline p-4 rounded-md transition-all duration-300 font-medium hover:bg-white/20">' . __('Contact', 'get-sheilded-theme') . '</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <script>
                function toggleMobileMenu() {
                    const mobileMenu = document.getElementById('mobileMenu');
                    const toggleButton = document.querySelector('.mobile-menu-toggle');
                    
                    if (mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.remove('hidden');
                        toggleButton.setAttribute('aria-expanded', 'true');
                    } else {
                        mobileMenu.classList.add('hidden');
                        toggleButton.setAttribute('aria-expanded', 'false');
                    }
                }
                </script>
            </div>
            <?php
        }
        ?>
    </header>

    <div id="content" class="site-content">
        <div class="container"><?php
