jQuery( document ).ready( function( $ ) {
    /* Billing Address */
    var $billing_postcode_item = $( '#billing_postcode' );
    
    $billing_postcode_item.on( 'focusout', function() {
        var selected_country = $( '#billing_country' ).select2( 'val' );
        if( selected_country === 'BR' ) {
            var request = postmon_request( $billing_postcode_item.val() );
            if( request !== false ) {
                $( '#billing_state' ).select2( 'val', request.estado );
                $( '#billing_city' ).val( request.cidade );
                $( '#billing_address_1' ).val( request.logradouro );
            }
        }
    });
    
    /* Shipping Address */
    var $shipping_postcode_item = $( '#shipping_postcode' );
    
    $shipping_postcode_item.on( 'focusout', function() {
        var selected_country = $( '#shipping_country' ).select2( 'val' );
        if( selected_country === 'BR' ) {
            var request = postmon_request( $shipping_postcode_item.val() );
            if( request !== false ) {
                $( '#shipping_state' ).select2( 'val', request.estado );
                $( '#shipping_city' ).val( request.cidade );
                $( '#shipping_address_1' ).val( request.logradouro );
            }
        }
    });
});