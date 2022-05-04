/**
 * Scripts loading on the frontend.
 *
 * @package \Product Notices for WooCommerce
 * @author Rohit Dokhe
 * @since 1.1.0
 */

var adminScript = ( function( $ ) {

	'use strict';

	var colorPicker = function() {

		/* Handles color picker on Product Edit screen. */
		if ( $( 'body' ).find( '.crwcpn-input-color' ).length ) {

			$( '.crwcpn-input-color' ).wpColorPicker();
		}

		if ( $( '#crwpcn_product_notice_background_color' ).val() === 'custom_color' || $( '#crwcpn_product_notice_color' ).val() === 'custom_color' ) {

			crwpcn_colorElementShow();
		} else {

			crwpcn_colorElementHide();
		}

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
	 * Handles
	 */
	displayRulesAdmin = function() {

		/* Placeholder for categories and tags field */
		$( "#crwpcn_product_notice_display_by_categories" ).selectWoo(
			{
				placeholder: typeof undefined !== crwcpn_admin && crwcpn_admin.category_placeholder_text,
				allowClear: true
			}
		);

		$( "#crwpcn_product_notice_display_by_tags" ).selectWoo(
			{
				placeholder: typeof undefined !== crwcpn_admin && crwcpn_admin.tag_placeholder_text,
				allowClear: true
			}
		);

		/* If checkbox is enabled, show fields */
		if ( $( '#crwpcn_global_product_notice_use_display_rules' ).is( ':checked' )) {
			crwpcn_Display_Category_Elementshow();
		} else {
			crwpcn_Display_Category_ElementHide();
		}

		/* Desiable warning notice if fields is not empty. */
		if ( $( '#crwpcn_global_product_notice_use_display_rules' ).is( ':checked' ) ) {
			$( '#crwpcn_product_notice_display_by_categories, #crwpcn_product_notice_display_by_tags' ).change(
				function () {
					var crwpcn_get_categories_val = $( '#crwpcn_product_notice_display_by_categories' ).val(),
					crwpcn_get_tags_val           = $( '#crwpcn_product_notice_display_by_tags' ).val(),
					validation_text               = typeof crwcpn_admin !== undefined && crwcpn_admin.display_rules_validation_text;

					if ( crwpcn_get_categories_val.length == 0 && crwpcn_get_tags_val.length == 0 ) {

						$( "#crwpcn_display_warning_notice-description" ).removeClass( 'hide' );
						$( "#crwpcn_display_warning_notice-description" ).html( '<span id="remove" class="notice notice-warning" style="display: block; margin-top: 0"><p>' + validation_text + '</p></span>' );

					} else if ( (crwpcn_get_categories_val.length == 0 && crwpcn_get_tags_val.length != 0) || (crwpcn_get_categories_val.length != 0 && crwpcn_get_tags_val.length == 0) ) {
						$( "#crwpcn_display_warning_notice-description" ).html( '' );
						$( "#crwpcn_display_warning_notice-description" ).addClass( 'hide' );
					}
				}
			);
		} else if ($( '#crwpcn_global_product_notice_use_display_rules' ).on( 'change' ) ) {
			$( '#crwpcn_product_notice_display_by_categories, #crwpcn_product_notice_display_by_tags' ).change(
				function () {
					var crwpcn_get_categories_val = $( '#crwpcn_product_notice_display_by_categories' ).val(),
					crwpcn_get_tags_val           = $( '#crwpcn_product_notice_display_by_tags' ).val(),
					validation_text               = typeof crwcpn_admin !== undefined && crwcpn_admin.display_rules_validation_text;
					if ( crwpcn_get_categories_val.length == 0 && crwpcn_get_tags_val.length == 0 ) {

						$( "#crwpcn_display_warning_notice-description" ).removeClass( 'hide' );
						$( "#crwpcn_display_warning_notice-description" ).html( '<span id="remove" class="notice notice-warning" style="display: block; margin-top: 0"><p>' + validation_text + '</p></span>' );

					} else if ( ( crwpcn_get_categories_val.length == 0 && crwpcn_get_tags_val.length != 0) || (crwpcn_get_categories_val.length != 0 && crwpcn_get_tags_val.length == 0 ) ) {
						$( "#crwpcn_display_warning_notice-description" ).html( '' );
						$( "#crwpcn_display_warning_notice-description" ).addClass( 'hide' );
					}
				}
			);
		}

		$( '#crwpcn_global_product_notice_use_display_rules' ).change(
			function () {

				if ($( this ).is( ":checked" )) {
					crwpcn_Display_Category_Elementshow();
				} else {
					crwpcn_Display_Category_ElementHide();
					$( "#crwpcn_display_warning_notice-description" ).html( '' );
				}
			}
		);

		function crwpcn_Display_Category_Elementshow()	{

			$( '#crwpcn_product_notice_display_by_categories' ).parents( 'tr' ).show( 'slow' );
			$( '#crwpcn_product_notice_display_by_tags' ).parents( 'tr' ).show( 'slow' );
		}

		function crwpcn_Display_Category_ElementHide()	{

			$( '#crwpcn_product_notice_display_by_categories' ).parents( 'tr' ).hide();
			$( '#crwpcn_product_notice_display_by_tags' ).parents( 'tr' ).hide( 'slow' );
		}

	},

	/**
	 * Bind behavior to events.
	 */
	ready = function() {

		// Run on document ready.
		colorPicker();
		displayRulesAdmin();

	};

	// Only expose the ready function to the world.
	return {
		ready: ready
	};

})( jQuery );
jQuery( adminScript.ready );
