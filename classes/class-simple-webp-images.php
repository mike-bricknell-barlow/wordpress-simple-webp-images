<?php

namespace SimpleWebpImages;

class Simple_Webp_Images {
    private $version = '1.0.0';

    function __construct() {
        $this->hooks_and_filters();
    }
    
    private function hooks_and_filters() {
        add_filter( 'wp_generate_attachment_metadata', array( $this, 'generate_webp_on_resize' ), 10, 2 );
        add_action( 'delete_attachment', array( $this, 'delete_webp_on_media_deletion' ) );
    }

    public function generate_webp_on_resize ( $metadata, $attachment_id ) {
        $file_path_arr = explode( '/', $metadata['file'] );
        $mime_type = false;

        foreach( $metadata['sizes'] as $size ) {
            $filepath = wp_get_upload_dir()['path'] . '/' . $size['file'];
            $mime_type = $size['mime-type'];
            $this->generate_webp( $filepath, $mime_type );
        }

        $this->generate_webp( wp_get_upload_dir()['basedir'] . '/' . $metadata['file'], $mime_type );
        
        return $metadata;
    }

    private function generate_webp ( $filename, $filetype ) {
        $image = false;
        
        switch ( $filetype ) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg( $filename );
                break;
        }

        if ( !$image ) {
            return false;
        }

        $created = imagewebp( $image, $filename . '.webp', 80 );
    }

    public function delete_webp_on_media_deletion ( $post_id ) {
        $attachment_meta = get_post_meta ( $post_id, '_wp_attachment_metadata', true );
        
        $file_path_arr = explode( '/', $attachment_meta['file'] );
        foreach( $attachment_meta['sizes'] as $size ) {
            $filepath = wp_get_upload_dir()['path'] . '/' . $size['file'] . '.webp';
            unlink( $filepath );
        }

        unlink( wp_get_upload_dir()['basedir'] . '/' . $attachment_meta['file'] . '.webp' );
    }
}