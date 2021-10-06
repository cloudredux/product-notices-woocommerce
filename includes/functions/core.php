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
			'default'      => __( 'Default', 'product-notices-woocommerce' ),
			'blue'         => __( 'Blue', 'product-notices-woocommerce' ),
			'yellow'       => __( 'Yellow', 'product-notices-woocommerce' ),
			'red'          => __( 'Red', 'product-notices-woocommerce' ),
			'green'        => __( 'Green', 'product-notices-woocommerce' ),
			'custom_color' => __( '&mdash; Custom Style &mdash;', 'product-notices-woocommerce' ),
		)
	);
}

/**
 * Default custom color styles used in notice appearance.
 *
 * @since 1.1.0
 *
 * @return array Default custom color style values
 */
function crwcpn_custom_style_defaults() {

	return apply_filters(
		'crwcpn_custom_style_defaults',
		array(
			'background_color' => '#e5effa',
			'text_color'       => '#21242f',
			'border_color'     => '#aaccf0',
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
 */
function crwcpn_global_product_notice_top() {

	$crwcpn_global_product_notice_text = get_option( 'crwpcn_global_product_notice' );

	// Do nothing, if notice not set.
	if ( empty( $crwcpn_global_product_notice_text ) ) {
		return;
	}

	$crwcpn_hide_global_notice = get_post_meta( get_the_ID(), 'crwcpn_hide_global_notice', 1 );

	$crwcpn_global_product_notice_style = get_option( 'crwpcn_product_notice_background_color' );

	/**
	 * Color picker options.
	 *
	 * @since 1.1.0
	 */
	$custom_background_color = get_option( 'crwpcn_product_notice_custom_background_color' );
	$custom_text_color       = get_option( 'crwpcn_product_notice_custom_text_color' );
	$custom_border_color     = get_option( 'crwpcn_product_notice_custom_border_color' );

	$styles = '';

	$color_class = esc_attr( $crwcpn_global_product_notice_style );

	if ( 'custom_color' === $crwcpn_global_product_notice_style ) {

		$styles = ' style="background-color: ' . esc_attr( $custom_background_color ) . '; color: ' . esc_attr( $custom_text_color ) . '; border-color: ' . esc_attr( $custom_border_color ) . '"';

		$color_class = 'crwcpn-custom-style';

	}

	if ( ! $crwcpn_hide_global_notice ) {
		?>
		<div class="crwcpn-notice crwcpn-global-notice <?php echo esc_attr( $color_class ); ?>"<?php echo $styles; ?>>
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
 * Display per-product notice
 */
function crwcpn_product_notice_top() {

	$crwcpn_single_product_notice_text = get_post_meta( get_the_ID(), 'crwcpn_product_notice', true );

	$crwpcn_single_product_notice_style = get_post_meta( get_the_ID(), 'crwpcn_single_product_notice_background_color', true );

	/**
	 * Color picker options.
	 *
	 * @since 1.1.0
	*/
	$single_custom_background_color = get_post_meta( get_the_ID(), 'crwpcn_single_product_notice_custom_background_color', true );
	$single_custom_text_color       = get_post_meta( get_the_ID(), 'crwpcn_single_product_notice_custom_text_color', true );
	$single_custom_border_color     = get_post_meta( get_the_ID(), 'crwpcn_single_product_notice_custom_border_color', true );

	$styles = '';

	$color_class = esc_attr( $crwpcn_single_product_notice_style );

	if ( 'custom_color' === $crwpcn_single_product_notice_style ) {

		$styles = ' style="background-color: ' . esc_attr( $single_custom_background_color ) . '; color: ' . esc_attr( $single_custom_text_color ) . '; border-color: ' . esc_attr( $single_custom_border_color ) . '"';

		$color_class = 'crwcpn-custom-style';

	}

	if ( ! empty( $crwcpn_single_product_notice_text ) ) {
		?>
		<div class="crwcpn-notice crwcpn-product-notice <?php echo esc_attr( $color_class ); ?>"<?php echo $styles; ?>>
			<?php echo wp_kses_post( do_shortcode( $crwcpn_single_product_notice_text ) ); ?>
		</div>
		<?php
	}
}
