Simple Webp Images plugin for WordPress
By Mike Bricknell-Barlow

This plugin allows the use of .webp images on a WordPress site in a simple and easy way.

** Installation **
 - Download the .zip archive from the github repository
 - In the WordPress admin area, go to Plugins -> Add New
 - Upload the .zip file
 - Activate the plugin

** How to use **
 - Once activated, .jpg or .png images uploaded to the Media Library will be automatically converted to .webp on upload.
 - Individual .jpg or .png images can be converted on the Media Library screen - click on an image, and then click the Convert link on the right hand side.
 - Bulk Conversion can be performed from the settings page, at Settings -> Simple Webp Images. Click the Bulk Convert button to start the process, and leave the window open until this is completed.

** Options **
 - Settings page available under Settings -> Simple Webp Images
 - Conversion quality - this can be set on the settings page at Settings -> Simple Webp Images. The quality value to be entered is a percentage - by default, this is set at 80%.
 - HTML Output Buffering - when activated, allows the plugin to perform a find-and-replace on the HTML that WordPress has generated for output to the browser to replace image tags. This tends to perform the image tag replacement more reliably, but can cause issues with some themes.
 - Lazy loading - when activated, generated <picture> tags are lazy-loaded.
 
 ** Development **
  - Human-readable assets are stored in the assets folder
  - Compiled versions are saved to the dist folder, and used in production
  - To make alterations to the asset files, edit those under assets and run the below build commands:
  
  * NPM version *
  `npm install && npm run build`
  
  * Yarn version *
  `yarn && yarn build`
  
  ** Changelog **
  
Version 1.0.0
29/12/2019
  - Initial plugin version
  - Bulk conversion of images from settings screen
  - Frontend img replacement via `the_content` filter
  
Version 1.0.5
04/01/20
   - Adding option for frontend img replacement via HTML output buffering
   - General bugfixes
   
Version 1.1.0
05/01/20
   - Adding option for lazy load functionality
   
Version 1.1.5
10/01/20
   - General bugfixes/stability improvements

Version 1.1.6
11/01/20
   - Bugfix, error with public assets version
