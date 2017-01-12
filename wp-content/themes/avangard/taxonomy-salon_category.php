<?php
/**
 * The template for displaying Salon Categories
 */
get_header(); ?>

<?php
$taxonomy = 'salon_category';     // имя таксономии
$category = get_queried_object(); // текущая категория салонов
$category_id = $category->term_id; // id категории салонов
$category_name = $category->name; // name категории салонов
$category_slug = $category->slug; // slug категории салонов
$category_description = $category->description; // description категории салонов
$parent_id = $category->parent; // id родительской категории

$thumbnail_id = get_salon_term_meta( $category_id, 'thumbnail_id', $single = true ); // id картинки, привязанной к топовой категории
if($thumbnail_id&&($thumbnail_id > 0)){
    $image = wp_get_attachment_image_src( $thumbnail_id, 'full', false ); // изображение по этому id
    if($image){
        $style = 'style="background: url('.$image[0].') top center no-repeat;height: 240px;"'; // устанавливаем фон контейнера сверху
    } else {
        $style = '';
    }
} else {
    $style = '';
}

$salons = array();

$centerLat = 0.000000; // широта центра карты
$centerLong = 0.000000; // долгота центра карты

$iconsize = '20,34'; // размер иконок
$iconoffset = '-10,-34'; // отступ иконок

$icon_salon = get_site_url().'/wp-content/uploads/salonA.png'; // путь к иконке салонов
$icon_podium = get_site_url().'/wp-content/uploads/podium.png'; // путь к иконке подиумов

$map = ''; // строка, содержащая шорткод яндекс карты

$template_part = '';
switch ($category_slug) {
    case "rossiya":
        $template_part = "rossiya";
        break;
    case "moscow":
        $template_part = "moscow";
        break;
    default:
        $template_part = "region";
}

include_once( 'template-parts/content-'.$template_part.'.php' );

get_footer();
