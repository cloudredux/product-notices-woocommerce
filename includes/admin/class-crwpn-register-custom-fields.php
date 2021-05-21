<?php
/**
 * Registers custom fields used in products
 *
 * @package \WooCommerce Product Notice
 * @author Aniket Desale
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CRWPN_Custom_Fields {

	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'meta_boxes' ) );

		add_action( 'save_post', array( $this, 'save' ) );

		add_action( 'woocommerce_single_product_summary', array( $this, 'crwpn_product_notice_top' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ), 20 );

	}

	public function meta_boxes() {

		add_meta_box( 'wpn-product-notice', __( 'Product Notice / Information', 'cr-woocommerce-product-notice' ), array( $this, 'cr_product_notice_mb' ), 'product', 'normal', 'high' );

		add_meta_box( 'wpn-product-notice-global', __( 'Global Notice', 'cr-woocommerce-global-product-notice' ), array( $this, 'cr_global_product_notice_mb' ), 'product', 'side' );

	}

	public function cr_global_product_notice_mb( $post ) {

		$global_product_notice_option = false;

		$global_product_notice_option = get_post_meta( $post->ID, '_crwpn_global_product_notice_option', 1 );

		?>

		<?php wp_nonce_field( 'crwpn_product_notice_field', 'crwpn_product_notice_field_nonce' );?>

		<div>
			<p>
				<label for="global_product_notice_option">
					<input id="global_product_notice_option" class="crwpn-input input-checkbox" name="global_product_notice_option" value="1" <?php checked( $global_product_notice_option, true ); ?> type="checkbox"><?php _e( 'Disable global notice for this product', 'cr-woocommerce-product-notice' ); ?>
				</label>
			</p>					
		</div>
		
		<?php
	}

	public function cr_product_notice_mb( $post ) {

		$product_notice_top = get_post_meta( $post->ID, '_crwpn_product_notice_top', 1 );

		$product_notice_color = get_post_meta($post->ID, '_crwpn_product_notice_color', true);

		if ( empty( $product_notice_top ) ) {

			$product_notice_top = '';
		}
		?>

		<?php wp_nonce_field( 'crwpn_product_notice_field', 'crwpn_product_notice_field_nonce' );?>
		
		<div class="product-notice product-notice-top">
			<h4>Notice Text</h4>
			<p><em>Enter the information that you wish to show up on the product page after the product title.</em></p>
			<textarea id="crwpn_product_notice_top" class="input-textarea field-product-notice-top" name="crwpn_product_notice_top" rows="5" cols="50" placeholder="<?php _e( 'Use of HTML is supported in this field' ); ?>" style="width: 100%;"><?php echo esc_textarea( $product_notice_top ); ?></textarea>
		</div>
		
		<div>
			<h4>Notice Appearance</h4>
			<em>Choose color of product notice : </em>
		    <select name="crwpn_product_notice_color" id="crwpn_product_notice_color">
		    	<option value="default" <?php selected( $product_notice_color, 'default' ); ?>>Default</option>
			  	<option value="blue" <?php selected( $product_notice_color, 'blue' ); ?>>Blue</option>
				<option value="yellow" <?php selected( $product_notice_color, 'yellow' ); ?>>Yellow</option>
		      	<option value="red" <?php selected( $product_notice_color, 'red' ); ?>>Red</option>
		    </select>
		</div>
		<?php
	}
	
	public function save( $post_id ) {

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['crwpn_product_notice_field_nonce'] ) && wp_verify_nonce( $_POST['crwpn_product_notice_field_nonce'], 'crwpn_product_notice_field' ) ) {
	
			$global_product_notice = ! empty( $_POST['global_product_notice_option'] ) ? wp_kses_post( $_POST['global_product_notice_option'] ) : '';

			update_post_meta( $post_id, '_crwpn_global_product_notice_option', $global_product_notice );
		}
		
		if ( isset( $_POST['crwpn_product_notice_field_nonce'] ) && wp_verify_nonce( $_POST['crwpn_product_notice_field_nonce'], 'crwpn_product_notice_field' ) ) {
	
			$product_notice_top = ! empty( $_POST['crwpn_product_notice_top'] ) ? wp_kses_post( $_POST['crwpn_product_notice_top'] ) : '';

			update_post_meta( $post_id, '_crwpn_product_notice_top', $product_notice_top );
		}

		if ( isset( $_POST['crwpn_product_notice_field_nonce'] ) && wp_verify_nonce( $_POST['crwpn_product_notice_field_nonce'], 'crwpn_product_notice_field' ) ) {
	
			$product_notice_color = ! empty( $_POST['crwpn_product_notice_color'] ) ? wp_kses_post( $_POST['crwpn_product_notice_color'] ) : '';

			update_post_meta( $post_id, '_crwpn_product_notice_color', $product_notice_color );
		}


	}

	//* Add product notice top to show up before the product description

	function crwpn_product_notice_top() {

		$product_notice_top = get_post_meta( get_the_ID(), '_crwpn_product_notice_top', 1 );

		$product_notice_color = get_post_meta( get_the_ID(), '_crwpn_product_notice_color', true);

		if ( ! empty( $product_notice_top ) ) {
		?>
			<div id="crwcpn-individual-product-notice">
				<p class="<?php echo $product_notice_color ?>"><?php echo wp_kses_post( $product_notice_top );?></p>
			</div>
		<?php	
		}
	}

	public function load_assets( ) {

		wp_enqueue_style( 'cr-product-notice-styles', crwpn()->plugin_url() . '/assets/css/admin/global.css' );

	}
}