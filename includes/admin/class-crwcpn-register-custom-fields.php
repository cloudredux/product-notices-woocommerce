<?php
/**
 * Registers custom fields used in products
 *
 * @package \WooCommerce Product Notice
 * @author Aniket Desale
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CRWCPN_Custom_Fields {

	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'meta_boxes' ) );

		add_action( 'save_post', array( $this, 'save' ) );

		add_action( 'woocommerce_single_product_summary', array( $this, 'crwcpn_product_notice_top' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ), 20 );

	}

	public function meta_boxes() {

		add_meta_box( 'crwcpn-product-notice', __( 'Product Notice / Information', 'cr-woocommerce-product-notice' ), array( $this, 'cr_product_notice_mb' ), 'product', 'normal', 'high' );

		add_meta_box( 'crwcpn-product-notice-global', __( 'Global Notice', 'cr-woocommerce-global-product-notice' ), array( $this, 'cr_global_product_notice_mb' ), 'product', 'side' );

	}

	public function cr_global_product_notice_mb( $post ) {

		$crwcpn_global_product_notice_option = false;

		$crwcpn_global_product_notice_option = get_post_meta( $post->ID, '_crwcpn_global_product_notice_option', 1 );

		?>

		<?php wp_nonce_field( 'crwcpn_product_notice_field', 'crwcpn_product_notice_field_nonce' );?>

		<div>
			<p>
				<label for="crwcpn_global_product_notice_option">
					<input id="crwcpn_global_product_notice_option" class="crwcpn-input input-checkbox" name="crwcpn_global_product_notice_option" value="1" <?php checked( $crwcpn_global_product_notice_option, true ); ?> type="checkbox"><?php _e( 'Disable global notice for this product', 'cr-woocommerce-product-notice' ); ?>
				</label>
			</p>					
		</div>
		
		<?php
	}

	public function cr_product_notice_mb( $post ) {

		$crwcpn_product_notice_text = get_post_meta( $post->ID, '_crwcpn_product_notice_top', 1 );

		$crwcpn_product_notice_color = get_post_meta($post->ID, '_crwcpn_product_notice_color', true);

		if ( empty( $crwcpn_product_notice_text ) ) {

			$crwcpn_product_notice_text = '';
		}
		?>

		<?php wp_nonce_field( 'crwcpn_product_notice_field', 'crwcpn_product_notice_field_nonce' );?>
		
		<div class="crwcpn-product-notice">
			<h4><?php _e( 'Notice Text', 'cr-woocommerce-product-notice' );?></h4>
			<p><em><?php _e( 'Enter the information that you wish to show up on the product page after the product title.', 'cr-woocommerce-product-notice' );?></em></p>
			<textarea id="crwcpn_product_notice_top" class="crwcpn-input-textarea" name="crwcpn_product_notice_top" rows="5" cols="50" placeholder="<?php _e( 'Use of HTML is supported in this field', 'cr-woocommerce-product-notice' ); ?>" style="width: 100%;"><?php echo esc_textarea( $crwcpn_product_notice_text ); ?></textarea>
		</div>
		
		<div>
			<h4><?php _e( 'Notice Appearance' );?></h4>
			<em><?php _e( 'Choose color of product notice : ');?></em>
		    <select name="crwcpn_product_notice_color" id="crwcpn_product_notice_color">
		    	<option value="default" <?php selected( $crwcpn_product_notice_color, 'default' ); ?>><?php _e( 'Default', 'cr-woocommerce-product-notice' ); ?></option>
			  	<option value="blue" <?php selected( $crwcpn_product_notice_color, 'blue' ); ?>><?php _e( 'Blue', 'cr-woocommerce-product-notice' ); ?></option>
				<option value="yellow" <?php selected( $crwcpn_product_notice_color, 'yellow' ); ?>><?php _e( 'Yellow', 'cr-woocommerce-product-notice' ); ?></option>
		      	<option value="red" <?php selected( $crwcpn_product_notice_color, 'red' ); ?>><?php _e( 'Red', 'cr-woocommerce-product-notice' ); ?></option>
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

		if ( isset( $_POST['crwcpn_product_notice_field_nonce'] ) && wp_verify_nonce( $_POST['crwcpn_product_notice_field_nonce'], 'crwcpn_product_notice_field' ) ) {
	
			$crwcpn_global_product_notice_display = ! empty( $_POST['crwcpn_global_product_notice_option'] ) ? wp_kses_post( $_POST['crwcpn_global_product_notice_option'] ) : '';

			update_post_meta( $post_id, '_crwcpn_global_product_notice_option', $crwcpn_global_product_notice_display );
		}
		
		if ( isset( $_POST['crwcpn_product_notice_field_nonce'] ) && wp_verify_nonce( $_POST['crwcpn_product_notice_field_nonce'], 'crwcpn_product_notice_field' ) ) {
	
			$crwcpn_product_notice_top = ! empty( $_POST['crwcpn_product_notice_top'] ) ? wp_kses_post( $_POST['crwcpn_product_notice_top'] ) : '';

			update_post_meta( $post_id, '_crwcpn_product_notice_top', $crwcpn_product_notice_top );
		}

		if ( isset( $_POST['crwcpn_product_notice_field_nonce'] ) && wp_verify_nonce( $_POST['crwcpn_product_notice_field_nonce'], 'crwcpn_product_notice_field' ) ) {
	
			$crwcpn_product_notice_color = ! empty( $_POST['crwcpn_product_notice_color'] ) ? wp_kses_post( $_POST['crwcpn_product_notice_color'] ) : '';

			update_post_meta( $post_id, '_crwcpn_product_notice_color', $crwcpn_product_notice_color );
		}
	}

	//* Add product notice top to show up before the product description

	function crwcpn_product_notice_top() {

		$crwcpn_product_notice_text_display = get_post_meta( get_the_ID(), '_crwcpn_product_notice_top', 1 );

		$crwcpn_product_notice_background_color = get_post_meta( get_the_ID(), '_crwcpn_product_notice_color', true);

		if ( ! empty( $crwcpn_product_notice_text_display ) ) {
		?>
			<div id="crwcpn-individual-product-notice">
				<p class="<?php echo $crwcpn_product_notice_background_color ?>"><?php echo wp_kses_post( $crwcpn_product_notice_text_display );?></p>
			</div>
		<?php	
		}
	}

	public function load_assets( ) {

		wp_enqueue_style( 'cr-product-notice-styles', crwcpn()->plugin_url() . '/assets/css/admin/global.css' );

	}
}