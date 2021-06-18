<?php
/**
 * Sanitization class to handle validation of user input
 *
 * Reference: Derived from Genesis framework's settings sanitization class
 *
 * @author Aniket Desale
 * @package \Product Notices for WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/** Settings sanitization class. Ensures saved values are of the expected type. **/
class CRWCPN_Settings_Sanitization {
	/**
	 * Class level variable
	 *
	 * @var Object
	 */
	public static $instance;

	/**
	 * Settings array
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Class Intialization.
	 */
	public function __construct() {

		self::$instance =& $this;

		do_action_ref_array( 'crwcpn_sanitize_settings', array( &$this ) );

	}

	/**
	 * Sanitization filter.
	 *
	 * @param string $filter Name of filter.
	 * @param array  $settings Setting variable name.
	 * @param array  $option Option setting.
	 *
	 * @return bool
	 */
	public function add_filter( $filter, $settings, $option = null ) {

		if ( is_array( $option ) ) {
			foreach ( $option as $name ) {
				$this->options[ $settings ][ $name ] = $filter;
			}
		} elseif ( is_null( $option ) ) {
			$this->options[ $settings ] = $filter;
		} else {
			$this->options[ $settings ][ $option ] = $filter;
		}

		add_filter( 'sanitize_option_' . $settings, array( $this, 'sanitize' ), 10, 2 );

		return true;

	}

	/**
	 * Do filter.
	 *
	 * @param string       $filter Name of filter.
	 * @param string|array $new_value Modified value.
	 * @param string|array $old_value Old value.
	 *
	 * @return string|array
	 */
	public function do_filter( $filter, $new_value, $old_value ) {

		$available_filters = $this->get_available_sanitization_filters();

		if ( ! in_array( $filter, array_keys( $available_filters ), true ) ) {
			return $new_value;
		}

		return call_user_func( $available_filters[ $filter ], $new_value, $old_value );

	}

	/**
	 * Sanitization filters array
	 *
	 * @return array
	 */
	public function get_available_sanitization_filters() {

		$default_filters = array(
			'one_zero'                 => array( $this, 'one_zero' ),
			'no_html'                  => array( $this, 'no_html' ),
			'absint'                   => array( $this, 'absint' ),
			'safe_html'                => array( $this, 'safe_html' ),
			'requires_unfiltered_html' => array( $this, 'requires_unfiltered_html' ),
			'unfiltered_or_safe_html'  => array( $this, 'unfiltered_or_safe_html' ),
			'url'                      => array( $this, 'url' ),
			'email_address'            => array( $this, 'email_address' ),
			'sanitize_string'          => array( $this, 'sanitize_string' ),
		);

		/**
		 * Filter the available sanitization filter types.
		 *
		 * @since 1.7.0
		 *
		 * @param array $default_filters Array with keys of sanitization types, and values of the filter function name as a callback
		 */
		return $default_filters;

	}

	/**
	 * Do filter.
	 *
	 * @param string       $new_value Santized value.
	 * @param string|array $input Provided input.
	 *
	 * @return string|array
	 */
	public function sanitize( $new_value, $input ) {

		if ( ! isset( $this->options[ $input ] ) ) {

			return $new_value;

		} elseif ( is_string( $this->options[ $input ] ) ) {

			return $this->do_filter( $this->options[ $input ], $new_value, get_option( $input ) );

		} elseif ( is_array( $this->options[ $input ] ) ) {

			$old_value = get_option( $input );

			foreach ( $this->options[ $input ] as $suboption => $filter ) {

				$old_value[ $suboption ] = isset( $old_value[ $suboption ] ) ? $old_value[ $suboption ] : '';
				$new_value[ $suboption ] = isset( $new_value[ $suboption ] ) ? $new_value[ $suboption ] : '';

				$new_value[ $suboption ] = $this->do_filter( $filter, $new_value[ $suboption ], $old_value[ $suboption ] );
			}

			return $new_value;

		} else {

			return $new_value;
		}

	}

	/**
	 * Returns a 1 or 0, for all truthy / falsy values.
	 *
	 * Uses double casting. First, we cast to bool, then to integer.
	 *
	 * @param bool $new_value sanitized boolean value.
	 */
	public function one_zero( $new_value ) {

		return (int) (bool) $new_value;

	}

	/**
	 * Returns a positive integer value.
	 *
	 * @param int $new_value sanitized positive value.
	 */
	public function absint( $new_value ) {

		return absint( $new_value );

	}

	/**
	 * Removes HTML tags from string.
	 *
	 * @param string $new_value Sanitized string without HTML tags.
	 */
	public function no_html( $new_value ) {

		return wp_strip_all_tags( $new_value );

	}

	/**
	 * Makes URLs safe
	 *
	 * @param string $new_value Cleaned url.
	 */
	public function url( $new_value ) {

		return esc_url_raw( $new_value );

	}

	/**
	 * Makes Email Addresses safe, via sanitize_email()
	 *
	 * @param string $new_value Striped email.
	 */
	public function email_address( $new_value ) {

		return sanitize_email( $new_value );

	}

	/**
	 * Removes unsafe HTML tags, via wp_kses_post().
	 *
	 * @param string $new_value Output with valid HTML tags.
	 */
	public function safe_html( $new_value ) {

		return wp_kses_post( $new_value );

	}

	/**
	 * Keeps the option from being updated if the user lacks unfiltered_html capability.
	 *
	 * @param string $new_value Unfiltered string with HTML tags.
	 * @param string $old_value Raw input value.
	 */
	public function requires_unfiltered_html( $new_value, $old_value ) {

		if ( current_user_can( 'unfiltered_html' ) ) {
			return $new_value;
		} else {
			return $old_value;
		}

	}

	/**
	 * Removes unsafe HTML tags when user does not have unfiltered_html capability.
	 *
	 * @param string $new_value Data without unsafe HTML tags.
	 * @param string $old_value Data with unsafe HTML tags.
	 */
	public function unfiltered_or_safe_html( $new_value, $old_value ) {

		if ( current_user_can( 'unfiltered_html' ) ) {
			return $new_value;
		}

		return wp_kses_post( $new_value );

	}

	/**
	 * Sanitizes the input strings based on WP's sanitize_title filter; strips all HTML, PHP tags, accents removed
	 *
	 * @param string $new_value Sanitized input string.
	 */
	public function sanitize_string( $new_value ) {

		return sanitize_title( $new_value );

	}

}
/**
 * Settings filter
 *
 * @param string $filter Name of filter.
 * @param array  $settings Setting variable name.
 * @param array  $option Option setting.
 *
 * @return array|bool|string|int
 */
function crwcpn_settings_filter( $filter, $settings, $option = null ) {

	return CRWCPN_Settings_Sanitization::$instance->add_filter( $filter, $settings, $option );

}

/**
 * Instantiate the Sanitizer.
 */
add_action( 'admin_init', 'crwcpn_sanitize_settings' );
/**
 * Sanitization function.
 */
function crwcpn_sanitize_settings() {

	new CRWCPN_Settings_Sanitization();

}
