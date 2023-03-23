<?php

GFForms::include_addon_framework();

class HSFormsAddOn extends GFAddOn {

    protected $_version = '1.0';
    protected $_min_gravityforms_version = '1.9';
    protected $_slug = 'hsformsaddon';
    protected $_path = 'hubspot-plugin-main/gf-hubspot.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Forms HubSpot Sync';
    protected $_short_title = 'HubSpot Sync';
    private static $_instance = null;

    /**
     * Get an instance of this class.
     *
     * @return HSFormsAddOn
     */
    public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new HSFormsAddOn();
        }

        return self::$_instance;
    }

    public function init() {
        parent::init();
        add_action( 'gform_after_submission', array( $this, 'after_submission' ), 10, 2 );
        add_action( 'wp_footer', array( $this, 'wp_footer'));
    }

    public function plugin_settings_fields() {
        return array(
            array(
                'title'  => 'HubSpot Sync Global Settings',
                'fields' => array(
                    array(
                        'name'              => 'hs_sync_token',
                        'label'             => 'Private App Token',
                        'type'              => 'text',
                        'class'             => 'small'
                    ),
                    array(
                        'name'              => 'hs_sync_account_id',
                        'label'             => 'Account ID',
                        'type'              => 'text',
                        'class'             => 'small'
                    )
                )
            )
        );
    }

    /**
     * Configures the settings which should be rendered on the Form Settings > Simple Add-On tab.
     *
     * @return array
     */
    public function form_settings_fields( $form ) {
        return array(
            array(
                'title' => 'HubSpot Sync Settings',
                'fields' => array(
                    array(
                        'type' => 'text',
                        'name' => 'hs_form_id',
                        'label' => 'HubSpot Form ID'
                    )
                )
            )
        );
    }

    public function after_submission($entry, $form) {

        $token = $this->get_plugin_setting('hs_sync_token');
        $account_id = $this->get_plugin_setting('hs_sync_account_id');
        $form_id = $form['hs_form_id'];

        error_log(">>>> t:$token a:$account_id");
        $context = array(
            'hutk' => isset($_COOKIE['hubspotutk']) ? $_COOKIE['hubspotutk'] : "",
            'ipAddress' => $entry['ip'],
            'pageUri' => $entry['source_url']
// pageName can go here
        );

// TODO: do we want to add utm fields from the referer.
        $fields = array();
        $consent = array();
// consent fields
// 'consentToProcess' => true/false
// 'subscriptionTypeId' => string
// 'text' => string

        foreach ( $form['fields'] as $field ) {
            $hsfield_name = false;
            $type = false;
            if (property_exists($field, 'hsfieldField') && $field->hsfieldField) {
                $hsfield_name = $field->hsfieldField;
                $type = 'field';
            }

            if (!$type) { continue; }

            $value = rgar($entry, (string) $field->id);

            $fields[] = array(
                'name' => $hsfield_name,
                'value' => $value
            );
        }

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

    public function wp_footer() {
        $account_id = $this->get_plugin_setting('hs_sync_account_id');
        if ($account_id) {
            echo <<<ENT
<!-- Start of HubSpot Embed Code -->
<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/$account_id.js"></script>
<!-- End of HubSpot Embed Code -->
ENT;
        }
    }
}
