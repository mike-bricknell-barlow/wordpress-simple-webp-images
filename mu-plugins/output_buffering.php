<?php
error_log("pre check");
if ( get_option ( 'simple-webp-images-output-buffering' ) != 'on' ) {
    return;
}
error_log("buffered");
ob_start();

add_action('shutdown', function() {
    $final = '';

    $levels = ob_get_level();

    for ($i = 0; $i < $levels; $i++) {
        $final .= ob_get_clean();
    }

    // Apply any filters to the final output
    echo apply_filters('final_output', $final);
}, 0);