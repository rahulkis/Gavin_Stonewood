<?php
/*
Plugin Name: Elfsight Cookie Consent CC
Description: Display a cookie consent bar for your visitors.
Plugin URI: https://elfsight.com/cookie-consent-widget/codecanyon/?utm_source=markets&utm_medium=codecanyon&utm_campaign=cookie-consent&utm_content=plugin-site
Version: 1.0.2
Author: Elfsight
Author URI: https://elfsight.com/?utm_source=markets&utm_medium=codecanyon&utm_campaign=cookie-consent&utm_content=plugins-list
*/

if (!defined('ABSPATH')) exit;


require_once('core/elfsight-plugin.php');

$elfsight_cookie_consent_config_path = plugin_dir_path(__FILE__) . 'config.json';
$elfsight_cookie_consent_config = json_decode(file_get_contents($elfsight_cookie_consent_config_path), true);

new ElfsightCookieConsentPlugin(
    array(
        'name' => esc_html__('Cookie Consent'),
        'description' => esc_html__('Display a cookie consent bar for your visitors.'),
        'slug' => 'elfsight-cookie-consent',
        'version' => '1.0.2',
        'text_domain' => 'elfsight-cookie-consent',
        'editor_settings' => $elfsight_cookie_consent_config['settings'],
        'editor_preferences' => $elfsight_cookie_consent_config['preferences'],
        'script_url' => plugins_url('assets/elfsight-cookie-consent.js', __FILE__),

        'plugin_name' => esc_html__('Elfsight Cookie Consent'),
        'plugin_file' => __FILE__,
        'plugin_slug' => plugin_basename(__FILE__),

        'vc_icon' => plugins_url('assets/img/vc-icon.png', __FILE__),
        'menu_icon' => plugins_url('assets/img/menu-icon.svg', __FILE__),

        'update_url' => esc_url('https://a.elfsight.com/updates/v1/'),
        'product_url' => esc_url('https://codecanyon.net/item/elfsight-cookie-consent/24049244?ref=Elfsight'),
        'support_url' => esc_url('https://elfsight.ticksy.com/submit/#100015432')
    )
);

?>
