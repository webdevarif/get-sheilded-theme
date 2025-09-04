        </div> <!-- .container -->
    </div> <!-- #content -->

    <footer id="colophon" class="site-footer">
        <?php
        use GetShieldedTheme\Admin\Templates;
        
        // Get custom footer template
        $footer_template = Templates::get_template('footer');
        
        if ($footer_template) {
            // Display custom footer content
            ?>
            <div class="gst-custom-footer" data-template-id="<?php echo esc_attr($footer_template->ID); ?>">
                <?php
                // Process blocks directly without interfering with main content
                echo do_blocks($footer_template->post_content);
                ?>
            </div>
            <?php
        } else {
            // Fallback to default footer - Modern Tailwind design
            ?>
            <div class="gst-default-footer bg-gradient-to-br from-slate-800 to-blue-600 text-white mt-16">
                <div class="footer-main py-12 border-b border-white/10">
                    <div class="container mx-auto px-4 max-w-7xl">
                        <div class="footer-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                            <div class="footer-widget">
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="bg-white/10 p-2 rounded-lg text-2xl">🛡️</span>
                                    <h3 class="m-0 text-2xl font-bold"><?php bloginfo('name'); ?></h3>
                                </div>
                                <p class="text-white/80 leading-relaxed mb-6">
                                    <?php 
                                    $description = get_bloginfo('description');
                                    echo $description ? esc_html($description) : esc_html__('Your trusted security partner. Protecting what matters most with advanced solutions and reliable service.', 'get-shielded-theme');
                                    ?>
                                </p>
                                <div class="social-links flex gap-4">
                                    <a href="#" class="inline-flex items-center justify-center w-10 h-10 bg-white/10 backdrop-blur rounded-full text-white no-underline transition-all duration-300 hover:bg-white/20 hover:-translate-y-1">📘</a>
                                    <a href="#" class="inline-flex items-center justify-center w-10 h-10 bg-white/10 backdrop-blur rounded-full text-white no-underline transition-all duration-300 hover:bg-white/20 hover:-translate-y-1">🐦</a>
                                    <a href="#" class="inline-flex items-center justify-center w-10 h-10 bg-white/10 backdrop-blur rounded-full text-white no-underline transition-all duration-300 hover:bg-white/20 hover:-translate-y-1">💼</a>
                                </div>
                            </div>

                            <div class="footer-widget">
                                <h3 class="m-0 mb-6 text-xl font-semibold"><?php esc_html_e('Quick Links', 'get-shielded-theme'); ?></h3>
                                <div class="footer-menu flex flex-col gap-3">
                                    <?php
                                    $footer_menu_items = wp_get_nav_menu_items('footer');
                                    if ($footer_menu_items) {
                                        foreach ($footer_menu_items as $item) {
                                            echo '<a href="' . esc_url($item->url) . '" class="text-white/80 no-underline transition-all duration-300 py-1 hover:text-white hover:pl-2">' . esc_html($item->title) . '</a>';
                                        }
                                    } else {
                                        // Default footer links
                                        echo '<a href="' . esc_url(home_url('/')) . '" class="text-white/80 no-underline transition-all duration-300 py-1 hover:text-white hover:pl-2">' . __('Home', 'get-shielded-theme') . '</a>';
                                        echo '<a href="#" class="text-white/80 no-underline transition-all duration-300 py-1 hover:text-white hover:pl-2">' . __('About Us', 'get-shielded-theme') . '</a>';
                                        echo '<a href="#" class="text-white/80 no-underline transition-all duration-300 py-1 hover:text-white hover:pl-2">' . __('Our Services', 'get-shielded-theme') . '</a>';
                                        echo '<a href="#" class="text-white/80 no-underline transition-all duration-300 py-1 hover:text-white hover:pl-2">' . __('Privacy Policy', 'get-shielded-theme') . '</a>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="footer-widget">
                                <h3 class="m-0 mb-6 text-xl font-semibold"><?php esc_html_e('Contact Info', 'get-shielded-theme'); ?></h3>
                                <div class="flex flex-col gap-4">
                                    <div class="flex items-center gap-3 text-white/80">
                                        <span class="bg-white/10 p-2 rounded-md text-base">📧</span>
                                        <span>info@<?php echo esc_html(str_replace(['http://', 'https://', 'www.'], '', home_url())); ?></span>
                                    </div>
                                    <div class="flex items-center gap-3 text-white/80">
                                        <span class="bg-white/10 p-2 rounded-md text-base">📞</span>
                                        <span>+1 (555) 123-4567</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-white/80">
                                        <span class="bg-white/10 p-2 rounded-md text-base">📍</span>
                                        <span><?php esc_html_e('123 Security St, Safe City, SC 12345', 'get-shielded-theme'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer-bottom py-6 bg-black/10">
                    <div class="container mx-auto px-4 max-w-7xl flex justify-between items-center flex-wrap gap-4">
                        <p class="m-0 text-white/80 text-sm">
                            &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. 
                            <?php esc_html_e('All rights reserved.', 'get-shielded-theme'); ?>
                        </p>
                        <p class="m-0 text-white/60 text-sm">
                            <?php esc_html_e('Powered by Get Shielded Theme', 'get-shielded-theme'); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </footer>
</div> <!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
