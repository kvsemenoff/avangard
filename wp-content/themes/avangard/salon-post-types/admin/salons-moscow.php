<?php
/* ==================================================
  Salons from Moscow
  ================================================== */
if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

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
                <th scope="col" id="metro_city" class="manage-column column-metro_city" style="width: 100px;">Станция метро, город</th>
                <th scope="col" id="name" class="manage-column column-name column-primary" style="width: 300px;">Название расположения</th>
                <th scope="col" id="salons" class="manage-column column-salons">Салоны</th>
                <th scope="col" id="salon_type" class="manage-column column-slug" style="width: 15%;">Тип салона</th>
                <th scope="col" id="posts" class="manage-column column-posts num">Товары</th>
            </tr>
            </thead>

            <tbody id="the-list" data-wp-lists="list:tag" class="ui-sortable"><?php

            if ( ! empty( $salon_categories ) && ! is_wp_error( $salon_categories ) ) {
                foreach ($salon_categories as $term) {
                    $count_products = 0;
                    $metro_city = get_field('metro_city', 'salon_category_' . $term->term_id); // Станция метро или город
                    $term_id = $term->term_id;
                    if ($metro_city == 'metro') {
                        $metro_city = 'Станция метро';
                    } else {
                        $metro_city = 'Город';
                    }

                    $args = array(
                        'posts_per_page' => -1,
                        'post_type' => 'salon',
                        'orderby' => 'menu_order ',
                        'order' => 'ASC',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'salon_category',
                                'field' => 'id',
                                'terms' => $term_id
                            )
                        )
                    );
                    $salon_posts = get_posts($args); // массив с салонами из текущей категории

                    $salons_string = '';
                    $salon_types_string = '';

                    if (count($salon_posts) > 0) {
                        foreach ($salon_posts as $sp => $salon_post) {
                            $salon_id = $salon_post->ID;

                            $args = array(
                                'posts_per_page' => -1,
                                'post_type' => 'product',
                                'orderby' => 'menu_order ',
                                'order' => 'ASC',
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'salons',
                                        'field' => 'slug',
                                        'terms' => $salon_post->post_name,
                                    )
                                )
                            );
                            $product_objects = get_posts($args); // массив с товарами с очередной коллекции
                            $count_products += count($product_objects);

                            $salon_title = $salon_post->post_title;
                            $salons_string .=  '<a href="/wp-admin/edit.php?post_type=salon&amp;page=salon_products_moscow&amp;salon_category=' . $term->slug . '&amp;salon_slug=' . $salon_post->post_name . '"><b>' . $salon_title . '</b></a>';

                            $salon_types = get_the_terms($salon_id, 'salon_type'); // массив с типами салонов с данным id

                            if($salon_types){
                                $salon_type = $salon_types[0];
                                if ($salon_type->slug == 'firmennye_salony') {
                                    $salon_type_name = 'Салон';
                                    $salon_type_href = '/wp-admin/edit-tags.php?action=edit&taxonomy=salon_type&tag_ID=96&post_type=salon';
                                } else if ($salon_type->slug == 'firmennye_podiumy') {
                                    $salon_type_name = 'Подиум';
                                    $salon_type_href = '/wp-admin/edit-tags.php?action=edit&taxonomy=salon_type&tag_ID=97&post_type=salon';
                                }

                                $salon_types_string .= '<a href="' . $salon_type_href . '" title="' . $salon_type_name . '">' . $salon_type_name . '</a>';
                            }

                            if (($sp + 1) < count($salon_posts)) {
                                $salons_string .= ' &nbsp;|&nbsp; ';
                                $salon_types_string .= ' &nbsp;|&nbsp; ';
                            }
                        }
                    }
?>
                    <tr id="tag-<?php echo $term_id ?>" class="ui-sortable-handle">
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="cb-select-<?php echo $term_id ?>">Выбрать <?php echo $term->name ?></label>
                            <input type="checkbox" name="delete_tags[]" value="<?php echo $term_id ?>" id="cb-select-<?php echo $term_id ?>">
                        </th>
                        <td class="thumb column-thumb" data-colname="Станция метро, город"><?php echo $metro_city ?></td>
                        <td class="name column-name has-row-actions column-primary" data-colname="Название расположения">
                            <strong><?php echo $term->name ?></strong><br>
                            <div class="hidden" id="inline_<?php echo $term_id ?>">
                                <div class="name"><?php echo $term->name ?></div>
                                <div class="slug"><?php echo $term->slug ?></div>
                                <div class="parent"><?php echo $term->parent ?></div>
                            </div>
                        </td>
                        <td class="description column-salons" data-colname="Салоны"><?php echo $salons_string; ?></td>
                        <td class="slug column-slug" data-colname="Тип салона" ><?php echo $salon_types_string; ?></td>
                        <td class="posts column-posts" data-colname="Товары"><?php echo $count_products ?></td>
                    </tr>

            <?php } ?>

            <?php } ?>

            </tbody>

            <tfoot>
            <tr>
                <td class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-2">Выделить все</label>
                    <input id="cb-select-all-2" type="checkbox">
                </td>
                <th scope="col" class="manage-column column-thumb">Станция метро, город</th>
                <th scope="col" class="manage-column column-name column-primary">Название расположения</th>
                <th scope="col" class="manage-column column-salons sortable desc">Салоны</th>
                <th scope="col" class="manage-column column-slug">Тип салона</th>
                <th scope="col" class="manage-column column-posts num sortable desc">Товары</th>
            </tr>
            </tfoot>

        </table>

        <br class="clear">
    </form>