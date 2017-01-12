<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package avangard
 */

?>

    </section><!-- #content -->

    <footer id="colophon" class="site-footer" role="contentinfo">
        <div class="footer_top cf">
            <div class="wrap">
                <?php wp_nav_menu( array( 'theme_location' => 'footer-menu_left', 'menu_class' => 'footer-menu_left', 'container' => ''  ) ); ?>
                <?php wp_nav_menu( array( 'theme_location' => 'footer-menu_right', 'menu_class' => 'footer-menu_right', 'container' => ''  ) ); ?>
                <div class="social">
                    <a href="http://vk.com/club57166671" title="ВКонтакте" class="social_vk"></a>
                    <a href="https://instagram.com/avangard_fm" title="Instagram" class="social_inst"></a>
                    <a href="https://www.facebook.com/avangard.biz" title="Facebook" class="social_fb"></a>
                </div>
            </div>
        </div>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

        <div class="footer_bottom cf">
            <div class="wrap">
                    <center>
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <p class="studio" style="margin-left: 0;">© 2009-2016 Мебельная фабрика Авангард<br>  Поддержка сайта - студия «<a href="https://media-bridge.ru/" target="_blank">Media-Bridge</a>»</p>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <p class="tel">+7(495)357-13-00</p>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <p class="copyright">Копирование, публикация и использование<br> материалов сайта ЗАПРЕЩЕНЫ!</p>
                        </div>
                    </center>
            </div>
        </div>
    </footer><!-- #colophon -->

<?php wp_footer(); ?>

</body>
</html>
