<?php

namespace SimpleWebpImages;

class Simple_Webp_Images_Admin {
    function __construct () {
        $this->hooks_and_filters();
    }

    private function hooks_and_filters () {
        add_action( 'admin_menu', array( $this, 'register_admin_menu_page' ) );
        add_action( 'register_settings', array( $this, 'register_admin_menu_page' ) );
        add_action( 'admin_post_update_settings', array( $this, 'update_settings' ) );
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
        include ABSPATH . '/wp-content/plugins/simple-webp-images/templates/template-admin-menu-page.php';
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
}