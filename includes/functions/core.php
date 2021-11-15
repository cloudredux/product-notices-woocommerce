<?php
/**
 * Defines all core plugin functions.
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

	/**
	 * @since	1.1.1
	 * @uses`	crwcpn_get_global_product_notice` function to render notice.
	 * 			previously the output markup was directly echoed at this hook location.
	 */
	echo crwcpn_get_global_product_notice(); // phpcs:ignore WordPress.Security.EscapeOutput 

}

/**
 * Get Global product notice based on the plugin setting.
 * 
 * @since 1.1.1
 *
 * @param bool $echo If boolean value is false fuction echo.
 * @return string Return or echo the product notice markup.
 */
function crwcpn_get_global_product_notice( $echo = false ) {

	$crwcpn_hide_global_notice = false;

	// Check if disabled on a product page.
	if ( function_exists( 'is_product' ) && is_product() ) {

		$crwcpn_hide_global_notice = get_post_meta( get_the_ID(), 'crwcpn_hide_global_notice', 1 );
	
	}

	$crwcpn_global_product_notice_text = get_option( 'crwpcn_global_product_notice' );
	$crwcpn_global_product_notice_style = get_option( 'crwpcn_product_notice_background_color' );

	// Bail, if notice not set.
	if ( empty( $crwcpn_global_product_notice_text ) ) {

		return;
	}

	/**
	 * Color picker options.
	 *
	 * @since 1.1.0
	 */
	$custom_background_color = get_option( 'crwpcn_product_notice_custom_background_color' );
	$custom_text_color       = get_option( 'crwpcn_product_notice_custom_text_color' );
	$custom_border_color     = get_option( 'crwpcn_product_notice_custom_border_color' );

	$styles      = '';
	$content     = $styles;
	$color_class = esc_attr( $crwcpn_global_product_notice_style );

	if ( 'custom_color' === $crwcpn_global_product_notice_style ) {

		$styles = ' style="background-color: ' . esc_attr( $custom_background_color ) . '; color: ' . esc_attr( $custom_text_color ) . '; border-color: ' . esc_attr( $custom_border_color ) . '"';

		$color_class = 'crwcpn-custom-style';

	}

	if ( ! $crwcpn_hide_global_notice ) {

		$content  = '<div class="crwcpn-notice crwcpn-global-notice ' . esc_attr( $color_class ) . '"' . $styles . '>';
		$content .= wp_kses_post( do_shortcode( $crwcpn_global_product_notice_text ) );
		$content .= '</div>';
	}

	// Return or echo.
	if ( $echo ) {

		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput 
		return;

	} else {

		return $content;
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

	/**
	 * @since	1.1.1
	 * @uses`	crwcpn_get_product_notice` function to render notice.
	 * 			previously the output markup was directly echoed at this hook location.
	 */
	echo crwcpn_get_product_notice( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput 
}

/**
 * Get product notice for specific product.
 * 
 * @since 1.1.1
 *
 * @param int  $id Product ID. Defaults to current product ID (global $post).
 * @param bool $echo Return or echo the product notice markup.
 */
function crwcpn_get_product_notice( $id = null, $echo = false ) {

	if ( null !== $id ) { // Use the ID if supplied.

		$product_id = intval( $id );

	} else { // Default to current product ID

		$product_id = ( function_exists( 'is_product' ) && is_product() ) ? get_the_ID() : 0;
	}

	// Do nothing if invalid product ID.
	if ( 0 === $product_id ) {
		return;
	}

	$crwcpn_single_product_notice_text = get_post_meta( $product_id, 'crwcpn_product_notice', true );

	$crwpcn_single_product_notice_style = get_post_meta( $product_id, 'crwpcn_single_product_notice_background_color', true );

	/**
	 * Color picker options.
	 *
	 * @since 1.1.0
	*/
	$single_custom_background_color = get_post_meta( $product_id, 'crwpcn_single_product_notice_custom_background_color', true );
	$single_custom_text_color       = get_post_meta( $product_id, 'crwpcn_single_product_notice_custom_text_color', true );
	$single_custom_border_color     = get_post_meta( $product_id, 'crwpcn_single_product_notice_custom_border_color', true );

	$styles      = '';
	$content     = $styles;
	$color_class = esc_attr( $crwpcn_single_product_notice_style );

	if ( 'custom_color' === $crwpcn_single_product_notice_style ) {

		$styles = ' style="background-color: ' . esc_attr( $single_custom_background_color ) . '; color: ' . esc_attr( $single_custom_text_color ) . '; border-color: ' . esc_attr( $single_custom_border_color ) . '"';

		$color_class = 'crwcpn-custom-style';

	}

	if ( ! empty( $crwcpn_single_product_notice_text ) ) {

		$content  = '<div class="crwcpn-notice crwcpn-product-notice ' . esc_attr( $color_class ) . '"' . $styles . '>';
		$content .= wp_kses_post( do_shortcode( $crwcpn_single_product_notice_text ) );
		$content .= '</div>';

	}

	// Return or echo.
	if ( $echo ) {

		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput 
		return;

	} else {

		return $content;

	}
}

/**
 * Shortcode support to display product notices.
 * 
 * # use attribute [type] to 
 * 
 * @since 1.1.1
 * 
 * @param array $atts Shortcode attributes.
 * @return Product notice markup.
 */
add_shortcode( 'crwcpn-notice', 'crwcpn_sc_notice' );

function crwcpn_sc_notice( $atts ) {

	$crwcpn_attrs = shortcode_atts(
		array(
			'type' => 'default',
			'id'   => '',
		),
		$atts,
		'crwcpn-notice'
	);

	$type = sanitize_text_field( $crwcpn_attrs['type'] );

	// Show global or specific product notice, as requested.
	switch ( $type ) {
		case 'product':
			if ( ! empty( $crwcpn_attrs['id'] ) ) {

				$id     = (int) $crwcpn_attrs['id'];
				$output = crwcpn_get_product_notice( $id, false );

			} else {

				$output = crwcpn_get_product_notice( null, false );

			}

			break;

		case 'global':
		default:		// Sbow global notice for any other type specified as shortcode attribute.
			$output = crwcpn_get_global_product_notice();
			
			break;
	}

	return $output;
}
