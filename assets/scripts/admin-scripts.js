( function ( $ ) {
    $( document ).ready( function () {
        window.ajaxrunning = false;

        if($('body').hasClass('post-type-attachment')) { 
            onLoadElements();
        }

        $(document).on( 'click', '#swi-convert-single-image', function (e) {
            e.preventDefault();
            convertSingleImage();
        } );

        $(document).on( 'click', '#start-bulk-conversion', function (e) {
            e.preventDefault();
            checkImageAmount();
        } );
    });

    function onLoadElements () {
        $(document).on( 'DOMSubtreeModified', '.media-frame-content', function () {
            if( $('.convert-image-setting').length === 0 )
                loadConvertLink();
        } );

        $(document).on( 'click', '.attachment', function () {
            if( $('.convert-image-setting').length === 0 )
                loadConvertLink();
        } );
    }

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
                if ( response === "Success!" ) {
                    $ ( '#swi-convert-single-image' ).text( 'Converted!' );
                }
            },
        } );
    }

    function checkImageAmount () {
        $( '#start-bulk-conversion' ).slideUp();
        $( '.step-2' ).slideDown();

        $.ajax( {
            'type': 'POST',
            'url': ajaxurl,
            'data': {
                'action': 'get_total_images',
            },
            'success': function ( response ) {
                $( '.step-2' ).slideUp();
                
                var totalImages = response;

                if ( isNaN(totalImages) )
                    totalImages = $ ( response ).text();

                $( '#total-images' ).text( totalImages );
                $( '.step-3' ).slideDown();
                window.bulkConvertRunning = false;
                window.bulkConvertPage = 1;
                bulkConvertImages();
            }
        } );
    }

    function bulkConvertImages () {
        setInterval( function () {
            if( window.bulkConvertRunning === false ) {
                window.bulkConvertRunning = true;

                $.ajax( {
                    'type': 'POST',
                    'url': ajaxurl,
                    'data': {
                        'action': 'bulk_convert_images',
                        'paged': window.bulkConvertPage,
                    },
                    'success': function ( response ) {
                        if( response == "All done" || $ ( response ).text().includes ( "All done" ) ) {
                            $( '.converting' ).slideUp();
                            $( '.step-4' ).slideDown();
                            return false;
                        } 
                        
                        var totalImages = parseInt( $( '#total-images' ).text() );
                        var processedImages = response;

                        if ( isNaN(processedImages) )
                            processedImages = $ ( response ).text();
                        
                        if ( parseInt ( processedImages ) > totalImages ) {
                            processedImages = totalImages;
                        }

			            if ( processedImages != "All done" )
                        	$( '#remaining-images' ).text( processedImages );
                        
			            window.bulkConvertRunning = false;
                        window.bulkConvertPage = parseInt(window.bulkConvertPage) + 1;
                    },
                } );
            }
        }, 100 );
    }
} ) ( jQuery );
