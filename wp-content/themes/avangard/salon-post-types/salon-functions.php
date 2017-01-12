<?php
/* ==================================================
  Salon Post Type Functions
  ================================================== */
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/* создание новых таксономий для салонов */
function create_my_taxonomies(){
    register_taxonomy('salons', 'product', array('hierarchical' => false,'label' => 'Теги салонов','query_var' => true,'rewrite' => true));
}
add_action('init', 'create_my_taxonomies', 0);
/* /создание новых таксономий для салонов */

/* создание новых таксономий для типов салонов */
function create_salon_type(){
    register_taxonomy('salon_type', 'salon', array('hierarchical' => false,'label' => 'Типы салонов','query_var' => true,'rewrite' => true));
}
add_action('init', 'create_salon_type', 0);
/* создание новых таксономий для типов салонов */

function restrict_salon_by_salon_category() {
    global $typenow;
    $post_type = 'salon'; // change HERE
    $taxonomy = 'salon_category'; // change HERE
    if ($typenow == $post_type) {
        $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
        $info_taxonomy = get_taxonomy($taxonomy);
        wp_dropdown_categories(array(
            'show_option_all' => __("Show All {$info_taxonomy->label}"),
            'taxonomy' => $taxonomy,
            'name' => $taxonomy,
            'orderby' => 'name',
            'selected' => $selected,
            'show_count' => true,
            'hide_empty' => false,
        ));
    };
}
add_action('restrict_manage_posts', 'restrict_salon_by_salon_category');

function convert_id_to_term_in_query($query) {
    global $pagenow;
    $post_type = 'salon'; // change HERE
    $taxonomy = 'salon_category'; // change HERE
    $q_vars = &$query->query_vars;
    if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
        $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
        $q_vars[$taxonomy] = $term->slug;
    }
}
add_filter('parse_query', 'convert_id_to_term_in_query');

/* функция сортировки массива с категориями по названию категории */
function sort_by_category_name($a, $b) {
    $category_name_a = preg_replace('/[^a-zA-Zа-яА-Я0-9]/ui', '',$a['category_name'] );
    $category_name_a = mb_strtolower($category_name_a);
    $category_name_b = preg_replace('/[^a-zA-Zа-яА-Я0-9]/ui', '',$b['category_name'] );
    $category_name_b = mb_strtolower($category_name_b);
    if ($category_name_a === $category_name_b) return 0;
    return $category_name_a > $category_name_b ? 1 : -1;
}

function get_regions($cols_number) {
    $output = '<div class="sity-list">';
    $taxonomy = 'salon_category'; //имя таксономии
    $region_slug = 'rossiya';
    $top_category_id = 0;

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

    $capital     = '';
    $i           = 0;
    $cut         = ceil( count( $salon_categories ) / $cols_number );
    $cutter      = $cut;
    $letter_i    = 0;
    $output      .= '<ul class="sity-list_parent">';
    if ( ! empty( $salon_categories ) && ! is_wp_error( $salon_categories ) ) {
        foreach ( $salon_categories as $term ) {
            $i ++;
            $firstletter = mb_substr( $term->name, 0, 1 );
            $firstletter = mb_strtoupper($firstletter);
            if ( $firstletter != $capital ) {
                $letter_i ++;
                if ( $letter_i != 1 ) {
                    $output .= '</ul>';
                }
                if ( $i > $cutter ) {
                    $output .= '</ul><ul class="sity-list_parent">';
                    $cutter = $cutter + $cut;
                }
                $capital = $firstletter;
                $output .= '<li><span>' . $capital . '</span><ul class="sity-list_child">';
            }
            $term = get_term_by( 'id', (int) $term->term_id, $taxonomy );
            $output .= '<li><a href="' . get_term_link( (int) $term->term_id, $taxonomy ) . '">' . $term->name . '</a></li>';
        }
        $output .= '</ul>';
        $output .= '</div>';
        echo $output;
        return true;
    }else{
        echo 'Тегов продуктов нет';
        return true;
    }

    return false;
}
add_action('get_regions', 'get_regions', 0);

function get_addresses($cols_number) {
    $output = '<div class="sity-list">';
    $taxonomy = 'salon_category'; //имя таксономии
    $region_slug = 'rossiya';
    $top_category_id = 0;

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

    $capital     = '';
    $i           = 0;
    $cut         = ceil( count( $salon_categories ) / $cols_number );
    $cutter      = $cut;
    $letter_i    = 0;
    $output      .= '<ul class="sity-list_parent">';
    if ( ! empty( $salon_categories ) && ! is_wp_error( $salon_categories ) ) {
        foreach ( $salon_categories as $term ) {
            $i ++;
            $firstletter = mb_substr( $term->name, 0, 1 );
            $firstletter = mb_strtoupper($firstletter);
            if ( $firstletter != $capital ) {
                $letter_i ++;
                if ( $letter_i != 1 ) {
                    $output .= '</ul>';
                }
                if ( $i > $cutter ) {
                    $output .= '</ul><ul class="sity-list_parent">';
                    $cutter = $cutter + $cut;
                }
                $capital = $firstletter;
                $output .= '<li><span>' . $capital . '</span><ul class="sity-list_child">';
            }
            $term = get_term_by( 'id', (int) $term->term_id, $taxonomy );
            $output .= '<li><a href="' . get_term_link( (int) $term->term_id, $taxonomy ) . '">' . $term->name . '</a></li>';
        }
        $output .= '</ul>';
        $output .= '</div>';
        echo $output;
        return true;
    }else{
        echo 'Тегов продуктов нет';
        return true;
    }

    return false;
}
add_action('get_addresses', 'get_addresses', 0);

function delete_salon_products( $salons_term_id, $taxonomy ) {
    global $wpdb;

    if ( ! taxonomy_exists($taxonomy) )
        return new WP_Error('invalid_taxonomy', __('Invalid taxonomy'));

    $delete = $wpdb->query("DELETE FROM $wpdb->term_relationships WHERE term_taxonomy_id = $salons_term_id");

    return $delete;
}

function set_salon_products( $salons_term_id, $product_ids, $taxonomy ) {
    global $wpdb;

    $salons_term_id = (int) $salons_term_id;

    if ( ! taxonomy_exists($taxonomy) )
        return new WP_Error('invalid_taxonomy', __('Invalid taxonomy'));

    if ( !is_array($product_ids) )
        $product_ids = array($product_ids);

    $product_ids = array_map('intval', $product_ids);

    $delete_info = delete_salon_products( $salons_term_id, $taxonomy );

    if(isset($delete_info)){
        $term_order = 0;
        foreach ( $product_ids as $product_id ){
            $values[] = $wpdb->prepare( "(%d, %d, %d)", $product_id, $salons_term_id, ++$term_order);
        }

        if ( $values ){
            if ( false === $wpdb->query( "INSERT INTO $wpdb->term_relationships (object_id, term_taxonomy_id, term_order) VALUES " . join( ',', $values ) . " ON DUPLICATE KEY UPDATE term_order = VALUES(term_order)" ) ){
                return new WP_Error( 'db_insert_error', __( 'Could not insert term relationship into the database' ), $wpdb->last_error );
            }
        }

    }

    wp_cache_delete( $salons_term_id, $taxonomy . '_relationships' );

    return $product_ids;
}
?>