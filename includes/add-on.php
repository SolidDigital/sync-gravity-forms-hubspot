<?php
namespace Sync_Solid;

// don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
    die();
}

add_action( 'gform_loaded', __NAMESPACE__ . '\\load_hsfields_addon', 5 );

function load_hsfields_addon() {
    if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
        return;
    }

    require_once GHS_PLUGIN_DIR_PATH . 'includes/class-hsformsaddon.php';
    \GFAddOn::register( 'HSFormsAddOn');
}
