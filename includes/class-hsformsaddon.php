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
        add_action( 'gform_form_settings_page_hsformsaddon', array( $this, 'form_sync_fields'), 10);
    }

    public function form_sync_fields() {
        
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $param_list = explode("&", $_SERVER['QUERY_STRING']);
            $params = [];
            foreach ($param_list as $param) {
                $item = explode("=", $param);
                $params[$item[0]] = $item[1];
            }

            $gf_form = GFAPI::get_form( $params['id'] );
            $hs_fields = $this->fetch_hs_fields($gf_form);
            $gf_form = $this->match_gf_fields_to_hs($gf_form, $hs_fields);
            
            GFAPI::update_form( $gf_form );
        }
    }

    // https://developers.hubspot.com/docs/api/marketing/forms
    private function fetch_hs_fields($gf_form) {
        list($token, $account_id, $form_id) = $this->get_hsforms_info($gf_form);
       
        if (!$token || !$account_id || !$form_id) {
            return;
        }
        
        $endpoint = 'https://api.hubapi.com/marketing/v3/forms/'.$form_id;
       
        $response = wp_remote_post($endpoint, array(
            'method' => 'GET',
            'headers' => array(
                "Content-Type" => "application/json",
                "Authorization" => "Bearer {$token}"
            )
        ));
        
        $response = json_decode(wp_remote_retrieve_body($response));

        if (!isset($response->fieldGroups)) {
            return;
        }
        
        return $response->fieldGroups;
    }

    private function match_gf_fields_to_hs($gf_form, $hs_fields) {
        foreach($hs_fields as $hs_field) {
            $match = array_filter($gf_form['fields'], function($gff) use ($hs_field){
                return $hs_field->fields[0]->name == $gff->hsfieldField;
            });
            count($match) > 0 ? $this->update_existing_gf_field(reset($match), $hs_field) : $gf_form['fields'][] = $this->create_new_gf_field($gf_form, $hs_field);
        }
        
        return $gf_form;
    }

    private function create_new_gf_field($gf_form, $hs_field) {
        $field = GF_Fields::create([
            'type' => $this->translate_hs_field_type($hs_field->fields[0]->fieldType),
            'id' =>  GFFormsModel::get_next_field_id($gf_form['fields']),
            'label' => $hs_field->fields[0]->label,
            'isRequired' => $hs_field->fields[0]->required,
            'hsfieldField' => $hs_field->fields[0]->name,
        ]);
        
        return $field;
    }

    private function update_existing_gf_field($gff, $hs_field) {
        $gff->type = $this->translate_hs_field_type($hs_field->fields[0]->fieldType);
        $gff->isRequired = $hs_field->fields[0]->required;
        $gff->label = $hs_field->fields[0]->label;
    }

    
    private function translate_hs_field_type($hsfieldType) {
        switch($hsfieldType) {
            case 'single_line_text':
            case 'email':
            case 'phone':
            case 'mobile_phone':
            case 'number':
                return 'text';
                break;
            case 'multi_line_text':
                return 'textarea';
                break;
            case 'single_checkbox':
                return 'checkbox';
                break;
            case 'dropdown':
                return 'select';
                break;
            case 'radio':
                return 'radio';
                break;
            case 'datepicker':
                return 'number';
                break;
            case 'multiple_checkboxes':
            case 'file':
                break;
        }
    }

    /**
     * These are global settings for the entire GF plugin - they must be filled in
     * @return array[]
     */
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
     * If the ID is not filled in for a form, that form should not be synced to HS
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
                    ),
                    array(
                        'type'     => 'save',
                        'messages' => array(
                            'error'   => esc_html__( 'Settings could not be updated.', 'sometextdomain' ),
                            'success' => esc_html__( 'Success! Settings have been updated.', 'sometextdomain' ),
                        ),
                        'value' => 'Save Settings & Sync Fields'
                    )
                )
            )
        );
    }

    private function get_hsforms_info($form) {

        // The $form_id is required to know which form in HS to push to
        // The token and ID is required to be able to authenticate
        // If any of the below are missing, we stop trying to process
        $token = $this->get_plugin_setting('hs_sync_token');
        $account_id = $this->get_plugin_setting('hs_sync_account_id');
       
        $form_id = $form['hsformsaddon']['hs_form_id'];
        return [$token, $account_id, $form_id];
    }

    public function after_submission($entry, $form) {
        list($token, $account_id, $form_id) = $this->get_hsforms_info($form);
        if (!$token || !$account_id || $form_id) {
            return;
        }

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
