/**
 * Scripts loading on the frontend.
 *
 * @package \Product Notices for WooCommerce
 * @author Rohit Dokhe
 * @since 1.1.0
 */

var adminScript = ( function( $ ) {

	'use strict';

	var color_Picker = function() {

		$( document ).ready(
			function() {

				if ( $( 'body' ).find( '.crwcpn-input-color' ).length ) {

					$( '.crwcpn-input-color' ).wpColorPicker();
				}

				if ( $( '#crwpcn_product_notice_background_color' ).val() === 'custom_color' || $( '#crwcpn_product_notice_color' ).val() === 'custom_color' ) {

					crwpcn_colorElementShow();
				} else {

					crwpcn_colorElementHide();
				}
			}
		);

		$( '#crwpcn_product_notice_background_color,#crwcpn_product_notice_color' ).change(
			function () {

				if ( $( this ).val() === 'custom_color' ) {

					crwpcn_colorElementShow();
				} else {

					crwpcn_colorElementHide();
				}
			}
		);

		/**
		 * Show HTML element on admin settings and product page.
		 *
		 * @since 1.1.0
		 */
		function crwpcn_colorElementShow() {

			// Custom color picker field Ids of the admin settings.
			$( '#crwpcn_product_notice_custom_background_color' ).parents( 'tr' ).show( 'slow' );
			$( '#crwpcn_product_notice_custom_text_color' ).parents( 'tr' ).show( 'slow' );
			$( '#crwpcn_product_notice_custom_border_color' ).parents( 'tr' ).show( 'slow' );

			// Custom color picker field Id of the edit product page.
			$( '#crwcpn-product-notice-custom-style' ).show( 'slow' );
		}

		/**
		 * Hide HTML element on admin settings and product page.
		 *
		 * @since 1.1.0
		 */
		function crwpcn_colorElementHide()	{

			// Custom color picker field Ids of the admin settings.
			$( '#crwpcn_product_notice_custom_background_color' ).parents( 'tr' ).hide( 'slow' );
			$( '#crwpcn_product_notice_custom_text_color' ).parents( 'tr' ).hide( 'slow' );
			$( '#crwpcn_product_notice_custom_border_color' ).parents( 'tr' ).hide( 'slow' );

			// Custom color picker field Id of the edit product page.
			$( '#crwcpn-product-notice-custom-style' ).hide( 'slow' );
		}
	},
	/**
	 * Bind behavior to events.
	 */
	ready = function() {

		// Run on document ready.
		color_Picker();

	};

	// Only expose the ready function to the world.
	return {
		ready: ready
	};

})( jQuery );
jQuery( adminScript.ready );
