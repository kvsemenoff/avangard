<?php
/**
 * Template name: Акции
 *
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package avangard
 */

get_header(); ?>

<div class="wrap">

<?php while ( have_posts() ) : the_post(); ?>

    <div class="content_top cf">
        <h1 class="entry-title"><?php the_title(); ?></h1>
    </div>

    <div class="content_middle cf">
<?php
    $args = array(
        'posts_per_page'  => -1,
        'post_type'       => 'banner',
        'orderby'         => 'menu_order ',
        'order'           => 'ASC',
        'tax_query'       => array(
            array(
                'taxonomy' => 'banner_groups',
                'field'    => 'slug',
                'terms'    => 'aktsii',
            )
        )
    );
    $banners = get_posts( $args ); // массив с баннерами с акциями
    ?>

     <?php foreach($banners as $banner){
            $image_title = $banner->post_title;
            $banner_content = $banner->post_content;
            $banner_url = get_post_meta($banner->ID, '_ikcf_target_url', true); // Акции в салоне
            $attachment_id = get_post_thumbnail_id( $banner->ID );
            $image = get_the_post_thumbnail( $banner->ID, 'rpwe-thumbnail', array(
                'title' => $image_title,
                'alt'   => $image_title
            ) );
            ?>

            <div class="banner-item">
                <div class="banner-item__left" style="vertical-align: top;"><a href="<?php echo $banner_url ?>"><?php echo $image ?></a></div>
               
                <div class="banner-item__right">
                    <?php echo $banner_content ?>
                </div>
            </div>
            
        <?php } ?>


 

    </div>
    <br /><br />
    <div class="content_bottom cf">
        <?php  the_content(); ?>
    </div>

<?php  endwhile; // End of the loop. ?>

</div>
<?php
get_footer();
