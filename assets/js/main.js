/**
 * 思い出広場 メインJavaScript
 */

(function($) {
    'use strict';

    // DOM Ready
    $(document).ready(function() {

        // モバイルメニューの開閉
        $('.menu-toggle').on('click', function() {
            $('#primary-menu').toggleClass('toggled');
            $(this).attr('aria-expanded', $(this).attr('aria-expanded') === 'false' ? 'true' : 'false');
        });

        // スムーススクロール
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.hash);
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 800, 'swing');
            }
        });

        // フェードインアニメーション
        function checkFadeIn() {
            $('.fade-in').each(function() {
                var elementTop = $(this).offset().top;
                var elementBottom = elementTop + $(this).outerHeight();
                var viewportTop = $(window).scrollTop();
                var viewportBottom = viewportTop + $(window).height();

                if (elementBottom > viewportTop && elementTop < viewportBottom) {
                    $(this).addClass('visible');
                }
            });
        }

        // 初期チェック
        checkFadeIn();

        // スクロール時のチェック
        $(window).on('scroll', function() {
            checkFadeIn();
        });

        // 商品画像ギャラリー
        if ($('.product-gallery-thumbs').length) {
            $('.product-gallery-thumbs img').on('click', function() {
                var newSrc = $(this).attr('src');
                var mainImage = $('.product-main-image img');

                // フェードアウト
                mainImage.fadeOut(200, function() {
                    // 画像を変更してフェードイン
                    $(this).attr('src', newSrc).fadeIn(200);
                });

                // アクティブなサムネイルのスタイル
                $('.product-gallery-thumbs img').css('border-color', 'transparent');
                $(this).css('border-color', 'var(--secondary-color)');
            });
        }

        // ヘッダーの固定
        var header = $('.main-navigation');
        var headerOffset = header.offset();

        if (headerOffset) {
            $(window).on('scroll', function() {
                if ($(window).scrollTop() > headerOffset.top) {
                    header.addClass('fixed-header');
                } else {
                    header.removeClass('fixed-header');
                }
            });
        }

        // 価格のフォーマット（カンマ区切り）
        $('.price-tag').each(function() {
            var priceText = $(this).text();
            // すでに¥マークがある場合は処理しない
            if (!priceText.includes('¥')) {
                var price = priceText.replace(/[^0-9]/g, '');
                if (price) {
                    var formattedPrice = parseInt(price).toLocaleString('ja-JP');
                    $(this).html('¥' + formattedPrice);
                }
            }
        });

        // 商品フィルター（アーカイブページ）
        if ($('.product-filters').length) {
            // URLパラメータの取得
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            // フィルターの状態を保持
            var currentCategory = getUrlParameter('category');
            var currentEra = getUrlParameter('era');

            if (currentCategory) {
                $('.filter-link[data-category="' + currentCategory + '"]').addClass('active');
            }
            if (currentEra) {
                $('.filter-link[data-era="' + currentEra + '"]').addClass('active');
            }
        }

        // 画像の遅延読み込み
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var image = entry.target;
                        image.src = image.dataset.src;
                        image.classList.remove('lazy');
                        imageObserver.unobserve(image);
                    }
                });
            });

            $('.lazy').each(function() {
                imageObserver.observe(this);
            });
        }

        // トップへ戻るボタン
        var backToTop = $('<button class="back-to-top" aria-label="トップへ戻る">↑</button>');
        $('body').append(backToTop);

        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 300) {
                backToTop.fadeIn();
            } else {
                backToTop.fadeOut();
            }
        });

        backToTop.on('click', function() {
            $('html, body').animate({
                scrollTop: 0
            }, 600);
            return false;
        });

        // アコーディオンメニュー（サイドバーウィジェット）
        $('.widget-title').on('click', function() {
            var widget = $(this).parent('.widget');
            if (widget.hasClass('collapsible')) {
                widget.toggleClass('collapsed');
                $(this).next().slideToggle(300);
            }
        });

        // 検索フォームのプレースホルダー
        $('.search-form input[type="search"]').attr('placeholder', 'レトロトイを検索...');

        // 商品カードのホバーエフェクト
        $('.product-card').on('mouseenter', function() {
            $(this).find('.product-info').addClass('show-details');
        }).on('mouseleave', function() {
            $(this).find('.product-info').removeClass('show-details');
        });

        // フォームバリデーション
        $('form').on('submit', function(e) {
            var isValid = true;
            $(this).find('[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('error');
                    isValid = false;
                } else {
                    $(this).removeClass('error');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('必須項目を入力してください。');
            }
        });

        // ローディング表示
        $(window).on('load', function() {
            $('.loading').fadeOut();
            $('body').addClass('loaded');
        });

    });

    // Window Load
    $(window).on('load', function() {
        // すべての画像が読み込まれた後の処理
        $('.site-main').addClass('loaded');
    });

})(jQuery);

// バニラJavaScriptの追加機能

// パフォーマンス最適化: スクロールイベントのスロットリング
function throttle(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// レトロな打ち込みアニメーション
function typeWriter(element, text, speed = 100) {
    let i = 0;
    element.innerHTML = '';

    function type() {
        if (i < text.length) {
            element.innerHTML += text.charAt(i);
            i++;
            setTimeout(type, speed);
        }
    }

    type();
}

// ページ読み込み時にヒーローテキストをアニメーション
document.addEventListener('DOMContentLoaded', function() {
    const heroTitle = document.querySelector('.hero-title');
    if (heroTitle) {
        const originalText = heroTitle.innerText;
        typeWriter(heroTitle, originalText, 80);
    }
});