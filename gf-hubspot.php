<?php
/*
Plugin Name:  Sync Gravity Forms and Hubspot Forms
Plugin URI:   https://github.com/SolidDigital/gravity-hub-sync
Description:  Wordpress plugin that integrates Gravity Forms with Hubspot forms.
Version:      1.0
Author:       Solid Digital
Author URI:   https://www.soliddigital.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  gfhubspot
*/

// don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
    die();
}

/**
 * The filesystem path of the directory that contains the plugin, includes trailing slash.
 */
define( 'GHS_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ), false );

require_once GHS_PLUGIN_DIR_PATH . 'includes/add-on.php';
require_once GHS_PLUGIN_DIR_PATH . 'includes/form-settings.php';
