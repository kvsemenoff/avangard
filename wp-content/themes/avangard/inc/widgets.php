<?php
/**
 * Custom Widgets
 *
 * @package avangard
 */

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function avangard_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( 'Сontent top', 'avangard' ),
        'id'            => 'content_top',
        'description'   => 'Виджеты вверху страницы',
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '',
        'after_title'   => '',
    ) );
    register_sidebar( array(
        'name'          => esc_html__( 'Top phone', 'avangard' ),
        'id'            => 'top_phone',
        'description'   => 'Телефон в шапке',
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '',
        'after_title'   => '',
    ) );
    register_sidebar( array(
        'name'          => esc_html__( 'Latest News', 'avangard' ),
        'id'            => 'latest_news',
        'description'   => 'Последние новости на главной',
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '<h2>',
        'after_title'   => '</h2>',
    ) );
    register_sidebar( array(
        'name'          => esc_html__( 'Latest Articles', 'avangard' ),
        'id'            => 'latest_articles',
        'description'   => 'Последние статьи на главной',
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '<h2>',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'avangard_widgets_init' );