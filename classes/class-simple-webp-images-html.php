<?php

class Simple_Webp_Images_HTML {
    function __construct() {
        $this->hooks_and_filters();
    }

    private function hooks_and_filters () {
        add_action( 'wp_ajax_output_single_convert_link', array ( $this, 'output_single_convert_link' ) );
        add_action( 'template_redirect', array ( $this, 'start_html_buffer' ), 0 );
	    add_action( 'wp_enqueue_scripts', array ( $this, 'enqueue_assets' ) );

        add_filter( 'wp_get_attachment_image_attributes', array( $this, 'add_id_attribute_to_image_tags' ), 10, 3 );
        add_filter( 'the_content', array ( $this, 'wrap_img_tags_with_picture_element' ), 20 );
    }

    public function output_single_convert_link () {
        include SIMPLE_WEBP_IMAGES_PLUGIN_DIR_PATH . '/templates/partial-single-convert-button.php';
        wp_die();
    }

    public function wrap_img_tags_with_picture_element ( $content ) {
        libxml_use_internal_errors ( true );
        $post = new DOMDocument ();
        $post->loadHTML ( $content );
        $imgs = $post->getElementsByTagName( 'img' );

        foreach ( $imgs as $img ) {
            
            if ( $img->parentNode->tagName == "source" || $img->parentNode->tagName == "picture" ) {
                continue;
            }

            if ( 
                strpos ( $img->getAttribute ( 'src' ), '.jpg' ) === FALSE &&
                strpos ( $img->getAttribute ( 'src' ), '.png' ) === FALSE 
                ) {
                continue;
            }
            
            $elem = $post->createElement( 'span' );
            $this->appendHTML($elem, $this->generate_picture_element ( $post->saveHTML( $img ), $img->getAttribute ( 'srcset' ), $img->getAttribute ( 'class' ), $img->getAttribute ( 'data-attachmentid' ) ) );
            $img->parentNode->insertBefore ( $elem, $img ); 
            $img->parentNode->removeChild ( $img );
        
        }
        
        return $post->saveHTML();
    }

    public function start_html_buffer () {
        add_action( 'shutdown', array ( $this, 'stop_html_buffer' ), PHP_INT_MAX );
        ob_start( array ( $this, 'modify_final_html_output' ) ); 
    }

    public function stop_html_buffer () {
        ob_end_flush();
    }

    public function modify_final_html_output ( $content ) {
        $content = $this->wrap_img_tags_with_picture_element ( $content );
        return $content;
    }

    private function get_all_image_sizes () {
        global $_wp_additional_image_sizes; 
        $sizes = $_wp_additional_image_sizes;

        $sizes['thumbnail'] = [
            'width' => get_option('thumbnail_size_w'),
            'height' => get_option('thumbnail_size_h'),
        ];

        $sizes['medium'] = [
            'width' => get_option('medium_size_w'),
            'height' => get_option('medium_size_h'),
        ];

        $sizes['large'] = [
            'width' => get_option('large_size_w'),
            'height' => get_option('large_size_h'),
        ];

        return $sizes;
    }

    private function generate_src_set ( $attachment_id, $sizes = array() ) {  
        if ( empty ( $sizes ) ) {
            $sizes = $this->get_all_image_sizes();
        }

        $sources = [];
        $src_set = "";
        foreach ( $sizes as $size_name => $size ) {
            if ( !in_array ( $source = wp_get_attachment_image_src (
                $attachment_id,
                $size_name
            ), $sources ) ) {
                $sources[] = $source;
            }
        }
        unset ( $source );

        if ( empty ( $sources ) ) {
            return false;
        }

        foreach ( $sources as $source ) {
            $src_set .= $source[0] . ' ' . $source[1] . 'w, ';
        }

        if ( strpos ( $src_set, 'http' ) === FALSE ) {
            return false;
        }

        return $src_set;
    }

    private function generate_sizes_string ( $src_set ) {
        $sizes = array();
        preg_match_all ( '/(\d{1,12})w/', $src_set, $sizes );
        sort ( $sizes[1] );

        $size_string = '';
        foreach ( $sizes[1] as $size ) {
            $size_string .= ' ( max-width: ' . $size .  'px ) ' . $size .  'px,';
        }

        $size_string = trim ( $size_string, ',' );
        return $size_string;
    }

    private function generate_picture_element ( $img_tag, $src_set, $classes, $attachment_id ) {
        $src_set = $this->generate_src_set ( $attachment_id );
        $size_string = $this->generate_sizes_string ( $src_set );
        
        $webp_src_set = $src_set;
        $webp_src_set = str_replace ( '.jpg', '.jpg.webp', $webp_src_set );
        $webp_src_set = str_replace ( '.png', '.png.webp', $webp_src_set );

        $img_type = false;
        switch ( $img_tag ) {
            case strpos ( $img_tag, '.jpg' ) !== FALSE:
                $img_type = 'image/jpg';
                break;

            case strpos ( $img_tag, '.png' ) !== FALSE:
                $img_type = 'image/png';
                break;
        }
        
        $new_img_tag = '<picture class="' . $classes . '">';
        
        if ( $webp_src_set ) {
            $new_img_tag .= '<source srcset="' . $webp_src_set . '" sizes="' . $size_string . '" type="image/webp">';
        }

        if ( $img_type && $src_set ) {
            $new_img_tag .= '<source srcset="' . $src_set . '" sizes="' . $size_string . '" type="' . $img_type . '">';
        }
            
        $new_img_tag .= $img_tag;
        $new_img_tag .= '</picture>';

        return $new_img_tag;
    }

    private function appendHTML ( DOMNode $parent, $source ) {
        $tmpDoc = new DOMDocument();
        $tmpDoc->loadHTML($source);
        foreach ( $tmpDoc->getElementsByTagName( 'body' )->item( 0 )->childNodes as $node ) {
            $node = $parent->ownerDocument->importNode ( $node, true );
            $parent->appendChild ( $node );
        }
    }

    public function add_id_attribute_to_image_tags ( $attr, $attachment, $size ) {
        $attr['data-attachmentid'] = $attachment->ID;
        return $attr;
    }

    public function enqueue_assets () {
        if ( get_option ( 'simple-webp-images-lazy-loading' ) == 'on' ) {
            wp_enqueue_script ( 'lazyload-scripts', SIMPLE_WEBP_IMAGES_PLUGIN_DIR_URL . 'assets/scripts/lazyload.min.js', array(), $this->version, true );
        }
    }
}