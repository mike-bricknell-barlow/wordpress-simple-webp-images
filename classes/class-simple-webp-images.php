<?php

class Simple_Webp_Images {
    private $version = SIMPLE_WEBP_IMAGES_VERSION;
    private $plugin_url;

    function __construct () {
        $this->hooks_and_filters();
    }

    public function set_plugin_url () {
        $this->plugin_url = SIMPLE_WEBP_IMAGES_PLUGIN_DIR_URL;
    }

    public function get_plugin_url () {
        return $this->plugin_url;
    }
    
    private function hooks_and_filters () {
        add_filter( 'wp_generate_attachment_metadata', array( $this, 'generate_webp_on_resize' ), 10, 2 );
        
        add_action( 'init', array( $this, 'set_plugin_url' ) );
        add_action( 'delete_attachment', array( $this, 'delete_webp_on_media_deletion' ) );
        add_action( 'wp_ajax_convert_single_attachment', array( $this, 'convert_single_attachment' ) );
    }

    public function generate_webp_on_resize ( $metadata, $attachment_id ) {
        $file_path_arr = explode( DIRECTORY_SEPARATOR, $metadata['file'] );
        $mime_type = false;

        foreach( $metadata['sizes'] as $size ) {
            $filepath = wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $file_path_arr[0] . DIRECTORY_SEPARATOR . $file_path_arr[1] . DIRECTORY_SEPARATOR . $size['file'];
            $mime_type = $size['mime-type'];
            $this->generate_webp( $filepath, $mime_type );
        }

        $this->generate_webp( wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $metadata['file'], $mime_type );
        
        return $metadata;
    }

    private function generate_webp ( $filename, $filetype ) {
        $image = false;
        
        switch ( $filetype ) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg( $filename );
                break;

            case 'image/png':
                $image = imagecreatefrompng( $filename );
                break;
        }

        if ( !$image ) {
            return false;
        }

        $quality = ( $set_qual = get_option( 'simple-webp-images-conversion-quality' ) ) ? $set_qual : 80;

        imagepalettetotruecolor ( $image );

        $created = imagewebp( $image, $filename . '.webp', $quality );

        return $created;
    }

    public function delete_webp_on_media_deletion ( $post_id ) {
        $attachment_meta = get_post_meta ( $post_id, '_wp_attachment_metadata', true );
        
        $file_path_arr = explode( DIRECTORY_SEPARATOR, $attachment_meta['file'] );
        foreach( $attachment_meta['sizes'] as $size ) {
            $filepath = wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $file_path_arr[0] . DIRECTORY_SEPARATOR . $file_path_arr[1] . DIRECTORY_SEPARATOR . $size['file'];
            unlink( $filepath );
        }

        unlink( wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $attachment_meta['file'] . '.webp' );
    }

    public function convert_single_attachment () {
        $created = $this->convert_single_attachment_by_attachment_id( intval( $_POST['attachment_id'] ) );
        
        if( $created ) {
            echo esc_html( 'Success!' );
            wp_die();
        } 
       
        echo esc_html( 'Failure.' );
        wp_die();
    }

    public function convert_single_attachment_by_attachment_id ( $attachment_id ) {
        $attachment_meta = get_post_meta ( $attachment_id, '_wp_attachment_metadata', true );
        $file_path_arr = explode( DIRECTORY_SEPARATOR, $attachment_meta['file'] );
        
        $mime_type = false;
        $created = false;
        foreach( $attachment_meta['sizes'] as $size ) {
            $filepath = wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $file_path_arr[0] . DIRECTORY_SEPARATOR . $file_path_arr[1] . DIRECTORY_SEPARATOR . $size['file'];
            $mime_type = $size['mime-type'];
            $this->generate_webp( $filepath, $mime_type );
        }

        if( ! $mime_type ) {
            switch ( $attachment_meta['file'] ) {
                case strpos ( $attachment_meta['file'], '.jpg' ) !== FALSE:
                case strpos ( $attachment_meta['file'], '.jpeg' ) !== FALSE:
                    $mime_type = 'image/jpeg';
                    break;
    
                case strpos ( $attachment_meta['file'], '.png' ) !== FALSE:
                    $mime_type = 'image/png';
                    break;
            }
        }

        $created = $this->generate_webp( wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $attachment_meta['file'], $mime_type );
        return $created;
    }
}