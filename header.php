<?php
/**
 * The header template
 *
 * @package Omoide_Hiroba
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <!-- SEO最適化メタタグ -->
    <meta name="description" content="<?php echo omoide_hiroba_get_meta_description(); ?>">
    <meta name="keywords" content="思い出広場,市原市,レトロトイ,おもちゃ,昭和レトロ,レトロゲーム,ファミコン,スーパーファミコン,フィギュア,超合金,ソフビ,五井駅,千葉県,中古おもちゃ,玩具店,トイショップ,リニューアルオープン">
    <meta name="author" content="思い出広場">
    <link rel="canonical" href="<?php echo esc_url( omoide_hiroba_get_canonical_url() ); ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo is_front_page() ? 'website' : 'article'; ?>">
    <meta property="og:url" content="<?php echo esc_url( omoide_hiroba_get_canonical_url() ); ?>">
    <meta property="og:title" content="<?php echo omoide_hiroba_get_og_title(); ?>">
    <meta property="og:description" content="<?php echo omoide_hiroba_get_meta_description(); ?>">
    <meta property="og:image" content="<?php echo esc_url( omoide_hiroba_get_og_image() ); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="ja_JP">
    <meta property="og:site_name" content="思い出広場 - 市原市のレトロトイショップ">
    <?php if ( is_singular() && ! is_front_page() ) : ?>
    <meta property="article:published_time" content="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
    <meta property="article:modified_time" content="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>">
    <?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo esc_url( omoide_hiroba_get_canonical_url() ); ?>">
    <meta name="twitter:title" content="<?php echo omoide_hiroba_get_og_title(); ?>">
    <meta name="twitter:description" content="<?php echo omoide_hiroba_get_meta_description(); ?>">
    <meta name="twitter:image" content="<?php echo esc_url( omoide_hiroba_get_og_image() ); ?>">

    <!-- ローカルビジネス構造化データ -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ToyStore",
        "name": "思い出広場",
        "alternateName": "Omoide Hiroba",
        "description": "千葉県市原市のレトロトイショップ。昭和から平成初期のおもちゃ、ゲーム、フィギュアなど懐かしいアイテムを取り扱っています。",
        "url": "https://omoidehiroba.com",
        "logo": "<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo.png' ); ?>",
        "image": "<?php echo esc_url( get_template_directory_uri() . '/assets/images/shop-photo.jpg' ); ?>",
        "telephone": "+81-436-37-5791",
        "priceRange": "¥¥",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "五井中央西1丁目22番地6 山崎第一ビル102",
            "addressLocality": "市原市",
            "addressRegion": "千葉県",
            "postalCode": "290-0081",
            "addressCountry": "JP"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": 35.51461437264152,
            "longitude": 140.08414537578028
        },
        "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
            "opens": "12:00",
            "closes": "18:00"
        },
        "sameAs": [
            "https://x.com/omoidehiroba",
            "https://www.youtube.com/channel/UCb5lRKpYCD7b-YN1Mm9migw"
        ],
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "レトロトイ商品カタログ",
            "itemListElement": [
                {
                    "@type": "Product",
                    "name": "レトロゲーム機・ソフト",
                    "category": "ゲーム"
                },
                {
                    "@type": "Product",
                    "name": "フィギュア・人形",
                    "category": "玩具"
                },
                {
                    "@type": "Product",
                    "name": "ミニカー・プラモデル",
                    "category": "模型"
                }
            ]
        }
    }
    </script>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'コンテンツへスキップ', 'omoide-hiroba' ); ?></a>

    <header id="masthead" class="site-header">
        <div class="container">
            <div class="site-branding">
                <?php
                if ( has_custom_logo() ) :
                    the_custom_logo();
                else :
                    ?>
                    <h1 class="site-title">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                            <?php bloginfo( 'name' ); ?>
                        </a>
                    </h1>
                    <?php
                endif;

                $description = get_bloginfo( 'description', 'display' );
                if ( $description || is_customize_preview() ) :
                    ?>
                    <p class="site-description"><?php echo $description; ?></p>
                <?php endif; ?>

                <!-- ヘッダーソーシャルリンク -->
                <div class="header-social">
                    <a href="https://x.com/omoidehiroba" target="_blank" rel="noopener noreferrer" class="social-icon-header" title="X（旧Twitter）">
                        <i class="fab fa-x-twitter"></i>
                    </a>
                    <a href="https://www.youtube.com/channel/UCb5lRKpYCD7b-YN1Mm9migw" target="_blank" rel="noopener noreferrer" class="social-icon-header" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <nav id="site-navigation" class="main-navigation">
        <div class="container">
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                <?php esc_html_e( 'メニュー', 'omoide-hiroba' ); ?>
            </button>
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'fallback_cb'    => false,
                )
            );
            ?>
        </div>
    </nav>

    <div id="content" class="site-content">
        <div class="container">