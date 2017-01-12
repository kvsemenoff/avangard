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

    <div class="wrap">
        <div class="content_top">
            <div class="cf">
                <div class="info">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                    <?php
                    $salon_post_id = get_the_ID();
                    $salon_category = get_the_terms( $salon_post_id, 'salon_category' );
                    $top_cat_id = get_top_cat_id( $salon_category[0]->term_id );
                    $top_cat = get_category($top_cat_id); // get the object for the catid
                    $top_cat_slug = $top_cat->slug;

                    $address = get_post_meta($salon_post_id, 'address', true); // Адрес салона
                    $telephone = get_post_meta($salon_post_id, 'telephone', true); // Телефон салона
                    $work_time = get_post_meta($salon_post_id, 'work_time', true); // Часы работы салона
                    $point = get_post_meta($salon_post_id, 'point', true); // Координаты салона
                    $panorama = get_post_meta($salon_post_id, 'panorama', true); // Скрипт панорамы

                    $height = 300; // высота карты
                    $zoom_inital = 13; // начальный зум

                    $iconsize = '20,34'; // размер иконок
                    $iconoffset = '-10,-34'; // отступ иконок

                    $icon_salon = get_site_url().'/wp-content/uploads/salonA.png'; // путь к иконке салонов
                    $icon_podium = get_site_url().'/wp-content/uploads/podium.png'; // путь к иконке подиумов

                    $salon_types = get_the_terms ( $salon_post_id, 'salon_type' ); // массив с типами салонов
                    if($salon_types){
                        $salon_type_slug = $salon_types[0]->slug;
                    } else {
                        $salon_type_slug = '';
                    }

                    if($point != ''){
                        $map = '[yandexMap center="'.$point.'" height="'.$height.'" zoom_inital='.$zoom_inital.']'; // начало шорткода
                        if($salon_type_slug == 'firmennye_salony'){
                            $description = '<h2 class="header"><img src="'.$icon_salon.'" /><b>Фирменный салон</b></h2><h3 class="on_map"><b>'.$post->post_title.'</b></h3><br>';
                            $description .= 'Адрес: <b>'.$address.'</b><br>Телефон: <b>'.$telephone.'</b><br>Часы работы: <b>'.$work_time.'</b>';
                            $description = refixFalseWord($description); // фикс тегов описания
                            /* добаляем в шорткод карты строку с описанием текущего салона */
                            $map .= '[yamap_label coord="'.$point.'" description="'.$description.'" icon="' . $icon_salon . '" iconsize="' . $iconsize . '" iconoffset="' . $iconoffset . '"]';
                        } else if($salon_type_slug == 'firmennye_podiumy'){
                            $description = '<h2 class="header"><img src="'.$icon_podium.'" /><b>Фирменный подиум</b></h2><h3 class="on_map"><b>'.$post->post_title.'</b></h3><br>';
                            $description .= 'Адрес: <b>'.$address.'</b><br>Телефон: <b>'.$telephone.'</b><br>Часы работы: <b>'.$work_time.'</b>';
                            $description = refixFalseWord($description); // фикс тегов описания
                            /* добаляем в шорткод карты строку с описанием текущего салона */
                            $map .= '[yamap_label coord="'.$point.'" description="'.$description.'" icon="' . $icon_podium . '" iconsize="' . $iconsize . '" iconoffset="' . $iconoffset . '"]';
                        }
                        $map .= '[/yandexMap]'; // конец шорткода
                    }
                    ?>
                    <p class="title">Адрес:</p>
                    <p class="content"><?php echo $address ?></p>
                    <p class="title">Телефон:</p>
                    <p class="content"><?php echo $telephone ?></p>
                    <p class="title">Часы работы:</p>
                    <p class="content"><?php echo $work_time ?></p>
                    <p class="title">Доп. инфо:</p>
                    <p class="content"><?php the_content(); ?></p>
                <?php endwhile; // End of the loop. ?>
                </div>
                <div class="foto">
                    <!-- Main container -->
                    <div class="sliderkit salon_images">
                        <!-- Panels container -->
                        <div class="sliderkit-panels">
                            <!-- Go buttons -->
                            <div class="sliderkit-go-btn sliderkit-go-prev"><a rel="nofollow" href="#" title="Назад"><span>Назад</span></a></div>
                            <div class="sliderkit-go-btn sliderkit-go-next"><a rel="nofollow" href="#" title="Вперёд"><span>Вперёд</span></a></div>
                            <?php echo do_shortcode('[mgop_gallery slug="galereya-salona" image_size="single_salon" markup="div"/]') ?>
                        </div><!-- // end of Panels container -->
                    </div><!-- // end of Main container -->

                </div>
            </div>
            <div class="aktsii">
                <h3>АКЦИИ</h3>
                <ul class="cf">
                    <?php
                    $aktsii = get_post_meta($salon_post_id, 'aktsii', true); // Акции в салоне

                    if($aktsii != ''){
                        $args = array(
                            'posts_per_page'  => -1,
                            'post_type'       => 'banner',
                            'orderby'         => 'menu_order ',
                            'order'           => 'ASC',
                            'include'         => $aktsii,
                            'tax_query'       => array(
                                array(
                                    'taxonomy' => 'banner_groups',
                                    'field'    => 'slug',
                                    'terms'    => 'aktsii',
                                )
                            )
                        );
                        $banners = get_posts( $args ); // массив с баннерами с акциями
                        foreach($banners as $banner){
                            $image_title = $banner->post_title;
                            $attachment_id = get_post_thumbnail_id( $banner->ID );
                            $image = get_the_post_thumbnail( $banner->ID, 'shop_catalog', array(
                                'title'	=> $image_title,
                                'alt'	=> $image_title
                            ) ); ?>
                            <li><a href="/aktsii/"><?php echo $image ?></a></li>
                <?php   } ?>
            <?php   } ?>
                </ul>
            </div>
        </div><!-- content_top -->

        <div class="content_middle">

    <?php if(isset($top_cat_slug) && $top_cat_slug == 'moscow') : ?>

            <h3>ДИВАНЫ В САЛОНЕ</h3>
        <?php
        $args = array(
            'hide_empty' => 0,
            'orderby'    => 'term_order',
            'order'      => 'ASC'
        );
        $collections = get_terms('product_tag', $args ); // массив с коллекциями

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
                        'terms'    => $collection_slug
                    ),
                    array(
                        'taxonomy' => 'salons',
                        'field'    => 'slug',
                        'terms'    => $post->post_name
                    )
                )
            );
            $product_objects = get_posts( $args ); // массив с товарами с очередной коллекции

        if($product_objects){
            echo '<p class="title">'.$collection_name.'</p>';
            echo '<ul class="cf items-list">';

            foreach($product_objects as $p => $single_product){
                $extended = new WC_Product_Factory();  // создаём новый товар
                $product = $extended->get_product($single_product->ID);
                $product_id = $product->id;
                $product_title = $single_product->post_title;
                $product_link = get_permalink( $product_id );

                $meta_key = 'product'.$product_id.'_images';
                $salon_product_image_meta = get_post_meta( $salon_post_id, $meta_key, true );

                $images = array();
                if ( ($salon_product_image_meta != '') && (is_array($salon_product_image_meta)) ) {
                    foreach($salon_product_image_meta as $i => $image_id){
                        $image_title 	= $product_title;
                        $image_caption 	= get_post( $image_id )->post_excerpt;
                        $image_link  	= wp_get_attachment_url( $image_id );
                        $image       	= wp_get_attachment_image( $image_id, 'shop_catalog', array(
                            'title'	=> $image_title,
                            'alt'	=> $image_title
                        ) );
                        $image_str = '<a class="fancybox" rel="gallery'.$product_id.'" ';
                        if($i > 0){
                            $image_str .= 'style="display:none" ';
                        }
                        $image_str .= 'href="'.$image_link.'" title="'.$image_title.'">';
                        $image_str .= $image;
                        $image_str .= '</a>';
                        $images[] = $image_str;
                    }

                } else {
                    $image = apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img width="370" height="250" src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $product_id );
                    $images[] = $image;
                }
                 ?>

            <li>
                <div class="item_wrap">
                    <a href="<?php echo $product_link ?>" title="<?php echo $product_title ?>">
                        <?php foreach($images as $image){ ?>
                            <?php echo $image ?>
                        <?php } ?>
                        <span class="cf">
                            <p class="left"><?php echo $product_title ?></p>
                        </span>
                    </a>
                </div>
            </li>
    <?php   } ?>

            </ul>

    <?php   } ?>
    <?php   } ?>

    <?php else: ?>

        <h3>ТОВАРЫ НА ЗАКАЗ В ЭТОМ САЛОНЕ</h3>
            <ul class="cf">
        <?php
        // Check if text is set
        $taxonomy = 'product_cat';
        $show_count = 0;      // 1 for yes, 0 for no
        $pad_counts = 0;      // 1 for yes, 0 for no
        $hierarchical = 1;      // 1 for yes, 0 for no
        $title = '';
        $empty = 0;
        $args = array(
            'taxonomy' => $taxonomy,
            'menu_order'    => 'ASC',
            'show_count' => $show_count,
            'pad_counts' => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li' => $title,
            'parent'  => 0,
            'hide_empty' => $empty
        );

        $top_categories = get_categories($args); // cписок топовых категорий

        foreach ($top_categories as $c => $top_category) {

            $top_category_id = $top_category->term_id;
            $args = array(
                'hierarchical' => 1,
                'show_option_none' => '',
                'hide_empty' => 0,
                'menu_order'    => 'ASC',
                'parent' => $top_category_id,
                'taxonomy' => $taxonomy
            );

            $subcats = get_categories($args); // список подкатегорий у очередной топовой категории

            foreach ($subcats as $sc => $subcat) {
                $subcat_id = $subcat->term_id;
                $subcat_name = $subcat->name;
                $link = get_term_link( $subcat->slug, $subcat->taxonomy );
                $thumbnail_id = get_woocommerce_term_meta( $subcat->term_id, 'thumbnail_id', true );
                if ( $thumbnail_id ) {
                    $image_title 	= $subcat_name;
                    $image_caption 	= get_post( $thumbnail_id )->post_excerpt;
                    $image_link  	= wp_get_attachment_url( $thumbnail_id );
                    $image       	= wp_get_attachment_image( $thumbnail_id, 'shop_catalog', array(
                        'title'	=> $image_title,
                        'alt'	=> $image_title
                    ) );
                } else {
                    $image = apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img width="370" height="250" src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $subcat_id );
                }
                ?>
                <li>
                    <a href="<?php echo $link; ?>" title="<?php echo $subcat_name; ?>">
                        <?php echo $image; ?>
                        <span><?php echo $subcat_name; ?></span>
                    </a>
                </li>
<?php
            }
        }
        ?>
        </ul>

    <?php endif; ?>

        </div><!-- content_middle -->

        <div class="content_bottom cf">
            <div class="map">
                <?php echo do_shortcode($map); ?>
            </div>
            <div class="view">
                <?php echo $panorama; ?>
            </div>
        </div><!-- content_bottom -->
    </div><!-- .wrap -->

    <script>
        jQuery(document).ready(function(){
            // Photo slider (Главная стр)
            jQuery(".salon_images").sliderkit({
                auto:false,
                circular:true,
                shownavitems:20,
                //panelclick:true,
                panelfx:"sliding",
                panelfxspeed:500
            });

            jQuery("a[rel=lightbox]").fancybox({
                'transitionIn'      : 'none',
                'transitionOut'     : 'none',
                'titlePosition'     : 'over',
                'titleFormat'       : function(title, currentArray, currentIndex, currentOpts) {
                    return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? '   ' + title : '') + '</span>';
                }
            });
        });
    </script>

<?php
get_footer();
