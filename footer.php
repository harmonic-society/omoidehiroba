<?php
/**
 * The footer template
 *
 * @package Omoide_Hiroba
 */

?>

        </div><!-- .container -->
    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="container">

            <?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) ) : ?>
                <div class="footer-widgets">
                    <div class="footer-widget-area">
                        <?php dynamic_sidebar( 'footer-1' ); ?>
                    </div>
                    <div class="footer-widget-area">
                        <?php dynamic_sidebar( 'footer-2' ); ?>
                    </div>
                    <div class="footer-widget-area">
                        <?php dynamic_sidebar( 'footer-3' ); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="site-info">
                <div class="shop-info">
                    <h3>思い出広場</h3>
                    <p>〒290-0081 千葉県市原市五井中央西1丁目22番地6 山崎第一ビル102</p>
                    <p>営業時間: 12:00 - 18:00（不定休）</p>
                    <p>TEL: 0436-37-5791</p>

                    <!-- ソーシャルリンク -->
                    <div class="social-links">
                        <a href="https://x.com/omoidehiroba" target="_blank" rel="noopener noreferrer" class="social-btn social-x" title="X（旧Twitter）をフォロー">
                            <i class="fab fa-x-twitter"></i>
                            <span>X</span>
                        </a>
                        <a href="https://www.youtube.com/channel/UCb5lRKpYCD7b-YN1Mm9migw" target="_blank" rel="noopener noreferrer" class="social-btn social-youtube" title="YouTubeチャンネルを見る">
                            <i class="fab fa-youtube"></i>
                            <span>YouTube</span>
                        </a>
                    </div>
                </div>

                <?php if ( has_nav_menu( 'footer' ) ) : ?>
                    <nav class="footer-navigation">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'footer',
                                'menu_class'     => 'footer-menu',
                                'container'      => false,
                                'depth'          => 1,
                            )
                        );
                        ?>
                    </nav>
                <?php endif; ?>

                <?php if ( has_nav_menu( 'social' ) ) : ?>
                    <nav class="social-navigation" aria-label="<?php esc_attr_e( 'ソーシャルリンク', 'omoide-hiroba' ); ?>">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'social',
                                'menu_class'     => 'social-menu',
                                'container'      => false,
                                'link_before'    => '<span class="screen-reader-text">',
                                'link_after'     => '</span>',
                                'depth'          => 1,
                            )
                        );
                        ?>
                    </nav>
                <?php endif; ?>

                <div class="copyright">
                    <p>&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>