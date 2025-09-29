<?php
/**
 * The template for displaying single product
 *
 * @package Omoide_Hiroba
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="container">

            <?php
            while ( have_posts() ) :
                the_post();

                $price = get_post_meta( get_the_ID(), '_product_price', true );
                $status = get_post_meta( get_the_ID(), '_product_status', true );
                ?>

                <article id="product-<?php the_ID(); ?>" <?php post_class( 'single-product' ); ?>>

                    <div class="product-container">
                        <div class="product-gallery">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="product-main-image">
                                    <?php the_post_thumbnail( 'product-large' ); ?>
                                </div>
                            <?php endif; ?>

                            <?php
                            // ギャラリー画像の表示
                            $gallery = get_post_gallery( get_the_ID(), false );
                            if ( $gallery ) :
                                ?>
                                <div class="product-gallery-thumbs">
                                    <?php
                                    $images = explode( ',', $gallery['ids'] );
                                    foreach ( $images as $image_id ) :
                                        echo wp_get_attachment_image( $image_id, 'thumbnail' );
                                    endforeach;
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="product-details">
                            <header class="product-header">
                                <h1 class="product-name"><?php the_title(); ?></h1>

                                <?php
                                // カテゴリーの表示
                                $product_cats = wp_get_post_terms( get_the_ID(), 'product_category' );
                                if ( ! empty( $product_cats ) ) :
                                    ?>
                                    <div class="product-categories">
                                        <?php foreach ( $product_cats as $cat ) : ?>
                                            <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="retro-badge">
                                                <?php echo esc_html( $cat->name ); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php
                                // 時代タグの表示
                                $product_eras = wp_get_post_terms( get_the_ID(), 'product_era' );
                                if ( ! empty( $product_eras ) ) :
                                    ?>
                                    <div class="product-eras">
                                        <?php foreach ( $product_eras as $era ) : ?>
                                            <span class="era-tag">#<?php echo esc_html( $era->name ); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </header>

                            <div class="product-meta retro-card">
                                <?php if ( $price ) : ?>
                                    <div class="product-price-section">
                                        <span class="price-label">価格</span>
                                        <span class="price-tag"><?php echo esc_html( number_format( $price ) ); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $status ) : ?>
                                    <div class="product-status-section">
                                        <span class="status-label">状態</span>
                                        <?php
                                        switch ( $status ) {
                                            case 'in_stock':
                                                echo '<span class="retro-badge">在庫あり</span>';
                                                break;
                                            case 'sold_out':
                                                echo '<span class="retro-badge sold-out">売り切れ</span>';
                                                break;
                                            case 'reserved':
                                                echo '<span class="retro-badge reserved">予約済み</span>';
                                                break;
                                            case 'new_arrival':
                                                echo '<span class="retro-badge new">新入荷</span>';
                                                break;
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <div class="product-contact">
                                    <p>この商品についてのお問い合わせ</p>
                                    <a href="tel:0436-37-5791" class="retro-button">電話で問い合わせ</a>
                                    <a href="mailto:omoidehiroba.mm09@gmail.com?subject=<?php echo urlencode( get_the_title() . 'について' ); ?>" class="retro-button">メールで問い合わせ</a>
                                </div>
                            </div>

                            <div class="product-description">
                                <h2>商品説明</h2>
                                <div class="description-content">
                                    <?php the_content(); ?>
                                </div>
                            </div>

                            <?php
                            // カスタムフィールドで商品情報を表示
                            $product_info = get_post_meta( get_the_ID(), '_product_info', true );
                            if ( $product_info ) :
                                ?>
                                <div class="product-additional-info retro-card">
                                    <h3>商品情報</h3>
                                    <?php echo wpautop( $product_info ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 関連商品 -->
                    <?php
                    $related_args = array(
                        'post_type'      => 'product',
                        'posts_per_page' => 4,
                        'post__not_in'   => array( get_the_ID() ),
                        'orderby'        => 'rand',
                    );

                    // 同じカテゴリーの商品を優先
                    if ( ! empty( $product_cats ) ) {
                        $cat_ids = wp_list_pluck( $product_cats, 'term_id' );
                        $related_args['tax_query'] = array(
                            array(
                                'taxonomy' => 'product_category',
                                'field'    => 'term_id',
                                'terms'    => $cat_ids,
                            ),
                        );
                    }

                    $related_query = new WP_Query( $related_args );

                    if ( $related_query->have_posts() ) :
                        ?>
                        <div class="related-products">
                            <h2>関連商品</h2>
                            <div class="product-grid">
                                <?php
                                while ( $related_query->have_posts() ) :
                                    $related_query->the_post();
                                    $related_price = get_post_meta( get_the_ID(), '_product_price', true );
                                    ?>
                                    <div class="product-card">
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail( 'product-thumb' ); ?>
                                            </a>
                                        <?php endif; ?>
                                        <div class="product-info">
                                            <h3 class="product-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h3>
                                            <?php if ( $related_price ) : ?>
                                                <div class="price-tag"><?php echo esc_html( number_format( $related_price ) ); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        <?php
                        wp_reset_postdata();
                    endif;
                    ?>

                </article>

            <?php endwhile; ?>

        </div>
    </main>
</div>

<?php
get_footer();