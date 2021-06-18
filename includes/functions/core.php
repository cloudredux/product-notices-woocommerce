<?php
/**
 * Defines all core plugin functions
 *
 * @package \Product Notices for WooCommerce
 * @author Aniket Desale
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Default supported color options for notice appearance
 *
 * @since 1.0.0
 *
 * @return array Default color values and labels
 */
function crwcpn_get_notice_colors() {
	return apply_filters(
		'crwcpn_notice_colors',
		array(
			'default' => __( 'Default', 'cr-woocommerce-product-notice' ),
			'blue'    => __( 'Blue', 'cr-woocommerce-product-notice' ),
			'yellow'  => __( 'Yellow', 'cr-woocommerce-product-notice' ),
			'red'     => __( 'Red', 'cr-woocommerce-product-notice' ),
			'green'   => __( 'Green', 'cr-woocommerce-product-notice' ),
		)
	);
}

/**
 * Show Global Notice on `woocommerce_single_product_summary` hook above the Product Short Description
 *
 * @since 1.0.0
 */
add_action( 'woocommerce_single_product_summary', 'crwcpn_global_product_notice_top', 12 );

/**
 * Display Global product notice.
 *
 * @since 1.0.0
 */
function crwcpn_global_product_notice_top() {

	$crwcpn_hide_global_notice = get_post_meta( get_the_ID(), 'crwcpn_hide_global_notice', 1 );

	$crwcpn_global_product_notice_text = get_option( 'crwpcn_global_product_notice' );

	$crwcpn_global_product_notice_background_color = get_option( 'crwpcn_product_notice_background_color' );

	if ( ! $crwcpn_hide_global_notice ) {
		?>
		<div class="crwcpn-notice crwcpn-global-notice <?php echo esc_attr( $crwcpn_global_product_notice_background_color ); ?>">
			<?php echo wp_kses_post( do_shortcode( $crwcpn_global_product_notice_text ) ); ?>
		</div>
		<?php
	}
}

/**
 * Show notice on `woocommerce_single_product_summary` hook above the Product Short Description for a product
 * Product notice content and appearance style are set as post meta for the product.
 * Shows nothing if per-product notice is not configured
 *
 * @since 1.0.0
 */

add_action( 'woocommerce_single_product_summary', 'crwcpn_product_notice_top', 12 );

/**
 * Display Per-product notice.
 *
 * @since 1.0.0
 */
function crwcpn_product_notice_top() {

	$crwcpn_single_product_notice_text = get_post_meta( get_the_ID(), 'crwcpn_product_notice', true );

	$crwpcn_single_product_notice_background_color = get_post_meta( get_the_ID(), 'crwpcn_single_product_notice_background_color', true );

	if ( ! empty( $crwcpn_single_product_notice_text ) ) {
		?>
		<div class="crwcpn-notice crwcpn-product-notice <?php echo esc_attr( $crwpcn_single_product_notice_background_color ); ?>">
			<?php echo wp_kses_post( do_shortcode( $crwcpn_single_product_notice_text ) ); ?>
		</div>
		<?php
	}
}
