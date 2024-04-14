<?php

/**
 * Plugin Name:       Simple Webp Images
 * Plugin URI:        
 * Description:       Generates webp images from uploaded jpg or png images, and outputs webp images instead of jpg or png images in compatible browsers. Also optionally provides lazy-load functionality for converted images.
 * Version:           2.0.0
 * Requires at least: 5.0.0
 * Requires PHP:      8.0
 * Author:            Mike Bricknell-Barlow
 * Author URI:        https://bricknellbarlow.co.uk
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       simple-webp-images
*/

define('SIMPLE_WEBP_IMAGES_VERSION', '2.0.0');
define('SIMPLE_WEBP_IMAGES_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('SIMPLE_WEBP_IMAGES_PLUGIN_DIR_PATH', dirname(__FILE__));

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once 'classes' . DIRECTORY_SEPARATOR . 'class-simple-webp-images.php';
require_once 'classes' . DIRECTORY_SEPARATOR . 'class-simple-webp-images-html.php';
require_once 'classes' . DIRECTORY_SEPARATOR . 'class-simple-webp-images-admin.php';

use SWI\Includes\File;

new Simple_Webp_Images();
new Simple_Webp_Images_HTML();
new Simple_Webp_Images_Admin();

function swi_activation() {
    if (!File::exists(WPMU_PLUGIN_DIR)) {
        File::mkdir(WPMU_PLUGIN_DIR);
    }
    
    File::copy(
        sprintf(
            '%s%smu-plugins%soutput_buffering.php',
            SIMPLE_WEBP_IMAGES_PLUGIN_DIR_PATH,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR
        ),
        sprintf(
            '%s%soutput_buffering.php',
            WPMU_PLUGIN_DIR,
            DIRECTORY_SEPARATOR
        )
    );
}
register_activation_hook(__FILE__, 'swi_activation');

function swi_deactivation() {
    File::delete(sprintf(
        '%s%soutput_buffering.php',
        WPMU_PLUGIN_DIR,
        DIRECTORY_SEPARATOR
    ));
}
register_deactivation_hook(__FILE__, 'swi_deactivation');

function swi_add_plugin_page_settings_link ($links) {
	$links[] = sprintf(
        '<a href="%s">%s</a>',
        admin_url('options-general.php?page=simple-webp-images'),
        __('Settings')
    );

    $links[] = sprintf(
        '<a href="%s">%s</a>',
        'https://www.buymeacoffee.com/ecHnK8Q',
        __('Buy me a coffee')
    );
    
    return $links;
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'swi_add_plugin_page_settings_link');

