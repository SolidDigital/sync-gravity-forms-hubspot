<?php
/**
 * GF HubSpot class.
 *
 * @category   Class
 * @package    GF Hubspot
 * @subpackage WordPress
 * @author     Solid Digital
 * @license    https://opensource.org/licenses/GPL-3.0 GPL-3.0-only
 */

if (!defined('ABSPATH')) {
    // Exit if accessed directly.
    exit;
}

final class GF_HubSpot
{
    /**
     * Plugin Version
     *
     * @since 1.0.0
     * @var string The plugin version.
     */
    const VERSION = '1.0.0';
    /**
     * Minimum Gravity Forms Version
     *
     * @since 1.0.0
     * @var string Minimum Gravity Forms version required to run the plugin.
     */
    const MINIMUM_GRAVITY_FORMS_VERSION = '2.7.2';
    /**
     * Minimum PHP Version
     *
     * @since 1.0.0
     * @var string Minimum PHP version required to run the plugin.
     */
    const MINIMUM_PHP_VERSION = '7.0';
    /**
     * Constructor
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct()
    {
        add_action('init', array($this, 'i18n'));
        add_action('plugins_loaded', array($this, 'init'));
    }
    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     * Fired by `init` action hook.
     *
     * @since 1.0.0
     * @access public
     */
    public function i18n()
    {
        load_plugin_textdomain('gfhubspot');
    }
    /**
     * Initialize the plugin
     *
     * Validates that Gravity Forms is already loaded.
     * Checks for basic plugin requirements, if one check fail don't continue,
     * if all check have passed include the plugin class.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @since 1.0.0
     * @access public
     */
    public function init()
    {
        // Check if Gravity Forms installed and activated.
        if (!is_plugin_active( 'gravityforms/gravityforms.php' )) {
            add_action('admin_notices', array($this, 'admin_notice_missing_main_plugin'));
            return;
        }
        // Check for required Gravity Forms version.
        if (!version_compare(GRAVITYFORMS_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', array($this, 'admin_notice_minimum_gravityforms_version'));
            return;
        }
        // Check for required PHP version.
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', array($this, 'admin_notice_minimum_php_version'));
            return;
        }
        // Once we get here, We have passed all validation checks so we can safely include our widgets.
        require_once 'class-widgets.php';
    }
    /**
     * Admin notice
     *
     * Warning when the site doesn't have Gravity Forms installed or activated.
     *
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_missing_main_plugin()
    {
        deactivate_plugins(plugin_basename(GF_HubSpot));
        return sprintf(
            wp_kses(
                '<div class="notice notice-warning is-dismissible"><p><strong>"%1$s"</strong> requires <strong>"%2$s"</strong> to be installed and activated.</p></div>',
                array(
                    'div' => array(
                        'class' => array(),
                        'p' => array(),
                        'strong' => array(),
                    ),
                )
            ),
            'GF HubSpot',
            'Gravity Forms'
        );
    }
    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required Gravity Forms version.
     *
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_minimum_gravityforms_version()
    {
        deactivate_plugins(plugin_basename(GF_HubSpot));
        return sprintf(
            wp_kses(
                '<div class="notice notice-warning is-dismissible"><p><strong>"%1$s"</strong> requires <strong>"%2$s"</strong> version %3$s or greater.</p></div>',
                array(
                    'div' => array(
                        'class' => array(),
                        'p' => array(),
                        'strong' => array(),
                    ),
                )
            ),
            'GF HubSpot',
            'Gravity Forms',
            self::MINIMUM_GRAVITY_FORMS_VERSION
        );
    }
    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required PHP version.
     *
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_minimum_php_version()
    {
        deactivate_plugins(plugin_basename(Firmpilot));
        return sprintf(
            wp_kses(
                '<div class="notice notice-warning is-dismissible"><p><strong>"%1$s"</strong> requires <strong>"%2$s"</strong> version %3$s or greater.</p></div>',
                array(
                    'div' => array(
                        'class' => array(),
                        'p' => array(),
                        'strong' => array(),
                    ),
                )
            ),
            'GF HubSpot',
            'PHP',
            self::MINIMUM_PHP_VERSION
        );
    }
}

new GF_HubSpot();
