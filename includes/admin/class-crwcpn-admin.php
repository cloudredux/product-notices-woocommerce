<?php
/**
 * Add tab to WooCommerce settings
 *
 * @package \Product Notices for WooCommerce
 * @author Aniket Desale
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Displays Product Notice tab in WooCommerce Settings
 *
 * @since 1.0.0
 */
class CRWCPN_Admin {

	/**
	 *  Settings tab ID
	 *
	 *  @var string sub-menu page hook suffix
	 */
	private $settings_tab_id = CRWCPN_AS_SLUG;

	/**
	 * Init and render Product Notice tab in WooCommerce settings.
	 */
	public function __construct() {

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 100 );

		// show settings.
		add_action( 'woocommerce_settings_tabs_' . $this->settings_tab_id, array( $this, 'render_settings' ) );

		// save settings.
		add_action( 'woocommerce_update_options_' . $this->settings_tab_id, array( $this, 'save_settings' ) );

		// Static assets for admin area.
		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );

	}

	/**
	 * Add tab to WooCommerce Settings tabs
	 *
	 * @param array $settings_tabs All settings.
	 * @return array Returns setting tab array
	 *
	 * @since 1.0.0
	 */
	public function add_settings_tab( $settings_tabs ) {

		$settings_tabs[ $this->settings_tab_id ] = __( 'Product Notice', 'product-notices-woocommerce' );

		return $settings_tabs;
	}

	/**
	 * Render the 'Product Notice' settings page
	 *
	 * @since 1.0.0
	 */
	public function render_settings() {

		?>
		<div class="crwcpn-settings-wrap crwcpn-flex crwcpn-flex-wrap">
			<div class="settings-col crwcpn-settings-left crwcpn-grid-1-2">
				<?php woocommerce_admin_fields( $this->get_settings() ); ?>
			</div>
			<div class="settings-col crwcpn-settings-right crwcpn-grid-1-3">
				<?php self::review_box(); ?>

				<?php self::features_widget(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Save the 'Product Notice' settings page
	 *
	 * @since 1.0.0
	 */
	public function save_settings() {

		woocommerce_update_options( $this->get_settings() );
	}

	/**
	 * Loads JS and CSS for admin page.
	 *
	 * @since 1.0.1
	 */
	public function load_assets() {

		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( 'woocommerce_page_wc-settings' === $screen_id ) {

			$script_file_path = crwcpn()->plugin_url() . '/assets/js/admin/';
			$script_file_name = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'admin-settings.js' : 'admin-settings.min.js';

			wp_enqueue_style( 'crwcpn-admin', crwcpn()->plugin_url() . '/assets/css/admin/admin.css', array(), CRWCPN_VER );
			wp_enqueue_script( 'crwcpn-admin', $script_file_path . $script_file_name, array(), CRWCPN_VER, true );
		}
	}


	/**
	 * Returns settings array for use by render/save/install default settings methods
	 *
	 * @since 1.0.0
	 * @return array settings
	 */
	public static function get_settings() {

		$custom_style_defaults = crwcpn_custom_style_defaults();

		return array(

			array(
				'name' => __( 'Product Notice &ndash; Global Settings', 'product-notices-woocommerce' ),
				'type' => 'title',
			),

			array(
				'id'       => 'crwpcn_global_product_notice',
				'name'     => __( 'Global Product Notice', 'product-notices-woocommerce' ),
				'desc_tip' => __( 'This will be displayed on all product pages.', 'product-notices-woocommerce' ),
				'type'     => 'textarea',
				'class'    => 'regular-text',
			),

			array(
				'id'       => 'crwpcn_product_notice_background_color',
				'name'     => __( 'Notice Appearance', 'product-notices-woocommerce' ),
				'desc'     => __( 'This is used to add color to product notice background.', 'product-notices-woocommerce' ),
				'type'     => 'select',
				'class'    => 'regular-text',
				'options'  => crwcpn_get_notice_colors(),
				'desc_tip' => true,
			),

			/**
			 * Color picker options.
			 *
			 * @since 1.1.0
			 */
			array(
				'id'       => 'crwpcn_product_notice_custom_background_color',
				'name'     => __( 'Background Color', 'product-notices-woocommerce' ),
				'type'     => 'color',
				'default'  => $custom_style_defaults['background_color'],
				'desc'     => __( 'This will be used as the background color of the notice box.', 'product-notices-woocommerce' ),
				'desc_tip' => true,
			),

			array(
				'id'       => 'crwpcn_product_notice_custom_text_color',
				'name'     => __( 'Text Color', 'product-notices-woocommerce' ),
				'type'     => 'color',
				'default'  => $custom_style_defaults['text_color'],
				'desc'     => __( 'This will be used as the text color for the text in the notice box.', 'product-notices-woocommerce' ),
				'desc_tip' => true,
			),

			array(
				'id'       => 'crwpcn_product_notice_custom_border_color',
				'name'     => __( 'Border Color', 'product-notices-woocommerce' ),
				'type'     => 'color',
				'default'  => $custom_style_defaults['border_color'],
				'desc'     => __( 'This will be used as the border color of the notice box.', 'product-notices-woocommerce' ),
				'desc_tip' => true,
			),

			array( 'type' => 'sectionend' ),
		);

	}

	/**
	 * Displays upcoming features of Product Notice for WooCommerce.
	 *
	 * @since 1.1.0
	 */
	public static function features_widget() {

		$args = array(
			'type'         => 'crwpcn-features-widget',
			'link'         => 'https://cloudredux.com/feed/?post_type=cr-plugin-features&cr_plugins=product-notices-for-woocommerce',
			'url'          => 'https://cloudredux.com/feed/?post_type=cr-plugin-features&cr_plugins=product-notices-for-woocommerce',
			'title'        => __( 'Upcoming Features', 'product-notices-woocommerce' ),
			'items'        => apply_filters( 'crwpcn_features_widget_items', 3 ),
			'show_summary' => 0,
			'show_author'  => 0,
			'show_date'    => 0,
		);

		?>
		<div class="crwcpn-upcoming-features">
			<h3 class="crwcpn-heading crwcpn-card"><span class="dashicons dashicons-bell"></span> <?php esc_html_e( 'Upcoming Features', 'product-notices-woocommerce' ); ?></h3>
			<div class="crwcpn-inside crwcpn-card rss-widget">
				<p><em><?php esc_html_e( 'Keep watching this space to know about upcoming planned features in the plugin.', 'product-notices-woocommerce' ); ?></em></p>
				<?php wp_widget_rss_output( $args['url'], $args ); ?>
				<hr />
				<p><?php esc_html_e( 'If you\'d like to suggest a feature, click on the button below to submit your suggestion.', 'product-notices-woocommerce' ); ?></p>
				<a href="<?php echo esc_url( 'https://cloudredux.com/wordpress-plugin-features-suggest-a-feature/?spf-plugin-name=Product%20Notices%20for%20WooCommerce' ); ?>" class="button secondary"><?php esc_html_e( 'Suggest a feature &rarr;', 'product-notices-woocommerce' ); ?></a>
			</div>
		</div>
		<?php

	}

	/**
	 * Product Notice for WooCommerce reviwe/customer feedback.
	 *
	 * @since 1.1.0
	 */
	public static function review_box() {

		?>
		<div class="crwcpn-review">
			<h3 class="crwcpn-heading crwcpn-card"><span class="dashicons dashicons-star-half"></span> <?php esc_html_e( 'We\'d love to hear your feedback', 'product-notices-woocommerce' ); ?></h3>
			<div class="crwcpn-inside crwcpn-card">
				<p><?php esc_html_e( 'If the plugin has helped enriching the customer experience on your online store, help spread the word.', 'product-notices-woocommerce' ); ?></p>
				<p>
					<?php
					/* translators: %1$s is replaced with the URL to the plugin page on CloudRedux.com, %2$s closes the anchor tag, %3$s is replaced with the URL to the plugin's review page on WordPress.org */
					printf( esc_html__( '%3$sLeave us a review and rate our plugin%2$s on WordPress.org so that several other businesses like yours are able to utilize what %1$sProduct Notices for WooCommerce%2$s has to offer.', 'product-notices-woocommerce' ), '<a href="" target="_blank">', '</a>', '<a href="https://wordpress.org/support/plugin/product-notices-for-woocommerce/reviews/" target="_blank">' );
					?>
				</p>
				<p><a class="crwcpn-button crwcpn-button-green" href="<?php echo esc_url( 'https://wordpress.org/support/plugin/product-notices-for-woocommerce/reviews/' ); ?>"><?php esc_html_e( 'Leave a Review', 'product-notices-woocommerce' ); ?></a></p>
			</div>
		</div>
		<?php
	}

}
