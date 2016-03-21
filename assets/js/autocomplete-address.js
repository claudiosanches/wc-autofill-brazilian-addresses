/* global wcabaParams */
/*!
 * WooCommerce Autofill Brazilian Addresses 2016.
 *
 * Autocomplete address with postcodes.
 *
 * Version: 1.0.0
 */

jQuery( function( $ ) {

	/**
	 * Autocomplete address class.
	 *
	 * @type {Object}
	 */
	var wc_brazilian_postcodes = {

		/**
		 * Initialize actions.
		 */
		init: function() {
			// Initial load.
			this.autocomplate( 'billing', true );

			$( '#billing_postcode' ).on( 'blur', function() {
				wc_brazilian_postcodes.autocomplate( 'billing' );
			});
			$( '#shipping_postcode' ).on( 'blur', function() {
				wc_brazilian_postcodes.autocomplate( 'shipping' );
			});
		},

		/**
		 * Block checkout.
		 */
		block: function() {
			$( 'form.checkout, form#order_review' )
				.addClass( 'processing' )
				.block({
					message: null,
					overlayCSS: {
					background: '#fff',
					opacity: 0.6
					}
				});
		},

		/**
		 * Unblock checkout.
		 */
		unblock: function() {
			$( 'form.checkout, form#order_review' )
				.removeClass( 'processing' )
				.unblock();
		},

		/**
		 * Autocomplate address.
		 *
		 * @param {String} field Target.
		 * @param {Boolean} copy
		 */
		autocomplate: function( field, copy ) {
			copy = copy || false;

			// Checks with *_postcode field exist.
			if ( $( '#' + field + '_postcode' ).length ) {

				// Valid CEP.
				var cep       = $( '#' + field + '_postcode' ).val().replace( '.', '' ).replace( '-', '' ),
					country   = $( '#' + field + '_country' ).val(),
					address_1 = $( '#' + field + '_address_1' ).val();

				// Check country is BR.
				if ( cep !== '' && 8 === cep.length && 'BR' === country && 0 === address_1.length ) {

					wc_brazilian_postcodes.block();

					// Gets the address.
					$.ajax({
						type: 'GET',
						url: wcabaParams.url + '&postcode=' + cep,
						dataType: 'json',
						contentType: 'application/json',
						success: function( address ) {
							if ( address.success ) {
								wc_brazilian_postcodes.fillFields( field, address.data );

								if ( copy ) {
									var newField = 'billing' === field ? 'shipping' : 'billing';

									wc_brazilian_postcodes.fillFields( newField, address.data );
								}
							}

							wc_brazilian_postcodes.unblock();
						}
					});
				}
			}
		},

		/**
		 * Fill fields.
		 *
		 * @param {String} field
		 * @param {Object} data
		 */
		fillFields: function( field, data ) {
			// Address.
			$( '#' + field + '_address_1' ).val( data.address ).change();

			// Neighborhood.
			if ( $( '#' + field + '_neighborhood' ).length ) {
				$( '#' + field + '_neighborhood' ).val( data.neighborhood ).change();
			} else {
				$( '#' + field + '_address_2' ).val( data.neighborhood ).change();
			}

			// City.
			$( '#' + field + '_city' ).val( data.city ).change();

			// State.
			$( '#' + field + '_state option:selected' ).attr( 'selected', false ).change();
			$( '#' + field + '_state option[value="' + data.state + '"]' ).attr( 'selected', 'selected' ).change();
			$( '#' + field + '_state' ).trigger( 'liszt:updated' ).trigger( 'chosen:updated' ); // Chosen support.
		}
	};

	wc_brazilian_postcodes.init();
});
