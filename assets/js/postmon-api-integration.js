function postmon_request( cep ) {
    var result = null;
    jQuery.ajax({
        url: '//api.postmon.com.br/v1/cep/' + cep,
        type: 'get',
        dataType: 'html',
        async: false,
        success: function( data ) {
            result = jQuery.parseJSON( data );
        } 
    });
    return result;
}