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
<div class="container">
    <div class="row">
        <div class="col-md-12">


<?php while ( have_posts() ) : the_post(); ?>

    <div class="content_top">
        
            <h1 class="entry-title"><?php the_title(); ?></h1>
        
    </div><!-- #primary -->

    <div class="content_middle">
       
            <?php the_content(); ?>
        
    </div><!-- #primary -->

    <div class="content_bottom cf">
      
    </div><!-- #primary -->

<?php endwhile; // End of the loop. ?>

<?php
get_footer();
?>
</div>      
</div>
</div>