<?php
/*
Plugin Name:  GF HubSpot Plugin
Plugin URI:   TBD
Description:  Wordpress plugin that integrates HubSpot with Gravity Forms
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
define( 'GFHS_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ), false );

require_once GFHS_PLUGIN_DIR_PATH . 'includes/add-on.php';
require_once GFHS_PLUGIN_DIR_PATH . 'includes/form-settings.php';
