<?php

// Don't trigger output buffering in admin area, or when option is disabled
if ( is_admin() || get_option ( 'simple-webp-images-output-buffering' ) != 'on' ) {
    return;
}

ob_start();

add_action( 'shutdown', function() {
    $final = '';

    $levels = ob_get_level();

    for ($i = 0; $i < $levels; $i++) {
        $final .= ob_get_clean();
    }

    // Apply any filters to the final output
    echo apply_filters( 'final_output', $final );
}, 0 );
