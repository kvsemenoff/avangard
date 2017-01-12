<?php
/* ==================================================
  Salons from Russia
  ================================================== */
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function salon_products_rossiya_func() {
    $taxonomy = 'salon_category'; //имя таксономии
    $region_slug = 'rossiya';
    $top_category_id = 0;

    $select_products = filter_input(INPUT_POST, 'select_products', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);

    $salon_category_slug = filter_input(INPUT_GET, 'salon_category', FILTER_SANITIZE_SPECIAL_CHARS);
    $salon_slug = filter_input(INPUT_GET, 'salon_slug', FILTER_SANITIZE_SPECIAL_CHARS);

    $args = array(
        'posts_per_page'  => -1,
        'post_type'       => 'product',
        'orderby'         => 'menu_order ',
        'order'           => 'ASC'
    );
    $all_products = get_posts( $args ); // массив со всеми салонами

    $args = array(
        'hide_empty' => 0,
        'parent'     => 0,
        'orderby'    => 'term_order',
        'order'      => 'ASC'
    );
    $top_categories = get_terms($taxonomy, $args );

    foreach ( $top_categories as $top_category ){
        if($top_category->slug == $region_slug){
            $top_category_id = $top_category->term_id;
        }
    }

    $args = array(
        'hide_empty' => 0,
        'parent'     => $top_category_id,
        'orderby'    => 'term_order',
        'order'      => 'ASC'
    );
    $salon_categories = get_terms($taxonomy, $args );

    ?>
    <div class="formcontainer">
        <h1>Товары в салонах России</h1>

        <?php if($salon_slug){
            $salon_post = get_page_by_path($salon_slug,OBJECT,'salon');
            $salon_post_id = $salon_post->ID;
            $custom_fields = get_post_custom($salon_post->ID); // все произвольные поля салона
            if(isset($custom_fields['address'])){
                $address = $custom_fields['address'][0];
            } else {
                $address = '';
            }
            $salon_category = get_term_by('slug', $salon_category_slug, 'salon_category');

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
        	$sort_by = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
                $args = array(
                    'posts_per_page'  => -1,
                    'post_type'       => 'product',
                    'orderby'  => 'menu_order',
                    'order'  => 'ASC',
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
                <?php wp_referer_field(); ?>
                <h2 class="screen-reader-text">Список рубрик</h2>
                <table class="wp-list-table widefat fixed striped tags">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column" style="width:90px;">
                            <label style="padding:5px;">В наличии</label><br><br>
                            <label class="screen-reader-text" for="cb-select-all-1">Выделить все</label>
                            <input id="cb-select-all-1" type="checkbox"></td>
                        <th scope="col" id="thumb" class="manage-column column-thumb" style="width: 110px;">Изображение</th>
                        <th scope="col" id="name" class="manage-column column-name column-primary" style="width: 300px;">Название товара</th>
                        <th scope="col" id="collections" class="manage-column column-collections">Коллекции</a></th>
                        <th scope="col" id="categories" class="manage-column column-categories" style="width: 480px;">Категории</a></th>
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

                            if ( has_post_thumbnail() ) {
                                $image_title 	= $product_title;
                                $image_caption 	= get_post( get_post_thumbnail_id() )->post_excerpt;
                                $image_link  	= wp_get_attachment_url( get_post_thumbnail_id() );
                                $image       	= get_the_post_thumbnail( $product_id, 'material_image', array(
                                    'title'	=> $image_title,
                                    'alt'	=> $image_title
                                ) );
                            } else {
                                $image = apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $product_id );
                            }

                            ?>
                            <tr id="tag-<?php echo $product_id ?>" class="ui-sortable-handle">
                                <th scope="row" class="check-column">
                                    <label class="screen-reader-text" for="cb-select-<?php echo $product_id ?>">Выбрать <?php echo $product_title ?></label>
                                    <input type="checkbox" name="select_products[]" value="<?php echo $product_id ?>" id="cb-select-<?php echo $product_id ?>" <?php echo $checked ?> />
                                </th>
                                <td class="thumb column-thumb" style="float: left; margin-right: 10px;"><a class="fancybox" href="<?php echo $image_link ?>" title="<?php echo $product_title ?>"><?php echo $image ?></a></td>
                                <td class="name column-name has-row-actions column-primary" data-colname="Название">
                                    <a href="/wp-admin/post.php?post=<?php echo $product_id ?>&action=edit" title="<?php echo $product_title ?>"><strong><?php echo $product_title ?></strong></a>
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
            <script>
                jQuery(document).ready(function() {
                    jQuery("a.fancybox").fancybox();
                });
            </script>

        <?php } else { /* ----------------------------------------------------------------------------------------------- */?>

            <h3>Выбор салона</h3>
            <form id="posts-filter" method="post">
                <input type="hidden" name="taxonomy" value="salon_category">
                <input type="hidden" name="post_type" value="salon">

                <?php wp_nonce_field(); ?>
                <?php wp_referer_field(); ?>
                <h2 class="screen-reader-text">Список рубрик</h2>
                <table class="wp-list-table widefat fixed striped tags ui-sortable">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-1">Выделить все</label>
                            <input id="cb-select-all-1" type="checkbox"></td>
                        <th scope="col" id="name" class="manage-column column-name column-primary" style="width: 300px;">Название расположения</th>
                        <th scope="col" id="salons" class="manage-column column-salons">Салоны</th>
                        <th scope="col" id="slug" class="manage-column column-slug">Ярлык</th>
                        <th scope="col" id="posts" class="manage-column column-posts num">Товары</th>
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:tag" class="ui-sortable">
                    <?php if ( ! empty( $salon_categories ) && ! is_wp_error( $salon_categories ) ) { ?>
                        <?php foreach ( $salon_categories as $term ) {
                            $count_products = 0;
                            $metro_city = get_field('metro_city', 'salon_category_'.$term->term_id); // Станция метро или город
                            $term_id = $term->term_id;
                            if($metro_city == 'metro') {
                                $metro_city = 'Станция метро';
                            } else {
                                $metro_city = 'Город';
                            }

                            $args = array(
                                'posts_per_page'  => -1,
                                'post_type'       => 'salon',
                                'orderby'         => 'menu_order ',
                                'order'           => 'ASC',
                                'tax_query'       => array(
                                    array(
                                        'taxonomy' => 'salon_category',
                                        'field'    => 'id',
                                        'terms'    => $term_id
                                    )
                                )
                            );
                            $salon_posts = get_posts( $args ); // массив с салонами из текущей категории
                            ?>
                            <tr id="tag-<?php echo $term_id ?>" class="ui-sortable-handle">
                                <th scope="row" class="check-column">
                                    <label class="screen-reader-text" for="cb-select-<?php echo $term_id ?>">Выбрать <?php echo $term->name ?></label>
                                    <input type="checkbox" name="delete_tags[]" value="<?php echo $term_id ?>" id="cb-select-<?php echo $term_id ?>">
                                </th>
                                <td class="name column-name has-row-actions column-primary" data-colname="Название расположения">
                                    <strong><?php echo $term->name ?></strong><br>
                                    <div class="hidden" id="inline_<?php echo $term_id ?>">
                                        <div class="name"><?php echo $term->name ?></div>
                                        <div class="slug"><?php echo $term->slug ?></div>
                                        <div class="parent"><?php echo $term->parent ?></div>
                                    </div>
                                </td>
                                <td class="description column-salons" data-colname="Салоны">
                                    <?php if(count($salon_posts) > 0){ ?>
                                        <?php   foreach($salon_posts as $sp => $salon_post){
                                            $salon_id = $salon_post->ID;

					    $sort_by = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
                                            $args = array(
                                                'posts_per_page'  => -1,
                                                'post_type'       => 'product',
                                                'orderby'  => array( $sort_by => 'DESC'),
                                                'tax_query'       => array(
                                                    array(
                                                        'taxonomy' => 'salons',
                                                        'field'    => 'slug',
                                                        'terms'    => $salon_post->post_name,
                                                    )
                                                )
                                            );
                                            $product_objects = get_posts( $args ); // массив с товарами с очередной коллекции
                                            $count_products += count($product_objects);

                                            $salon_title = $salon_post->post_title;
                                            echo '<a href="/wp-admin/edit.php?post_type=salon&amp;page=salon_products_rossiya&amp;salon_category='.$term->slug.'&amp;salon_slug='.$salon_post->post_name.'"><b>'.$salon_title.'</b></a>';
                                            if(($sp + 1) < count($salon_posts)){
                                                echo ' &nbsp;|&nbsp; ';
                                            }
                                        } ?>
                                    <?php } ?>
                                </td>
                                <td class="slug column-slug" data-colname="Ярлык" ><?php echo $term->slug ?></td>
                                <td class="posts column-posts" data-colname="Товары"><?php echo $count_products ?></td>
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
                        <th scope="col" id="name" class="manage-column column-name column-primary" style="width: 300px;">Название расположения</th>
                        <th scope="col" id="salons" class="manage-column column-salons">Салоны</th>
                        <th scope="col" id="slug" class="manage-column column-slug">Ярлык</th>
                        <th scope="col" id="posts" class="manage-column column-posts num">Товары</th>
                    </tr>
                    </tfoot>

                </table>

                <br class="clear">
            </form>
        <?php } ?>
    </div>
<?php
}