<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package avangard
 */

?><!DOCTYPE html>
<html lang="ru-RU">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="stylesheet" href="css/style4.scss">
<link rel="stylesheet" href="css/style2.scss">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'avangard' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
        <div class="header_top cf">
            <div class="wrap">
                <div class="links">
                    <?php wp_nav_menu( array( 'theme_location' => 'top', 'menu_id' => 'top-menu', 'container' => 'div'  ) ); ?>
                </div>
                <div class="search">
                    <?php get_search_form(); ?>
                </div>
            </div>
        </div>

        <div class="header_bottom cf">
            <div class="wrap">
                <div class="logo">
                    <a href="/"><img src="<?php echo get_template_directory_uri() ?>/img/logo.jpg" alt="Логотип"></a>
                </div>
                <div class="right">
                    <div class="header_contacts">
                        <?php if ( is_active_sidebar( 'top_phone' ) ) : ?>
                            <?php dynamic_sidebar( 'top_phone' ); ?>
                        <?php endif; ?>
                        <p class="tel_desc">многоканальный телефон</p>
                    </div>
                    <div class="sity_select">
                        <span>Регион:</span>
                        <?php do_action( 'get_regions',4 ); /* salon-post-types/salon-type.php */?>
                    </div><!-- sity_select -->
                    <div class="credit_btn"><a href="/kredit/">&nbsp;</a></div>
                </div>
            </div>
        </div>

	</header><!-- #masthead -->

    <nav id="site-navigation" class="main-navigation" role="navigation">
        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'avangard' ); ?></button>
        <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu', 'container' => 'div', 'container_class' => 'row',  ) ); ?>
    </nav><!-- #site-navigation -->

	<section id="content" class="site-content">
