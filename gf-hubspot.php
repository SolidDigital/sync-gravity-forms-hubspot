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
    $form_id = "";

    $context = array(
        // TODO: include tracking script for cookie.
        // 'hutk' => isset($_COOKIE['hubspotutk']) ? $_COOKIE['hubspotutk'] : "",
        'ipAddress' => $entry['ip'],
        'pageUri' => $entry['source_url']
        // Other fields
        // pageName
    );

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
        // TODO: consent fields are untested.
        // 'legalConsentOptions' => array(
        //     'consent' => $consent
        // )
    ];

    $endpoint = "https://api.hsforms.com/submissions/v3/integration/secure/submit/$account_id/$form_id";

    $response = wp_remote_post($endpoint, array(
        'body' => wp_json_encode($body),
        'headers' => array(
            "Content-Type" => "application/json",
            "Authorization" => "Bearer {$token}"
        )
    ));

    // TODO: save response status on entry?

    error_log(print_r($response, true));
}

function hs_api_v3($entry, $form) {
    $form_id = rgar( $entry, '12', 'PSIExams Website' );
    $context = [
        'hutk' => $_COOKIE['hubspotutk'],
        'ipAddress' => $_SERVER['REMOTE_ADDR'],
        'pageUri' => $_SERVER['HTTP_REFERER'],
        'pageName'=> rgar( $entry, '13', 'PSIExams Website' )
    ];

// exceptions
    $no_rating = [
        // CER-US-LG-Certification Newsletter Registration Pop-Up-NOV22 (form 11)
        'b943ab56-be2f-41ff-8e28-7bfd9d550fee'
    ];

    $no_opt_in = [
        // CRE-ALL-LG-RPNow Form Demo-OCT22 (form 8)
        'dc97c173-6c46-409d-a081-b05c85d42060',

        // CER-ALL-LG-Request a Demo Online Proctoring (form 4)
        'a7a79190-ad21-42f8-83fc-e20e7c966a7a',

        // CER-ALL-LG-Request a Demo Multi-Modal Testing (form 12)
        'b53eae7d-0d41-451a-9766-f45f73b314d7',

        // CER-ALL-LG-Certification Content Download (form 1)
        '539b9c77-7d15-42eb-adff-7b5253703e20',

        // LIC-ALL-LG-Licensure Content Download (form 2)
        'd9c639ab-391a-42a1-9ea2-31bd0726186b'

    ];

    $alt_comment_fields = [
        // CER-ALL-LG-Request a Demo Online Proctoring (form 4)
        'a7a79190-ad21-42f8-83fc-e20e7c966a7a' => 'let_us_know_what_you_are_looking_for_',

        // CER-ALL-LG-Request a Demo Multi-Modal Testing (form 12)
        'b53eae7d-0d41-451a-9766-f45f73b314d7' => 'let_us_know_what_you_are_looking_for_',

        // ALL-ALL-LG-Global Support Form (form 17)
        '15ae65a2-44dd-468b-9381-7d936d0a460e' => 'how_can_we_help_you_today_'
    ];

    $fields = [
        [
            'name'=>'firstname',
            'value'=> rgar( $entry, '1', '')
        ],
        [
            'name'=>'lastname',
            'value'=> rgar( $entry, '3', '')
        ],
        [
            'name'=>'company',
            'value'=> rgar( $entry, '4', '')
        ],
        [
            'name'=>'jobtitle',
            'value'=> rgar( $entry, '5', '')
        ],
        [
            'name'=>'email',
            'value'=> rgar( $entry, '6', '')
        ],
        [
            'name'=>'phone',
            'value'=> rgar( $entry, '7', '')
        ],
        [
            'name'=>'mailing_country',
            'value'=> rgar( $entry, '8', '')
        ],
        [
            'name'=>'mailing_state_province',
            'value'=> rgar( $entry, '11', rgar( $entry, '23', ''))
        ],
        [
            'name'=> rgar( $alt_comment_fields, $form_id, 'comments'),
            'value'=> rgar( $entry, '9', '')
        ],
        [
            'name'=>'i_would_like_support_for',
            'value'=> rgar( $entry, '17', '')
        ],
        [
            'name'=>'i_am_a',
            'value'=> rgar( $entry, '18', '')
        ],
        [
            'name'=>'how_many_candidates_do_you_test_per_year_',
            'value'=> rgar( $entry, '20', '')
        ],
        [
            'name'=>'services',
            'value'=> rgar( $entry, '21', '')
        ],
        [
            'name'=>'global_contact_form_opt_in',
            'value'=> in_array($form_id, $no_opt_in) ?'' :'Yes'
        ],
        [
            'name'=>'lead_source',
            'value'=> 'Web'
        ],
        [
            'name'=>'rating',
            'value'=> in_array($form_id, $no_rating) ?'' :'Hot'
        ],
        [
            'name'=>'lead_source_description',
            'value'=> rgar( $entry, '13', '')
        ],
        [
            'name'=>'area_of_interest',
            'value'=> rgar( $entry, '14', rgar( $entry, '19', ''))
        ]
    ];


    $utm_params = [];
    $url_params = get_url_params();
    $utm_param_names = ['utm_source','utm_keyword','utm_campaign','utm_term','utm_medium','utm_keyword','google_click_id'];
    foreach ($utm_param_names as $name) {
        if ($url_params[$name]) {
            $utm_params[] = [
                'name'=>$name,
                'value'=>$url_params[$name]
            ];
        }
    }
    $fields = array_merge($fields, $utm_params);


    $legal_consent = [

        'consent' => [
            'consentToProcess' => !!empty($entry[10]),
            'subscriptionTypeId' => rgar( $entry, '15', '156595285' ),
            'text' => rgar( $entry, '16','I agree to receive updates on best practices and industry trends from PSI.')
        ]
    ];

    $postData = [
        'context' => $context,
        'fields' => $fields,
        'legalConsentOptions' => $legal_consent
    ];

    $token = 'pat-eu1-2abe5ba2-39fe-4d46-af0e-36f8f5cef399';
    $endpoint = "https://api.hsforms.com/submissions/v3/integration/secure/submit/26495755/{$form_id}";


    $ch = @curl_init();
    @curl_setopt($ch, CURLOPT_URL, $endpoint);
    @curl_setopt($ch, CURLOPT_POST, true);
    @curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    @curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: Bearer {$token}",
    ));
    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $time_pre = microtime(true);
    $response    = @curl_exec($ch); //Log the response from HubSpot as needed.
    $time_post = microtime(true);
    $exec_time = $time_post - $time_pre;
    error_log("hubspot response time: $exec_time");

    $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE); //Log the response status code
    @curl_close($ch);

    $exec_time = $time_post - $time_pre;
    error_log($status_code . " " . $response);

    return $entry;


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

function get_comments_field_title($form_id) {
    switch ($form_id) {
        case 'a7a79190-ad21-42f8-83fc-e20e7c966a7a':
            return 'let_us_know_what_you_are_looking_for_';
            break;

        case 'b53eae7d-0d41-451a-9766-f45f73b314d7':
            return 'how_can_we_help_you_today_';
            break;

        default:
            return 'comments';
            break;
    }
}
