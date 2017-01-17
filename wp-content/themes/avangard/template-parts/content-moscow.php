<?php
/**
 * @package avangard
 */
?>
<?php
$center = '55.8219, 37.6019'; // координаты центра карты

$height = 435;
$zoom_inital = 8;

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

$salons_list = array();

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

    $salons_list[$st]['name'] = $salon_type_name;
    $salons_list[$st]['slug'] = $salon_type_slug;

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
        foreach ( $salon_posts as $sp => $salon_post ) { // перебор салонов
            $custom_fields = get_post_custom($salon_post->ID); // все произвольный поля салона

            $salons_in_type[$sp]['salon_address'] = $custom_fields['address'][0]; // Адрес
            $salons_in_type[$sp]['salon_telephone'] = $custom_fields['telephone'][0]; // Телефон
            $salons_in_type[$sp]['salon_work_time'] = $custom_fields['work_time'][0]; // Часы работы

            $salon_category_arr = get_the_terms( $salon_post->ID , $taxonomy );
            $salon_category = $salon_category_arr[0];
            $salons_in_type[$sp]['category_name'] = $salon_category->name;
            $salons_in_type[$sp]['category_slug'] = $salon_category->slug;
            $salons_in_type[$sp]['metro_city'] = get_field('metro_city', 'salon_category_'.$salon_category->term_id); // Станция метро или город
            $salons_in_type[$sp]['salon_id'] = $salon_post->ID;
            $salons_in_type[$sp]['salon_title'] = esc_html($salon_post->post_title);
            $salons_in_type[$sp]['salon_link'] = get_permalink( $salon_post->ID, false );

            $pointArr = explode(',',$custom_fields['point'][0]); // координаты салона в массив

            if($salon_type_slug == 'firmennye_salony'){
                $description = '<h2 class="header"><img src="'.$icon_salon.'" /><b>Фирменный салон</b></h2><h3 class="on_map"><b>'.$salon_post->post_title.'</b></h3><br>';
                $description .= 'Адрес: <b>'.$custom_fields['address'][0].'</b><br>Телефон: <b>'.$custom_fields['telephone'][0].'</b><br>Часы работы: <b>'.$custom_fields['work_time'][0].'</b>';
                $description = refixFalseWord($description); // фикс тегов описания
                /* добаляем в шорткод карты строку с описанием текущего салона */
                $map .= '[yamap_label coord="'.$custom_fields['point'][0].'" description="'.$description.'" icon="' . $icon_salon . '" iconsize="' . $iconsize . '" iconoffset="' . $iconoffset . '"]';
            } else if($salon_type_slug == 'firmennye_podiumy'){
                $description = '<h2 class="header"><img src="'.$icon_podium.'" /><b>Фирменный подиум</b></h2><h3 class="on_map"><b>'.$salon_post->post_title.'</b></h3><br>';
                $description .= 'Адрес: <b>'.$custom_fields['address'][0].'</b><br>Телефон: <b>'.$custom_fields['telephone'][0].'</b><br>Часы работы: <b>'.$custom_fields['work_time'][0].'</b>';
                $description = refixFalseWord($description); // фикс тегов описания
                /* добаляем в шорткод карты строку с описанием текущего салона */
                $map .= '[yamap_label coord="'.$custom_fields['point'][0].'" description="'.$description.'" icon="' . $icon_podium . '" iconsize="' . $iconsize . '" iconoffset="' . $iconoffset . '"]';
            }
        }

        $salons_list[$st]['salons'] = $salons_in_type;

    }
}

$map0 = '[yandexMap center="'.$center.'" height="'.$height.'" zoom_inital='.$zoom_inital.']'; // начало шорткода
$map .= '[/yandexMap]'; // конец шорткода
$map = $map0.$map; // шорткод готов
?>

<div class="content_top cf">
<div class="wrap">
    <div id="region-img" <?php echo $style ?>>&nbsp;</div>
    <div class="entry-title cf">
<h1 style="float: left;margin-right: 10px;"><?php echo $category_name ?></h1>
<?php if($map){ ?>
    (<span class="show_map">ПОСМОТРЕТЬ НА КАРТЕ</span>)
<?php } ?>
    </div>
</div>

<?php if($map){ ?>
<div class="map">
    <?php echo do_shortcode($map); ?>
</div>
<?php } ?>

</div>

<div class="content_middle <?php echo $category_slug; ?> salons_list">
    <div class="wrap">
<?php
foreach ( $salons_list as $salons_block ) {
    if(isset($salons_block['salons'])&&!empty($salons_block['salons'])){
        uasort($salons_block['salons'], 'sort_by_category_name'); ?>

    <p class="title <?php echo $salons_block['slug']; ?>"><?php echo $salons_block['name']; ?></p>

<script
              src="https://code.jquery.com/jquery-2.2.4.min.js"
              integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
              crossorigin="anonymous"></script>
              
        <script type="text/javascript">
           $( document ).ready(function() {
               $('.for_hover').hover(function() {
                   $( this ).css("color", "red");
               }, function() {
                   $( this ).css("color", "#333333");
               });
        });
    </script>

    <div class="container">
    <?php foreach ( $salons_block['salons'] as $salon_params ) { ?>
            <div class="row">
                <div class="col-md-4 col-lg-4 col-sm-12 col-xs-12 <?php echo $salon_params['metro_city']; ?>">&nbsp&nbsp&nbsp&nbsp<?php echo $salon_params['category_name']; ?></div>
                <div class="col-md-4 col-lg-4 col-sm-12 col-xs-12">
                    <a class="for_hover" style="color: #333333;" href="<?php echo $salon_params['salon_link']; ?>"><?php echo $salon_params['salon_title']; ?></a>
                </div>
                <div class="col-md-4 col-lg-4 col-sm-12 col-xs-12"><?php echo $salon_params['salon_address']; ?></div>
            </div>
    <?php } ?>
        </div>
    <?php } ?>
<?php } ?>
    </div>
</div>

