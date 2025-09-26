<?php
/**
 * The front page template
 *
 * @package Omoide_Hiroba
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <!-- ヒーローセクション -->
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title fade-in">懐かしさと出会える場所</h1>
                <p class="hero-subtitle fade-in">昭和レトロから平成初期まで、思い出のおもちゃが勢ぞろい</p>
                <a href="#featured-products" class="retro-button fade-in">商品を見る</a>
            </div>
            <div class="hero-decoration">
                <span class="retro-badge">Since 2020</span>
            </div>
        </section>

        <!-- 店舗紹介セクション -->
        <section class="about-section">
            <div class="container">
                <h2>思い出広場へようこそ</h2>
                <div class="about-content">
                    <div class="about-text">
                        <p>千葉県市原市にある「思い出広場」は、昭和から平成初期のおもちゃやゲーム、フィギュアなど、懐かしいアイテムを取り扱うレトロトイショップです。</p>
                        <p>子供の頃に遊んだあのおもちゃ、憧れだったあのゲーム機、今では手に入りにくいレアアイテムまで、幅広い商品をご用意しております。</p>
                        <p>ぜひ一度、懐かしい思い出を探しにお越しください。</p>
                    </div>
                    <div class="about-features">
                        <div class="feature-card retro-card">
                            <h3>豊富な品揃え</h3>
                            <p>ファミコン、スーパーファミコンから、超合金、ソフビ人形まで</p>
                        </div>
                        <div class="feature-card retro-card">
                            <h3>状態の良い商品</h3>
                            <p>丁寧にメンテナンスされた、状態の良い商品を厳選</p>
                        </div>
                        <div class="feature-card retro-card">
                            <h3>適正価格</h3>
                            <p>市場価格を考慮した、適正な価格設定</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 注目商品セクション -->
        <section id="featured-products" class="featured-products">
            <div class="container">
                <h2>注目の商品</h2>

                <?php
                $featured_args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => 6,
                    'meta_key'       => '_product_status',
                    'meta_value'     => 'new_arrival',
                    'meta_compare'   => '=',
                );

                $featured_query = new WP_Query( $featured_args );

                if ( $featured_query->have_posts() ) :
                    ?>
                    <div class="product-grid">
                        <?php
                        while ( $featured_query->have_posts() ) :
                            $featured_query->the_post();
                            $price = get_post_meta( get_the_ID(), '_product_price', true );
                            $status = get_post_meta( get_the_ID(), '_product_status', true );
                            ?>
                            <div class="product-card fade-in">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail( 'product-thumb' ); ?>
                                    </a>
                                <?php else : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/no-image.jpg' ); ?>" alt="<?php the_title(); ?>">
                                    </a>
                                <?php endif; ?>

                                <div class="product-info">
                                    <h3 class="product-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>

                                    <?php if ( $status === 'new_arrival' ) : ?>
                                        <span class="retro-badge new">新入荷</span>
                                    <?php endif; ?>

                                    <?php if ( $price ) : ?>
                                        <div class="price-tag"><?php echo esc_html( number_format( $price ) ); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="view-all">
                        <a href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ); ?>" class="retro-button">
                            すべての商品を見る
                        </a>
                    </div>

                    <?php
                    wp_reset_postdata();
                else :
                    // 商品がない場合は通常の投稿を表示
                    $recent_args = array(
                        'post_type'      => 'post',
                        'posts_per_page' => 3,
                    );

                    $recent_query = new WP_Query( $recent_args );

                    if ( $recent_query->have_posts() ) :
                        ?>
                        <div class="recent-posts">
                            <h3>最新のお知らせ</h3>
                            <div class="posts-grid">
                                <?php
                                while ( $recent_query->have_posts() ) :
                                    $recent_query->the_post();
                                    ?>
                                    <article class="retro-card">
                                        <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                        <div class="entry-meta">
                                            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                                <?php echo get_the_date(); ?>
                                            </time>
                                        </div>
                                        <?php the_excerpt(); ?>
                                    </article>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        <?php
                        wp_reset_postdata();
                    endif;
                endif;
                ?>
            </div>
        </section>

        <!-- カテゴリーセクション -->
        <section class="categories-section">
            <div class="container">
                <h2>商品カテゴリー</h2>
                <div class="category-grid">
                    <a href="#" class="category-card retro-card">
                        <h3>ゲーム機・ソフト</h3>
                        <p>ファミコン、スーパーファミコン、ゲームボーイなど</p>
                    </a>
                    <a href="#" class="category-card retro-card">
                        <h3>フィギュア・人形</h3>
                        <p>超合金、ソフビ、リカちゃん人形など</p>
                    </a>
                    <a href="#" class="category-card retro-card">
                        <h3>ミニカー・プラモデル</h3>
                        <p>トミカ、チョロQ、ガンプラなど</p>
                    </a>
                    <a href="#" class="category-card retro-card">
                        <h3>カード・シール</h3>
                        <p>ビックリマンシール、カードダスなど</p>
                    </a>
                    <a href="#" class="category-card retro-card">
                        <h3>ボードゲーム</h3>
                        <p>人生ゲーム、ドンジャラなど</p>
                    </a>
                    <a href="#" class="category-card retro-card">
                        <h3>その他</h3>
                        <p>駄菓子屋グッズ、昭和レトロ雑貨など</p>
                    </a>
                </div>
            </div>
        </section>

        <!-- アクセス情報セクション -->
        <section class="access-section">
            <div class="container">
                <h2>アクセス・営業時間</h2>
                <div class="access-content">
                    <div class="access-info retro-card">
                        <h3>店舗情報</h3>
                        <dl>
                            <dt>住所</dt>
                            <dd>〒290-0000 千葉県市原市○○町1-2-3</dd>

                            <dt>電話番号</dt>
                            <dd>0436-XX-XXXX</dd>

                            <dt>営業時間</dt>
                            <dd>10:00 - 19:00</dd>

                            <dt>定休日</dt>
                            <dd>水曜日</dd>

                            <dt>駐車場</dt>
                            <dd>店舗前に5台分あり</dd>
                        </dl>
                    </div>

                    <div class="access-map">
                        <h3>地図</h3>
                        <div class="map-placeholder retro-card">
                            <p>Google マップがここに表示されます</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- お問い合わせセクション -->
        <section class="contact-section">
            <div class="container">
                <h2>お問い合わせ</h2>
                <div class="contact-content retro-card">
                    <p>商品に関するお問い合わせや、在庫確認など、お気軽にご連絡ください。</p>
                    <div class="contact-methods">
                        <div class="contact-method">
                            <h3>お電話でのお問い合わせ</h3>
                            <p class="contact-number">0436-XX-XXXX</p>
                            <p>営業時間内にお願いいたします</p>
                        </div>
                        <div class="contact-method">
                            <h3>メールでのお問い合わせ</h3>
                            <p>info@omoidehiroba.com</p>
                            <a href="mailto:info@omoidehiroba.com" class="retro-button">メールを送る</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
</div>

<?php
get_footer();