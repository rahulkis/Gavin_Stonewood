<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              #
 * @since             1.0.0
 * @package           WC_Sales_Up
 *
 * @wordpress-plugin
 * Plugin Name:       Sales UP for WooCommerce
 * Description:       Sales UP for WooCommerce is a WooCommerce extension that helps you increase the revenue of every single order by suggesting Up-selling and Cross-selling products with discount to customers.
 * Version:           1.0.3
 * Author:            Ritte Vile
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-sales-up
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SALES_UP_WC_VERSION', '1.0.3' );
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wsp_plugin_links' );

/**
 * Display links
 *
 * @param html $links links.
 */
function wsp_plugin_links( $links ) {
	$action_links = array(
		'settings' => '<a href="' . esc_url( admin_url( 'admin.php?page=woo_on_product' ) ) . '" title="' . esc_attr__( 'View Settings', 'sales-up-pro' ) . '">' . esc_html__( 'Settings', 'sales-up-pro' ) . '</a>',
	);
	$links        = array_merge( $action_links, $links );
	return $links;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wc-sales-up-activator.php
 */
function wsp_activate() {
	if ( is_plugin_active( 'sales-up-pro/sales-up-pro.php' ) ) {
		deactivate_plugins( '/sales-up-pro/sales-up-pro.php' );
	}
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-sales-up-activator.php';
	WC_Sales_Up_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wc-sales-up-deactivator.php
 */
function wsp_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-sales-up-deactivator.php';
	WC_Sales_Up_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'wsp_activate' );
register_deactivation_hook( __FILE__, 'wsp_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wc-sales-up.php';
require plugin_dir_path( __FILE__ ) . 'includes/functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function wsp_run() {

	$plugin = new WC_Sales_Up();
	$plugin->run();

}
wsp_run();

if(!function_exists('wsp_is_wc_active')) {
	/**
	 *
	 * Check is WooCommerce Plugin Activated.
	 *
	 * @return void
	 */
	function wsp_is_wc_active() {
		if ( is_admin() && current_user_can( 'activate_plugins' ) && ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'admin_notices', 'wsp_activation_error_msg' );
			deactivate_plugins( plugin_basename( __FILE__ ) );
			if ( isset( $_POST['nonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
				if ( ! wp_verify_nonce( $nonce, 'activate-nonce' ) ) {
					wp_send_json_error( array( 'status' => 'Nonce error' ) );
					die();
				}
			}
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}
	add_action( 'admin_init', 'wsp_is_wc_active' );
}

if(!function_exists('wsp_detect_deactivate_wc')) {
	/**
	 *  Deactivation plugin when WooCommerce deactivate.
	 *
	 * @param string  $plugin             plugin name.
	 * @param boolean $network_activation is network activation true or false.
	 * @return void
	 */
	function wsp_detect_deactivate_wc( $plugin, $network_activation ) {
		if ( 'woocommerce/woocommerce.php' === $plugin) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
	}
	add_action( 'deactivated_plugin', 'wsp_detect_deactivate_wc', 10, 2 );
}

if(!function_exists('wsp_activation_error_msg')) {
	/**
	 *
	 * Error Notice if WooCommerce plugin not activated.
	 *
	 * @return void
	 */
	function wsp_activation_error_msg() { ?>
		<div class="error">
			<p>
			<?php
			printf( esc_html( '%s' ), '<a href="' . esc_url( admin_url( 'plugin-install.php?tab=search&s=woocommerce' ) ) . '"><b>WooCommerce</b></a>' );
			esc_html_e( ' must be installed and activated for the Sales Up for WooCommerce plugin to work.', 'wc-sales-up' );
			?>
			</p>
		</div>
		<?php
	}
}


if ( ! function_exists( 'wsp_wc_plugin' ) ) {
	/**
	 * Active woocommerce plugin
	 *
	 * @return boolean
	 */
	function wsp_wc_plugin() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return true;
		} else {
			return false;
		}
	}
	add_action( 'init', 'wsp_wc_plugin' );
}