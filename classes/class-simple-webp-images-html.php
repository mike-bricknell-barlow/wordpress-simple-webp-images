<?php

namespace SimpleWebpImages;

class Simple_Webp_Images_HTML {
    function __construct() {
        $this->hooks_and_filters();
    }

    private function hooks_and_filters () {
        add_action( 'wp_ajax_output_single_convert_link', array( $this, 'output_single_convert_link' ) );
    }

    public function output_single_convert_link () {
        include ABSPATH . '/wp-content/plugins/simple-webp-images/templates/partial-single-convert-button.php';
        wp_die();
    }
}