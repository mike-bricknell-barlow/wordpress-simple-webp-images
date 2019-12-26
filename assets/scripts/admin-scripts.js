( function ( $ ) {
    $( document ).ready( function () {
        setTimeout( function () {
            $.ajax( {
                'url': ajaxurl,
                'data': {
                    'action': 'output_single_convert_link'
                },
                'type': 'POST',
                'success': function( response ) {
                    $( '.settings' ).append( response );
                },
            } );
        }, 500 );

        $(document).on( 'click', '#swi-convert-single-image', function (e) {
            e.preventDefault();
            convertSingleImage();
        } );
    });

    function convertSingleImage () {
        var urlParams = new URLSearchParams(window.location.search);
        var attachmentId = urlParams.get('item');

        $.ajax( {
            'url': ajaxurl,
            'data': {
                'action': 'convert_single_attachment',
                'attachment_id': attachmentId,
            },
            'type': 'POST',
            'success': function( response ) {
                console.log( response );
            },
        } );
    }
} ) ( jQuery );