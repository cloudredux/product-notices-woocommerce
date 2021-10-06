<?php
/**
 * @package   Product Notices for WooCommerce
 * @author    CloudRedux
 * @copyright Copyright (C) 2021, CloudRedux - support@cloudredux.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name:       Product Notices for WooCommerce
 * Plugin URI:        https://cloudredux.com/contributions/wordpress/product-notices-for-woocommerce/
 * Description:       Make the best of product announcements, promos, discounts, alerts, etc. on your store with this one of its kind WooCommerce extension.
 * Version:           1.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            CloudRedux
 * Author URI:        https://cloudredux.com
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'CRWCPN_Main' ) ) :
	/**
	 * Main plugin class
	 *
	 * @since 1.0.0
	 */
	class CRWCPN_Main {
		// @codingStandardsIgnoreLine
		/**
		 * @var CRWCPN_Custom_Fields class object
		 */
		private $post_fields;

		// @codingStandardsIgnoreLine
		/**
		 * @var CRWCPN_Admin class object
		 */
		private $admin_page;

		// @codingStandardsIgnoreLine
		/**
		 * @var Single instance of the class
		 */
		protected static $_instance = null;

		/**
		 * Defines single instance of this class
		 *
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->define_constants();

			add_action( 'admin_notices', array( $this, 'crwcpn_wc_activation_notice' ) );

			if ( $this->is_woocommerce_active() ) {

				$this->includes();

				$this->init_hooks();

				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'crwcpn_settings_link' ) );

				add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
			}

		}

		/**
		 * Checks if WooCommerce is installed and active.
		 *
		 * @since 1.0.0
		 */
		public function is_woocommerce_active() {

			$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {
				$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
			}

			return in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );

		}

		/**
		 * Generates plugin action link for settings page.
		 *
		 * @since 1.0.0
		 *
		 * @param array $links Array of default plugin action link.
		 * @return array $links New array of plugin action link.
		 */
		public function crwcpn_settings_link( array $links ) {

			$url           = 'admin.php?page=wc-settings&tab=' . CRWCPN_AS_SLUG;
			$settings_link = '<a href="' . $url . '">' . __( 'Settings', 'product-notices-woocommerce' ) . '</a>';
			$links[]       = $settings_link;

			return $links;

		}

		/**
		 * Defines constants.
		 *
		 * @since 1.0.0
		 */
		public function define_constants() {

			define( 'CRWCPN_SLUG', 'crwcpn-settings' );

			define( 'CRWCPN_VER', '1.1.0' );

			define( 'CRWCPN_AS_SLUG', 'product-notices-woocommerce' );
		}

		/**
		 * All core plugin file includes
		 *
		 * @since 1.0.0
		 */
		private function includes() {

			include $this->plugin_path() . '/includes/admin/class-crwcpn-custom-fields.php';

			include $this->plugin_path() . '/includes/admin/class-crwcpn-admin.php';

			include $this->plugin_path() . '/includes/class-crwcpn-settings-sanitization.php';

			include $this->plugin_path() . '/includes/functions/core.php';

		}

		/**
		 * Core plugin actions.
		 *
		 * @since 1.0.0
		 */
		private function init_hooks() {

			$this->post_fields = new CRWCPN_Custom_Fields();

			if ( is_admin() ) {

				$this->admin_page = new CRWCPN_Admin();
			}

		}

		/**
		 * Show an admin notice if WooCommerce is not active or not installed.
		 *
		 * @since 1.0.0
		 */
		public function crwcpn_wc_activation_notice() {

			if ( ! $this->is_woocommerce_active() ) {
				echo '<div class="error"><p><strong>';
					/* translators: %s is replaced with url to woocommerce.com */
					printf( esc_html__( 'Product Notice requires WooCommerce to be installed and active. You can download %s here.', 'product-notices-woocommerce' ), '<a href="' . esc_url( network_admin_url( 'plugin-install.php?s=woocommerce&amp;tab=search&amp;type=term' ) ) . '">WooCommerce</a>' );
				echo '</strong></p></div>';
			}
		}

		/**
		 * Load frontend-facing static assests
		 *
		 * @since 1.0.0
		 */
		public function load_assets() {

			wp_enqueue_style( 'cr-product-notice-styles', $this->plugin_url() . '/assets/css/frontend/global.css', array(), CRWCPN_VER );

		}

		/**
		 * Get the plugin url.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function plugin_url() {

			return untrailingslashit( plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function plugin_path() {

			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}
	}

endif;

/**
 * Instantiates the class.
 *
 * @since 1.0.0
 */
function crwcpn() {
	return CRWCPN_Main::instance();
}

// fire it up!
crwcpn();
