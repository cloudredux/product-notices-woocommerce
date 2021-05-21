<?php
/**
 * Add tab to WooCommerce settings
 *
 * @package \WooCommerce Product Notice
 * @author Aniket Desale
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CRWPN_Admin {

	/** @var string sub-menu page hook suffix */
	private $settings_tab_id = 'notice';

	public function __construct() {

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 100 );

		// show settings
		add_action( 'woocommerce_settings_tabs_' . $this->settings_tab_id, array( $this, 'render_settings' ) );

		// save settings
		add_action( 'woocommerce_update_options_' . $this->settings_tab_id, array( $this, 'save_settings' ) );

	}

	/**
	 * Add tab to WooCommerce Settings tabs
	 */
	public function add_settings_tab( $settings_tabs ) {

		$settings_tabs[ $this->settings_tab_id ] = __( 'Product Notice', 'woocommerce-product-notice' );

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
				'name' => __( 'Product Notice - Global Settings', 'woocommerce-product-notice' ),
				'type' => 'title'
			),

			array(
				'id'       => 'wc_product_notice',
				'name'     => __( 'Global Product Notice', 'woocommerce-product-notice' ),
				'desc_tip' => __( 'This will be displayed on all product pages', 'woocommerce-product-notice' ),
				'type'     => 'textarea',
				'class'    => 'regular-text',
			),

			
			array(
				'id'      => 'wc_product_notice_color',
			    'name'    => __( 'Notice Color', 'woocommerce-product-notice-color' ),
			    'desc'    => __( 'This is used to add color to product notice background', 'woocommerce-product-notice-color' ),
			    'type'    => 'select',
			    'class'    => 'regular-text',
			    'options' => array(
			      	'default'        => __( 'Default', 'woocommerce' ),
			      	'blue'       => __( 'Blue', 'woocommerce' ),
			      	'yellow'  => __( 'Yellow', 'woocommerce' ),
			      	'red' => __( 'Red', 'woocommerce' )
			    ),
			    'desc_tip' =>  true,
			),

			array( 'type' => 'sectionend' ),
		);
	}

}