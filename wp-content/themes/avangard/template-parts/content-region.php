<?php
/**
 * @package avangard
 */
?>
<?php
$height = 435; // высота карты
$zoom_inital = 12; // начальный зум

$args = array(
    'posts_per_page'  => -1,
    'post_type'       => 'salon',
    'orderby'         => 'menu_order ',
    'order'           => 'ASC',
    'tax_query'       => array(
        array(
            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => $category_slug
        )
    )
);
$salon_posts = get_posts( $args ); // массив с салонами из текущей категории
?>

<div class="content_top cf">
    <div class="wrap">
        <div class="entry-title cf">
            <h1 style="float: left;margin-right: 10px;">Региональная сеть. <?php echo $category_name ?></h1>
        </div>
    </div>
</div>

<div class="content_middle region salons_list">
    <div class="wrap">
        <table>
            <tbody>
            <?php if( $salon_posts ) { ?>
                <?php foreach ( $salon_posts as $salon_post ) { // перебор салонов ?>
                    <?php
                    $custom_fields = get_post_custom($salon_post->ID); // все произвольный поля салона
                    $pointArr = explode(',',$custom_fields['point'][0]); // координаты салона в массив

                    $centerLat += floatval($pointArr[0]); // прибавляем широту салона
                    $centerLong += floatval($pointArr[1]); // прибавляем долготу салона

                    $description = '<h2 class="header"><img src="'.$icon_podium.'" /><b>Фирменный подиум</b></h2><h3 class="on_map"><b>'.$salon_post->post_title.'</b></h3><br>';
                    $description .= 'Адрес: <b>'.$custom_fields['address'][0].'</b><br>Телефон: <b>'.$custom_fields['telephone'][0].'</b><br>Часы работы: <b>'.$custom_fields['work_time'][0].'</b>';
                    $description = refixFalseWord($description); // фикс тегов описания
                    /* добаляем в шорткод карты строку с описанием текущего салона */
                    $map .= '[yamap_label coord="'.$custom_fields['point'][0].'" description="'.$description.'" icon="' . $icon_podium . '" iconsize="' . $iconsize . '" iconoffset="' . $iconoffset . '"]';
                    ?>
                <tr>
                    <td class="col-1"><a href="<?php echo get_permalink( $salon_post->ID, false ) ?>"><?php echo $salon_post->post_title ?></a></td>
                    <td class="col-2"><?php echo $custom_fields['address'][0] ?></td>
                    <td class="col-3"><?php echo $custom_fields['telephone'][0] ?></td>
                </tr>
                <?php } ?>
                <?php
                    $centerLat = $centerLat/count($salon_posts); // находим среднюю широту
                    $centerLong = $centerLong/count($salon_posts); // находим среднюю долготу
                    $center = $centerLat.','.$centerLong; // координаты центра карты
                    $map0 = '[yandexMap center="'.$center.'" height="'.$height.'" zoom_inital='.$zoom_inital.']'; // начало шорткода
                    $map .= '[/yandexMap]'; // конец шорткода
                    $map = $map0.$map; // шорткод готов
                ?>
            <?php } else { ?>
                <tr>
                    <td class="col-1" style="padding: 0 10px">Нет салонов</td>
                    <td class="col-2"></td>
                    <td class="col-3"></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="content_bottom cf">
    <?php echo do_shortcode($map); ?>
</div>



