<?php
/**
 * The main template file
 *
 * @package Omoide_Hiroba
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="container">

            <?php if ( is_home() && ! is_front_page() ) : ?>
                <header class="page-header">
                    <h1 class="page-title"><?php single_post_title(); ?></h1>
                </header>
            <?php endif; ?>

            <?php if ( have_posts() ) : ?>

                <?php if ( is_home() && is_front_page() ) : ?>
                    <header class="page-header">
                        <h2 class="page-title"><?php esc_html_e( '最新のお知らせ', 'omoide-hiroba' ); ?></h2>
                    </header>
                <?php endif; ?>

                <div class="posts-grid">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        ?>

                        <article id="post-<?php the_ID(); ?>" <?php post_class( 'retro-card fade-in' ); ?>>

                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail( 'medium' ); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <header class="entry-header">
                                <?php
                                if ( is_singular() ) :
                                    the_title( '<h1 class="entry-title">', '</h1>' );
                                else :
                                    the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
                                endif;
                                ?>

                                <div class="entry-meta">
                                    <span class="posted-on">
                                        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                            <?php echo get_the_date(); ?>
                                        </time>
                                    </span>

                                    <?php
                                    $categories = get_the_category();
                                    if ( ! empty( $categories ) ) :
                                        ?>
                                        <span class="cat-links">
                                            <?php foreach ( $categories as $category ) : ?>
                                                <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="retro-badge">
                                                    <?php echo esc_html( $category->name ); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </header>

                            <div class="entry-content">
                                <?php
                                if ( is_singular() ) :
                                    the_content();

                                    wp_link_pages(
                                        array(
                                            'before' => '<div class="page-links">' . esc_html__( 'ページ:', 'omoide-hiroba' ),
                                            'after'  => '</div>',
                                        )
                                    );
                                else :
                                    the_excerpt();
                                    ?>
                                    <a href="<?php the_permalink(); ?>" class="retro-button">
                                        <?php esc_html_e( '続きを読む', 'omoide-hiroba' ); ?>
                                    </a>
                                    <?php
                                endif;
                                ?>
                            </div>

                            <?php if ( is_singular() ) : ?>
                                <footer class="entry-footer">
                                    <?php
                                    $tags = get_the_tags();
                                    if ( $tags ) :
                                        ?>
                                        <div class="tags-links">
                                            <span><?php esc_html_e( 'タグ:', 'omoide-hiroba' ); ?></span>
                                            <?php foreach ( $tags as $tag ) : ?>
                                                <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" rel="tag">
                                                    #<?php echo esc_html( $tag->name ); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php omoide_hiroba_social_share_buttons(); ?>
                                </footer>
                            <?php endif; ?>

                        </article>

                    <?php endwhile; ?>
                </div>

                <div class="pagination">
                    <?php
                    the_posts_pagination(
                        array(
                            'mid_size'  => 2,
                            'prev_text' => '&laquo; ' . __( '前へ', 'omoide-hiroba' ),
                            'next_text' => __( '次へ', 'omoide-hiroba' ) . ' &raquo;',
                        )
                    );
                    ?>
                </div>

            <?php else : ?>

                <article class="no-results not-found retro-card">
                    <header class="page-header">
                        <h1 class="page-title"><?php esc_html_e( 'お探しのコンテンツが見つかりませんでした', 'omoide-hiroba' ); ?></h1>
                    </header>

                    <div class="page-content">
                        <?php if ( is_search() ) : ?>

                            <p><?php esc_html_e( '申し訳ございません。検索条件に一致するものが見つかりませんでした。他のキーワードでもう一度お試しください。', 'omoide-hiroba' ); ?></p>
                            <?php get_search_form(); ?>

                        <?php else : ?>

                            <p><?php esc_html_e( 'お探しのコンテンツが見つかりませんでした。検索をお試しください。', 'omoide-hiroba' ); ?></p>
                            <?php get_search_form(); ?>

                        <?php endif; ?>
                    </div>
                </article>

            <?php endif; ?>

        </div>
    </main>
</div>

<?php
get_sidebar();
get_footer();