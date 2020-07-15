<?php

class Simple_Webp_Images_HTML {
    function __construct() {
        $this->hooks_and_filters();
    }

    private function hooks_and_filters () {
        add_action( 'wp_ajax_output_single_convert_link', array ( $this, 'output_single_convert_link' ) );
	    add_action( 'wp_enqueue_scripts', array ( $this, 'enqueue_assets' ) );

        add_filter( 'wp_get_attachment_image_attributes', array( $this, 'add_id_attribute_to_image_tags' ), 10, 3 );
        add_filter( 'the_content', array ( $this, 'wrap_img_tags_with_picture_element' ), 20 );
        add_filter( 'final_output', array ( $this, 'wrap_img_tags_with_picture_element' ) );
    }

    public function output_single_convert_link () {
        include SIMPLE_WEBP_IMAGES_PLUGIN_DIR_PATH . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'partial-single-convert-button.php';
        wp_die();
    }

    private function is_HTML( $string ) {
        if( $string != strip_tags( $string ) ) {
            return true;
        } else {
            return false;
        }
    }

    private function is_xml( $string ) {
        $doc = @simplexml_load_string( $string );
        if ($doc) {
            return true; 
        } else {
            return false;
        }
    }

    private function is_valid_string( $content ) {
        if ( current_filter () == 'final_output' && ! $this->is_output_buffering_enabled () ) {
            return false;
        }
	    
    	if( $this->is_json( $content ) ) {
            // Ignore JSON payloads, such as those used by Gutenberg in admin
            return false;
        }

        if( wp_doing_ajax() ) {
            // Processing HTML generated in ajax requests can break functionality - skip these
            return false;
        }

        if( $this->is_xml( $content ) ) {
            // If string is XML, don't try to process it
            return false;
        }

        if( ! $this->is_HTML( $content ) ) {
            // If string is not HTML, don't try to process it
            return false;
        }

        return true;
    }
	
    public function is_json( $string ) {
        json_decode( $string );
        return ( json_last_error() == JSON_ERROR_NONE );
    }  

    public function wrap_img_tags_with_picture_element ( $content ) {
        
        if( ! $this->is_valid_string( $content ) ) {
            return $content;
        }
        
        libxml_use_internal_errors ( true );
        $post = new DOMDocument ();
        $post->loadHTML ( '<?xml encoding="utf-8" ?>' . $content );
        $imgs = $post->getElementsByTagName( 'img' );

        foreach ( $imgs as $img ) {
            
            if ( $img->parentNode->tagName == "source" || $img->parentNode->tagName == "picture" ) {
                continue;
            }

            if ( 
                strpos ( $img->getAttribute ( 'src' ), '.jpg' ) === FALSE &&
                strpos ( $img->getAttribute ( 'src' ), '.jpeg' ) === FALSE &&
                strpos ( $img->getAttribute ( 'src' ), '.png' ) === FALSE 
                ) {
                continue;
            }
            
            $elem = $post->createElement( 'span' );
            $this->appendHTML($elem, $this->generate_picture_element ( $post->saveHTML( $img ), $img->getAttribute ( 'srcset' ), $img->getAttribute ( 'class' ), $img->getAttribute ( 'data-attachmentid' ), $img->getAttribute ( 'src' ) ) );
            $img->parentNode->insertBefore ( $elem, $img ); 
            $img->parentNode->removeChild ( $img );
        
        }
	
	    $new_content = str_replace( '<?xml encoding="utf-8" ?>', '', $post->saveHTML() );
        return str_replace( '&amp;', '&', $new_content );        
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

    private function is_excluded_from_lazy_loading( $classes ) {
        $excluded_classes = get_option( 'simple-webp-images-excluded-lazy-loading' );

        if( ! $excluded_classes ) {
            return false;
        }

        $excluded_classes_array = array_filter( array_map( 'trim', explode( ',', $excluded_classes ) ) );
        $img_classes_array = array_filter( array_map( 'trim', explode( ' ', $classes ) ) );

        foreach( $img_classes_array as $img_class ) {
            if( in_array( $img_class, $excluded_classes_array ) ) {
                return true;
            }
        }
        
        return false;
    }

    private function generate_picture_element ( $img_tag, $src_set, $classes, $attachment_id, $src = false ) {
	    if ( ! $attachment_id ) {
            if ( strpos ( $classes, 'wp-image-' ) !== FALSE ) {
                $ids = array();
                preg_match_all ( '/wp-image-(\d{1,12})/', $classes, $ids );
                if ( isset ( $ids[1][0] ) ) {
                    $attachment_id = $ids[1][0];
                }
            }
        }    
        
        if( ! $attachment_id ) {
            $src_arr = explode( '/', $src );
            $filename = array_pop( $src_arr );

            $maybe_attachment_id = $this->wp_get_attachment_by_file_name( $filename );

            if( $maybe_attachment_id ) {
                $attachment_id = $maybe_attachment_id;
            }
        }

        $src_set = $this->generate_src_set ( $attachment_id );
            $size_string = $this->generate_sizes_string ( $src_set );
            
            $webp_src_set = $src_set;
            $webp_src_set = str_replace ( '.jpg', '.jpg.webp', $webp_src_set );
            $webp_src_set = str_replace ( '.jpeg', '.jpeg.webp', $webp_src_set );
            $webp_src_set = str_replace ( '.png', '.png.webp', $webp_src_set );

            $img_type = false;
            switch ( $img_tag ) {
                case strpos ( $img_tag, '.jpg' ) !== FALSE:
                case strpos ( $img_tag, '.jpeg' ) !== FALSE:
                    $img_type = 'image/jpg';
                    break;

                case strpos ( $img_tag, '.png' ) !== FALSE:
                    $img_type = 'image/png';
                    break;
            }

            $src_set_title = 'srcset';
            $sizes_title = 'sizes';

        $img_tag = str_replace ( 'class="', 'class=" ' . $classes, $img_tag );

        if ( strpos ( 'class', $img_tag ) === FALSE ) {
            $img_tag = str_replace ( 'src', 'class="' . $classes . '" src', $img_tag );
        }

        if ( $this->is_lazy_loading_enabled () && ! $this->is_excluded_from_lazy_loading( $classes ) ) {
            $src_set_title = 'data-srcset';
            $sizes_title = 'data-sizes';

            $img_tag = str_replace ( 'src', 'data-src', $img_tag );
            $img_tag = str_replace ( 'class="', 'class="lazy ', $img_tag );

            if ( strpos ( 'class', $img_tag ) === FALSE ) {
                $img_tag = str_replace ( 'data-src', 'class="lazy" data-src', $img_tag );
            }
            
            $classes .= ' lazy';
        }

        $new_img_tag = '<picture>';
        
        if ( $webp_src_set ) {
            $new_img_tag .= '<source ' . $src_set_title . '="' . $webp_src_set . '" ' . $sizes_title . '="' . $size_string . '" type="image/webp">';
        }

        if ( $img_type && $src_set ) {
            $new_img_tag .= '<source ' . $src_set_title . '="' . $src_set . '" ' . $sizes_title . '="' . $size_string . '" type="' . $img_type . '">';
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
        if ( $this->is_lazy_loading_enabled () ) {
            wp_enqueue_script ( 'lazyload-scripts', SIMPLE_WEBP_IMAGES_PLUGIN_DIR_URL . 'assets/scripts/lazyload.min.js', array (), SIMPLE_WEBP_IMAGES_VERSION, true );
            wp_enqueue_script ( 'swi-public-scripts', SIMPLE_WEBP_IMAGES_PLUGIN_DIR_URL . 'dist/scripts/public-scripts.js', array ( 'lazyload-scripts' ), SIMPLE_WEBP_IMAGES_VERSION, true );
        }
    }

    private function is_lazy_loading_enabled () {
        if( apply_filters( 'simple-webp-images-exclude-from-lazy-loading', false ) ) {
            return false;
        }
        
        if ( get_option ( 'simple-webp-images-lazy-loading' ) == 'on' ) {
            return true;
        }

        return false;
    }

    private function is_output_buffering_enabled () {
        $excluded_pages = get_option( 'simple-webp-images-excluded-html-ob' );

        if( is_array( $excluded_pages ) && in_array( get_the_id(), $excluded_pages ) ) {
            return false;
        }

        if( apply_filters( 'simple-webp-images-exclude-from-output-buffering', false ) ) {
            return false;
        }

        if ( get_option ( 'simple-webp-images-output-buffering' ) == 'on' ) {
            return true;
        }

        return false;
    }

    private function wp_get_attachment_by_file_name( $filename ) {
        global $wpdb;
        $query = "SELECT DISTINCT `post_id` FROM " . $wpdb->postmeta . " WHERE `meta_key` = '_wp_attached_file' AND `meta_value` LIKE '%" . esc_sql( $filename ) . "%'";
        $results = $wpdb->get_results( $query );

        if( ! $results ) {
            return false;
        }

        return $results[0]->post_id;
    }
}

