<?php
/**
 * 思い出広場 functions and definitions
 *
 * @package Omoide_Hiroba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * テーマの初期設定
 */
function omoide_hiroba_setup() {
    // 翻訳ファイルのサポート
    load_theme_textdomain( 'omoide-hiroba', get_template_directory() . '/languages' );

    // 自動フィードリンクのサポート
    add_theme_support( 'automatic-feed-links' );

    // タイトルタグのサポート
    add_theme_support( 'title-tag' );

    // アイキャッチ画像のサポート
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 800, 450, true );
    add_image_size( 'product-thumb', 300, 300, true );
    add_image_size( 'product-large', 800, 600, true );

    // ナビゲーションメニューの登録
    register_nav_menus(
        array(
            'primary' => esc_html__( 'プライマリメニュー', 'omoide-hiroba' ),
            'footer'  => esc_html__( 'フッターメニュー', 'omoide-hiroba' ),
            'social'  => esc_html__( 'ソーシャルメニュー', 'omoide-hiroba' ),
        )
    );

    // HTML5マークアップのサポート
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // カスタムロゴのサポート
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 150,
            'width'       => 350,
            'flex-width'  => true,
            'flex-height' => true,
        )
    );

    // カスタム背景のサポート
    add_theme_support(
        'custom-background',
        array(
            'default-color' => 'faf0e6',
        )
    );

    // ブロックエディタのサポート
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'omoide_hiroba_setup' );

/**
 * コンテンツ幅の設定
 */
function omoide_hiroba_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'omoide_hiroba_content_width', 1140 );
}
add_action( 'after_setup_theme', 'omoide_hiroba_content_width', 0 );

/**
 * ウィジェットエリアの登録
 */
function omoide_hiroba_widgets_init() {
    register_sidebar(
        array(
            'name'          => esc_html__( 'サイドバー', 'omoide-hiroba' ),
            'id'            => 'sidebar-1',
            'description'   => esc_html__( 'サイドバーに表示されるウィジェットエリア', 'omoide-hiroba' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s retro-card">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );

    register_sidebar(
        array(
            'name'          => esc_html__( 'フッター 1', 'omoide-hiroba' ),
            'id'            => 'footer-1',
            'description'   => esc_html__( 'フッター左側のウィジェットエリア', 'omoide-hiroba' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );

    register_sidebar(
        array(
            'name'          => esc_html__( 'フッター 2', 'omoide-hiroba' ),
            'id'            => 'footer-2',
            'description'   => esc_html__( 'フッター中央のウィジェットエリア', 'omoide-hiroba' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );

    register_sidebar(
        array(
            'name'          => esc_html__( 'フッター 3', 'omoide-hiroba' ),
            'id'            => 'footer-3',
            'description'   => esc_html__( 'フッター右側のウィジェットエリア', 'omoide-hiroba' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );
}
add_action( 'widgets_init', 'omoide_hiroba_widgets_init' );

/**
 * スクリプトとスタイルの読み込み
 */
function omoide_hiroba_scripts() {
    // メインスタイルシート
    wp_enqueue_style( 'omoide-hiroba-style', get_stylesheet_uri(), array(), '1.0.0' );

    // Google Fonts - ゆるい手書きフォント
    wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Klee+One:wght@400;600&family=Yusei+Magic&family=RocknRoll+One&family=Zen+Kurenaido&display=swap', array(), null );

    // カスタムCSS
    wp_enqueue_style( 'omoide-hiroba-custom', get_template_directory_uri() . '/assets/css/custom.css', array( 'omoide-hiroba-style' ), '1.0.0' );

    // メインJavaScript
    wp_enqueue_script( 'omoide-hiroba-main', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), '1.0.0', true );

    // コメント返信スクリプト
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'omoide_hiroba_scripts' );

/**
 * カスタム投稿タイプ「商品」の登録
 */
function omoide_hiroba_register_product_post_type() {
    $labels = array(
        'name'                  => esc_html__( '商品', 'omoide-hiroba' ),
        'singular_name'         => esc_html__( '商品', 'omoide-hiroba' ),
        'menu_name'             => esc_html__( '商品管理', 'omoide-hiroba' ),
        'add_new'               => esc_html__( '新規追加', 'omoide-hiroba' ),
        'add_new_item'          => esc_html__( '新しい商品を追加', 'omoide-hiroba' ),
        'edit_item'             => esc_html__( '商品を編集', 'omoide-hiroba' ),
        'new_item'              => esc_html__( '新しい商品', 'omoide-hiroba' ),
        'view_item'             => esc_html__( '商品を表示', 'omoide-hiroba' ),
        'view_items'            => esc_html__( '商品一覧', 'omoide-hiroba' ),
        'search_items'          => esc_html__( '商品を検索', 'omoide-hiroba' ),
        'not_found'             => esc_html__( '商品が見つかりませんでした', 'omoide-hiroba' ),
        'not_found_in_trash'    => esc_html__( 'ゴミ箱に商品はありません', 'omoide-hiroba' ),
        'all_items'             => esc_html__( 'すべての商品', 'omoide-hiroba' ),
        'archives'              => esc_html__( '商品アーカイブ', 'omoide-hiroba' ),
        'attributes'            => esc_html__( '商品の属性', 'omoide-hiroba' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'products' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-cart',
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'revisions' ),
        'show_in_rest'       => true,
    );

    register_post_type( 'product', $args );
}
add_action( 'init', 'omoide_hiroba_register_product_post_type' );

/**
 * 商品カテゴリーのカスタムタクソノミーを登録
 */
function omoide_hiroba_register_product_category() {
    $labels = array(
        'name'              => esc_html__( '商品カテゴリー', 'omoide-hiroba' ),
        'singular_name'     => esc_html__( '商品カテゴリー', 'omoide-hiroba' ),
        'search_items'      => esc_html__( 'カテゴリーを検索', 'omoide-hiroba' ),
        'all_items'         => esc_html__( 'すべてのカテゴリー', 'omoide-hiroba' ),
        'parent_item'       => esc_html__( '親カテゴリー', 'omoide-hiroba' ),
        'parent_item_colon' => esc_html__( '親カテゴリー:', 'omoide-hiroba' ),
        'edit_item'         => esc_html__( 'カテゴリーを編集', 'omoide-hiroba' ),
        'update_item'       => esc_html__( 'カテゴリーを更新', 'omoide-hiroba' ),
        'add_new_item'      => esc_html__( '新しいカテゴリーを追加', 'omoide-hiroba' ),
        'new_item_name'     => esc_html__( '新しいカテゴリー名', 'omoide-hiroba' ),
        'menu_name'         => esc_html__( '商品カテゴリー', 'omoide-hiroba' ),
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'product-category' ),
        'show_in_rest'      => true,
    );

    register_taxonomy( 'product_category', array( 'product' ), $args );
}
add_action( 'init', 'omoide_hiroba_register_product_category' );

/**
 * 商品の時代タグのカスタムタクソノミーを登録
 */
function omoide_hiroba_register_product_era() {
    $labels = array(
        'name'              => esc_html__( '時代', 'omoide-hiroba' ),
        'singular_name'     => esc_html__( '時代', 'omoide-hiroba' ),
        'search_items'      => esc_html__( '時代を検索', 'omoide-hiroba' ),
        'all_items'         => esc_html__( 'すべての時代', 'omoide-hiroba' ),
        'edit_item'         => esc_html__( '時代を編集', 'omoide-hiroba' ),
        'update_item'       => esc_html__( '時代を更新', 'omoide-hiroba' ),
        'add_new_item'      => esc_html__( '新しい時代を追加', 'omoide-hiroba' ),
        'new_item_name'     => esc_html__( '新しい時代名', 'omoide-hiroba' ),
        'menu_name'         => esc_html__( '時代タグ', 'omoide-hiroba' ),
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => false,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'era' ),
        'show_in_rest'      => true,
    );

    register_taxonomy( 'product_era', array( 'product' ), $args );
}
add_action( 'init', 'omoide_hiroba_register_product_era' );

/**
 * カスタムフィールドの追加（商品価格）
 */
function omoide_hiroba_add_product_meta_boxes() {
    add_meta_box(
        'product_price',
        esc_html__( '商品価格', 'omoide-hiroba' ),
        'omoide_hiroba_product_price_callback',
        'product',
        'side',
        'high'
    );

    add_meta_box(
        'product_status',
        esc_html__( '商品ステータス', 'omoide-hiroba' ),
        'omoide_hiroba_product_status_callback',
        'product',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'omoide_hiroba_add_product_meta_boxes' );

/**
 * 商品価格メタボックスのコールバック
 */
function omoide_hiroba_product_price_callback( $post ) {
    wp_nonce_field( 'omoide_hiroba_save_product_price', 'omoide_hiroba_product_price_nonce' );
    $price = get_post_meta( $post->ID, '_product_price', true );
    ?>
    <label for="product_price"><?php esc_html_e( '価格 (円)', 'omoide-hiroba' ); ?></label>
    <input type="number" id="product_price" name="product_price" value="<?php echo esc_attr( $price ); ?>" min="0" step="1" style="width: 100%;">
    <?php
}

/**
 * 商品ステータスメタボックスのコールバック
 */
function omoide_hiroba_product_status_callback( $post ) {
    wp_nonce_field( 'omoide_hiroba_save_product_status', 'omoide_hiroba_product_status_nonce' );
    $status = get_post_meta( $post->ID, '_product_status', true );
    ?>
    <label for="product_status"><?php esc_html_e( 'ステータス', 'omoide-hiroba' ); ?></label>
    <select id="product_status" name="product_status" style="width: 100%;">
        <option value=""><?php esc_html_e( '-- 選択 --', 'omoide-hiroba' ); ?></option>
        <option value="in_stock" <?php selected( $status, 'in_stock' ); ?>><?php esc_html_e( '在庫あり', 'omoide-hiroba' ); ?></option>
        <option value="sold_out" <?php selected( $status, 'sold_out' ); ?>><?php esc_html_e( '売り切れ', 'omoide-hiroba' ); ?></option>
        <option value="reserved" <?php selected( $status, 'reserved' ); ?>><?php esc_html_e( '予約済み', 'omoide-hiroba' ); ?></option>
        <option value="new_arrival" <?php selected( $status, 'new_arrival' ); ?>><?php esc_html_e( '新入荷', 'omoide-hiroba' ); ?></option>
    </select>
    <?php
}

/**
 * 商品メタデータの保存
 */
function omoide_hiroba_save_product_meta( $post_id ) {
    // 価格の保存
    if ( ! isset( $_POST['omoide_hiroba_product_price_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['omoide_hiroba_product_price_nonce'], 'omoide_hiroba_save_product_price' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( isset( $_POST['product_price'] ) ) {
        update_post_meta( $post_id, '_product_price', sanitize_text_field( $_POST['product_price'] ) );
    }

    // ステータスの保存
    if ( isset( $_POST['omoide_hiroba_product_status_nonce'] ) && wp_verify_nonce( $_POST['omoide_hiroba_product_status_nonce'], 'omoide_hiroba_save_product_status' ) ) {
        if ( isset( $_POST['product_status'] ) ) {
            update_post_meta( $post_id, '_product_status', sanitize_text_field( $_POST['product_status'] ) );
        }
    }
}
add_action( 'save_post', 'omoide_hiroba_save_product_meta' );

/**
 * 管理画面用のスタイルを追加
 */
function omoide_hiroba_admin_styles() {
    ?>
    <style>
        .post-type-product .column-thumbnail {
            width: 80px;
        }
        .post-type-product .column-price {
            width: 100px;
        }
    </style>
    <?php
}
add_action( 'admin_head', 'omoide_hiroba_admin_styles' );

/**
 * SEO用メタディスクリプションを取得
 */
function omoide_hiroba_get_meta_description() {
    if ( is_front_page() ) {
        return '思い出広場は千葉県市原市五井駅近くのレトロトイショップです。昭和レトロから平成初期のおもちゃ、ファミコン、スーパーファミコン、フィギュア、超合金など懐かしいアイテムが勢ぞろい。2025年10月リニューアルオープン！';
    } elseif ( is_singular( 'product' ) ) {
        global $post;
        $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words( strip_tags( $post->post_content ), 30 );
        return '思い出広場で' . get_the_title() . 'を販売中。' . $excerpt . ' 千葉県市原市のレトロトイショップ。';
    } elseif ( is_post_type_archive( 'product' ) || is_tax( 'product_category' ) || is_tax( 'product_era' ) ) {
        return '思い出広場の商品一覧。レトロゲーム、フィギュア、ミニカー、プラモデルなど懐かしいおもちゃを多数取り揃え。千葉県市原市五井駅から徒歩5分のレトロトイショップ。';
    } elseif ( is_page() ) {
        return get_the_title() . ' | 思い出広場 - 千葉県市原市のレトロトイショップ。昭和レトロおもちゃ、レトロゲーム専門店。';
    } else {
        return '思い出広場のお知らせ・ブログ。千葉県市原市のレトロトイショップより、新入荷情報やイベント情報をお届けします。';
    }
}

/**
 * SEO用OGタイトルを取得
 */
function omoide_hiroba_get_og_title() {
    if ( is_front_page() ) {
        return '思い出広場 | 市原市のレトロトイショップ - 昭和レトロおもちゃ・レトロゲーム専門店';
    } elseif ( is_singular() ) {
        return get_the_title() . ' | 思い出広場';
    } elseif ( is_post_type_archive( 'product' ) ) {
        return '商品一覧 | 思い出広場 - 市原市のレトロトイショップ';
    } elseif ( is_tax( 'product_category' ) ) {
        return single_term_title( '', false ) . ' | 思い出広場';
    } else {
        return wp_get_document_title();
    }
}

/**
 * Canonical URLを取得
 */
function omoide_hiroba_get_canonical_url() {
    global $wp;
    if ( is_front_page() ) {
        return home_url( '/' );
    } else {
        return home_url( add_query_arg( array(), $wp->request ) );
    }
}

/**
 * タイトルタグを最適化
 */
function omoide_hiroba_document_title_parts( $title ) {
    if ( is_front_page() ) {
        $title['title'] = '思い出広場';
        $title['tagline'] = '市原市のレトロトイショップ | 昭和レトロおもちゃ・ゲーム専門店';
    } elseif ( is_singular( 'product' ) ) {
        $title['title'] = get_the_title() . ' | レトロトイ商品';
        $title['site'] = '思い出広場';
    } elseif ( is_post_type_archive( 'product' ) ) {
        $title['title'] = 'レトロトイ商品一覧';
        $title['site'] = '思い出広場 - 市原市';
    }
    return $title;
}
add_filter( 'document_title_parts', 'omoide_hiroba_document_title_parts' );

/**
 * タイトルセパレーターを変更
 */
function omoide_hiroba_document_title_separator( $sep ) {
    return '|';
}
add_filter( 'document_title_separator', 'omoide_hiroba_document_title_separator' );

/**
 * XMLサイトマップのサポート
 */
function omoide_hiroba_sitemap_init() {
    // WordPress 5.5以降の標準XMLサイトマップを有効化
    add_filter( 'wp_sitemaps_enabled', '__return_true' );
}
add_action( 'init', 'omoide_hiroba_sitemap_init' );

/**
 * 画像のalt属性を自動設定
 */
function omoide_hiroba_auto_alt_text( $attr, $attachment ) {
    if ( empty( $attr['alt'] ) ) {
        $attr['alt'] = '思い出広場 - ' . get_the_title( $attachment->ID );
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'omoide_hiroba_auto_alt_text', 10, 2 );