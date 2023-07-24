<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    WC_Sales_Up
 * @subpackage WC_Sales_Up/public
 */

if ( ! class_exists( 'WC_Sales_Up_Cart' ) ) {
	/**
	 * The public-facing functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the public-facing stylesheet and JavaScript.
	 *
	 * @package    WC_Sales_Up
	 * @subpackage WC_Sales_Up/public
	 */
	class WC_Sales_Up_Cart {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			WC_Sales_Up_Product::set_global_options();
			add_action( 'woocommerce_cart_calculate_fees', array( &$this, 'apply_fbq_fee' ), 30, 1 );
		}

		/**
		 * Add discount coupon.
		 *
		 * @since    1.0.0
		 * @param      object $cart    Cart object.
		 */
		public function apply_fbq_fee( $cart ) {
			global $woocommerce;
			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				return;
			}

			$cart_product_ids = array();
			foreach ( $cart->get_cart() as $cart_item ) {
				if ( isset( $cart_item['wsp_fbq_discount'] ) ) {
					$fbq_main_product = $cart_item['wsp_fbq_discount'];
					$cart_product_ids_array[$fbq_main_product][] = ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] > 0 ) ? $cart_item['variation_id'] : $cart_item['product_id'];
				}
			}
			if ( ! empty( $cart_product_ids_array ) ) {
				foreach($cart_product_ids_array as $fbq_main_product=>$cart_product_ids) {
					if ( isset( $fbq_main_product ) && ! empty( $cart_product_ids ) ) {
						$is_wsp_fbq_discount_valid = WC_Sales_Up_Product::is_wsp_fbq_discount_valid( $fbq_main_product, $cart_product_ids );
						if ( ! $is_wsp_fbq_discount_valid ) {
							return;
						}
						$discounted_price = WC_Sales_Up_Product::get_wsp_fbq_discounted_price( $fbq_main_product, $cart_product_ids );
						
						if ( empty( $discounted_price ) ) {
							return;
						}
						if ( $discounted_price > 0 ) {
							$discounted_price = 0 - $discounted_price;
						}
						$fee = array(
							'id'        => 'wsp_fbt_fee',
							'name'      => __( 'Frequently Bought Together Discount','wc-sales-up'),
							'amount'    => $discounted_price,
							'taxable'   => false,
							'tax_class' => '',
						);
						$existing_fees = $cart->fees_api()->get_fees();
						if ( isset($existing_fees['wsp_fbt_fee']) ) {
								$existing_amount  = $existing_fees['wsp_fbt_fee']->amount;
								$existing_fees['wsp_fbt_fee']->amount = $discounted_price + $existing_amount;
								$cart->fees_api()->set_fees($existing_fees);
						}
						else {
							$cart->fees_api()->add_fee( $fee );
						}
					}
				}
			}
		}

	}
	new WC_Sales_Up_Cart();
}
