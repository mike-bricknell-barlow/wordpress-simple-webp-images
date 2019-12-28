<?php

class Simple_Webp_Images_HTML {
    function __construct() {
        $this->hooks_and_filters();
    }

    private function hooks_and_filters () {
        add_action( 'wp_ajax_output_single_convert_link', array ( $this, 'output_single_convert_link' ) );
        
        add_filter( 'the_content', array ( $this, 'wrap_img_tags_with_picture_element' ), 20 );
    }

    public function output_single_convert_link () {
        include ABSPATH . '/wp-content/plugins/simple-webp-images/templates/partial-single-convert-button.php';
        wp_die();
    }

    public function wrap_img_tags_with_picture_element ( $content ) {
        libxml_use_internal_errors( true );
        $post = new DOMDocument ();
        $post->loadHTML ( $content );
        $imgs = $post->getElementsByTagName( 'img' );

        foreach ( $imgs as $img ) {
            $elem = $post->createElement('div');
            $this->appendHTML($elem, $this->generate_picture_element ( $post->saveHTML( $img ), $img->getAttribute ( 'srcset' ), $img->getAttribute ( 'class' ) ) );
            $img->parentNode->insertBefore($elem, $img); 
            $img->parentNode->removeChild($img);
        }
        
        return $post->saveHTML();
    }

    private function generate_picture_element ( $img_tag, $src_set, $classes ) {
        $webp_src_set = $src_set;
        $webp_src_set = str_replace( '.jpg', '.jpg.webp', $webp_src_set );
        $webp_src_set = str_replace( '.png', '.png.webp', $webp_src_set );

        $img_type = false;
        switch ( $img_tag ) {
            case strpos( $img_tag, '.jpg' ) !== FALSE:
                $img_type = 'image/jpg';
                break;

            case strpos( $img_tag, '.png' ) !== FALSE:
                $img_type = 'image/png';
                break;
        }
        
        $new_img_tag = '<picture class="' . $classes . '">';
        $new_img_tag .= '<source srcset="' . $webp_src_set . '" type="image/webp">';

        if ( $img_type ) {
            $new_img_tag .= '<source srcset="' . $src_set . '" type="image/jpg">';
        }
            
        $new_img_tag .= $img_tag;
        $new_img_tag .= '</picture>';

        return $new_img_tag;
    }

    private function appendHTML(DOMNode $parent, $source) {
        $tmpDoc = new DOMDocument();
        $tmpDoc->loadHTML($source);
        foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $node = $parent->ownerDocument->importNode($node, true);
            $parent->appendChild($node);
        }
    }
}