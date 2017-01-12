<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $product;
?>
<div class="wrap cf">

    <div class="col-left">
        <div class="content_top">

            <div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

                <div class="cf">
                    <h1 itemprop="name" class="product_title entry-title"><?php the_title(); ?></h1>
                    <h3>КОЛЛЕКЦИЯ:<span><?php  echo $product->get_tags( ',', '', '' ); ?></span></h3>
                </div>

                <div class="sliderkit thumb-slider">
                    <?php do_action( 'woo_images' ); // /inc/woo_actions.php ?>
                </div><!-- .sliderkit -->

            </div><!-- #product-<?php the_ID(); ?> -->
            <meta itemprop="url" content="<?php the_permalink(); ?>" />

        </div><!-- content_top -->

        <div class="content_middle cf">
            <?php do_action( 'woo_tabs' ); // /inc/woo_actions.php ?>
        </div><!-- content_middle -->

        <div class="content_bottom cf">
            <?php do_action( 'woo_recents',5 ); // /inc/woo_actions.php ?>
        </div><!-- content_bottom -->
    </div>

    <div class="col-right">
        <?php do_action( 'woo_related' ); // /inc/woo_actions.php ?>
    </div>

</div>
