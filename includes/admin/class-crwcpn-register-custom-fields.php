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

		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ), 20 );

	}

	/**
	 * Add metabox to Product page
	 *
	 * @since 1.0
	 */
	public function meta_boxes() {

		add_meta_box( 'crwcpn-product-notice', __( 'Product Notice/Information', 'cr-woocommerce-product-notice' ), array( $this, 'cr_product_notice_mb' ), 'product', 'normal', 'high' );

		add_meta_box( 'crwcpn-product-notice-global', __( 'Global Notice', 'cr-woocommerce-global-product-notice' ), array( $this, 'cr_global_product_notice_mb' ), 'product', 'side' );

	}

	/**
	 * Show checkbox on product editor to disable global notice
	 *
	 * @since 1.0
	 */
	public function cr_global_product_notice_mb( $post ) {

		$crwcpn_hide_global_notice = get_post_meta( $post->ID, 'crwcpn_hide_global_notice', true );

		$crwcpn_hide_global_notice = empty( $crwcpn_hide_global_notice ) ? false : $crwcpn_hide_global_notice;

		wp_nonce_field( 'crwcpn_product_notice_field', 'crwcpn_product_notice_field_nonce' );
		
		?>
		<div>
			<p>
				<label for="crwcpn_hide_global_notice">
					<input id="crwcpn_hide_global_notice" class="crwcpn-input input-checkbox" name="crwcpn_hide_global_notice" value="1" <?php checked( $crwcpn_hide_global_notice, true ); ?> type="checkbox"><?php _e( 'Disable global notice for this product', 'product-notices-woocommerce' ); ?>
				</label>
			</p>					
		</div>
		<?php
	}

	/**
	 * Product notice textarea and color metabox
	 *
	 * @since 1.0
	 */
	public function cr_product_notice_mb( $post ) {

		$crwcpn_product_notice_text = get_post_meta( $post->ID, 'crwcpn_product_notice', 1 );

		$crwcpn_product_notice_color = get_post_meta($post->ID, 'crwpcn_single_product_notice_background_color', true);

		if ( empty( $crwcpn_product_notice_text ) ) {

			$crwcpn_product_notice_text = '';
		}
		
		wp_nonce_field( 'crwcpn_product_notice_field', 'crwcpn_product_notice_field_nonce' );

		$color_options = crwcpn_get_notice_colors();

		?>
		<div class="crwcpn-product-notice">
			<h4><?php _e( 'Notice Text', 'product-notices-woocommerce' ); ?></h4>
			<p><em><?php _e( 'Enter the information that you wish to show up on the product page after the product title.', 'product-notices-woocommerce' ); ?></em></p>
			<textarea id="crwcpn_product_notice_top" class="crwcpn-input-textarea" name="crwcpn_product_notice_top" rows="5" cols="50" placeholder="<?php _e( 'Use of HTML is supported in this field', 'product-notices-woocommerce' ); ?>" style="width: 100%;"><?php echo esc_textarea( $crwcpn_product_notice_text ); ?></textarea>
		</div>
		
		<div>
			<h4><?php _e( 'Notice Appearance', 'product-notices-woocommerce' ); ?></h4>
			<label for="crwcpn_product_notice_color"><em><?php _e( 'Choose color of product notice:', 'product-notices-woocommerce'); ?></em></label><br>
			<select name="crwcpn_product_notice_color" id="crwcpn_product_notice_color">
				<?php foreach( $color_options as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $crwcpn_product_notice_color, $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}
	
	/**
	 * Save post meta information
	 *
	 * @since 1.0
	 */
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
	
			$crwcpn_hide_global_notice = ! empty( $_POST['crwcpn_hide_global_notice'] ) ? true : false;

			update_post_meta( $post_id, 'crwcpn_hide_global_notice', $crwcpn_hide_global_notice );
		}
		
		if ( isset( $_POST['crwcpn_product_notice_field_nonce'] ) && wp_verify_nonce( $_POST['crwcpn_product_notice_field_nonce'], 'crwcpn_product_notice_field' ) ) {
	
			$crwcpn_product_notice = ! empty( $_POST['crwcpn_product_notice_top'] ) ? wp_filter_post_kses( do_shortcode( $_POST['crwcpn_product_notice_top'] ) ) : '';

			update_post_meta( $post_id, 'crwcpn_product_notice', $crwcpn_product_notice );
		}

		if ( isset( $_POST['crwcpn_product_notice_field_nonce'] ) && wp_verify_nonce( $_POST['crwcpn_product_notice_field_nonce'], 'crwcpn_product_notice_field' ) ) {
	
			$crwcpn_product_notice_color = ! empty( $_POST['crwcpn_product_notice_color'] ) ? sanitize_html_class( $_POST['crwcpn_product_notice_color'] ) : '';

			update_post_meta( $post_id, 'crwpcn_single_product_notice_background_color', $crwcpn_product_notice_color );
		}
	}

	/**
	 * Load assest used for the plugin
	 *
	 * @since 1.0
	 */
	public function load_assets( ) {

		wp_enqueue_style( 'cr-product-notice-styles', crwcpn()->plugin_url() . '/assets/css/admin/global.css' );

	}
}