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

// NOTE: currently does not support "advanced" GF fields.
// NOTE: assumes "required" fields  match in GF and HS.

add_action( 'gform_after_submission', 'gform_after_submission', 10, 2 );

function gform_after_submission($entry, $form) {
    // TODO: pull account info from option.

    $token = 'pat-na1-c8267d93-d087-4933-b545-cd2110ae43a0';
    $account_id = '24231628';
    $form_id = "806ef897-c280-41a3-a236-36b5e452d9db";

    $context = array(
        // TODO: include tracking script for cookie.
        // 'hutk' => isset($_COOKIE['hubspotutk']) ? $_COOKIE['hubspotutk'] : "",
        'ipAddress' => $entry['ip'],
        'pageUri' => $entry['source_url']
        // Other fields
        // pageName
    );

    // TODO: do we want to add utm fields from the referer.
    $fields = array();
    $consent = array();
    // consent fields
    // 'consentToProcess' => true/false
    // 'subscriptionTypeId' => string
    // 'text' => string

    foreach ( $form['fields'] as $field ) {
        error_log("type: ".$field->type);

        $config_raw = $field->type === 'hidden' ? $field->label : $field->cssClass;
        error_log("config_raw: ".$config_raw);

        // Get hs field config (e.g. hs_field_firstname) from the css class field.
        preg_match("/hs_.+?\b/", $config_raw, $matches);

        error_log("matches: ".print_r($matches, true));

        if (empty($matches)) continue;

        $config = explode("_", $matches[0]);

        $type = $config[1];
        $name = isset($config[2]) ? $config[2] : "";
        $value = rgar($entry, (string) $field->id);

        switch($type) {
            case 'formid':
                $form_id = $value;
                break;
            case 'field':
                $fields[] = array(
                    'name' => $name,
                    'value' => $value
                );
                break;
            case 'context':
                $context[$name] = $value;
                break;
            case 'consent':
                if ($name === "consentToProcess") {
                    $consent[$name] = !empty($value);
                } else {
                    $consent[$name] = $value;
                }
                break;
        }
    }

    error_log("formid: ".$form_id);
    if ($form_id === "") return;

    $body = [
        'context' => $context,
        'fields' => $fields,
    ];

    $endpoint = "https://api.hsforms.com/submissions/v3/integration/secure/submit/$account_id/$form_id";

    $response = wp_remote_post($endpoint, array(
        'body' => wp_json_encode($body),
        'headers' => array(
            "Content-Type" => "application/json",
            "Authorization" => "Bearer {$token}"
        )
    ));

    error_log(print_r($response, true));
}

function get_url_params() {
    $referrer_params = $_SERVER['HTTP_REFERER'];
    $referrer_params = explode("?", $referrer_params)[1];
    $referrer_params = explode("&", $referrer_params);

    $param_array = [];
    foreach($referrer_params as $param) {
        $param = explode('=', $param);
        $param_array[$param[0]] = $param[1];
    }
    $referrer_params = $param_array;

    return $referrer_params;

}
