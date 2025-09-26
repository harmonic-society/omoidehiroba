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

    // Google Fonts
    wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&family=Sawarabi+Mincho&display=swap', array(), null );

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