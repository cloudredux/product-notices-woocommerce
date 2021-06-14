<?php
/**
 *
 *	Plugin Name: Product Notices for WooCommerce
 *	Plugin URI: https://cloudredux.com
 *	Description: This plugin is used to show Product Notices on WooCommerce products.
 *	Version: 1.0.0
 * 	Requires at least: 5.2
 * 	Requires PHP:      7.2
 *	Author: CloudRedux
 *	Author URI: https://cloudredux.com
 *	License: GPL-2.0+
 *	License URI: http://www.opensource.org/licenses/gpl-license.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'CRWCPN_Main' ) ) :

class CRWCPN_Main {

	private $post_fields;

	private $admin_page;

	/**
	 * @var Single instance of the class		 
	 */
	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct(){

		$this->define_constants();

		$this->includes();	
		
		$this->init_hooks();

		add_action( 'admin_notices', array( $this, 'crwcpn_wc_activation_notice' ) );

		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'crwcpn_settings_link' ) );

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

		return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
		
	}

	/**
	 * Generates plugin action link for settings page.
	 * 
	 * @since 1.0.0
	 * 
	 * @param array $links Array of default plugin action link
	 * 
	 * @return array $links New array of plugin action link
	 */
	public function crwcpn_settings_link( array $links ) {

		$url = 'admin.php?page=wc-settings&tab='.CRWCPN_AS_SLUG;
		$settings_link = '<a href="' . $url . '">' . __('Settings', 'product-notices-woocommerce') . '</a>';
		$links[] = $settings_link;

		return $links;

    }

	/**
	 * Defines constants.
	 * 
	 * @since 1.0.0
	 */
	public function define_constants() {

		define( 'CRWCPN_SLUG', 'crwcpn-settings' );

		define( 'CRWCPN_VER', '1.0.0' );

		define( 'CRWCPN_AS_SLUG', 'product-notices-woocommerce' );
	}

	private function includes() {

		include( $this->plugin_path() . '/includes/admin/class-crwcpn-register-custom-fields.php' );

		include( $this->plugin_path() . '/includes/admin/class-crwcpn-admin.php' );

		include( $this->plugin_path() . '/includes/class-sanitization.php' );

		include( $this->plugin_path() . '/includes/functions/core.php' );

	}

	private function init_hooks() {

		$this->post_fields = new CRWCPN_Custom_Fields();

		if ( is_admin()	) {

			$this->admin_page = new CRWCPN_Admin();
		}

	}
	
	/**
	 * Checks if WooCommerce is active if not then shows WooCommerce is not installed.
	 * 
	 * @since 1.0.0
	 */
	public function crwcpn_wc_activation_notice() {

		if ( ! $this->is_woocommerce_active() ) {
		?>
			<div class="error"><p><strong><?php printf( __( 'Product Notice requires WooCommerce to be installed and active. You can download <a href="%s" target="_blank">WooCommerce</a> here.', 'product-notices-woocommerce' ), esc_url( 'https://woocommerce.com/' ) ); ?></strong></p></div>
		<?php
		}
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {

		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path(){

		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
}

endif;

add_filter( 'load_textdomain_mofile', 'my_plugin_load_my_own_textdomain', 10, 2 );

function my_plugin_load_my_own_textdomain( $mofile, $domain ) {
    if ( 'my-domain' === $domain && false !== strpos( $mofile, WP_LANG_DIR . '/plugins/' ) ) {
        $locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
        $mofile = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/languages/' . $domain . '-' . $locale . '.mo';
    }
    return $mofile;
}

function crwcpn() {
	return CRWCPN_Main::instance();
}

// fire it up!
crwcpn();