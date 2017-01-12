<?php
/**
 * Template Name: Home
 *
 * @package avangard
 */

get_header(); ?>

    <div class="content_top">
        <div class="main-slider sliderkit cf" style="display: block;">
            <?php putRevSlider("slider-home","homepage") ?>
        </div><!-- slider -->
    </div><!-- content_top -->

    <div class="content_middle">
        <div class="wrap">
            <?php echo do_shortcode("[banner group='home' count='6' orderby='menu_order']") ?>
        </div>
    </div><!-- content_middle -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <div class="content_bottom cf">
        <div class="wrap">
            <div class="advantage">
                <h2>Наши преимущества:</h2>
                <ul>
                    <li><span><a href="">Изящество и Комфорт</a></span></li>
                    <li><span><a href="">Натуральный массив</a></span></li>
                    <li><span><a href="">Ортопедические Диваны</a></span></li>
                    <li><span><a href="">Экологичность конструкции</a></span></li>
                    <li><span><a href="">Гарантия 10 лет</a></span></li>
                    <li><span><a href="">Индивидуальный дизайн</a></span></li>
                </ul>
            </div>
            <div class="recommended">
                <?php if ( is_active_sidebar( 'latest_news' ) ) : ?>
                <div class="last-news">
                    <?php dynamic_sidebar( 'latest_news' ); ?>
                </div>
                <?php endif; ?>
                <?php if ( is_active_sidebar( 'latest_articles' ) ) : ?>
                <div class="suggest">
                    <?php dynamic_sidebar( 'latest_articles' ); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- content_bottom -->
    <script
              src="https://code.jquery.com/jquery-3.1.1.min.js"
              integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
              crossorigin="anonymous"></script>

    <script type="text/javascript">
        $( document ).ready(function() {
            var MIN_WIDTH = 900;

            var set_width = function () {
                var width = $(window).width();

                if (width < MIN_WIDTH) {
                    $('.advantage').width('100%');
                    $('.recommended').width('100%');
                    $('.advantage').css('margin-left', '5%');
                    $('.recommended').css('margin-left', '5%');
                    $('.advantage').css('margin-left', '5%');
                    $('.recommended').css('margin-left', '5%');
                } else {
                    $('.advantage').width('37%');
                    $('.recommended').width('37%');                    
                }
            }
            setInterval(set_width, 100);
        });

    </script>

<?php
get_footer();
