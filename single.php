<?php
/**
 * The template for displaying all single posts
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
                    
                    <div class="entry-meta">
                        <span class="posted-on">
                            <?php echo esc_html(get_the_date()); ?>
                        </span>
                        <span class="byline">
                            <?php esc_html_e('by', 'get-shielded-theme'); ?> 
                            <?php the_author(); ?>
                        </span>
                        <?php if (has_category()) : ?>
                            <span class="categories">
                                <?php the_category(', '); ?>
                            </span>
                        <?php endif; ?>
                    </div>
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

                <footer class="entry-footer">
                    <?php if (has_tag()) : ?>
                        <div class="tag-list">
                            <strong><?php esc_html_e('Tags:', 'get-shielded-theme'); ?></strong>
                            <?php the_tags('', ', ', ''); ?>
                        </div>
                    <?php endif; ?>
                </footer>
            </article>

            <?php
            // Post navigation
            the_post_navigation(array(
                'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'get-shielded-theme') . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'get-shielded-theme') . '</span> <span class="nav-title">%title</span>',
            ));

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
