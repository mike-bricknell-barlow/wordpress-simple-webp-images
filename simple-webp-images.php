<?php
/**
 * Plugin Name:       Simple Webp Images
 * Plugin URI:        
 * Description:       Generates webp images from uploaded jpg or png images, and outputs webp images instead of jpg or png images in compatible browsers. 
 * Version:           1.0.2
 * Requires at least: 5.0.0
 * Requires PHP:      7.0
 * Author:            Mike Bricknell-Barlow
 * Author URI:        https://bricknellbarlow.co.uk
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       simple-webp-images
*/

define('SIMPLE_WEBP_IMAGES_VERSION', '1.0.2');
define('SIMPLE_WEBP_IMAGES_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('SIMPLE_WEBP_IMAGES_PLUGIN_DIR_PATH', dirname(__FILE__));

require 'classes/class-simple-webp-images.php';
require 'classes/class-simple-webp-images-html.php';
require 'classes/class-simple-webp-images-admin.php';

global $simple_webp_images;
new Simple_Webp_Images_HTML();
$simple_webp_images = new Simple_Webp_Images();
new Simple_Webp_Images_Admin();
