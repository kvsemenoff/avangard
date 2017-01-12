<?php
/**
 * @package avangard
 */
?>
<?php

$center = '56.5,70'; // координаты центра карты

$height = 435;
$zoom_inital = 4;

$args = array(
    'hide_empty' => 0,
    'parent'     => $category_id,
    'orderby'    => 'term_order',
    'order'      => 'ASC'
);
$salon_categories = get_terms($taxonomy, $args ); // массив с категориями салонов, подкатегориями данной топовой категории

$salon_category_ids = array(); // строка с id категорий в данной топовой категории
$salon_categories_nbr = count($salon_categories) - 1;
foreach ( $salon_categories as $sc => $salon_category ) {     // перебираем массив с категориями салонов
    $salon_category_term_id = $salon_category->term_id; // id категории салонов
    $salon_category_ids[] = $salon_category_term_id;
}

$args = array(
    'hide_empty' => 0,
    'orderby'    => 'term_order',
    'order'      => 'ASC'
);
$salon_types = get_terms('salon_type', $args ); // массив с типами салонов

$salons_list = array();
foreach($salon_types as $st => $salon_type){
    $salon_type_name = $salon_type->name;
    $salon_type_slug = $salon_type->slug;

    $args = array(
        'posts_per_page'  => -1,
        'post_type'       => 'salon',
        'orderby'         => 'menu_order ',
        'order'           => 'ASC',
        'tax_query'       => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'salon_type',
                'field'    => 'slug',
                'terms'    => array( $salon_type_slug ),
            ),
            array(
                'taxonomy' => $taxonomy,
                'field'    => 'id',
                'terms'    => $salon_category_ids
            )
        )
    );
    $salon_posts = get_posts( $args ); // массив с салонами с текущим типом салона и подкатегорий из текущей топовой категории

    if( $salon_posts ) {
        foreach ( $salon_posts as $salon_post ) { // перебор салонов
            $custom_fields = get_post_custom($salon_post->ID); // все произвольный поля салона
            $pointArr = explode(',',$custom_fields['point'][0]); // координаты салона в массив

            if($salon_type_slug == 'firmennye_salony'){
                $description = '<h2 class="header"><img src="'.$icon_salon.'" /><b>Фирменный салон</b></h2><h3 class="on_map"><b>'.$salon_post->post_title.'</b></h3><br>';
            } else if($salon_type_slug == 'firmennye_podiumy'){
                $description = '<h2 class="header"><img src="'.$icon_podium.'" /><b>Фирменный подиум</b></h2><h3 class="on_map"><b>'.$salon_post->post_title.'</b></h3><br>';
            }

            $description .= 'Адрес: <b>'.$custom_fields['address'][0].'</b><br>Телефон: <b>'.$custom_fields['telephone'][0].'</b><br>Часы работы: <b>'.$custom_fields['work_time'][0].'</b>';
            $description = refixFalseWord($description); // фикс тегов описания
            /* добаляем в шорткод карты строку с описанием текущего салона */
            $map .= '[yamap_label coord="'.$custom_fields['point'][0].'" description="'.$description.'" icon="' . $icon_podium . '" iconsize="' . $iconsize . '" iconoffset="' . $iconoffset . '"]';
        }

    }
}

$map0 = '[yandexMap center="'.$center.'" height="'.$height.'" zoom_inital='.$zoom_inital.']'; // начало шорткода
$map .= '[/yandexMap]'; // конец шорткода
$map = $map0.$map; // шорткод готов

?>

<div class="content_top cf">
    <div class="wrap">
        <div class="entry-title cf">
        <h1 style="float: left;margin-right: 10px;">Региональная сеть</h1>
        </div>
    </div>
</div>

<div class="content_middle <?php echo $category_slug ?> map">
    <?php echo do_shortcode($map); ?>
</div>

<div class="content_bottom <?php echo $category_slug ?> salons_letters cf">
    <div class="wrap">
    <?php echo do_action( 'get_regions', 5 ); /* salon-post-types/salon-functions.php */ ?>
    </div>
</div>
