<?php

class Simple_Webp_Images_Admin {
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
        add_action( 'init', array( $this, 'set_plugin_url' ) );
        add_action( 'admin_menu', array( $this, 'register_admin_menu_page' ) );
        add_action( 'register_settings', array( $this, 'register_admin_menu_page' ) );
        add_action( 'admin_post_update_settings', array( $this, 'update_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_ajax_get_total_images', array( $this, 'get_all_images' ) );
        add_action( 'wp_ajax_bulk_convert_images', array( $this, 'bulk_convert_images' ) );
    }

    public function enqueue_admin_assets () {
        wp_enqueue_script (
            'simple-webp-images-admin-scripts',
            $this->get_plugin_url() . 'dist/scripts/admin-scripts.js',
            array( 'jquery' ),
            $this->version
        );

        wp_enqueue_style(
            'simple-webp-images-admin-styles',
            $this->get_plugin_url() . 'dist/styles/admin-styles.css',
            array(),
            $this->version
        );
    }

    public function register_admin_menu_page () {
        add_submenu_page(
            'options-general.php',
            'Simple Webp Images',
            'Simple Webp Images',
            'manage_options',
            'simple-webp-images',
            array ( $this, 'display_admin_menu_page' )
        );
    }

    public function display_admin_menu_page () {
        $fields = $this->get_options_fields();
        include SIMPLE_WEBP_IMAGES_PLUGIN_DIR_PATH . '/templates/template-admin-menu-page.php';
    }

    public function register_settings () {
        $fields = $this->get_options_fields();
        $option_group = 'simple-webp-images-options-group';
        
        foreach ( $fields as $field ) {
            register_setting (
                $option_group,
                $field['id'],
                array ()
            );
        }
    }

    private function get_options_fields () {
        return array (
            array ( 
                'label' => 'Conversion Quality (%)',
                'type' => 'text',
                'id' => 'simple-webp-images-conversion-quality',
                'value' => get_option('simple-webp-images-conversion-quality')
            ),
        );
    }

    public function update_settings () {
        $fields = $this->get_options_fields();

        foreach ( $fields as $field ) {
            if( isset( $_POST[$field['id']] ) ) {
                update_option( $field['id'], $_POST[$field['id']] );
            }
        }

        wp_redirect( $_SERVER["HTTP_REFERER"] );
        exit();
    }

    public function get_all_images () {
        $total_images = $this->get_count_of_images();
        echo $total_images;
        wp_die();
    }

    private function get_count_of_images () {
        $attachment_query = new WP_Query ( array (
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'post_status' => 'inherit',
        ) );

        $total_images = $attachment_query->found_posts;
        return $total_images;
    }

    private function get_images_paged ( $paged ) {
        $attachment_query = new WP_Query ( array (
            'post_type' => 'attachment',
            'posts_per_page' => 10,
            'fields' => 'ids',
            'post_status' => 'inherit',
            'paged' => $paged,
        ) );

        return $attachment_query->get_posts();
    }

    public function bulk_convert_images () {
        $paged = $_POST['paged'];
        $images = $this->get_images_paged( $paged );

        if( !$images ) {
            echo "All done";
            wp_die();
        }

        global $simple_webp_images;

        foreach ( $images as $image ) {
            $simple_webp_images->convert_single_attachment_by_attachment_id( $image );
        }

        $images_converted = $paged * 10;

        echo $images_converted;
        wp_die();
    }
}