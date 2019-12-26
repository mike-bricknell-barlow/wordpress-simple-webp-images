( function ( $ ) {
    $( document ).ready( function () {
        window.ajaxrunning = false;
        
        if($('body').hasClass('post-type-attachment')) {
            $(document).on( 'DOMSubtreeModified', '.media-frame-content', function () {
                if( $('.convert-image-setting').length === 0 )
                    loadConvertLink();
            } );

            $(document).on( 'click', '.attachment', function () {
                if( $('.convert-image-setting').length === 0 )
                    loadConvertLink();
            } );
        }

        $(document).on( 'click', '#swi-convert-single-image', function (e) {
            e.preventDefault();
            convertSingleImage();
        } );
    });

    function loadConvertLink () {
        if(!window.ajaxrunning) {
            window.ajaxrunning = true;
            $.ajax( {
                'url': ajaxurl,
                'data': {
                    'action': 'output_single_convert_link'
                },
                'type': 'POST',
                'success': function( response ) {
                    $( '.settings' ).append( response );
                    window.ajaxrunning = false;
                },
            } );
        }
    }

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