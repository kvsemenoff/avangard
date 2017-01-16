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
        <div class="container">
        <div class="row">            
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 advantage" style="max-width: 300px;">
                <h2 style="max-width: 300px;">Наши преимущества:</h2>
                <ul style="max-width: 300px;">
                    <li><span><a href="">Изящество и Комфорт</a></span></li>
                    <li><span><a href="">Натуральный массив</a></span></li>
                    <li><span><a href="">Ортопедические Диваны</a></span></li>
                    <li><span><a href="">Экологичность конструкции</a></span></li>
                    <li><span><a href="">Гарантия 10 лет</a></span></li>
                    <li><span><a href="">Индивидуальный дизайн</a></span></li>
                </ul>
            </div>
            
            <div class="recommended col-xs-12 col-sm-12 col-md-6 col-lg-6"  style="margin-left: 0px;">
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
        </div>
    </div><!-- content_bottom -->

    <script
              src="https://code.jquery.com/jquery-3.1.1.min.js"
              integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
              crossorigin="anonymous"></script>

    <style type="text/css">
    </style>

    <script type="text/javascript">
        $( document ).ready(function() {
            var MIN_WIDTH = 900;

            var set_width = function () {
                var width = $(window).width();

                if (width < MIN_WIDTH) {

                    $('.advantage').width('90%');
                    $('.recommended').width('90%'); 
                    $('.advantage').css('margin-left', (width - 300)/2 + 10 + 'px');
                    $('.recommended').css('margin-left', (width - 300)/2 + 10 + 'px');
                } else {
                    $('.advantage').width('37%');
                    $('.recommended').width('37%');                    
                    $('.advantage').css('margin-left', '8%');
                    $('.recommended').css('margin-left', '18%');
                }
            }
            setInterval(set_width, 100);

            var flag = true;
            var set_br = function() {
                var width = $(window).width();                
                if (width < MIN_WIDTH && flag) {
                    flag=false;
                    $(".post-prew_img-wrap").after('<br class="mybr"><br class="mybr"><br class="mybr"><br class="mybr"><br class="mybr"><br class="mybr">');
                }

                if (width >= MIN_WIDTH && !flag) {
                    flag=true;
                    $(".mybr").remove();
                }
            }
            setInterval(set_br, 500);
        });

    </script>

<?php
get_footer();
