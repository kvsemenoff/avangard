<?php
/**
 * Template name: Товар в салонах
 *
 * @package avangard
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header();

while ( have_posts() ) : the_post();

    $taxonomy = 'salon_category'; //имя таксономии
    $product_id = filter_input(INPUT_GET, 'product_id', FILTER_SANITIZE_SPECIAL_CHARS);

    if($product_id){
        $extended = new WC_Product_Factory();  // создаём новый товар
        $product = $extended->get_product($product_id);
        $product_post = $product->post;
        $product_title = $product_post->post_title;
        $product_slug = $product_post->post_name;
    } else {
        $product_title = 'Товар';
        $product_slug = '';
    }

    $args = array(
        'hide_empty' => 0,
        'orderby'    => 'term_order',
        'order'      => 'ASC'
    );
    $salons_types = get_terms('salon_type', $args ); // массив со всеми типами салонов

    $salons_tags = get_the_terms( $product_id, 'salons' ); // массив с тегами салонов, где есть данный товар
    $salons_ids = array();
    $salons_list = array();

    if($salons_tags){

        foreach($salons_tags as $salons_tag) { // перебираем теги салонов, где есть товар в наличии
            $salon_post = get_page_by_path($salons_tag->slug,OBJECT,'salon'); // находим запись салона с таким же слугом
            $salons_ids[] = $salon_post->ID;
        }

        foreach($salons_types as $st => $salon_type){
            $salon_type_name = $salon_type->name;
            $salon_type_slug = $salon_type->slug;

            $salons_list[$st]['name'] = $salon_type_name;
            $salons_list[$st]['slug'] = $salon_type_slug;

            $args = array(
                'posts_per_page'  => -1,
                'post_type'       => 'salon',
                'orderby'         => 'menu_order ',
                'order'           => 'ASC',
                'include'         => $salons_ids,
                'tax_query'       => array(
                    array(
                        'taxonomy' => 'salon_type',
                        'field'    => 'slug',
                        'terms'    => array( $salon_type_slug ),
                    )
                )
            );
            $salon_posts = get_posts( $args ); // массив с салонами с текущим типом салона и подкатегорий из текущей топовой категории

            if( $salon_posts ) {
                foreach ( $salon_posts as $sp => $salon_post ) { // перебор салонов данного типа
                    $custom_fields = get_post_custom($salon_post->ID); // все произвольный поля салона

                    $salons_in_type[$sp]['salon_address'] = $custom_fields['address'][0]; // Адрес
                    $salons_in_type[$sp]['salon_telephone'] = $custom_fields['telephone'][0]; // Телефон
                    $salons_in_type[$sp]['salon_work_time'] = $custom_fields['work_time'][0]; // Часы работы

                    $salon_category_arr = get_the_terms( $salon_post->ID , $taxonomy );
                    $salon_category = $salon_category_arr[0]; // находим категорию салона
                    $salons_in_type[$sp]['category_name'] = $salon_category->name;
                    $salons_in_type[$sp]['category_slug'] = $salon_category->slug;
                    $salons_in_type[$sp]['metro_city'] = get_field('metro_city', 'salon_category_'.$salon_category->term_id); // Станция метро или город
                    $salons_in_type[$sp]['salon_id'] = $salon_post->ID;
                    $salons_in_type[$sp]['salon_title'] = esc_html($salon_post->post_title);
                    $salons_in_type[$sp]['salon_link'] = get_permalink( $salon_post->ID, false );

                    $meta_key = 'product'.$product_id.'_images';
                    $salon_product_image_meta = get_post_meta( $salon_post->ID, $meta_key, true );
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
                            $image_str = '<a class="fancybox" rel="gallery'.$salon_post->ID.'" ';
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

                    $salons_in_type[$sp]['salon_images'] = $images;
                }

                $salons_list[$st]['salons'] = $salons_in_type;

            }
        }
    }
?>
    <div class="content_top">
        <div class="wrap">
            <h1 class="entry-title"><?php echo $product_title; ?> в салонах</h1>
        </div>
    </div>

    <div class="content_middle">
        <div class="wrap">
<?php
foreach ( $salons_list as $salons_block ) {
    if(isset($salons_block['salons'])&&!empty($salons_block['salons'])){
        uasort($salons_block['salons'], 'sort_by_category_name'); ?>

        <p class="title <?php echo $salons_block['slug']; ?>"><?php echo $salons_block['name']; ?></p>
        <ul class="cf">
            <?php foreach ( $salons_block['salons'] as $sp => $salon_params ) { ?>
                <li>
                    <?php foreach($salon_params['salon_images'] as $image){ ?>
                        <?php echo $image ?>
                    <?php } ?>
                    <a href="<?php echo $salon_params['salon_link'] ?>" title="<?php echo $salon_params['salon_title'] ?>">
                        <span><?php echo $salon_params['salon_title'] ?></span>
                    </a>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
<?php } ?>
        </div>

<?php
endwhile; // End of the loop.
?>
</div>
<?php
get_footer();
