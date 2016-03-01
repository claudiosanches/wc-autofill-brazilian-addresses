jQuery( document ).ready( function( $ ) {
    var $postcode_item = $( '#billing_postcode' );
    
        $postcode_item.on( 'focusout', function() {
            var selected_country = $( '#billing_country' ).select2( 'val' );
            if( selected_country === 'BR' ) {
                var request = postmon_request( $postcode_item.val() );
                if( request !== false ) {
                    $( '#billing_state' ).select2( 'val', request.estado );
                    $( '#billing_city' ).val( request.cidade );
                    $( '#billing_address_1' ).val( request.logradouro );
                }
            }
        });
});