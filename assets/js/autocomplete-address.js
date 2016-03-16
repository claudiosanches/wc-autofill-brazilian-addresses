/* global wcBrazilianPostcodesParams */
/*!
 * WooCommerce Brazilian Postcodes 2016.
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
			// Auto complete billing address.
			this.autocomplate( 'billing' );
			this.autocomplateOnChange( 'billing' );

			// Auto complete shipping address.
			this.autocomplate( 'shipping' );
			this.autocomplateOnChange( 'shipping' );
		},

		/**
		 * Autocomplate address.
		 *
		 * @param {String} field Target.
		 */
		autocomplate: function( field ) {
			// Checks with *_postcode field exist.
			if ( $( '#' + field + '_postcode' ).length ) {

				// Valid CEP.
				var cep       = $( '#' + field + '_postcode' ).val().replace( '.', '' ).replace( '-', '' ),
					country   = $( '#' + field + '_country' ).val(),
					address_1 = $( '#' + field + '_address_1' ).val();

				// Check country is BR.
				if ( cep !== '' && 8 === cep.length && 'BR' === country && 0 === address_1.length ) {

					// Gets the address.
					$.ajax({
						type: 'GET',
						url: wcBrazilianPostcodesParams.url + '&postcode=' + cep,
						dataType: 'json',
						contentType: 'application/json',
						success: function( address ) {
							// Validate request.
							if ( ! address.success ) {
								return;
							}

							// Address.
							$( '#' + field + '_address_1' ).val( address.data.address ).change();

							// Neighborhood.
							$( '#' + field + '_neighborhood' ).val( address.data.neighborhood ).change();

							// City.
							$( '#' + field + '_city' ).val( address.data.city ).change();

							// State.
							$( '#' + field + '_state option:selected' ).attr( 'selected', false ).change();
							$( '#' + field + '_state option[value="' + address.data.state + '"]' ).attr( 'selected', 'selected' ).change();
							$( '#' + field + '_state' ).trigger( 'liszt:updated' ).trigger( 'chosen:updated' ); // Chosen support.
						}
					});
				}
			}
		},

		/**
		 * Autocomplate address on field change.
		 *
		 * @param {String} field Target.
		 */
		autocomplateOnChange: function( field ) {
			$( '#' + field + '_postcode' ).on( 'blur', function() {
				wc_brazilian_postcodes.autocomplate( field );
			});
		}
	};

	wc_brazilian_postcodes.init();
});
