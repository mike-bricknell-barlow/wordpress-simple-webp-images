=== Simple Webp Images ===
Contributors: mikebricknellbarlow
Donate link: https://paypal.me/mikebarlow1989
Tags: pagespeed, webp, images, speed, conversion
Requires at least: 5.0.0
Tested up to: 5.3
Requires PHP: 7.0
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generates webp images from uploaded images, and outputs webp images in content in compatible browsers. Optionally provides lazy-load functionality.

== Description ==

This plugin helps to increase site speed, and helps to improve scores in Google's Pagespeed Insights tool by addressing the common recommendation "Serve next-gen image formats".

For the best speeds, Google recommends using new image formats such as .webp, which offer smaller file sizes than common formats such as .jpg or .png with no visible loss in quality. However, not all internet browsers support the .wepb image format, so only using that format would result in broken images in older browsers.

This plugin addresses that problem, and allows your site to use .webp images when it can. This plugin will convert images from .jpg or .png automatically, and save .webp versions of the image alongside the original. When a user visits the site in a compatible browser, they will be shown the .webp versions of all images, making your site load faster and improving your scores in Google Pagespeed Insights. Users on older browsers that aren't compatible with .webp images will be shown the .jpg or .png versions of the images instead.

When first installing the plugin, run the Bulk Converter to convert all your existing images. See the Installation section for instructions.

After installation, any .jpg or .png image uploaded to the Media Library will be automatically converted. 

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Install the plugin through the WordPress plugins screen directly, or download the .zip file, and upload it by going to Plugins -> Add New -> Upload .zip file.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Bulk Converter to convert all existing images to .webp. Go to Settings -> Simple Webp Images, and click the Start Bulk Conversion button at the bottom. The plugin will count all the images, and start processing them 10 at a time. Leave the page open until the conversion is completed.
4. Configure other plugin settings on the plugin settings page, at Settings -> Simple Webp Images. See below for a description of the available settings.

== Settings ==

* Conversion quality - Controls the level of compression on the generated .webp images. The lower the value, the smaller the file size of the generated images, but the more chance there is of a visual degradation of quality. The quality value to be entered is a percentage - by default, this is set at 80%. Most users won't need to change this.

* HTML Output Buffering - This allows the plugin to perform a find-and-replace on the HTML that WordPress has generated for output to the browser to replace image tags. This tends to perform the image tag replacement more reliably, but can cause issues with some themes. Users are advised to have this setting turned on, but they should check that the site is displaying as it should, and turn this option off if any issues arise.

* Lazy loading - This improves the site speed further by not loading images that are further down the page until the user has scrolled down to them. This saves time downloading images that the user hasn't got to yet, or might not get to at all. Users are advised to have this setting turned on, but they should check that the site is displaying as it should, and turn this option off if any issues arise.

== Frequently Asked Questions ==

= My site doesn't display correctly with HTML Output Buffering turned on - what should I do? =

Firstly, turn off the setting so that your site starts displaying correctly again. Then, send me a message describing the issue that you saw - I'll try to issue a fix and come back to you!

== Changelog ==

= 1.1.10 = 
* Bugfix - adding check to verify if output buffered string is HTML before attempting to process 

= 1.1.9 = 
* Adding settings link to plugins page for ease of use

= 1.1.8 = 
* Preventing HTML output buffering from affecting wp ajax requests, as this breaks some functionality

= 1.1.7 = 
* Sanitizing input/output

= 1.1.6 =
* Bugfix, error with public assets version

= 1.1.5 = 
* General bugfixes/stability improvements

= 1.1.0 =
* Adding option for lazy load functionality

= 1.0.5 =
* Adding option for frontend img replacement via HTML output buffering
* General bugfixes

= 1.0.0 =
* Initial plugin version
* Bulk conversion of images from settings screen
* Frontend img replacement via the_content filter

