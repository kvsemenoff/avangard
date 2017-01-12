<?php
/* вывод описания из произвольного поля внизу категории */
add_action( 'woocommerce_archive_description', 'add_description_in_category', 15, 2 );
if ( ! function_exists( 'add_description_in_category' ) ) {
    function add_description_in_category() {
        if ( is_tax( array( 'product_cat', 'product_tag' ) ) && 0 === absint( get_query_var( 'paged' ) ) ) {
            $top_category_id = get_queried_object_id();
            $description = get_field('description', 'product_cat_'.$top_category_id); // описание из произвольного поля
            if ( $description ) {
                echo $description;
            }
        }
    }
}

/* вывод изображений в товаре */
add_action( 'woo_images', 'woo_images', 15, 2 );
if ( ! function_exists( 'woo_images' ) ) {
    function woo_images() {
        global $post, $woocommerce, $product;
        $post_thumbnail_id = get_post_thumbnail_id();
        $attachment_ids = $product->get_gallery_attachment_ids();
        array_unshift($attachment_ids, $post_thumbnail_id);
        $images_t = array();

        if ( $attachment_ids ) {
            $loop 		= 0;
            foreach ( $attachment_ids as $attachment_id ) {

		if($loop==0) { $num = 1; }
		elseif($loop==1) { $num = 0; }
		else {$num = $loop;}

                $image_link = wp_get_attachment_url( $attachment_id );

                if ( ! $image_link )
                    continue;

                $image_title = esc_attr( get_the_title( $attachment_id ) );

                $thumbnail = wp_get_attachment_image( $attachment_id, 'shop_thumbnail', false );
                $image_single = wp_get_attachment_image( $attachment_id, 'shop_single', false );

                $images_t[$num]['image_link'] = $image_link;
                $images_t[$num]['thumbnail'] = $thumbnail;
                $images_t[$num]['image_single'] = $image_single;
                $images_t[$num]['image_title'] = $image_title;

                $loop++;
            } 
	    $images = array();
	    $co = count($images_t);
	    for($i=0;$i<$co;$i++)
		{
		    $images[$i]['image_link'] = $images_t[$i]['image_link'];
            	    $images[$i]['thumbnail'] = $images_t[$i]['thumbnail'];
            	    $images[$i]['image_single'] = $images_t[$i]['image_single'];
            	    $images[$i]['image_title'] = $images_t[$i]['image_title'];
		}
            ?>
            <div class="sliderkit-nav">
                <div class="sliderkit-btn sliderkit-nav-btn sliderkit-nav-prev"><a rel="nofollow" href="#" title="Previous"><span></span></a></div>
                <div class="sliderkit-btn sliderkit-nav-btn sliderkit-nav-next"><a rel="nofollow" href="#" title="Next"><span></span></a></div>
                <div class="sliderkit-nav-clip">
                    <ul>
                        <?php foreach($images as $image){ ?>
                            <li>
                                <a href="<?php echo $image['image_link'] ?>" rel="nofollow" title="<?php echo $image['image_title'] ?>">
                                    <?php echo $image['thumbnail'] ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="sliderkit-panels">
                <?php   foreach($images as $image){ ?>
                    <div class="sliderkit-panel">
                        <?php echo $image['image_single'] ?>
                    </div>
                <?php   } ?>
            </div>
            <script>
                var $ = jQuery.noConflict();
                jQuery(document).ready(function(){
                    // card-slider > Horizontal
                    jQuery(".sliderkit.thumb-slider").sliderkit({
                        auto:false,
                        shownavitems:7,
                        panelfx:"sliding",
                        panelfxspeed:500,
                        //mousewheel:true,
                        //navitemshover:true,
                        keyboard:true,
                        circular:true
                    });
                });
            </script>
        <?php
        }
    }
}

/* вывод Также рекомендуем в товаре */
add_action( 'woo_related', 'woo_related', 15, 2 );
if ( ! function_exists( 'woo_related' ) ) {
    function woo_related() {
        global $product, $woocommerce_loop;

        if ( empty( $product ) || ! $product->exists() ) {
            return;
        }

	$related = $product->get_upsells();
//	if ( sizeof( $related ) === 0 ) $related = $product->get_related( 5 );
        if ( sizeof( $related ) === 0 ) return;

        $args = apply_filters( 'woocommerce_related_products_args', array(
            'post_type'            => 'product',
            'ignore_sticky_posts'  => 1,
            'no_found_rows'        => 1,
            'posts_per_page'       => 5,
            'orderby'              => 'name',
            'post__in'             => $related,
            'post__not_in'         => array( $product->id )
        ) );

        $products = new WP_Query( $args );

        if ( $products->have_posts() ) : ?>
            <p class="title">Также рекомендуем</p>
            <ul>
            <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                <?php // Increase loop count
                $woocommerce_loop['loop']++;
                $post_id = get_the_ID();
                $extended = new WC_Product_Factory();  // создаём новый товар
                $related_product = $extended->get_product($post_id);
                if($related_product->product_type != "simple"){
                    $prices = $related_product->get_variation_prices();
                    $min_price = min($prices['price']); // минимальная цена, руб.
                    $min_price_html = 'от <b>'.number_format($min_price, 0, '', ' ').'</b> руб.';
                } else {
                    $min_price = (int)$related_product->price; // минимальная цена со скидкой, руб.
                    $min_price_html = '<b>'.number_format($min_price, 0, '', ' ').'</b> руб.';
                }
                $post_thumbnail_id = get_post_thumbnail_id();
                $image = wp_get_attachment_image( $post_thumbnail_id, 'product_box', false );
                ?>
                <li>
                    <a href="<?php echo get_the_permalink() ?>">
                        <?php echo $image; ?>
                        <?php echo '<p class="name"><b>' . get_the_title() . '</b></p>'; ?>
                        <p class="price"><?php echo $min_price_html; ?></p>
                    </a>
                </li>
            <?php endwhile; // end of the loop. ?>
            </ul>
        <?php endif;

        wp_reset_postdata();
    }
}

/* вывод вкладок с параметрами в товаре */
add_action( 'woo_tabs', 'woocommerce_output_product_data_tabs', 15, 2 );

/* вкладка матрасы */
if ( ! function_exists( 'woo_matraz_tab' ) ) {
    function woo_matraz_tab() {
        global $product, $woocommerce_loop,$wp_query;

        if ( empty( $product ) || ! $product->exists() ) {
            return;
        }

        $matraz = get_field('matraz',$product->id);
        if($matraz){
            echo $matraz;
        } else {
            echo '';
        }
    }
}

/* вкладка цвет массива бука */
if ( ! function_exists( 'woo_beech_color_tab' ) ) {
    function woo_beech_color_tab() {
        global $product, $woocommerce_loop,$wp_query;

        if ( empty( $product ) || ! $product->exists() ) {
            return;
        }

        $grouped_attributes = get_grouped_attributes($product->id,$product->get_attributes()); // все атрибуты текущего товара
        ?>
    <?php if(isset($grouped_attributes['pa_beech_color'])){ ?>
        <ul class="cf">
            <?php foreach($grouped_attributes['pa_beech_color']['values'] as $с => $beech_color_value){ ?>
                <?php $image_src = (isset($beech_color_value['image'])) ? $beech_color_value['image'] : "" ?>
                <li>
                    <img src="<?php echo $image_src ?>" alt="<?php echo $beech_color_value['name'] ?>">
                    <span><?php echo $beech_color_value['name'] ?></span>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
        <?php

    }
}

/* вкладка варианты чехла */
if ( ! function_exists( 'woo_cover_options_tab' ) ) {
    function woo_cover_options_tab() {
        global $product, $woocommerce_loop,$wp_query;

        if ( empty( $product ) || ! $product->exists() ) {
            return;
        }

        $grouped_attributes = get_grouped_attributes($product->id,$product->get_attributes()); // все атрибуты текущего товара
        ?>
    <?php if(isset($grouped_attributes['pa_beech_color'])){ ?>
        <ul class="cf">
            <?php foreach($grouped_attributes['pa_cover_options']['values'] as $с => $cover_options_value){ ?>
                <?php $image_src = (isset($cover_options_value['image'])) ? $cover_options_value['image'] : "" ?>
                <li>
                    <img src="<?php echo $image_src ?>" alt="<?php echo $cover_options_value['name'] ?>">
                    <span><?php echo $cover_options_value['name'] ?></span>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
        <?php
    }
}

/* вкладка размеры */
if ( ! function_exists( 'woo_sizes_tab' ) ) {
    function woo_sizes_tab() {
        global $product, $woocommerce_loop,$wp_query;

        if ( empty( $product ) || ! $product->exists() ) {
            return;
        }

        $variations = $product->get_available_variations(); // все вариации текущего товара

        $variation_list = array(); // массив с вариациями текущего товара
        $drop_attributes = array("completeness","width","depth","height","bed_size","transformation");

        foreach ($variations as $key => $variation) {
            $attributes = $variation['attributes'];  // находим его атрибуты (4,4,4)

            foreach($drop_attributes as $drop_attribute){
                $slug = 'attribute_pa_'.$drop_attribute;
                if(isset($attributes[$slug]) && $attributes[$slug]){
                    $variation_list[$key][$drop_attribute] = get_attribute_value($slug,$attributes[$slug]);
                } else {
                    $variation_list[$key][$drop_attribute] = '';
                }
            }

            $display_price = $variation['display_price']; // минимальная цена, руб.
            $variation_list[$key]['display_price'] = number_format($display_price, 0, '', ' ');
        } ?>
        <table>
            <tbody><tr class="row-1">
                <th colspan="7">ГАБАРИТНЫЕ РАЗМЕРЫ</th>
            </tr>
            <tr class="row-2">
                <td class="col-1">Комплектность</td>
                <td class="col-2">Ширина</td>
                <td class="col-3">Глубина</td>
                <td class="col-4">Высота</td>
                <td class="col-5">Спальное место</td>
                <td class="col-6">Механизм трансформации</td>
                <td class="col-7">Цена от (руб.)</td>
            </tr>
            <?php foreach($variation_list as $с => $variation_row){ ?>
                <tr class="row-3">
                    <td class="col-1"><?php echo $variation_row['completeness'] ?></td>
                    <td class="col-2"><?php echo $variation_row['width'] ?></td>
                    <td class="col-3"><?php echo $variation_row['depth'] ?></td>
                    <td class="col-4"><?php echo $variation_row['height'] ?></td>
                    <td class="col-5"><?php echo $variation_row['bed_size'] ?></td>
                    <td class="col-6"><?php echo $variation_row['transformation'] ?></td>
                    <td class="col-7"><?php echo $variation_row['display_price'] ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php
    }
}

/* функция для получения кода вкладок в товаре из соответствующих функций */
add_filter('woo_product_tabs', 'woo_product_tabs', 10);
if ( ! function_exists( 'woo_product_tabs' ) ) {
    function woo_product_tabs() {

        global $product, $post;
        $tabs = array();

        $custom_fields = get_post_custom($post->ID); // все произвольные поля салона

        // matraz tab - shows matraz info
        if ( isset($custom_fields['matraz']) && ($custom_fields['matraz'][0] != '') ) {
            $tabs['matraz'] = array(
                'title'    => 'матрасы',
                'priority' => 10,
                'callback' => 'woo_matraz_tab'
            );
        }

        // цвет массива бука tab - shows attributes
        if ( $product && ( $product->has_attributes() ) && isset($product->product_attributes['pa_beech_color']) ) {
            $tabs['beech_color'] = array(
                'title'    => 'цвет массива бука',
                'priority' => 20,
                'callback' => 'woo_beech_color_tab'
            );
        }

        // варианты чехла tab - shows attributes
        if ( $product && ( $product->has_attributes() ) && isset($product->product_attributes['pa_cover_options']) ) {
            $tabs['cover_options'] = array(
                'title'    => 'варианты чехла',
                'priority' => 30,
                'callback' => 'woo_cover_options_tab'
            );
        }

        // размеры tab - shows attributes
        if ( $product && ( $product->has_attributes() ) &&
               (isset($product->product_attributes['pa_width']) ||
                isset($product->product_attributes['pa_depth']) ||
                isset($product->product_attributes['pa_height']) )
        ) {
            $tabs['sizes'] = array(
                'title'    => 'размеры',
                'priority' => 40,
                'callback' => 'woo_sizes_tab'
            );
        }

        // info tab - shows product content
        if ( $post->post_content ) {
            $tabs['description'] = array(
                'title'    => 'информация',
                'priority' => 100,
                'callback' => 'woocommerce_product_description_tab'
            );
        }

        return $tabs;
    }
}

function get_grouped_attributes($product_id,$attributes) {
    foreach ($attributes as $attribute) {  // перебираем атрибуты этого товара
        if ($attribute['is_visible']) {  // если атрибут надо показывать
            $all_attributes[] = $attribute['name'];  // записываем все атрибуты в массив
        }
    }

    $all_attributes_terms = array();

    if($all_attributes){
        foreach ($all_attributes as $attribute_slug) {  // перебираем все атрибуты данной категории
            $fields_slugs = array();  // массив для слугов полей очередного атрибута
            $fields = get_fields($attribute_slug); // находим все поля очередного атрибута
            if ($fields) {                // если у атрибута есть поля
                foreach ($fields as $field_slug => $field) {
                    if ($field_slug) {
                        $fields_slugs[] = $field_slug;
                    }
                }
            }

            $tax = get_taxonomy($attribute_slug); // находим таксономию атрибута
            $title = $tax->labels->name;            // находим название атрибута

            $all_attributes_terms[$attribute_slug]['name'] = $title;
            $all_attributes_terms[$attribute_slug]['slug'] = $attribute_slug;

            $args = array(
                'page' => 1,
                'number' => 80,
                'orderby' => "term_order",
                'order' => "ASC",
                'search' => '',
                'hide_empty' => 0
            );

            $attribute_terms = wp_get_object_terms($product_id, $attribute_slug,$args); //  массив значений текущего атрибута

            foreach ($attribute_terms as $attribute_term) { //  перебираем массив значений текущего атрибута
                $tmp_arr_terms = array();
                $tmp_arr_terms['term_id'] = $attribute_term->term_id;               // ID значения атрибута
                $tmp_arr_terms['name'] = $attribute_term->name;                     // Название значения атрибута
                $tmp_arr_terms['description'] = $attribute_term->description;       // Описание значения атрибута

                foreach ($fields_slugs as $field_slug) {                // перебираем ярлыки полей очередного атрибута (deco_karkas_image)
                    $cur_field_value = get_field($field_slug, $attribute_slug . '_' . $tmp_arr_terms['term_id']); // находим величину текущего поля
                    if ($cur_field_value) {                             // если у поля установлена величина
                        $tmp_arr_terms[$field_slug] = $cur_field_value; // записываем в отдельную строку величину
                        if (stristr($field_slug, '_image')) {            // если в названии поля есть _image
                            $tmp_arr_terms['image'] = $cur_field_value; // записываем в отдельную строку изображение
                        } else {
                            $tmp_arr_terms['image'] = "";
                        }
                    }
                }

                $all_attributes_terms[$attribute_slug]['values'][] = $tmp_arr_terms;
            } // #foreach ($attribute_terms as $attribute_term) { //  перебираем массив значений текущего атрибута
        }
    }

    return $all_attributes_terms;
}

function get_attribute_value($attribute_slug,$attribute_value_slug) {
    // If this is a term slug, get the term's nice name
    if ( taxonomy_exists( esc_attr( str_replace( 'attribute_', '', $attribute_slug ) ) ) ) {
        $term = get_term_by( 'slug', $attribute_value_slug, esc_attr( str_replace( 'attribute_', '', $attribute_slug ) ) );
        if ( ! is_wp_error( $term ) && ! empty( $term->name ) ) {
            $attribute_value = $term->name;
        }
    } else {
        $attribute_value = ucwords( str_replace( '-', ' ', $attribute_value_slug ) );
    }

    return $attribute_value;
}

function get_grouped_terms($terms) {
    $grouped_terms = array();
    foreach ($terms as $term) { //  перебираем массив значений текущего атрибута
        $parent_term_id = $term->parent;                          // ID родительского значения атрибута
        $term_id = $term->term_id;
        $taxonomy = $term->taxonomy;
        $tmp_arr_terms = array();
        $tmp_arr_terms['term_id'] = $term_id;       // ID значения атрибута
        $tmp_arr_terms['name'] = $term->name;       // Название значения атрибута
        $tmp_arr_terms['slug'] = $term->slug;       // Слуг значения атрибута
        $tmp_arr_terms['taxonomy'] = $taxonomy;             // Таксономия атрибута
        $tmp_arr_terms['description'] = $term->description; // Описание значения атрибута
        $tmp_arr_terms['parent_term_id'] = $parent_term_id;
        $fields = get_fields($taxonomy); // находим все поля очередного атрибута
        if ($fields) {                // если у атрибута есть поля
            foreach ($fields as $fk1 => $field) {                // перебираем ярлыки полей очередного атрибута (deco_karkas_image)
                $cur_field_value = get_field($fk1, $taxonomy . '_' . $term_id); // находим величину текущего поля
                if ($cur_field_value) {                             // если у поля установлена величина
                    if (stristr($fk1, '_image')) {            // если в названии поля есть _image
                        $tmp_arr_terms['image'] = $cur_field_value; // записываем в отдельную строку изображение
                    } else {
                        $tmp_arr_terms['image'] = "";
                    }
                }
            }
        }
        $grouped_terms[] = $tmp_arr_terms;
    }

    return $grouped_terms;
}

/* вывод недавно просмотренных товаров в товаре */
add_action( 'woo_recents', 'woo_recents', 15, 2 );
if ( ! function_exists( 'woo_recents' ) ) {
    function woo_recents( $per_page ) {

        // Get WooCommerce Global
        global $woocommerce,$woocommerce_loop,$product;

        // Get recently viewed product cookies data
        $viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
        $viewed_products = array_filter( array_map( 'absint', $viewed_products ) );

        $key = array_search($product->id, $viewed_products);
        if ($key !== false)
        {
            unset($viewed_products[$key]);
        }

        // If no data, quit
        if ( empty( $viewed_products ) )
            return __( 'Вы еще не смотрели не одного продукта', 'rc_wc_rvp' );

        // Create the object
        ob_start();

        // Get products per page
        if( !isset( $per_page ) ? $number = 5 : $number = $per_page )

            // Create query arguments array
            $query_args = array(
                'posts_per_page' => $number,
                'no_found_rows'  => 1,
                'post_status'    => 'publish',
                'post_type'      => 'product',
                'post__in'       => $viewed_products,
                'orderby'        => 'rand'
            );

        // Add meta_query to query args
        $query_args['meta_query'] = array();

        // Check products stock status
        $query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();

        // Create a new query
        $r = new WP_Query($query_args);

        // If query return results

        if ( $r->have_posts() ) { ?>

            <p class="title">Вы недавно смотрели</p>
            <div class="sliderkit recent-products">
            <div class="sliderkit-nav">
            <div class="sliderkit-nav-clip">
            <ul>

            <?php while ( $r->have_posts() ) : $r->the_post(); ?>
                <?php // Increase loop count
                $woocommerce_loop['loop']++;
                $post_id = get_the_ID();
                $extended = new WC_Product_Factory();  // создаём новый товар
                $related_product = $extended->get_product($post_id);
                if($related_product->product_type != "simple"){
                    $prices = $related_product->get_variation_prices();
                    $min_price = min($prices['price']); // минимальная цена, руб.
                    $min_price_html = 'от <b>'.number_format($min_price, 0, '', ' ').'</b> руб.';
                } else {
                    $min_price = (int)$related_product->price; // минимальная цена со скидкой, руб.
                    $min_price_html = '<b>'.number_format($min_price, 0, '', ' ').'</b> руб.';
                }
                $post_thumbnail_id = get_post_thumbnail_id();
                $image = wp_get_attachment_image( $post_thumbnail_id, 'product_box', false );
                ?>
                <li>
                    <a href="<?php echo get_the_permalink() ?>">
                        <?php echo $image; ?>
                        <?php echo '<p class="name"><b>' . get_the_title() . '</b></p>'; ?>
                        <p class="price"><?php echo $min_price_html ?></p>
                    </a>
                </li>
            <?php endwhile; // end of the loop. ?>
            </ul>
            </div>
                <div class="sliderkit-btn sliderkit-nav-btn sliderkit-nav-prev"><a href="#" title="Step backward"><span></span></a></div>
                <div class="sliderkit-btn sliderkit-nav-btn sliderkit-nav-next"><a href="#" title="Step forward"><span></span></a></div>
            </div>
            </div>
            <script>
                $(document).ready(function(){
                    jQuery(".sliderkit.recent-products").sliderkit({
                        auto:false,
                        shownavitems:2,
                        scroll:1,
                        circular:true
                    });
                });
            </script>
<?php
        }

        wp_reset_postdata();
        // Return whole content
        return true;
    }
}

/**
 * Track product views.
 */
function woo_track_product_view() {
    if ( ! is_singular( 'product' ) ) {
        return;
    }

    global $post;

    if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) )
        $viewed_products = array();
    else
        $viewed_products = (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] );

    if ( ! in_array( $post->ID, $viewed_products ) ) {
        $viewed_products[] = $post->ID;
    }

    if ( sizeof( $viewed_products ) > 15 ) {
        array_shift( $viewed_products );
    }

    // Store for session only
    wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
}
add_action( 'template_redirect', 'woo_track_product_view', 15 );

