/* global ajaxurl, wcBrazilianPostcodesAdminParams */
jQuery( function( $ ) {

	/**
	 * Admin class.
	 *
	 * @type {Object}
	 */
	var wc_brazilian_postcodes_admin = {

		/**
		 * Initialize actions.
		 */
		init: function() {
			$( document.body ).on( 'click', '#woocommerce_brazilian-postcodes_empty_database', this.empty_database );
		},

		/**
		 * Empty database.
		 *
		 * @return {String}
		 */
		empty_database: function() {
			if ( ! window.confirm( wcBrazilianPostcodesAdminParams.i18n_confirm_message ) ) {
				return;
			}

			$( '#mainform' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'brazilian_postcodes_empty_database',
					nonce: wcBrazilianPostcodesAdminParams.empty_database_nonce
				},
				success: function( response ) {
					/*jshint devel: true */
					console.log(response);
					window.alert( response.data.message );
					$( '#mainform' ).unblock();
				}
			});
		}
	};

	wc_brazilian_postcodes_admin.init();
});
