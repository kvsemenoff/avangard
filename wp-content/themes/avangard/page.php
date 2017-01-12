<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package avangard
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

    <div class="content_top">
        <div class="wrap">
            <h1 class="entry-title"><?php the_title(); ?></h1>
        </div><!-- #main -->
	</div><!-- #primary -->

    <div class="content_middle">
        <div class="wrap">
            <?php the_content(); ?>
        </div><!-- #main -->
    </div><!-- #primary -->

    <div class="content_bottom cf">
        <div class="wrap">

        </div><!-- #main -->
    </div><!-- #primary -->

<?php endwhile; // End of the loop. ?>

<?php
get_footer();
