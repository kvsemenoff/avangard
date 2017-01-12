<?php
/* ==================================================
  Salon Post Type
  ================================================== */
if (!defined('ABSPATH')) exit; // Exit if accessed directly

add_action( 'init', 'salon_metadata_wpdbfix', 0 );
function salon_metadata_wpdbfix() {
    global $wpdb;
    $termmeta_name = 'salon_termmeta';
    $wpdb->salon_termmeta = $wpdb->prefix . $termmeta_name;
    $wpdb->tables[] = 'salon_termmeta';
}

add_action('init', 'salon_register');
function salon_register() {

    $args_c = array(
        "label" => __('Расположения салонов', "avangard"),
        "singular_label" => __('Расположение салона', "avangard"),
        'public' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_in_nav_menus' => true,
        'args' => array('orderby' => 'term_order'),
        'rewrite' => array('slug' => 'salons'),
        'query_var' => true,
        'show_admin_column' => true,
    );
    register_taxonomy('salon_category', 'salon', $args_c);

    $labels = array(
        'name' => __('Салоны', 'avangard'),
        'singular_name' => __('Салон', 'avangard'),
        'add_new' => __('Добавить новый', 'avangard'),
        'all_items'=> __('Салоны', 'avangard'),
        'add_new_item' => __('Добавить новый салон', 'avangard'),
        'edit_item' => __('Редактировать салон', 'avangard'),
        'new_item' => __('Новый салон', 'avangard'),
        'view_item' => __('Просмотр салона', 'avangard'),
        'search_items' => __('Поиск салона', 'avangard'),
        'not_found' => __('Салонов ещё нет', 'avangard'),
        'not_found_in_trash' => __('В корзине ничего не найдено', 'avangard'),
        'parent_item_colon' => '',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'has_archive' => true,
        'hierarchical' => true,
        'rewrite' => array('slug' => 'salons/%salon_category%','with_front' => false ),
        'supports' => array('title', 'thumbnail','excerpt','editor','custom-fields'),
        'has_archive' => true,
    );
    register_post_type('salon', $args);

    flush_rewrite_rules();
}

add_action('admin_menu', 'menu_products_in_salon'); // добавляем в меню салонов два пункта для редактирования привязки товаров к салонам
function menu_products_in_salon() {
    if (function_exists('add_options_page')) {
        add_submenu_page( 'edit.php?post_type=salon', 'Товары в салонах Москвы и области', 'Товары в салонах Москвы и области', 'manage_options', 'salon_products_moscow', 'salon_products_moscow_func' );
        //add_submenu_page( 'edit.php?post_type=salon', 'Товары в салонах России', 'Товары в салонах России', 'manage_options', 'salon_products_rossiya', 'salon_products_rossiya_func' );
    }
}

add_action('edit_post', 'get_salon_point');
function get_salon_point($post_id) { // получение координат салона по его id
    $post = get_post($post_id);
    if ($post->post_type == 'salon') {
        $address_field = 'field_56c644d660433';
        $custom_fields = filter_input(INPUT_POST, 'fields', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        if($custom_fields && $address_field){
            $address = $custom_fields[$address_field];
            $point = get_point($address);
            if ( ! add_post_meta( $post_id, 'point', $point, true ) ) {
                update_post_meta ( $post_id, 'point', $point );
            }
        }
    }
}

function get_point($address) { // получение координат по адресу
    $res = json_decode(file_get_contents('https://geocode-maps.yandex.ru/1.x/?format=json&geocode='.$address.'"'));
    $featureMember = $res->response->GeoObjectCollection->featureMember;
    if(!empty($featureMember)){
        $pos = $featureMember[0]->GeoObject->Point->pos;
        $pointArr = explode(' ',$pos);
        $tmpLat = floatval($pointArr[1]);
        $tmpLong = floatval($pointArr[0]);
        $point = $tmpLat.','.$tmpLong;
        return $point;
    } else {
        if(mb_ereg('осква', $address)){
            $point = "0,0";
            return $point;
        } else {
            $point = get_point('Москва,'.$address);
            return $point;
        }
    }
}

add_filter('post_type_link', 'salons_permalink_structure', 10, 4);
function salons_permalink_structure($post_link, $post, $leavename, $sample)
{
    if ( false !== strpos( $post_link, '%salon_category%' ) ) {
        $salon_type_term = get_the_terms( $post->ID, 'salon_category' );
        if($salon_type_term){
            $post_link = str_replace( '%salon_category%', array_pop( $salon_type_term )->slug, $post_link );
        } else {
            $post_link = '';
        }
    }
    return $post_link;
}

function add_salon_term_meta( $term_id, $meta_key, $meta_value, $unique = false ){
    return add_metadata( 'salon_term', $term_id, $meta_key, $meta_value, $unique );
}
function get_salon_term_meta( $term_id, $key, $single = true ) {
    return get_metadata( 'salon_term', $term_id, $key, $single );
}
function update_salon_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
    return update_metadata( 'salon_term', $term_id, $meta_key, $meta_value, $prev_value );
}

function my_plugin_notice() {
    $products_link = filter_input(INPUT_POST, 'products_link', FILTER_SANITIZE_SPECIAL_CHARS);
    if($products_link){
    ?>
    <div id="message" class="updated">
        <p><strong>Элемент обновлён.</strong></p>
        <p><a href="<?php echo $products_link ?>">← Назад к списку товаров в салоне</a></p>
    </div>
<?php
    }
}
add_action( 'admin_notices', 'my_plugin_notice' );
?>