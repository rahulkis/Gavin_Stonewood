<?php
/**
 * Plugin Name: Checkout Field Editor - Custom Fields WooCommerce Checkout Page
 * Description: Customize WooCommerce checkout and my account page edit woocommerce checkout fields (Add, Edit, Delete and re-arrange fields). best checkout fields editor plugin for woocommerce.
 * Author:      Jcodex
 * Version:     2.0.5
 * Author URI:  https://www.jcodex.com
 * Plugin URI:  https://jcodex.com/plugins/woocommerce-custom-checkout-field-editor/
 * Text Domain: jwcfe
 * Domain Path: /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 7.6.0
 *
 * Copyright (C) 2018-2023 Jcodex Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */



if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

if(!defined( 'ABSPATH' )) exit;


if (!function_exists('jwcfe_is_woocommerce_active')){
	function jwcfe_is_woocommerce_active(){
	    $active_plugins = (array) get_option('active_plugins', array());
	    if(is_multisite()){
		   $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
	    }
	    return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
	}
}


if(jwcfe_is_woocommerce_active()) {

	!defined('JWCFE_BASE_NAME') && define('JWCFE_BASE_NAME', plugin_basename( __FILE__ ));

	load_plugin_textdomain( 'jwcfe', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/**
	 * woocommerce_init_checkout_field_editor function.
	 */
	function jwcfe_init_checkout_field_editor_lite() {
		global $supress_field_modification;
		$supress_field_modification = false;
		
		 define('JWCFE_VERSION', '1.2.3');
		!defined('JWCFE_URL') && define('JWCFE_URL', plugins_url( '/', __FILE__ ));
		!defined('JWCFE_ASSETS_URL') && define('JWCFE_ASSETS_URL', JWCFE_URL . 'assets/');

		if(!class_exists('JWCFE_WC_Checkout_Field_Editor')){
			require_once('classes/class-jwcfe-wc-checkout-field-editor.php');
		}

		if (!class_exists('JWCFE_WC_Checkout_Field_Editor_Export_Handler')){
			require_once('classes/class-jwcfe-wc-checkout-field-editor-export-handler.php');
		}
		new JWCFE_WC_Checkout_Field_Editor_Export_Handler();

		$GLOBALS['JWCFE_WC_Checkout_Field_Editor'] = new JWCFE_WC_Checkout_Field_Editor();
	}
	add_action('init', 'jwcfe_init_checkout_field_editor_lite');
	
	function jwcfe_is_locale_field( $field_name ){
		if(!empty($field_name) && in_array($field_name, array(
			'billing_address_1', 'billing_address_2', 'billing_state', 'billing_postcode', 'billing_city',
			'shipping_address_1', 'shipping_address_2', 'shipping_state', 'shipping_postcode', 'shipping_city',
		))){
			return true;
		}
		return false;
	}
	 
	function jwcfe_woocommerce_version_check( $version = '3.0' ) {
	  	if(function_exists( 'jwcfe_is_woocommerce_active' ) && jwcfe_is_woocommerce_active() ) {
			global $woocommerce;
			if( version_compare( $woocommerce->version, $version, ">=" ) ) {
		  		return true;
			}
	  	}
	  	return false;
	}
	
	function jwcfe_enqueue_scripts(){	
		global $wp_scripts;

		if(is_checkout()){
			$in_footer = apply_filters( 'jwcfe_enqueue_script_in_footer', true );

			wp_register_script('jwcfe-field-editor-script', JWCFE_ASSETS_URL.'js/jwcfe-checkout-field-editor-frontend.js', 
			array('jquery', 'select2'), JWCFE_VERSION, $in_footer);
			
			wp_enqueue_script('jwcfe-field-editor-script');	
		}
	}
	add_action('wp_enqueue_scripts', 'jwcfe_enqueue_scripts');
	
	
	function jwcfe_admin_scripts( $hook ) {


    if ( $hook == 'user-edit.php' ) {
       wp_enqueue_script( 'wc-checkout-editor', plugin_dir_url( __FILE__ ) . 'assets/js/checkout.js', array( 'jquery', 'jquery-ui-datepicker' ), '', true );
    }
}
add_action( 'admin_enqueue_scripts', 'jwcfe_admin_scripts', 10, 1 );

	
	/**
	 * Hide Additional Fields title if no fields available.
	 *
	 * @param mixed $old
	 */
	function jwcfe_enable_order_notes_field() {
		global $supress_field_modification;

		if($supress_field_modification){
			return $fields;
		}

		$additional_fields = get_option('wc_fields_additional');
		if(is_array($additional_fields)){
			$enabled = 0;
			foreach($additional_fields as $field){
				if($field['enabled']){
					$enabled++;
				}
			}
			return $enabled > 0 ? true : false;
		}
		return true;
	}
	add_filter('woocommerce_enable_order_notes_field', 'jwcfe_enable_order_notes_field', 1000);
		
	function jwcfe_woo_default_address_fields( $fields ) {
		$sname = apply_filters('jwcfe_address_field_override_with', 'billing');
		
		if($sname === 'billing' || $sname === 'shipping'){
			$address_fields = get_option('wc_fields_'.$sname);
			
			if(is_array($address_fields) && !empty($address_fields) && !empty($fields)){
				$override_required = apply_filters( 'jwcfe_address_field_override_required', true );
				
				foreach($fields as $name => $field) {
					$fname = $sname.'_'.$name;
					
					if(jwcfe_is_locale_field($fname) && $override_required){
						$custom_field = isset($address_fields[$fname]) ? $address_fields[$fname] : false;
						
						if($custom_field && !( isset($custom_field['enabled']) && $custom_field['enabled'] == false )){
							$fields[$name]['required'] = isset($custom_field['required']) && $custom_field['required'] ? true : false;
						}
					}
				}
			}
		}
		
		return $fields;
	}	
	add_filter('woocommerce_default_address_fields' , 'jwcfe_woo_default_address_fields' );
	
	function jwcfe_prepare_country_locale($fields) {
		if(is_array($fields)){
			foreach($fields as $key => $props){
				$override_ph = apply_filters('jwcfe_address_field_override_placeholder', true);
				$override_label = apply_filters('jwcfe_address_field_override_label', true);
				$override_required = apply_filters('jwcfe_address_field_override_required', false);
				$override_priority = apply_filters('jwcfe_address_field_override_priority', true);
				
				if($override_ph && isset($props['placeholder'])){
					unset($fields[$key]['placeholder']);
				}
				if($override_label && isset($props['label'])){
					unset($fields[$key]['label']);
				}
				if($override_required && isset($props['required'])){
					unset($fields[$key]['required']);
				}
				
				if($override_priority && isset($props['priority'])){
					unset($fields[$key]['priority']);
					//unset($fields[$key]['order']);
				}
			}
		}
		return $fields;
	} 
	add_filter('woocommerce_get_country_locale_default', 'jwcfe_prepare_country_locale');
	add_filter('woocommerce_get_country_locale_base', 'jwcfe_prepare_country_locale');
	
	function jwcfe_woo_get_country_locale($locale) {
		if(is_array($locale)){
			foreach($locale as $country => $fields){
				$locale[$country] = jwcfe_prepare_country_locale($fields);
			}
		}
		return $locale;
	}
	add_filter('woocommerce_get_country_locale', 'jwcfe_woo_get_country_locale');
	
	
	/**
	 * wc_checkout_fields_modify_billing_fields function.
	 *
	 * @param mixed $fields
	 */
	function jwcfe_billing_fields_lite($fields, $country){
		global $supress_field_modification;

		if($supress_field_modification){
			return $fields;
		}
		if(is_wc_endpoint_url('edit-address')){
			return $fields;
		}else{
			
				return jwcfe_prepare_address_fields(get_option('wc_fields_billing'), $fields, 'billing', $country);
			
			
		}
	}
	add_filter('woocommerce_billing_fields', 'jwcfe_billing_fields_lite', 1000, 2);


	/**
	 * wc_checkout_fields_modify_shipping_fields function.
	 *
	 * @param mixed $old
	 */
	function jwcfe_shipping_fields_lite($fields, $country){
		global $supress_field_modification;

		if ($supress_field_modification){
			return $fields;
		}
		if(is_wc_endpoint_url('edit-address')){
			return $fields;
		}else{
			return jwcfe_prepare_address_fields(get_option('wc_fields_shipping'), $fields, 'shipping', $country);
		}
	}
	add_filter('woocommerce_shipping_fields', 'jwcfe_shipping_fields_lite', 1000, 2);

	/**
	 * wc_checkout_fields_modify_shipping_fields function.
	 *
	 * @param mixed $old
	 */
	function jwcfe_checkout_fields_lite( $fields ) {
		
		global $supress_field_modification;

		if($supress_field_modification){
			return $fields;
		}

		if($additional_fields = get_option('wc_fields_additional')){
			if( isset($fields['order']) && is_array($fields['order']) ){
				$fields['order'] = $additional_fields + $fields['order'];
			}

			// check if order_comments is enabled/disabled
			if(is_array($additional_fields) && !$additional_fields['order_comments']['enabled']){
				unset($fields['order']['order_comments']);
			}
		}
				
		if(isset($fields['order']) && is_array($fields['order'])){
			$fields['order'] = jwcfe_prepare_checkout_fields_lite($fields['order'], false);
		}

		if(isset($fields['order']) && !is_array($fields['order'])){
			unset($fields['order']);
		}

		return $fields;
	}
	add_filter('woocommerce_checkout_fields', 'jwcfe_checkout_fields_lite', apply_filters('jwcfe_checkout_fields_priority', 1000));

	
	/**
	 *
	 */
	function jwcfe_prepare_address_fields($fieldset, $original_fieldset = false, $sname = 'billing', $country){
		if(is_array($fieldset) && !empty($fieldset)) {
			$locale = WC()->countries->get_country_locale();
			if(isset($locale[ $country ]) && is_array($locale[ $country ])) {

				foreach($locale[ $country ] as $key => $value){
					
					if(is_array($value) && isset($fieldset[$sname.'_'.$key])){
						if(isset($value['required'])){
							$fieldset[$sname.'_'.$key]['required'] = $value['required'];
						}
					}
					
				}
			}
		
			
			$fieldset = jwcfe_prepare_checkout_fields_lite($fieldset, $original_fieldset, $sname);

			return $fieldset;
		}else {

			return $original_fieldset;
		}
	}
	
	

	/**
	 * checkout_fields_modify_fields function.
	 *
	 * @param mixed $data
	 * @param mixed $old
	 */
	 function jwcfe_prepare_checkout_fields_lite($fields, $original_fields, $sname = "") {
	
		  
		if(is_array($fields) && !empty($fields)) {
			foreach($fields as $name => $field) {
				if(isset($field['enabled']) && $field['enabled'] == false ) {
					unset($fields[$name]);
				}else{
					$new_field = false;
					
					if($original_fields && isset($original_fields[$name])){
						$new_field = $original_fields[$name];
						
						$new_field['label'] = isset($field['label']) ? $field['label'] : '';
						$new_field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';
						
						$new_field['class'] = isset($field['class']) && is_array($field['class']) ? $field['class'] : array();
						$new_field['label_class'] = isset($field['label_class']) && is_array($field['label_class']) ? $field['label_class'] : array();
						$new_field['validate'] = isset($field['validate']) && is_array($field['validate']) ? $field['validate'] : array();
						
						$new_field['required'] = isset($field['required']) ? $field['required'] : 0;
						$new_field['clear'] = isset($field['clear']) ? $field['clear'] : 0;
					}else{
						$new_field = $field;
					}
					
					if(isset($new_field['type']) && $new_field['type'] === 'select'){
						if(apply_filters('jwcfe_enable_select2_for_select_fields', true)){
							$new_field['input_class'][] = 'jwcfe-enhanced-select';
						}
					}

					$new_field['order'] = isset($field['order']) && is_numeric($field['order']) ? $field['order'] : 0;
					if(isset($new_field['order']) && is_numeric($new_field['order'])){
						$priority = ($new_field['order']+1)*10;
						$new_field['priority'] = $priority;
						//$new_field['priority'] = $new_field['order'];
					}
					
					
					$fields[$name] = $new_field;
				}
			}								
			return $fields;
		}else {
			return $original_fields;
		}
	}
	
	/*****************************************
	 ----- Display Field Values - START ------
	 *****************************************/
	
	/**
	 * Display custom fields in emails
	 *
	 * @param array $keys
	 * @return array
	 */
	function jwcfe_display_custom_fields_in_emails_lite($order, $sent_to_admin, $plain_text){
		$fields_html = '';
		$value_check = false;
		if(get_option( 'jwcfe_account_sync_fields') && get_option( 'jwcfe_account_sync_fields') == "on"){
				
				
				$fields = array_merge(JWCFE_WC_Checkout_Field_Editor::get_fields('account'), JWCFE_WC_Checkout_Field_Editor::get_fields('billing'), JWCFE_WC_Checkout_Field_Editor::get_fields('shipping'), 
		JWCFE_WC_Checkout_Field_Editor::get_fields('additional'));
		
			}
			else{
				$fields = array_merge(JWCFE_WC_Checkout_Field_Editor::get_fields('billing'), JWCFE_WC_Checkout_Field_Editor::get_fields('shipping'), 
		JWCFE_WC_Checkout_Field_Editor::get_fields('additional'));
			}
			
		if($plain_text === false){
			$fields_html .=  '<h2>'.esc_html('Checkout Fields','jwcfe').'</h2>';
			$fields_html .= '<table border="1" style="border: solid 1px; width: 100%; margin-bottom: 10px;">';
		}
		
		// Loop through all custom fields to see if it should be added
		foreach( $fields as $key => $options ) {
			if(isset($options['show_in_email']) && $options['show_in_email']){
				$value = '';
				if(jwcfe_woo_version_check()){
				if($options['type'] == 'select'){
						$value = get_post_meta( $order->get_id(), $key, true );
						if(is_array($value)){
							$value = implode(",",$value);
						}
						else{
							$value = get_post_meta( $order->get_id(), $key, true );
						}
						
					}else{
						$value = get_post_meta( $order->get_id(), $key, true );
					}
					
				}else{
					if($options['type'] == 'select'){
						$value = get_post_meta( $order->id, $key, true );
						if(is_array($value)){
							$value = implode(",",$value);
						}
						else{
							$value = get_post_meta( $order->id, $key, true );
						}
						
					}else{
						$value = get_post_meta( $order->id, $key, true );
					}
					
					
					
				}
				

				
				if(!empty($value)){
					$value_check = true;
					$label = isset($options['label']) && $options['label'] ? $options['label'] : $key;
					$label = esc_attr($label);
					if($plain_text === false){
						$fields_html .= '<tr><td><strong>'.$label.': </strong></td><td>'.$value.'</td></tr>';
					}else{
						$fields_html .= $label .':'.$value;
					}
					
					
				}
			}
		}

		if($plain_text === false){
						$fields_html .= '</table>';
					}
					
					if($value_check){
						$allowtags = array( 'h2' => array(), 'table' => array(), 'tr' => array(), 'td' => array(), 'strong' => array(), 'br' => array(), 'th' => array(), 'p' => array() );

						echo wp_kses($fields_html,$allowtags);
					}
					
	}	
	//add_filter('woocommerce_email_order_meta_fields', 'jwcfe_display_custom_fields_in_emails_lite', 10, 3);
	add_action( 'woocommerce_email_order_meta', 'jwcfe_display_custom_fields_in_emails_lite', 10, 3 );
	/**
	 * Display custom checkout fields on view order pages
	 *
	 * @param  object $order
	 */

	/**
	 * Display field value on the order edit page
	 */
	add_action( 'woocommerce_admin_order_data_after_billing_address', 'jwcfe_checkout_field_display_admin_order_meta_billing', 10, 1 );
	

	function jwcfe_checkout_field_display_admin_order_meta_billing($order){

		if(jwcfe_woocommerce_version_check()){
			$order_id = $order->get_id();	
		}else{
			$order_id = $order->id;
		}

		$fields = array();		
		$fields = JWCFE_WC_Checkout_Field_Editor::get_fields('billing');
		$fields_html = '';
		if(is_array($fields) && !empty($fields)){
			// Loop through all custom fields to see if it should be added
			foreach($fields as $name => $options){
				
				$enabled = (isset($options['enabled']) && $options['enabled'] == false) ? false : true;
				$is_custom_field = (isset($options['custom']) && $options['custom'] == true) ? true : false;
			     
				if(isset($options['show_in_order']) && $options['show_in_order'] && $enabled && $is_custom_field){
					if($options['type'] == 'select'){
						$value = get_post_meta($order_id,$name,true);
						if(is_array($value)){
							$value = implode(",",$value);
						}
						else{
							$value = get_post_meta($order_id,$name,true);
						}
						
					}else{
						$value = get_post_meta($order_id,$name,true);
					}
					
					if(!empty($value)){
						$label = isset($options['label']) && !empty($options['label']) ? __( $options['label'], 'jwcfe' ) : $name;
						$fields_html .= '<p><strong>'.__($label,'jwcfe').':</strong> <br/>' . $value . '</p>';
					}

					


				}

			}//end of fields loop

			$allowtags = array( 'h2' => array(), 'table' => array(), 'tr' => array(), 'td' => array(), 'strong' => array(), 'br' => array(), 'th' => array(), 'p' => array() );
			echo wp_kses($fields_html,$allowtags);
		}


	    
	}

	add_action( 'woocommerce_admin_order_data_after_shipping_address', 'jwcfe_checkout_field_display_admin_order_meta_shipping', 10, 1 );



	function jwcfe_checkout_field_display_admin_order_meta_shipping($order){


		if(jwcfe_woocommerce_version_check()){
			$order_id = $order->get_id();	
		}else{
			$order_id = $order->id;
		}

		$fields = array();
		if(!wc_ship_to_billing_address_only() && $order->needs_shipping_address()){
			$fields = array_merge(JWCFE_WC_Checkout_Field_Editor::get_fields('shipping'), JWCFE_WC_Checkout_Field_Editor::get_fields('additional'));
		}
		$fields_html = '';
		if(is_array($fields) && !empty($fields)){
			// Loop through all custom fields to see if it should be added
			foreach($fields as $name => $options){
				
				$enabled = (isset($options['enabled']) && $options['enabled'] == false) ? false : true;
				$is_custom_field = (isset($options['custom']) && $options['custom'] == true) ? true : false;
			     
				if(isset($options['show_in_order']) && $options['show_in_order'] && $enabled && $is_custom_field){

					if($options['type'] == 'select'){
						$value = get_post_meta($order_id,$name,true);
						if(is_array($value)){
							$value = implode(",",$value);
						}
						else{
							$value = get_post_meta($order_id,$name,true);
						}
						
					}else{

						$value = get_post_meta($order_id,$name,true);
					}
					
					if(!empty($value)){
						$label = isset($options['label']) && !empty($options['label']) ? __( $options['label'], 'jwcfe' ) : $name;
						$fields_html .= '<p><strong>'.__($label,'jwcfe').':</strong> <br/>' . $value . '</p>';
					}



				}

			}//end of fields loop


			$allowtags = array( 'h2' => array(), 'table' => array(), 'tr' => array(), 'td' => array(), 'strong' => array(), 'br' => array(), 'th' => array(), 'p' => array() );
			echo wp_kses($fields_html,$allowtags);
		}


	    
	}



	add_filter('woocommerce_checkout_get_value', 'jwcfe_populate_custom_checkout_field', 10, 2);

	function jwcfe_populate_custom_checkout_field($value, $input) {



		$fields = array();

		if(get_option( 'jwcfe_account_sync_fields') && get_option( 'jwcfe_account_sync_fields') == "on"){
			
			$fields = array_merge(JWCFE_WC_Checkout_Field_Editor::get_fields('account'), JWCFE_WC_Checkout_Field_Editor::get_fields('billing'), JWCFE_WC_Checkout_Field_Editor::get_fields('additional'));
		}
		else{
			$fields = array_merge(JWCFE_WC_Checkout_Field_Editor::get_fields('billing'), JWCFE_WC_Checkout_Field_Editor::get_fields('additional'));
		}

		if(is_array($fields) && !empty($fields)){
				foreach($fields as $name => $options){
					$enabled = (isset($options['enabled']) && $options['enabled'] == false) ? false : true;
				$is_custom_field = (isset($options['custom']) && $options['custom'] == true) ? true : false;
				if($enabled && $is_custom_field){
					if ($input === $name && !empty(WC()->session->get($name))) {
				        return WC()->session->get($name);
				    }
				}
				}

			}

	    return $value;
	}


	function jwcfe_order_details_after_customer_details_lite($order){
		if(jwcfe_woocommerce_version_check()){
			$order_id = $order->get_id();	
		}else{
			$order_id = $order->id;
		}
		
		
		$fields = array();		
		if(!wc_ship_to_billing_address_only() && $order->needs_shipping_address()){
			
			if(get_option( 'jwcfe_account_sync_fields') && get_option( 'jwcfe_account_sync_fields') == "on"){
			$fields = array_merge(JWCFE_WC_Checkout_Field_Editor::get_fields('account'), JWCFE_WC_Checkout_Field_Editor::get_fields('billing'), JWCFE_WC_Checkout_Field_Editor::get_fields('shipping'), 
			JWCFE_WC_Checkout_Field_Editor::get_fields('additional'));
			}
			else{
				$fields = array_merge(JWCFE_WC_Checkout_Field_Editor::get_fields('billing'), JWCFE_WC_Checkout_Field_Editor::get_fields('shipping'), 
			JWCFE_WC_Checkout_Field_Editor::get_fields('additional'));
			}
		}else{
			
			if(get_option( 'jwcfe_account_sync_fields') && get_option( 'jwcfe_account_sync_fields') == "on"){
				
				$fields = array_merge(JWCFE_WC_Checkout_Field_Editor::get_fields('account'), JWCFE_WC_Checkout_Field_Editor::get_fields('billing'), JWCFE_WC_Checkout_Field_Editor::get_fields('additional'));
			}
			else{
				$fields = array_merge(JWCFE_WC_Checkout_Field_Editor::get_fields('billing'), JWCFE_WC_Checkout_Field_Editor::get_fields('additional'));
			}


			
		}

		
		if(is_array($fields) && !empty($fields)){
			
			$fields_html = '';
			// Loop through all custom fields to see if it should be added
			foreach($fields as $name => $options){

			
				
				$enabled = (isset($options['enabled']) && $options['enabled'] == false) ? false : true;
				$is_custom_field = (isset($options['custom']) && $options['custom'] == true) ? true : false;
			     
				if(isset($options['show_in_order']) && $options['show_in_order'] && $enabled && $is_custom_field){
					if($options['type'] == 'select'){
						$value = get_post_meta($order_id, $name, true);
						if(is_array($value)){
							$value = implode(",",$value);
						}
						else{
							$value = get_post_meta($order_id, $name, true);
						}
						
					}else{
						$value = get_post_meta($order_id, $name, true);
					}
					
					if(!empty($value)){
						$label = isset($options['label']) && !empty($options['label']) ? __( $options['label'], 'jwcfe' ) : $name;
						
						if(is_account_page()){
							if(apply_filters( 'jwcfe_view_order_customer_details_table_view', true )){
								
								$fields_html .= '<tr><th>'. esc_attr($label) .':</th><td>'. wptexturize($value) .'</td></tr>';
							}else{
								
								$fields_html .= '<br/><dt>'. esc_attr($label) .':</dt><dd>'. wptexturize($value) .'</dd>';
							}
						}else{
							
							if(apply_filters( 'jwcfe_thankyou_customer_details_table_view', true )){
								$fields_html .= '<tr><th>'. esc_attr($label) .':</th><td>'. wptexturize($value) .'</td></tr>';
							}else{
								$fields_html .= '<br/><dt>'. esc_attr($label) .':</dt><dd>'. wptexturize($value) .'</dd>';
							}
						}
					}
				}
			}
			
			if($fields_html && !empty($fields_html)){
				do_action( 'jwcfe_order_details_before_custom_fields_table', $order ); 
				?>
				<h2 class="woocommerce-order-details__title"><?php esc_html_e('Checkout Fields','jwcfe'); ?></h2>
				<table class="woocommerce-table woocommerce-table--custom-fields shop_table custom-fields">
					<?php
						$allowtags = array( 'h2' => array(), 'table' => array(), 'tr' => array(), 'td' => array(), 'strong' => array(), 'br' => array(), 'th' => array(), 'p' => array() );

						echo wp_kses($fields_html,$allowtags);

					?>
				</table>
				<?php
				do_action( 'jwcfe_order_details_after_custom_fields_table', $order ); 
			}
		}
	}
	
	/**
	 * Register meta box(es).
	 */
	function jwcfe_register_order_meta_boxes() {
		add_meta_box( 'jwcfe-custom-order-box', __( 'Checkout Fields', 'jwcfe' ), 'jwcfe_orderbox_display_callback', 'shop_order', 'normal', 'high' );
	}
	//add_action( 'add_meta_boxes', 'jwcfe_register_order_meta_boxes' );
	 
	/**
	 * Meta box display callback.
	 *
	 * @param WP_Post $post Current post object.
	 */
	function jwcfe_orderbox_display_callback( $post ) {
		
		// Display code/markup goes here. Don't forget to include nonces!
		 $order = new WC_Order( $post->ID );
		
		if(jwcfe_woocommerce_version_check()){
			$order_id = $order->get_id();	
		}else{
			$order_id = $order->id;
		}
		
		
		$fields = array();		
		if(!wc_ship_to_billing_address_only() && $order->needs_shipping_address()){
			
			if(get_option( 'jwcfe_account_sync_fields') && get_option( 'jwcfe_account_sync_fields') == "on"){
			$fields = array_merge(JWCFE_WC_Checkout_Field_Editor::get_fields('account'), JWCFE_WC_Checkout_Field_Editor::get_fields('billing'), JWCFE_WC_Checkout_Field_Editor::get_fields('shipping'), 
			JWCFE_WC_Checkout_Field_Editor::get_fields('additional'));
			}
			else{
				$fields = array_merge(JWCFE_WC_Checkout_Field_Editor::get_fields('billing'), JWCFE_WC_Checkout_Field_Editor::get_fields('shipping'), 
			JWCFE_WC_Checkout_Field_Editor::get_fields('additional'));
			}
		}else{
			
			if(get_option( 'jwcfe_account_sync_fields') && get_option( 'jwcfe_account_sync_fields') == "on"){
				
				$fields = array_merge(JWCFE_WC_Checkout_Field_Editor::get_fields('account'), JWCFE_WC_Checkout_Field_Editor::get_fields('billing'), JWCFE_WC_Checkout_Field_Editor::get_fields('additional'));
			}
			else{
				$fields = array_merge(JWCFE_WC_Checkout_Field_Editor::get_fields('billing'), JWCFE_WC_Checkout_Field_Editor::get_fields('additional'));
			}
			
			
		}
		
		
		if(is_array($fields) && !empty($fields)){
			
			$fields_html = '';
			// Loop through all custom fields to see if it should be added
			foreach($fields as $name => $options){

			
				
				$enabled = (isset($options['enabled']) && $options['enabled'] == false) ? false : true;
				$is_custom_field = (isset($options['custom']) && $options['custom'] == true) ? true : false;
			     
				if(isset($options['show_in_order']) && $options['show_in_order'] && $enabled && $is_custom_field){
					
					if($options['type'] == 'select'){
						$value = get_post_meta($order_id, $name, true);
						if(is_array($value)){
							$value = implode(",",$value);
						}
						else{
							
							$value = get_post_meta($order_id, $name, true);
						
						}
						
					}else{
						
						$value = get_post_meta($order_id, $name, true);
					}
					
					if(!empty($value)){
						$label = isset($options['label']) && !empty($options['label']) ? __( $options['label'], 'jwcfe' ) : $name;
						
						if(is_account_page()){
							
								
								$fields_html .= '<tr><th style="text-align:left; width:50%">'. esc_attr($label) .':</th><td style="text-align:left; width:50%">'. wptexturize($value) .'</td></tr>';
							
						}else{
							
							
								$fields_html .= '<tr><th style="text-align:left; width:50%">'. esc_attr($label) .':</th><td style="text-align:left; width:50%">'. wptexturize($value) .'</td></tr>';
							
						}
					}
				}
			}
			
			if($fields_html){
				
				?>
				
				<table width="100%" class="woocommerce-table woocommerce-table--custom-fields shop_table custom-fields">
					<?php
						$allowtags = array( 'h2' => array(), 'table' => array(), 'tr' => array(), 'td' => array(), 'strong' => array(), 'br' => array(), 'th' => array(), 'p' => array() );

						echo wp_kses($fields_html,$allowtags);
					?>
				</table>
				<?php
				
			}
		}
	}
	 
	/**
	 * Save meta box content.
	 *
	 * @param int $post_id Post ID
	 */
	function jwcfe_save_order_meta_box( $post_id ) {
		// Save logic goes here. Don't forget to include nonce checks!
	}
	add_action( 'save_post', 'jwcfe_save_order_meta_box' );

	
	add_action('woocommerce_order_details_after_order_table', 'jwcfe_order_details_after_customer_details_lite', 20, 1);
	
	/*****************************************
	 ----- Display Field Values - END --------
	 *****************************************/

	function jwcfe_woo_version_check( $version = '3.0' ) {
	  	if(function_exists( 'jwcfe_is_woocommerce_active' ) && jwcfe_is_woocommerce_active() ) {
			global $woocommerce;
			if( version_compare( $woocommerce->version, $version, ">=" ) ) {
		  		return true;
			}
	  	}
	  	return false;
	}
	 
}



register_activation_hook( __FILE__, 'jwcfe_activate' );
add_action( 'admin_init', 'jwcfe_activation_redirect' );

/**
 * Plugin activation callback. Registers option to redirect on next admin load.
 */
function jwcfe_activate() {
	deactivate_plugins( '/woo-checkout-field-editor-pro/checkout-form-designer.php' );
	add_option( 'jwcfe_activation_redirect', true );
}

/**
 * Redirects the user after plugin activation
 */
function jwcfe_activation_redirect() {
	// Make sure it's the correct user

	if ( get_option( 'jwcfe_activation_redirect', false ) ) {
		// Make sure we don't redirect again after this one
		delete_option( 'jwcfe_activation_redirect' );
		wp_safe_redirect( admin_url( 'admin.php?page=jwcfe_checkout_register_editor' ) );
		exit;
	}
}