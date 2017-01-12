<?php
/**
 * avangard functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package avangard
 */

if ( ! function_exists( 'avangard_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function avangard_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on avangard, use a find and replace
	 * to change 'avangard' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'avangard', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

    register_nav_menus( array(
        'top' => esc_html__( 'Top', 'avangard' ),
    ) );

	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'avangard' ),
	) );

    register_nav_menus( array(
        'footer-menu_left' => esc_html__( 'Footer Menu Left', 'avangard' ),
    ) );

    register_nav_menus( array(
        'footer-menu_right' => esc_html__( 'Footer Menu Right', 'avangard' ),
    ) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'avangard_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

    add_image_size( 'mega_cat_icon', 50, 16,true );
    add_image_size( 'material_image', 110, 65,true );
    add_image_size( 'product_box', 300, 210,true );
    add_image_size( 'single_salon', 435, 300,true );

    remove_action( 'wp_head', 'feed_links_extra', 3 );
    remove_action( 'wp_head', 'feed_links', 2 );
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'index_rel_link' );
    remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
    remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
    remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
    remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
    remove_action( 'wp_head', 'wp_generator' );
    remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

}
endif;
add_action( 'after_setup_theme', 'avangard_setup' );

remove_action( 'load-update-core.php', 'wp_update_plugins' );
add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );
wp_clear_scheduled_hook( 'wp_update_plugins' );

function override_mce_options($initArray) {
    $opts = '*[*]';
    $initArray['valid_elements'] = $opts;
    $initArray['extended_valid_elements'] = $opts;
    return $initArray;
}
add_filter('tiny_mce_before_init', 'override_mce_options');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function avangard_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'avangard_content_width', 640 );
}
add_action( 'after_setup_theme', 'avangard_content_width', 0 );

/**
 * Enqueue scripts and styles.
 */
function avangard_scripts() {
    wp_enqueue_style( 'reset-style', get_template_directory_uri().'/css/reset.css' );
    wp_enqueue_style( 'avangard-fonts', get_template_directory_uri().'/css/fonts.css' );
    wp_enqueue_style( 'avangard-fancybox-css', get_template_directory_uri().'/css/jquery.fancybox.css' );
	wp_enqueue_style( 'avangard-style', get_stylesheet_uri() );

	wp_enqueue_script( 'avangard-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

    wp_enqueue_script( 'avangard-fancybox', get_template_directory_uri() . '/js/jquery.fancybox.pack.js', array('jquery'), '', true );
    wp_enqueue_script( 'avangard-main', get_template_directory_uri() . '/js/main.js', array('jquery'), '', true );

	wp_enqueue_script( 'avangard-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

    if ( is_product() || is_singular() ) {
        wp_enqueue_style( 'sliderkit-css', get_template_directory_uri().'/css/sliderkit-core.css' );
        wp_enqueue_script( 'sliderkit-js', get_template_directory_uri() . '/js/jquery.sliderkit.1.4.min.js', array('jquery'), '', true );
    }

    if ( is_salon_category() ) {
        wp_register_script('yandexMaps', "http://api-maps.yandex.ru/2.1/?load=package.full&lang=ru-RU");
        wp_enqueue_script('yandexMaps');
    }

    if ( is_page_template('template-kredit.php') ) {
        wp_enqueue_style( 'retailcredit-uicss', '/retailcredit/js/jquery-ui.min.css' );
        wp_enqueue_style( 'retailcredit-uithemecss', '/retailcredit/js/jquery-ui.theme.min.css' );
        wp_enqueue_script( 'retailcredit-ui', '/retailcredit/js/jquery-ui.min.js', array('jquery'), '', true );
        wp_enqueue_script( 'retailcredit-js', '/retailcredit/js/retailcredit.js', array('jquery'), '', true );
    }
}
add_action( 'wp_enqueue_scripts', 'avangard_scripts' );

function my_enqueue($hook) {
    $post_type = filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_SPECIAL_CHARS);
    if ( $post_type != "salon" ) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_style( 'admin-fancybox-css', get_template_directory_uri().'/css/jquery.fancybox.css' );
    wp_enqueue_style( 'admin-salon-images-css', get_template_directory_uri().'/css/admin.css' );
    wp_enqueue_script( 'admin-fancybox', get_template_directory_uri() . '/js/jquery.fancybox.pack.js', array('jquery'), '', true );
    wp_enqueue_script( 'admin-salon-images', get_template_directory_uri() . '/js/admin.js', array('jquery'), '', true );
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );

/* функция для определения категории салонов */
function is_salon_category() {
    $category = get_queried_object(); // текущая категория
    if(isset($category->taxonomy)&&$category->taxonomy == 'salon_category'){
        return true;
    } else {
        return false;
    }
}

function refixFalseWord($value){
    $value = str_replace('<','(lt)',$value);
    $value = str_replace('>','(gt)',$value);
    $value = str_replace('"','(quot)',$value);
    $value = str_replace('=','(equiv)',$value);
    return $value;
}

/**
 * Returns ID of top-level parent category, or current category if you are viewing a top-level
 *
 * @param    string      $catid      Category ID to be checked
 * @return   string      $catParent  ID of top-level parent category
 */
function get_top_cat_id( $catid ) {
    while ($catid) {
        $cat = get_category($catid); // get the object for the catid
        $catid = $cat->category_parent; // assign parent ID (if exists) to $catid
        // the while loop will continue whilst there is a $catid
        // when there is no longer a parent $catid will be NULL so we can assign our $catParent
        $catParent = $cat->cat_ID;
    }
    return $catParent;
}

/**
 * Implement the Custom Widgets.
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load Salons files.
 */
require get_template_directory() . '/salon-post-types/salon-taxonomy.php';
require get_template_directory() . '/salon-post-types/salon-type.php';
require get_template_directory() . '/salon-post-types/salon-functions.php';
require get_template_directory() . '/salon-post-types/admin/moscow.php';
require get_template_directory() . '/salon-post-types/admin/salons-rossiya.php';

/* WOOCOMMERCE */

/* Поддержка woocommerce */
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

/* Отключаем родную таблицу стилей и начинаем с нуля */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Load WOOCOMMERCE ACTIONS.
 */
require get_template_directory() . '/inc/woo_actions.php';























