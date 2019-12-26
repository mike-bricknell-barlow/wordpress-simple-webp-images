<?php

namespace SimpleWebpImages;

class Simple_Webp_Images {
    private $version = '1.0.0';
    private $plugin_url;

    function __construct () {
        $this->hooks_and_filters();
    }

    public function set_plugin_url () {
        $this->plugin_url = get_home_url() . '/wp-content/plugins/simple-webp-images';
    }

    public function get_plugin_url () {
        return $this->plugin_url;
    }
    
    private function hooks_and_filters () {
        add_filter( 'wp_generate_attachment_metadata', array( $this, 'generate_webp_on_resize' ), 10, 2 );
        
        add_action( 'init', array( $this, 'set_plugin_url' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'delete_attachment', array( $this, 'delete_webp_on_media_deletion' ) );
        add_action( 'wp_ajax_convert_single_attachment', array( $this, 'convert_single_attachment' ) );
    }

    public function enqueue_admin_assets () {
        wp_enqueue_script(
            'simple-webp-images-admin-scripts',
            $this->get_plugin_url() . '/dist/scripts/admin-scripts.js',
            ['jquery'],
            $this->version
        );
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

            case 'image/png':
                $image = imagecreatefrompng( $filename );
                break;
        }

        if ( !$image ) {
            return false;
        }

        $created = imagewebp( $image, $filename . '.webp', 80 );

        return $created;
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

    public function convert_single_attachment () {
        $attachment_id = $_POST['attachment_id'];
        $attachment_meta = get_post_meta ( $attachment_id, '_wp_attachment_metadata', true );
        $file_path_arr = explode( '/', $attachment_meta['file'] );
        
        $mime_type = false;
        $created = false;
        foreach( $attachment_meta['sizes'] as $size ) {
            $filepath = wp_get_upload_dir()['path'] . '/' . $size['file'];
            $mime_type = $size['mime-type'];
            $this->generate_webp( $filepath, $mime_type );
        }

        $created = $this->generate_webp( wp_get_upload_dir()['basedir'] . '/' . $attachment_meta['file'], $mime_type );

        if( $created ) {
            echo 'Success!';
            wp_die();
        } 
       
        echo 'Failure.';
        wp_die();
    }
}