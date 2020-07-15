<?php
/**
 * Plugin Name:       Simple Webp Images
 * Plugin URI:        
 * Description:       Generates webp images from uploaded jpg or png images, and outputs webp images instead of jpg or png images in compatible browsers. Also optionally provides lazy-load functionality for converted images.
 * Version:           1.2.0
 * Requires at least: 5.0.0
 * Requires PHP:      7.0
 * Author:            Mike Bricknell-Barlow
 * Author URI:        https://bricknellbarlow.co.uk
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       simple-webp-images
*/

define('SIMPLE_WEBP_IMAGES_VERSION', '1.2.0');
define('SIMPLE_WEBP_IMAGES_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('SIMPLE_WEBP_IMAGES_PLUGIN_DIR_PATH', dirname(__FILE__));

require_once 'classes' . DIRECTORY_SEPARATOR . 'class-simple-webp-images.php';
require_once 'classes' . DIRECTORY_SEPARATOR . 'class-simple-webp-images-html.php';
require_once 'classes' . DIRECTORY_SEPARATOR . 'class-simple-webp-images-admin.php';

global $simple_webp_images;
$simple_webp_images = new Simple_Webp_Images();
new Simple_Webp_Images_HTML();
new Simple_Webp_Images_Admin();

function swi_activation () {
    if ( ! file_exists ( WPMU_PLUGIN_DIR ) ) {
        mkdir ( WPMU_PLUGIN_DIR, 0777, true );
    }
    
    copy ( 
        SIMPLE_WEBP_IMAGES_PLUGIN_DIR_PATH . DIRECTORY_SEPARATOR . 'mu-plugins' . DIRECTORY_SEPARATOR . 'output_buffering.php',
        WPMU_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'output_buffering.php'
    );
}
register_activation_hook( __FILE__, 'swi_activation' );

function swi_deactivation () {
    unlink ( WPMU_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'output_buffering.php' );
}
register_deactivation_hook( __FILE__, 'swi_deactivation' );

function swi_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' .
		admin_url( 'options-general.php?page=simple-webp-images' ) .
        '">' . __('Settings') . '</a>';
        
    $links[] = '<a href="' .
		'https://www.buymeacoffee.com/ecHnK8Q' .
        '">' . __('Buy me a coffee') . '</a>';
    
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'swi_add_plugin_page_settings_link' );

