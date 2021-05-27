<?php
/**
 * Defines all core plugin functions
 *
 * @package \WooCommerce Product Notice
 * @author Aniket Desale
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	add_action( 'woocommerce_single_product_summary', 'crwcpn_global_product_notice_top' );

//* Add global product notice top to show up before the product description

function crwcpn_global_product_notice_top() {
	global $post;

	$crwcpn_global_product_notice_option = get_post_meta( $post->ID, '_crwcpn_global_product_notice_option', 1 );

	$crwcpn_global_product_notice_text = get_option( 'crwpcn_global_product_notice' );

	$crwcpn_global_product_notice_background_color = get_option( 'crwpcn_product_notice_background_color' );

	if ( 0 == $crwcpn_global_product_notice_option )
	{
	?>
		<div id="crwcpn-global-notice">
			<div class="<?php echo $crwcpn_global_product_notice_background_color ?>"><?php echo wp_kses_post( $crwcpn_global_product_notice_text ); ?></div>
		</div>
	<?php
	}
}

