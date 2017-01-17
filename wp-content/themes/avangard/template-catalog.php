<?php
/**
 * Template name: Каталог
 *
 * @package avangard
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header(); ?>

<div class="wrap">

<?php while ( have_posts() ) : the_post(); ?>

    <div class="content_top">
        <h1 class="entry-title "><?php the_title(); ?></h1>
    </div>
    <div class="content_middle">
        <?php
        $collection = get_queried_object(); // текущая категория
        $collection_id = $collection->term_id; // id
        $collection_slug = $collection->slug; // slug
        $collection_name = $collection->name; // Название

        $args = array(
            'hide_empty' => 0,
            'orderby'    => 'term_order',
            'order'      => 'ASC'
        );
        $collections = get_terms('product_tag', $args ); // массив с коллекциями

	$sort_by = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

        foreach($collections as $collection){ // перебираем все коллекции
            $collection_name = $collection->name; // название коллекции
            $collection_slug = $collection->slug;

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
                echo '<p class="title">'.$collection_name.'</p>';
                echo '<div class="container">';
                echo '<div class="row">';
                
                $i = 0;
                foreach($product_objects as $single_product){
                    $extended = new WC_Product_Factory();  // создаём новый товар
                    $product = $extended->get_product($single_product->ID);
                    $product_id = $product->id;
                    $product_title = $single_product->post_title;
                    $product_link = get_permalink( $product_id );

                    $variation_list = array(); // массив с вариациями текущего товара

                    if($product->product_type != "simple"){
                        $prices = $product->get_variation_prices();
                        $min_regular_price = min($prices['regular_price']); // минимальная цена, руб.
                        $min_price = min($prices['price']); // минимальная цена со скидкой, руб.
                        if(intval($min_price)>0)
                    	    $min_price_html = 'Цена от <b>'.number_format($min_price, 0, '', ' ').'</b> руб.';
                    	else
                    	    $min_price_html = '';

                        $variations = $product->get_available_variations(); // все вариации текущего товара

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



                    $attachment_id = get_post_thumbnail_id( $product_id );
                    if ( $attachment_id != '' ) {
                        $image_title 	= $product_title;
                        $image_caption 	= get_post( $attachment_id )->post_excerpt;
                        $image_link  	= wp_get_attachment_url( $attachment_id );
                        $image       	= get_the_post_thumbnail( $product_id, 'shop_catalog', array(
                            'title'	=> $image_title,
                            'alt'	=> $image_title
                        ) );
                    } else {
                        $image = apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img width="370" height="250" src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $product_id );
                    }

                    ?>
                        <div class="col-md-4 col-lg-4 col-sm-12 col-xs-12">
                        <div class="item_wrap">
                            <a href="<?php echo $product_link ?>" title="<?php echo $product_title ?>">
                                <?php echo $image ?>
                                <span class="cf">
                            <p class="left"><?php echo $product_title ?></p>
                            <p class="left"><?php echo $min_price_html ?></p>
                        </span>
                            </a>
                            <table>
                                <tbody>
                                <tr class="row-1">
                                    <td class="col-1">Комплектность</td>
                                    <td class="col-2">Ш x Г x В</td>
                                    <td class="col-3">Спальное место</td>
                                    <td class="col-4">Механизм</td>
                                </tr>
                                <?php
                                foreach($variation_list as $variation_row){
                                    echo '<tr class="row-2">';
                                    foreach($variation_row as $vn => $variation_col){
                                        echo '<td class="col-'.($vn+1).'">'.$variation_col.'</td>';
                                    }
                                    echo '</tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        </div>

                <?php $i++; if ($i % 3 == 0) echo '<div class="clearfix"></div>'; }

                echo '</div>';
                echo '</div>';
             
            }
        }
        ?>   
        <script
              src="https://code.jquery.com/jquery-2.2.4.min.js"
              integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
              crossorigin="anonymous"></script>

    </div>
<?php
endwhile; // End of the loop.
?>
</div>
<?php
get_footer();
