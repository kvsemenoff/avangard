<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package avangard
 */

get_header(); ?>

        <div class="content_top">
            <div class="wrap">
               <h1 class="page-title"><?php esc_html_e( 'Ошибка! Страница не найдена' ); ?></h1>
            </div>
        </div>

        <div class="content_middle">
            <div class="wrap">

                <p><?php esc_html_e( 'Может попробовать поискать в другом месте?' ); ?></p>

                <div id="search2">
                    <form role="search" method="get" id="searchform2" action="<?php echo home_url( '/' ) ?>" >
                        <input type="text" value="<?php echo get_search_query() ?>" name="s" id="s" placeholder="Поиск" /><button type="submit" value=""></button>
                    </form>
                </div>

            </div><!-- .wrap -->
        </div><!-- .content_middle -->

<?php
get_footer();
