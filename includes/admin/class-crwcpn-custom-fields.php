<?php
/**
 * Registers custom fields for the plugin
 *
 * @package \Product Notices for WooCOmmerce
 * @author Aniket Desale
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Add settings for per product notice using custom fields
 *
 * @since 1.0.0
 */
class CRWCPN_Custom_Fields {

	/**
	 * Init and render Product Notice metabox on per product basis.
	 */
	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'meta_boxes' ) );

		add_action( 'save_post', array( $this, 'save' ) );

		// Static assets for admin area.
		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );

	}

	/**
	 * Loads scripts and styles for Edit Product screen.
	 *
	 * @since 1.1.0
	 */
	public function load_assets() {

		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( ( isset( $screen->base ) && 'post' === $screen->base ) && 'product' === $screen_id ) {

			$script_file_path = crwcpn()->plugin_url() . '/assets/js/admin/';
			$script_file_name = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'admin-settings.js' : 'admin-settings.min.js';

			wp_enqueue_style( 'wp-color-picker' );

			wp_enqueue_script( 'crwcpn-admin', $script_file_path . $script_file_name, array( 'wp-color-picker' ), CRWCPN_VER, true );
		}

	}

	/**
	 * Add metabox to Product page
	 *
	 * @since 1.0.0
	 */
	public function meta_boxes() {

		add_meta_box( 'crwcpn-product-notice', __( 'Product Notice/Information', 'product-notices-woocommerce' ), array( $this, 'cr_product_notice_mb' ), 'product', 'normal', 'high' );

		add_meta_box( 'crwcpn-product-notice-global', __( 'Global Product Notice', 'product-notices-woocommerce' ), array( $this, 'cr_global_product_notice_mb' ), 'product', 'side' );

	}

	/**
	 * Show checkbox on product editor to disable global notice
	 *
	 * @param object $post Product details.
	 *
	 * @since 1.0.0
	 */
	public function cr_global_product_notice_mb( $post ) {

		$crwcpn_hide_global_notice = get_post_meta( $post->ID, 'crwcpn_hide_global_notice', true );

		$crwcpn_hide_global_notice = empty( $crwcpn_hide_global_notice ) ? false : $crwcpn_hide_global_notice;

		wp_nonce_field( 'crwcpn_product_notice_field', 'crwcpn_product_notice_field_nonce' );

		?>
		<div>
			<p>
				<label for="crwcpn_hide_global_notice">
					<input id="crwcpn_hide_global_notice" class="crwcpn-input input-checkbox" name="crwcpn_hide_global_notice" value="1" <?php checked( $crwcpn_hide_global_notice, true ); ?> type="checkbox"><?php esc_html_e( 'Disable global notice for this product', 'product-notices-woocommerce' ); ?>
				</label>
			</p>					
		</div>
		<?php
	}

	/**
	 * Product notice textarea and color metabox
	 *
	 * @param object $post Product details.
	 *
	 * @since1.0.0
	 */
	public function cr_product_notice_mb( $post ) {

		$custom_style_defaults = crwcpn_custom_style_defaults();

		$crwcpn_product_notice_text = get_post_meta( $post->ID, 'crwcpn_product_notice', 1 );

		$crwcpn_product_notice_color = get_post_meta( $post->ID, 'crwpcn_single_product_notice_background_color', true );

		$background_color = get_post_meta( $post->ID, 'crwpcn_single_product_notice_custom_background_color', true );
		$text_color       = get_post_meta( $post->ID, 'crwpcn_single_product_notice_custom_text_color', true );
		$border_color     = get_post_meta( $post->ID, 'crwpcn_single_product_notice_custom_border_color', true );

		$background_color = empty( $background_color ) ? $custom_style_defaults['background_color'] : $background_color;
		$text_color       = empty( $text_color ) ? $custom_style_defaults['text_color'] : $text_color;
		$border_color     = empty( $border_color ) ? $custom_style_defaults['border_color'] : $border_color;

		if ( empty( $crwcpn_product_notice_text ) ) {

			$crwcpn_product_notice_text = '';
		}

		wp_nonce_field( 'crwcpn_product_notice_field', 'crwcpn_product_notice_field_nonce' );

		$color_options = crwcpn_get_notice_colors();

		?>
		<div class="crwcpn-product-notice">
			<h4><?php esc_html_e( 'Notice Text', 'product-notices-woocommerce' ); ?></h4>
			<p>
				<em><?php esc_html_e( 'Enter the information that you wish to show up on the product page after the product title.', 'product-notices-woocommerce' ); ?></em><br>
				<textarea id="crwcpn_product_notice_top" class="crwcpn-input-textarea" name="crwcpn_product_notice_top" rows="5" cols="50" placeholder="<?php esc_html__( 'Use of HTML is supported in this field', 'product-notices-woocommerce' ); ?>" style="width: 100%; margin-top: 8px;"><?php echo esc_textarea( $crwcpn_product_notice_text ); ?></textarea>
			</p>
		</div>
		<div class="crwcpn-product-notice-appearance">
			<h4><?php esc_html_e( 'Notice Appearance', 'product-notices-woocommerce' ); ?></h4>
			<p>
				<label for="crwcpn_product_notice_color"><em><?php esc_html_e( 'Choose from preset styles or configure custom color styles for your notice below.', 'product-notices-woocommerce' ); ?></em></label><br>
				<select id="crwcpn_product_notice_color" name="crwcpn_product_notice_color" style="margin-top: 8px;">
					<?php foreach ( $color_options as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $crwcpn_product_notice_color, $value ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<?php
			/**
			 * Color picker options.
			 *
			 * @since 1.1.0
			 */
			?>
			<style type="text/css">
				#crwcpn-product-notice-custom-style {
					margin-top: 20px;
				}

				#crwcpn-product-notice-custom-style label {
					display: inline-block;
					min-width: 110px;
				}
			</style>
			<div id="crwcpn-product-notice-custom-style">
				<p>
					<label for="crwcpn_product_notice_background_color"><?php esc_html_e( 'Background Color', 'product-notices-woocommerce' ); ?></label>	
					<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'This will be used as the background color of the notice box.', 'product-notices-woocommerce' ); ?>"></span>
					<input id="crwcpn_product_notice_background_color" class="crwcpn-input-color" name="crwcpn_product_notice_background_color" value="<?php echo esc_attr( $background_color ); ?>" type="text" data-default-color="<?php echo esc_attr( $custom_style_defaults['background_color'] ); ?>">
				</p>
				<p>
					<label for="crwcpn_product_notice_text_color"><?php esc_html_e( 'Text Color', 'product-notices-woocommerce' ); ?></label>
					<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'This will be used as the text color for the text in the notice box.', 'product-notices-woocommerce' ); ?>"></span>	
					<input type="text" id="crwcpn_product_notice_text_color" class="crwcpn-input-color" name="crwcpn_product_notice_text_color" value="<?php echo esc_attr( $text_color ); ?>" data-default-color="<?php echo esc_attr( $custom_style_defaults['text_color'] ); ?>">
				</p>
				<p>
					<label for="crwcpn_product_notice_border_color"><?php esc_html_e( 'Border Color', 'product-notices-woocommerce' ); ?></label>	
					<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'This will be used as the border color of the notice box.', 'product-notices-woocommerce' ); ?>"></span>	
					<input type="text" id="crwcpn_product_notice_border_color" class="crwcpn-input-color" name="crwcpn_product_notice_border_color" value="<?php echo esc_attr( $border_color ); ?>" data-default-color="<?php echo esc_attr( $custom_style_defaults['border_color'] ); ?>">
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Save post meta data
	 *
	 * @param int $post_id Product ID.
	 *
	 * @since 1.0.0
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

		if ( isset( $_POST['crwcpn_product_notice_field_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['crwcpn_product_notice_field_nonce'] ), 'crwcpn_product_notice_field' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$crwcpn_hide_global_notice = ! empty( wp_unslash( $_POST['crwcpn_hide_global_notice'] ) ) ? true : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			update_post_meta( $post_id, 'crwcpn_hide_global_notice', $crwcpn_hide_global_notice );
		}

		if ( isset( $_POST['crwcpn_product_notice_field_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['crwcpn_product_notice_field_nonce'] ), 'crwcpn_product_notice_field' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$crwcpn_product_notice = ! empty( $_POST['crwcpn_product_notice_top'] ) ? wp_filter_post_kses( wp_unslash( $_POST['crwcpn_product_notice_top'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			update_post_meta( $post_id, 'crwcpn_product_notice', $crwcpn_product_notice );
		}

		if ( isset( $_POST['crwcpn_product_notice_field_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['crwcpn_product_notice_field_nonce'] ), 'crwcpn_product_notice_field' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$crwcpn_product_notice_color = ! empty( $_POST['crwcpn_product_notice_color'] ) ? sanitize_html_class( wp_unslash( $_POST['crwcpn_product_notice_color'] ) ) : '';

			// If custom color is selected for single product notice.
			if ( 'custom_color' === $crwcpn_product_notice_color ) {

				$crwcpn_product_notice_background_color = ! empty( $_POST['crwcpn_product_notice_background_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['crwcpn_product_notice_background_color'] ) ) : '';
				$crwcpn_product_notice_text_color       = ! empty( $_POST['crwcpn_product_notice_text_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['crwcpn_product_notice_text_color'] ) ) : '';
				$crwcpn_product_notice_border_color     = ! empty( $_POST['crwcpn_product_notice_border_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['crwcpn_product_notice_border_color'] ) ) : '';

				/* Update values in meta */
				update_post_meta( $post_id, 'crwpcn_single_product_notice_custom_background_color', $crwcpn_product_notice_background_color );
				update_post_meta( $post_id, 'crwpcn_single_product_notice_custom_text_color', $crwcpn_product_notice_text_color );
				update_post_meta( $post_id, 'crwpcn_single_product_notice_custom_border_color', $crwcpn_product_notice_border_color );

				/* Indicate custom style */
				update_post_meta( $post_id, 'crwpcn_single_product_notice_background_color', $crwcpn_product_notice_color );

			} else {

				update_post_meta( $post_id, 'crwpcn_single_product_notice_background_color', $crwcpn_product_notice_color );
			}
		}
	}
}
