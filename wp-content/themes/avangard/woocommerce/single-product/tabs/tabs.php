<?php
/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woo_product_tabs', array() );

if ( ! empty( $tabs ) ) : ?>
    <?php global $product; ?>
    <?php $cnt = 1; ?>
    <ul class="tab-nav cf">
        <?php foreach ( $tabs as $key => $tab ) : ?>
            <li data-link="tab-<?php echo esc_attr( $cnt ); ?>" class="<?php echo ($cnt == 1) ? 'active' : ''; ?>"><span><?php echo esc_html( $tab['title'] ); ?></span></li>
            <?php $cnt++; ?>
        <?php endforeach; ?>
    </ul>

    <div class="btn-wrap">
        <a href="/product_salons/?product_id=<?php echo $product->id; ?>">НАЛИЧИЕ В САЛОНАХ</a>
        <a href="/salons/rossiya/">КУПИТЬ В РОССИИ</a>
    </div>

    <?php $cnt = 1; ?>
    <div class="tab-content">
        <?php foreach ( $tabs as $key => $tab ) : ?>
            <div id="tab-<?php echo esc_attr( $cnt ); ?>" class="tab <?php echo esc_attr( $key ); ?>">
                <?php call_user_func( $tab['callback'], $key, $tab ); ?>
                <?php $cnt++; ?>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>
