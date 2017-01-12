<?php
/* ==================================================
  Salons from Moscow
  ================================================== */
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function salon_products_moscow_func() {
    $taxonomy = 'salon_category'; //имя таксономии
    $region_slug = 'moscow';
    $top_category_id = 0;

    $select_products = filter_input(INPUT_POST, 'select_products', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);

    $salon_category_slug = filter_input(INPUT_GET, 'salon_category', FILTER_SANITIZE_SPECIAL_CHARS);
    $salon_slug = filter_input(INPUT_GET, 'salon_slug', FILTER_SANITIZE_SPECIAL_CHARS);
    $select_product_id = filter_input(INPUT_GET, 'product_id', FILTER_SANITIZE_SPECIAL_CHARS);

    $args = array(
        'posts_per_page'  => -1,
        'post_type'       => 'product',
        'orderby'         => 'menu_order ',
        'order'           => 'ASC'
    );
    $all_products = get_posts( $args ); // массив со всеми товарами

    $args = array(
        'hide_empty' => 0,
        'parent'     => 0,
        'orderby'    => 'term_order',
        'order'      => 'ASC'
    );
    $top_categories = get_terms($taxonomy, $args ); // топовые категории салонов(москва и россия)

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
    $salon_categories = get_terms($taxonomy, $args ); // массив со всеми категориями салонов в данной топовой категории

?>
<div class="wrap">
    <h1>Товары в салонах Москвы и области</h1>

<?php if($salon_slug){

        require_once get_template_directory() . '/salon-post-types/admin/products-moscow.php';

    } else { /* ----------------------------------------------------------------------------------------------- */

        require_once get_template_directory() . '/salon-post-types/admin/salons-moscow.php';
    } ?>
</div>
<?php
}