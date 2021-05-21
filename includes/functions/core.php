<?php
/**
 * Defines all core plugin functions
 *
 * @package \WooCommerce Product Notice
 * @author Aniket Desale
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	add_action( 'woocommerce_single_product_summary', 'crwpn_global_product_notice_top' );

	//* Add global product notice top to show up before the product description

	function crwpn_global_product_notice_top() {
		global $post;

		$global_product_notice_option = get_post_meta( $post->ID, '_crwpn_global_product_notice_option', 1 );

		$global_product_notice_top = get_option( 'wc_product_notice' );

		$global_product_notice_color = get_option( 'wc_product_notice_color' );

		if( $global_product_notice_option == 0 )
		{
		?>
			<div id="crwcpn-global-notice">
				<div class="<?php echo $global_product_notice_color ?>"><?php echo $global_product_notice_top;?></div>
			</div>
		<?php
		}
	}

