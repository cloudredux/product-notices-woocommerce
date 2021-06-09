<?php
/**
 * Add tab to WooCommerce settings
 *
 * @package \WooCommerce Product Notice
 * @author Aniket Desale
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CRWCPN_Admin {

	/** @var string sub-menu page hook suffix */
	private $settings_tab_id = CRWCPN_AS_SLUG;

	public function __construct() {

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 100 );

		// show settings
		add_action( 'woocommerce_settings_tabs_' . $this->settings_tab_id, array( $this, 'render_settings' ) );

		// save settings
		add_action( 'woocommerce_update_options_' . $this->settings_tab_id, array( $this, 'save_settings' ) );

	}

	/**
	 * Add tab to WooCommerce Settings tabs
	 *
	 * @since 1.0
	 */
	public function add_settings_tab( $settings_tabs ) {

		$settings_tabs[ $this->settings_tab_id ] = __( 'Product Notice', 'product-notices-woocommerce' );

		return $settings_tabs;
	}

	/**
	 * Render the 'Product Notice' settings page
	 *
	 * @since 1.0
	 */
	public function render_settings() {

		woocommerce_admin_fields( $this->get_settings() );
	}

	/**
	 * Save the 'Product Notice' settings page
	 *
	 * @since 1.0
	 */
	public function save_settings() {

		woocommerce_update_options( $this->get_settings() );
	}


	/**
	 * Returns settings array for use by render/save/install default settings methods
	 *
	 * @since 1.0
	 * @return array settings
	 */
	public static function get_settings() {

		return array(

			array(
				'name' => __( 'Product Notice &ndash; Global Settings', 'product-notices-woocommerce' ),
				'type' => 'title'
			),

			array(
				'id'       => 'crwpcn_global_product_notice',
				'name'     => __( 'Global Product Notice', 'product-notices-woocommerce' ),
				'desc_tip' => __( 'This will be displayed on all product pages', 'product-notices-woocommerce' ),
				'type'     => 'textarea',
				'class'    => 'regular-text',
			),
	
			array(
				'id'      => 'crwpcn_product_notice_background_color',
				'name'    => __( 'Notice Appearance', 'product-notices-woocommerce' ),
				'desc'    => __( 'This is used to add color to product notice background', 'product-notices-woocommerce' ),
				'type'    => 'select',
				'class'   => 'regular-text',
				'options' => crwcpn_get_notice_colors(),
				'desc_tip' =>  true,
			),

			array( 'type' => 'sectionend' ),
		);
	}

}