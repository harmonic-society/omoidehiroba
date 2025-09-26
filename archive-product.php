<?php
/**
 * The template for displaying product archives
 *
 * @package Omoide_Hiroba
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="container">

            <header class="page-header">
                <h1 class="page-title">商品一覧</h1>

                <?php
                // カテゴリーまたはタグアーカイブの場合、説明を表示
                $term = get_queried_object();
                if ( is_tax() && $term ) :
                    ?>
                    <div class="archive-description">
                        <h2><?php echo esc_html( $term->name ); ?></h2>
                        <?php if ( $term->description ) : ?>
                            <p><?php echo esc_html( $term->description ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </header>

            <!-- フィルターセクション -->
            <div class="product-filters retro-card">
                <h3>商品を絞り込む</h3>

                <div class="filter-group">
                    <label>カテゴリー</label>
                    <div class="filter-options">
                        <?php
                        $product_categories = get_terms(
                            array(
                                'taxonomy'   => 'product_category',
                                'hide_empty' => true,
                            )
                        );

                        if ( ! empty( $product_categories ) ) :
                            foreach ( $product_categories as $category ) :
                                ?>
                                <a href="<?php echo esc_url( get_term_link( $category ) ); ?>" class="filter-link <?php echo ( is_tax( 'product_category', $category->term_id ) ) ? 'active' : ''; ?>">
                                    <?php echo esc_html( $category->name ); ?>
                                    <span>(<?php echo esc_html( $category->count ); ?>)</span>
                                </a>
                            <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>

                <div class="filter-group">
                    <label>時代</label>
                    <div class="filter-options">
                        <?php
                        $product_eras = get_terms(
                            array(
                                'taxonomy'   => 'product_era',
                                'hide_empty' => true,
                            )
                        );

                        if ( ! empty( $product_eras ) ) :
                            foreach ( $product_eras as $era ) :
                                ?>
                                <a href="<?php echo esc_url( get_term_link( $era ) ); ?>" class="filter-link <?php echo ( is_tax( 'product_era', $era->term_id ) ) ? 'active' : ''; ?>">
                                    <?php echo esc_html( $era->name ); ?>
                                    <span>(<?php echo esc_html( $era->count ); ?>)</span>
                                </a>
                            <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </div>

            <!-- 商品グリッド -->
            <?php if ( have_posts() ) : ?>

                <div class="products-header">
                    <div class="result-count">
                        <?php
                        global $wp_query;
                        $total = $wp_query->found_posts;
                        $current_page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
                        $per_page = get_query_var( 'posts_per_page' );
                        $from = ( ( $current_page - 1 ) * $per_page ) + 1;
                        $to = min( $current_page * $per_page, $total );

                        printf(
                            esc_html__( '全%1$s件中 %2$s〜%3$s件を表示', 'omoide-hiroba' ),
                            $total,
                            $from,
                            $to
                        );
                        ?>
                    </div>
                </div>

                <div class="product-grid">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        $price = get_post_meta( get_the_ID(), '_product_price', true );
                        $status = get_post_meta( get_the_ID(), '_product_status', true );
                        ?>

                        <div class="product-card fade-in">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>" class="product-image">
                                    <?php the_post_thumbnail( 'product-thumb' ); ?>
                                    <?php if ( $status === 'sold_out' ) : ?>
                                        <span class="sold-out-overlay">SOLD OUT</span>
                                    <?php endif; ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>" class="product-image">
                                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/no-image.jpg' ); ?>" alt="<?php the_title(); ?>">
                                </a>
                            <?php endif; ?>

                            <div class="product-info">
                                <h2 class="product-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>

                                <?php
                                // カテゴリー表示
                                $product_cats = wp_get_post_terms( get_the_ID(), 'product_category' );
                                if ( ! empty( $product_cats ) ) :
                                    ?>
                                    <div class="product-categories">
                                        <?php foreach ( $product_cats as $cat ) : ?>
                                            <span class="category-badge"><?php echo esc_html( $cat->name ); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $status === 'new_arrival' ) : ?>
                                    <span class="retro-badge new">新入荷</span>
                                <?php elseif ( $status === 'reserved' ) : ?>
                                    <span class="retro-badge reserved">予約済み</span>
                                <?php endif; ?>

                                <?php if ( $price ) : ?>
                                    <div class="price-tag"><?php echo esc_html( number_format( $price ) ); ?></div>
                                <?php endif; ?>

                                <a href="<?php the_permalink(); ?>" class="view-detail">詳細を見る →</a>
                            </div>
                        </div>

                    <?php endwhile; ?>
                </div>

                <!-- ページネーション -->
                <div class="pagination">
                    <?php
                    the_posts_pagination(
                        array(
                            'mid_size'  => 2,
                            'prev_text' => '&laquo; 前のページ',
                            'next_text' => '次のページ &raquo;',
                        )
                    );
                    ?>
                </div>

            <?php else : ?>

                <div class="no-products">
                    <div class="retro-card">
                        <h2>商品が見つかりませんでした</h2>
                        <p>申し訳ございません。現在、該当する商品がございません。</p>
                        <p>他のカテゴリーもぜひご覧ください。</p>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="retro-button">トップページへ戻る</a>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </main>
</div>

<?php
get_footer();