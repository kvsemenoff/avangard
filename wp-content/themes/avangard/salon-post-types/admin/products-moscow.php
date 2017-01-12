<?php
/* ==================================================
  Products from Moscow Salon
  ================================================== */
if (!defined('ABSPATH')) exit; // Exit if accessed directly

    $salon_post = get_page_by_path($salon_slug,OBJECT,'salon');
    $salon_post_id = $salon_post->ID;
    $custom_fields = get_post_custom($salon_post->ID); // все произвольные поля салона
    if(isset($custom_fields['address'])){
        $address = $custom_fields['address'][0];
    } else {
        $address = '';
    }
    $salon_category = get_term_by('slug', $salon_category_slug, 'salon_category');

    if($select_product_id){

        $product = new WC_product($select_product_id);
        $product_post = $product->post;
        $product_title = $product_post->post_title;
        $product_slug = $product_post->post_name;

        $meta_key = 'product'.$select_product_id.'_images';

        if($action && $action == "save"){

            $data = filter_input(INPUT_POST, 'product_images', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

            add_post_meta($salon_post->ID, $meta_key, $data, true) or
            update_post_meta($salon_post->ID, $meta_key, $data);

        }

        $salon_product_image_meta = get_post_meta( $salon_post->ID, $meta_key, true );
        ?>

<form id="salon_product_images" method="post">
    <input type="hidden" name="taxonomy" value="salon_category">
    <input type="hidden" name="post_type" value="salon">
    <input type="hidden" name="product_id" value="<?php echo $select_product_id ?>">
    <input type="hidden" name="products_link" value="<?php echo '/wp-admin/edit.php?post_type=salon&page=salon_products_moscow&salon_category='.$salon_category_slug.'&salon_slug='.$salon_slug; ?>">
    <input type="hidden" id="hiddenaction" name="action" value="save" />
    <?php wp_nonce_field(); ?>

    <div id="post-body-content" style="position: relative;">

        <h3><?php echo $product_title ?> в салоне <?php echo $salon_post->post_title ?> <?php echo $salon_category->name ?> (<?php echo $address ?>)</h3>

        <?php
        $box = array(
            'id' => 'mgop_mb_salon-product-images',
            'title' => 'Salon Product Images'
        );
        ?>

        <div id="mgop_mb_galereya-salona" class="postbox ">
            <h2 style="padding: 8px 12px;margin: 0;" class="hndle"><span>Галерея изображений товара <?php echo $product_title ?> в салоне</span></h2>
            <div class="inside">
                <a href="#" class="salon_product_image_add" data-for="<?php echo $salon_post->ID; ?>" title="Галерея изображений товара <?php echo $product_title ?>">Добавить фото</a>
                <ul id="salon_product_images_<?php echo $salon_post->ID; ?>" class="mgop-wrapper-sortable">
                    <?php
                    if(is_array($salon_product_image_meta) && count($salon_product_image_meta)){
                        foreach($salon_product_image_meta as $attc_id){
                            $url = wp_get_attachment_thumb_url( $attc_id );
                            ?>
                            <li class="mgop_thumnails" title="Перетаскивайте для сортировки">
                                <div>
                                    <span class="mgop-movable"></span>
                                    <a href="#" class="mgop_remove_item" title="Клик, чтобы удалить фото"><span>удалить</span></a>
                                    <img src="<?php echo $url ?>"><input type="hidden" name="product_images[]" value="<?php echo $attc_id ?>" />
                                </div>
                            </li>
                        <?php }
                    } ?>
                </ul>
            </div>
        </div>
    </div>

    <input type="submit" style="float: left;margin:10px 40px 10px 10px" class="button button-primary" value="Сохранить" />
</form>

<?php } else { /* if($select_product_id){ */

        $salon_product_ids = array(); // массив с id товаров в данном салоне

        if($action && $action == "save"){
            $salons_term = term_exists($salon_post->post_name, 'salons'); // вернет массив, если таксономия существует
            $salons_term_id = $salons_term['term_id']; // получим числовое значения термина
            if(!$salons_term){
                wp_insert_term(
                    $salon_post->post_title, // новый термин
                    'salons', // таксономия
                    array(
                        'slug' => $salon_post->post_name,
                        'parent'=> $salons_term_id
                    )
                );
            } else {
                wp_update_term(
                    $salon_post->post_title, // новый термин
                    'salons', // таксономия
                    array(
                        'slug' => $salon_post->post_name,
                        'parent'=> $salons_term_id
                    )
                );
            }

            $set_salon_products = set_salon_products( $salons_term_id, $select_products, 'salons' );

            $salon_product_ids = $set_salon_products; // массив с id товаров в данном салоне
        } else {
            $args = array(
                'posts_per_page'  => -1,
                'post_type'       => 'product',
                'orderby'         => 'menu_order ',
                'order'           => 'ASC',
                'tax_query'       => array(
                    array(
                        'taxonomy' => 'salons',
                        'field'    => 'slug',
                        'terms'    => $salon_post->post_name,
                    )
                )
            );
            $salon_products = get_posts( $args ); // массив с товарами с данным тегом салона

            foreach($salon_products as $salon_product){
                $salon_product_ids[] = $salon_product->ID;
            }
        }

        $checked = '';

        ?>
        <h3>Товары в салоне <?php echo $salon_post->post_title ?> <?php echo $salon_category->name ?> <?php echo $address ?></h3>

        <form id="posts-filter" method="post">
                <input type="submit" style="float: left;margin:10px 40px 10px 10px" class="button button-primary" value="Сохранить" />
                <input type="hidden" name="taxonomy" value="salon_category">
                <input type="hidden" name="post_type" value="salon">
                <input type="hidden" id="hiddenaction" name="action" value="save" />
                <?php wp_nonce_field(); ?>
                <h2 class="screen-reader-text">Список рубрик</h2>
                <table class="wp-list-table widefat fixed striped tags">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column" style="width:90px;">
                            <label style="padding:5px;">В наличии</label><br><br>
                            <label class="screen-reader-text" for="cb-select-all-1">Выделить все</label>
                            <input id="cb-select-all-1" type="checkbox"></td>
                        <th scope="col" id="thumb" class="manage-column column-thumb" style="width: 110px;">Изображение</th>
                        <th scope="col" id="name" class="manage-column column-name column-primary" style="min-width: 300px;">Название товара</th>
                        <th scope="col" id="collections" class="manage-column column-collections" style="min-width: 140px;">Коллекции</a></th>
                        <th scope="col" id="categories" class="manage-column column-categories" style="min-width: 480px;">Категории</a></th>
                        <th scope="col" id="posts" class="manage-column column-posts">Салоны</a></th>
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:tag">
                    <?php if ( ! empty( $all_products ) && ! is_wp_error( $all_products ) ) { ?>
                        <?php foreach ( $all_products as $product ) {
                            $product_id = $product->ID;
                            $product_title = $product->post_title;
                            $product_slug = $product->post_name;

                            if($salon_product_ids){
                                if (in_array($product_id, $salon_product_ids)) {
                                    $checked = ' checked="checked"';
                                } else {
                                    $checked = '';
                                }
                            }

                            $tags_array = array(); // метки товаров - коллекции
                            $product_tags = get_the_terms( $product_id, 'product_tag' );
                            if($product_tags){
                                foreach($product_tags as $product_tag){
                                    $tags_array[] = '<a href="/wp-admin/edit-tags.php?action=edit&taxonomy=product_tag&tag_ID='.$product_tag->term_id.'&post_type=product">'.$product_tag->name.'</a>';
                                }
                            }
                            $tags = implode ( ', ' , $tags_array );

                            $cats_array = array(); // категории товаров
                            $product_cats = get_the_terms( $product_id, 'product_cat' );
                            if($product_cats){
                                foreach($product_cats as $product_cat){
                                    $cats_array[] = '<a href="/wp-admin/edit-tags.php?action=edit&taxonomy=product_cat&tag_ID='.$product_cat->term_id.'&post_type=product">'.$product_cat->name.'</a>';
                                }
                            }
                            $cats = implode ( ', ' , $cats_array );

                            $salon_tags = wp_get_object_terms( $product_id, 'salons' );
                            $count_salon_tags = 0;
                            if($salon_tags){
                                $count_salon_tags = count($salon_tags);
                            }

                            $meta_key = 'product'.$product_id.'_images';
                            $salon_product_image_meta = get_post_meta( $salon_post->ID, $meta_key, true );

                            $images = array();
                            if ( ($salon_product_image_meta != '') && (is_array($salon_product_image_meta)) ) {
                                foreach($salon_product_image_meta as $i => $image_id){
                                    $image_title 	= $product_title;
                                    $image_caption 	= get_post( $image_id )->post_excerpt;
                                    $image_link  	= wp_get_attachment_url( $image_id );
                                    $image       	= wp_get_attachment_image( $image_id, 'material_image', array(
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
                                $image = apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img width="110" height="65" src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $product_id );
                                $images[] = $image;
                            }
                            $product_image_link = '/wp-admin/edit.php?post_type=salon&page=salon_products_moscow&salon_category='.$salon_category_slug.'&salon_slug='.$salon_slug.'&product_id='.$product_id;

                            ?>
                            <tr id="tag-<?php echo $product_id ?>" class="ui-sortable-handle">
                                <th scope="row" class="check-column">
                                    <label class="screen-reader-text" for="cb-select-<?php echo $product_id ?>">Выбрать <?php echo $product_title ?></label>
                                    <input type="checkbox" name="select_products[]" value="<?php echo $product_id ?>" id="cb-select-<?php echo $product_id ?>" <?php echo $checked ?> />
                                </th>
                                <td class="thumb column-thumb" style="float: left; margin-right: 10px;">
                                    <?php foreach($images as $image){ ?>
                                        <?php echo $image ?>
                                    <?php } ?>
                                </td>
                                <td class="name column-name has-row-actions column-primary" data-colname="Название">
                                    <a href="<?php echo $product_image_link ?>" title="Редактировать фото <?php echo $product_title ?> в салоне"><strong><?php echo $product_title ?></strong></a>
                                    <div class="hidden" id="inline_<?php echo $product_id ?>">
                                        <div class="name"><?php echo $product_title ?></div>
                                        <div class="slug"><?php echo $product_title ?></div>
                                        <div class="parent"><?php echo $product_title ?></div>
                                    </div>
                                </td>
                                <td class="description column-collections" data-colname="Коллекции"><?php echo $tags ?></td>
                                <td class="categories column-categories" data-colname="Категории" ><?php echo $cats ?></td>
                                <td class="posts column-posts" data-colname="Салоны"><?php echo $count_salon_tags ?></td>
                            </tr>

                        <?php } ?>
                    <?php } else { ?>

                    <?php } ?>

                    </tbody>

                    <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-2">Выделить все</label>
                            <input id="cb-select-all-2" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-thumb">Изображение</th>
                        <th scope="col" class="manage-column column-name column-primary">Название товара</th>
                        <th scope="col" class="manage-column column-collections ">Коллекции</th>
                        <th scope="col" class="manage-column column-categories">Категории</th>
                        <th scope="col" class="manage-column column-posts num">Салоны</th>
                    </tr>
                    </tfoot>

                </table>

                <br class="clear">
                <input type="submit" style="float: left;margin:10px 40px 10px 10px" class="button button-primary" value="Сохранить" />
            </form>

<?php } ?>

<script>
    jQuery(document).ready(function() {
        jQuery("a.fancybox").fancybox({
            prevEffect	: 'elastic',
            nextEffect	: 'elastic',
            openEffect	: 'elastic',
            closeEffect	: 'elastic',
            helpers	: {
                title	: {
                    type: 'outside'
                },
                thumbs	: {
                    width	: 50,
                    height	: 50
                }
            }
        });
    });
</script>
