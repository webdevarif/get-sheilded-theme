<?php
/**
 * The template for displaying all pages
 *
 * @package GetShieldedTheme
 * @since 1.0.0
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header>

                <div class="entry-content">
                    <?php the_content(); ?>
                    
                    <?php
                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . esc_html__('Pages:', 'get-shielded-theme'),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>
            </article>

            <?php
            // Comments
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>
        <?php endwhile; ?>
    </main>
</div>

<?php
get_sidebar();
get_footer();
?>
