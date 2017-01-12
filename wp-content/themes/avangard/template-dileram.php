<?php
/**
 * Template name: Дилерам
 *
 * @package avangard
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

    <div class="content_top">
        <div class="wrap">

            <?php putRevSlider( "slider_dileram" ) ?>

            <div class="block-links">
                <h1 class="entry-title"><?php the_title(); ?></h1>
                <ul>
                    <li><a data-href="#earn" href="#">Зарабатывайте с нами</a></li>
                    <li><a data-href="#terms" href="#">Условия сотрудничества</a></li>
                    <li><a data-href="#manuals" href="#">Изучение продукции (Обучающий фильм) и Методические пособия</a></li>
                    <li><a data-href="#tissue" href="#">Информация по тканям</a></li>
                    <li><a data-href="#warehouses" href="#">Работа склада</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="content_bottom cf">
        <?php the_content(); ?>
    </div>
<?php
endwhile; // End of the loop.
?>
<?php
get_footer();
