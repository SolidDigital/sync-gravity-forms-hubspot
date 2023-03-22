<?php
// don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
    die();
}

add_action( 'gform_field_standard_settings', 'my_standard_settings', 10, 2 );
function my_standard_settings( $position, $form_id ) {

    // $position is where on settings are the field is displayed - see form_detail.php in the GF plugin - search for gform_field_standard_settings
    if ( $position == 5 ) {
        // the setting name (hsfield) has to match the name in the other callbacks
        ?>
        <li class="hsfield_setting field_setting">
            <input type="text" id="field_hsfield_value" onchange="SetFieldProperty('hsfieldField', this.value);" />
            <label for="field_hsfield_value" style="display:inline;">
                <?php _e("HubSpot Field Name", "your_text_domain"); ?>
                <?php gform_tooltip("form_field_hsfield_value") ?>
            </label>
        </li>
        <?php
    }
}
//Action to inject supporting script to the form editor page
add_action( 'gform_editor_js', 'editor_script' );
function editor_script(){
    ?>
    <script type='text/javascript'>
        //adding setting to fields of type "text"
        fieldSettings.email += ', .hsfield_setting';
        fieldSettings.hidden += ', .hsfield_setting';
        fieldSettings.text += ', .hsfield_setting';
        fieldSettings.textarea += ', .hsfield_setting';
        //binding to the load field settings event to initialize the text field
        jQuery(document).on('gform_load_field_settings', function(event, field, form){
            jQuery( '#field_hsfield_value' ).val( field['hsfieldField'] );
        });
    </script>
    <?php
}
//Filter to add a new tooltip
add_filter( 'gform_tooltips', 'add_encryption_tooltips' );
function add_encryption_tooltips( $tooltips ) {
    $tooltips['form_field_hsfield_value'] = "<h6>HubSpot Field Name</h6>Enter the field name used in HubSpot";
    return $tooltips;
}
