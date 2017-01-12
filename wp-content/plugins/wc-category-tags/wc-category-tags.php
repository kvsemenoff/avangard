<?php
/*
  Plugin Name: Woocommerce Categories and Tags
  Plugin URI:
  Description: This is widget plugin which shows the list of woocommerce categories and tags.
  Version: 1.0.0
  Author: cancer
  Author URI:
  License: GPL2
 */

class wc_category_tags extends WP_Widget {

    // constructor
    function wc_category_tags() {
        parent::__construct(false, $name = __('Woocommerce Categories and Tags', 'wc_category_tags'));
    }

    // widget form creation
    function form($instance) {
        // Check values
        if ($instance) {
            if(isset($instance['ntags'])){
                $ntags = esc_attr($instance['ntags']);
            } else {
                $ntags = '';
            }
        } else {
            $ntags = '';
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('ntags'); ?>"><?php _e('Number of tags', 'wc_category_tags'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('ntags'); ?>" name="<?php echo $this->get_field_name('ntags'); ?>" type="text" value="<?php echo $ntags; ?>" />
        </p><?php
    }

    // update widget

    function update($new_instance, $old_instance) {

        $instance = $old_instance;

        // Fields
        $instance['ntags'] = strip_tags($new_instance['ntags']);

        return $instance;
    }

    // widget display
    // display widget

    function widget($args, $instance) {

        extract($args);

        $before_widget = '';
        $before_title = '';
        $after_title = '';
        $after_widget = '';

        // these are the widget options
        if(isset($instance['ntags'])){
            $ntags = $instance['ntags'];
        } else {
            $ntags = '';
        }

        echo $before_widget;

        // Check if text is set
        $taxonomy = 'product_cat';
        $show_count = 0;      // 1 for yes, 0 for no
        $pad_counts = 0;      // 1 for yes, 0 for no
        $hierarchical = 1;      // 1 for yes, 0 for no
        $title = '';
        $empty = 0;
        $args = array(
            'taxonomy' => $taxonomy,
            'menu_order'    => 'ASC',
            'show_count' => $show_count,
            'pad_counts' => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li' => $title,
            'parent'  => 0,
            'hide_empty' => $empty
        );

        $top_categories = get_categories($args); // cписок топовых категорий

        echo '<div class="menu_child_top cf">';

        foreach ($top_categories as $c => $top_category) {

            echo '<ul class="col-'.($c+1).'">';
            echo '<li class="menu_child_title">'.$top_category->name.'</li>';

            $top_category_id = $top_category->term_id;
            $args = array(
                'hierarchical' => 1,
                'show_option_none' => '',
                'hide_empty' => 0,
                'menu_order'    => 'ASC',
                'parent' => $top_category_id,
                'taxonomy' => $taxonomy
            );

            $subcats = get_categories($args); // список подкатегорий у очередной топовой категории

            foreach ($subcats as $sc => $subcat) {
                $subcat_id = $subcat->term_id;
                $icon = get_field('mega_cat_icon', 'product_cat_'.$subcat_id);
                if($icon){
                    $style = 'style="background: url('.$icon["url"].') left center no-repeat;"'; // устанавливаем фон контейнера сверху
                } else {
                    $style = '';
                }
                $link = get_term_link( $subcat->slug, $subcat->taxonomy );
                echo '<li '.$style.' class="row-'.($sc+1).'"><a href="'. $link .'">'.$subcat->name.'</a></li>';
            }
            echo '</ul>';
        }

        echo '</div>';

        $args = array(
            'orderby'       => 'none',
            'order'         => 'ASC',
            'number'        => $ntags,
            'fields'        => 'all',
            'slug'          => '',
            'parent'         => '',
            'hierarchical'  => true,
            'child_of'      => 0,
            'hide_empty'    => 0,
            'get'           => '', // ставим all чтобы получить все термины
            'name__like'    => '',
            'pad_counts'    => false,
            'offset'        => '',
            'search'        => '',
            'cache_domain'  => 'core',
            'name'          => '', // str/arr поле name для получения термина по нему. C 4.2.
            'childless'     => false, // true не получит (пропустит) термины у которых есть дочерние термины. C 4.2.
            'update_term_meta_cache' => true, // подгружать метаданные в кэш
            'meta_query'    => '',
        );

        $product_tags = get_terms('product_tag',$args); // список тегов продуктов

        echo '<div class="menu_child_bottom">';
        echo '<p class="menu_child_title">Коллекции</p>';
        echo '<ul class="cf">';
        foreach ($product_tags as $pt => $product_tag) {
            $tag_id = $product_tag->term_id;
            $link = get_term_link( $product_tag->slug, $product_tag->taxonomy );
            echo '<li class="tag-'.$tag_id.'"><a href="'. $link .'">Коллекция '.$product_tag->name.'</a></li>';
        }
        echo '</ul>';
        echo '<a href="/catalog/" class="more_btn" title="Просмотр всех коллекций">Все коллекции</a>';
        echo '</div>';

        echo $after_widget;
    }

}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("wc_category_tags");'));

// Register style sheet.
add_action('wp_enqueue_scripts', 'wc_category_tags_style');

/**

 * Register style sheet.

 */
function wc_category_tags_style() {
    wp_register_style('wc_category_tags_style', plugins_url('woocommerce-category-widget/css/style.css'));
    wp_enqueue_style('wc_category_tags_style');
}

add_action('admin_notices', 'wc_category_tags_cat_admin_notice');

function wc_category_tags_cat_admin_notice() {

}

add_action('admin_init', 'wc_category_tags_cat_notice_ignore');

function wc_category_tags_cat_notice_ignore() {
}
