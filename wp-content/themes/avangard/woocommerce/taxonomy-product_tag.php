<?php
/**
 * The Template for displaying products in a product tag. Simply includes the archive template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/taxonomy-product_tag.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' ); ?>

<?php
/**
 * woocommerce_before_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 */
do_action( 'woocommerce_before_main_content' );
?>

<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
    <div class="content_top">
        <div class="wrap">
            <h1 class="page-title">Коллекция <?php woocommerce_page_title(); ?></h1>
        </div>
    </div>
<?php endif; ?>

<div class="content_middle">
    <div class="wrap">

        <?php
        /**
         * woocommerce_before_shop_loop hook.
         *
         * @hooked woocommerce_result_count - 20
         * @hooked woocommerce_catalog_ordering - 30
         */
        do_action( 'woocommerce_before_shop_loop' );
        ?>

        <?php
        $collection = get_queried_object(); // текущая категория
        $collection_id = $collection->term_id; // id
        $collection_slug = $collection->slug; // slug
        $collection_name = $collection->name; // Название

	$sort_by = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

        $args = array(
            'posts_per_page'  => -1,
            'post_type'       => 'product',
            'orderby'  => 'menu_order',
            'order'  => 'ASC',
            'tax_query'       => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'product_tag',
                    'field'    => 'slug',
                    'terms'    => $collection_slug,
                )
            )
        );
        $product_objects = get_posts( $args ); // массив с товарами с очередной коллекции

        if($product_objects){
            echo '<ul class="cf items-list">';

            foreach($product_objects as $p => $single_product){
                $extended = new WC_Product_Factory();  // создаём новый товар
                $product = $extended->get_product($single_product->ID);
                $product_id = $product->id;
                $product_title = $single_product->post_title;
                $product_link = get_permalink( $product_id );

                if($product->product_type != "simple"){
                    $prices = $product->get_variation_prices();
                    $min_regular_price = min($prices['regular_price']); // минимальная цена, руб.
                    $min_price = min($prices['price']); // минимальная цена со скидкой, руб.
                    $min_price_html = 'Цена от <b>'.number_format($min_price, 0, '', ' ').'</b> руб.';
                    $variations = $product->get_available_variations(); // все вариации текущего товара

                    $variation_list = array(); // массив с вариациями текущего товара
                    $drop_attributes = array("completeness","width","depth","height","bed_size","transformation");

                    foreach ($variations as $key => $variation) {
                        $attributes = $variation['attributes'];  // находим его атрибуты (4,4,4)

                        foreach($drop_attributes as $drop_attribute){
                            $slug = 'attribute_pa_'.$drop_attribute;
                            if(isset($attributes[$slug])&&$attributes[$slug]){
                                ${$drop_attribute} = get_attribute_value($slug,$attributes[$slug]);
                            } else {
                                ${$drop_attribute} = '';
                            }
                        }

                        $shgv = '';
                        if($width != ''){
                            $shgv = $width;
                            if($depth != ''){
                                $shgv .= 'x'.$depth;
                                if($height != ''){
                                    $shgv .= 'x'.$height;
                                }
                            } else {
                                if($height != ''){
                                    $shgv .= 'x'.$height;
                                }
                            }
                        } else {
                            if($depth != ''){
                                $shgv .= 'x'.$depth;
                                if($height != ''){
                                    $shgv .= 'x'.$height;
                                }
                            } else {
                                if($height != ''){
                                    $shgv .= 'x'.$height;
                                }
                            }
                        }

                        $variation_list[$key][] = $completeness;
                        $variation_list[$key][] = $shgv;
                        $variation_list[$key][] = $bed_size;
                        $variation_list[$key][] = $transformation;
                    }
                } else {
                    $min_price = (int)$product->price; // минимальная цена со скидкой, руб.
                    if(intval($min_price)>0)
                	$min_price_html = 'Цена <b>'.number_format($min_price, 0, '', ' ').'</b> руб.';
            	    else
            		$min_price_html = '';
                }

                if ( has_post_thumbnail() ) {
                    $image_title 	= $product_title;
                    $image_caption 	= get_post( get_post_thumbnail_id() )->post_excerpt;
                    $image_link  	= wp_get_attachment_url( get_post_thumbnail_id() );
                    $image       	= get_the_post_thumbnail( $single_product->ID, 'shop_catalog', array(
                        'title'	=> $image_title,
                        'alt'	=> $image_title
                    ) );
                } else {
                    $image = apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img width="370" height="250" src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $single_product->ID );
                } ?>

                <li>
                    <div class="item_wrap">
                        <a href="<?php echo $product_link ?>" title="<?php echo $product_title ?>">
                            <?php echo $image ?>
                            <span class="cf">
                        <p class="left"><?php echo $product_title ?></p>
                        <p class="right"><?php echo $min_price_html ?></p>
                    </span>
                        </a>
                    </div>
                </li>
            <?php   }

            echo '</ul>';

        }
        ?>

        <?php
        /**
         * woocommerce_after_shop_loop hook.
         *
         * @hooked woocommerce_pagination - 10
         */
        do_action( 'woocommerce_after_shop_loop' );
        ?>
    </div>
</div>

<div class="content_bottom cf">
    <div class="wrap">
        <?php
        /**
         * woocommerce_archive_description hook.
         *
         * @hooked woocommerce_taxonomy_archive_description - 10
         * @hooked woocommerce_product_archive_description - 10
         */
        do_action( 'woocommerce_archive_description' );
        ?>
        <?php
        /**
         * woocommerce_after_main_content hook.
         *
         * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
         */
        do_action( 'woocommerce_after_main_content' );
        ?>

        <?php
        /**
         * woocommerce_sidebar hook.
         *
         * @hooked woocommerce_get_sidebar - 10
         */
        do_action( 'woocommerce_sidebar' );
        ?>
    </div>
</div>

<?php get_footer( 'shop' ); ?>
