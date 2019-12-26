<?php
/**
 * Plugin Name:       Simple Webp Images
 * Plugin URI:        
 * Description:       Generates webp images from uploaded jpg or png images, and outputs webp images instead of jpg or png images in compatible browsers. 
 * Version:           1.0.0
 * Requires at least: 5.0.0
 * Requires PHP:      7.0
 * Author:            Mike Bricknell-Barlow
 * Author URI:        https://bricknellbarlow.co.uk
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       simple-webp-images
*/

require 'classes/class-simple-webp-images.php';
require 'classes/class-simple-webp-images-html.php';
use SimpleWebpImages;

$simple_webp_images = new SimpleWebpImages\Simple_Webp_Images_HTML();
new SimpleWebpImages\Simple_Webp_Images();
