<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=upsell-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package     woo_one_click_upsell_funnel
 * @subpackage woo_one_click_upsell_funnel/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ONBOARD_PLUGIN_NAME', 'One Click Upsell Funnel for Woocommerce' );

if ( class_exists( 'WPSwings_Onboarding_Helper' ) ) {
	$this->onboard = new WPSwings_Onboarding_Helper();
}

$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

if ( ! $id_nonce_verified ) {
	wp_die( esc_html__( 'Nonce Not verified', ' woo-one-click-upsell-funnel' ) );
}

$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'funnels-list';

if ( 'overview' === get_transient( 'wps_upsell_default_settings_tab' ) ) {

	$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'overview';
}

?>

<div class="wps-notice-wrapper">
<?php do_action( 'wps_wocuf_pro_setting_tab_active', '', '', '' ); ?>
</div>

<?php if ( ! wps_upsell_lite_elementor_plugin_active() && false === get_transient( 'wps_upsell_elementor_inactive_notice' ) ) : ?>

<div id="wps_upsell_elementor_notice" class="notice notice-info is-dismissible">
	<p><span class="wps_upsell_heading_span"><?php esc_html_e( 'We have integrated with Elementor', 'woo-one-click-upsell-funnel' ); ?></span><?php esc_html_e( ' – now the most advanced WordPress page builder can be used to completely customize Upsell Offer pages. Moreover, we provide three stunning and beautiful offer templates.', 'woo-one-click-upsell-funnel' ); ?></p>

	<p><?php esc_html_e( 'To completely utilize all features of this plugin please activate Elementor.', 'woo-one-click-upsell-funnel' ); ?></p>

	<p><?php esc_html_e( 'Elementor is FREE and available on ORG ', 'woo-one-click-upsell-funnel' ); ?><a href="https://wordpress.org/plugins/elementor/" target="_blank"><?php esc_html_e( 'here', 'woo-one-click-upsell-funnel' ); ?></a></p>

	<p><?php esc_html_e( 'You don\'t need to worry about Elementor as it works independently and won\'t conflict with other page builders or WordPress new editor.', 'woo-one-click-upsell-funnel' ); ?></p>

	<p class="submit">

		<a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' ) ); ?>" id="wps_upsell_activate_elementor" class="button" target="_blank"><?php esc_html_e( 'Install and activate Elementor now &rarr;', 'woo-one-click-upsell-funnel' ); ?></a>
		<br>
		<a id="wps_upsell_dismiss_elementor_inactive_notice" href="javascript:void(0)" class="button"><?php esc_html_e( 'Dismiss this notice', 'woo-one-click-upsell-funnel' ); ?></a>

	</p>
</div>

<?php endif; ?>

<div class="wrap woocommerce" id="wps_wocuf_pro_setting_wrapper">

	<!-- To make WordPress notice appear at this place. As it searchs from top and appears at the 1st heading tag-->
	<h1></h1>

	<div class="hide"  id="wps_wocuf_pro_loader">	
		<img id="wps-wocuf-loading-image" src="<?php echo 'images/spinner-2x.gif'; ?>" >
	</div>
	<div class="wps_wocuf_pro_header">
		<div class="wps_wocuf_pro_setting_title"><?php esc_html_e( 'One Click Upsell Funnel for WooCommerce', 'woo-one-click-upsell-funnel' ); ?></div>

		<div id="wps_upsell_skype_connect_with_us">   
			<div class="wps_upsell_skype_connect_title"><?php esc_html_e( 'Connect with Us in one click', 'woo-one-click-upsell-funnel' ); ?></div>

			<a class="button" target="_blank" href="https://join.skype.com/invite/xCmwbfxx8MCX"><img src="<?php echo esc_url( WPS_WOCUF_URL . 'admin/resources/skype_logo.png' ); ?>"><?php esc_html_e( 'Connect', 'woo-one-click-upsell-funnel' ); ?></a>

			<p><?php esc_html_e( 'Regarding any issue, query or feature request for Upsell', 'woo-one-click-upsell-funnel' ); ?></p>
		</div>
	</div>

	<?php if ( empty( get_option( 'wocuf_lite_migration_status', false ) ) ) { ?>
		<div id="wps-wocuf-thirty-days-notify" class="notice notice-error">
			<p>
				<strong>
					<?php esc_html_e( 'We have done a major changes in plugin! Please ', 'woo-one-click-upsell-funnel' ); ?>
					<a href="?page=wps-wocuf-setting&tab=funnels-list#wps_wocuf_migration_button">
						<?php esc_html_e( 'Migrate', 'woo-one-click-upsell-funnel' ); ?>
					</a>
					<?php esc_html_e( ' or you may risk losing data and the plugin will also become dysfunctional.', 'woo-one-click-upsell-funnel' ); ?>
				</strong>
			</p>
		</div>
	<?php } else { ?>
		<div id="wps-wocuf-thirty-days-notify" class="notice notice-success">
			<p>
				<strong>
					<?php esc_html_e( 'Migration was successful! If you want to reset the migration, please click here. ', 'woo-one-click-upsell-funnel' ); ?>
					<a href="?page=wps-wocuf-setting&tab=funnels-list&reset_migration=1&wocuf_nonce=<?php echo esc_attr( wp_create_nonce( 'wocuf_lite_migration' ) ); ?>">
						<?php esc_html_e( 'Reset Migration', 'woo-one-click-upsell-funnel' ); ?>
					</a>
				</strong>
			</p>
		</div>
	<?php } ?>
	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a class="nav-tab <?php echo 'creation-setting' === $active_tab ? 'nav-tab-active' : ''; ?>" href="?page=wps-wocuf-setting&tab=creation-setting"><?php esc_html_e( 'Save Funnel', 'woo-one-click-upsell-funnel' ); ?></a>
		<a class="nav-tab <?php echo 'funnels-list' === $active_tab ? 'nav-tab-active' : ''; ?>" href="?page=wps-wocuf-setting&tab=funnels-list"><?php esc_html_e( 'Funnels List', 'woo-one-click-upsell-funnel' ); ?></a>
		<a class="nav-tab <?php echo 'shortcodes' === $active_tab ? 'nav-tab-active' : ''; ?>" href="?page=wps-wocuf-setting&tab=shortcodes"><?php esc_html_e( 'Shortcodes', 'woo-one-click-upsell-funnel' ); ?></a>
		<a class="nav-tab <?php echo 'settings' === $active_tab ? 'nav-tab-active' : ''; ?>" href="?page=wps-wocuf-setting&tab=settings"><?php esc_html_e( 'Global Settings', 'woo-one-click-upsell-funnel' ); ?></a>
		<a class="nav-tab <?php echo 'overview' === $active_tab ? 'nav-tab-active' : ''; ?>" href="?page=wps-wocuf-setting&tab=overview"><?php esc_html_e( 'Overview', 'woo-one-click-upsell-funnel' ); ?></a>

		<?php do_action( 'wps_wocuf_pro_setting_tab' ); ?>	
	</nav>
	<?php

	if ( 'creation-setting' === $active_tab ) {
		include_once 'templates/wps-wocuf-pro-creation.php';
	} elseif ( 'funnels-list' === $active_tab ) {
		include_once 'templates/wps-wocuf-pro-funnels-list.php';
	} elseif ( 'shortcodes' === $active_tab ) {
		include_once 'templates/wps-wocuf-pro-shortcodes.php';
	} elseif ( 'settings' === $active_tab ) {
		include_once 'templates/wps-wocuf-pro-settings.php';
	} elseif ( 'overview' === $active_tab ) {
		include_once 'templates/wps-wocuf-overview.php';
	}

		do_action( 'wps_wocuf_pro_setting_tab_html' );
	?>
</div>
