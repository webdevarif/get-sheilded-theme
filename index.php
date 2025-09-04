<?php
/**
 * Main index template
 * 
 * @package GetsheildedTheme
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title">
                            <a href="<?php the_permalink(); ?>" rel="bookmark">
                                <?php the_title(); ?>
                            </a>
                        </h1>
                    </header>

                    <div class="entry-content">
                        <?php the_excerpt(); ?>
                        <a href="<?php the_permalink(); ?>" class="read-more">
                            <?php esc_html_e('Read More', 'get-sheilded-theme'); ?>
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>

            <?php
            the_posts_navigation(array(
                'prev_text' => __('Older posts', 'get-sheilded-theme'),
                'next_text' => __('Newer posts', 'get-sheilded-theme'),
            ));
            ?>
        <?php else : ?>
            <section class="no-results not-found">
                <header class="page-header">
                    <h1 class="page-title"><?php esc_html_e('Nothing here', 'get-sheilded-theme'); ?></h1>
                </header>
                <div class="page-content">
                    <p><?php esc_html_e('It looks like nothing was found at this location.', 'get-sheilded-theme'); ?></p>
                </div>
            </section>
        <?php endif; ?>
    </main>
</div>

<?php
get_sidebar();
get_footer();
?>
