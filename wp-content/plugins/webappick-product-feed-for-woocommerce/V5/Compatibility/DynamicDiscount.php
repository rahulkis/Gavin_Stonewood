<?php
namespace CTXFeed\V5\Compatibility;
class DynamicDiscount {

}

// Discounted price filter
add_filter( 'woo_feed_filter_product_sale_price', 'woo_feed_get_dynamic_discounted_product_price', 9, 4 );
add_filter( 'woo_feed_filter_product_sale_price_with_tax', 'woo_feed_get_dynamic_discounted_product_price', 9, 4 );

if ( ! function_exists('woo_feed_apply_tax_location_data') ) {
	/**
	 * Filter and Change Location data for tax calculation
	 *
	 * @param array $location Location array.
	 * @param string $tax_class Tax class.
	 * @param WC_Customer $customer WooCommerce Customer Object.
	 *
	 * @return array
	 */
	function woo_feed_apply_tax_location_data( $location, $tax_class, $customer ) {
		// @TODO use filter. add tab in feed editor so user can set custom settings.
		// @TODO tab should not list all country and cities. it only list available tax settings and user can just select one.
		// @TODO then it will extract the location data from it to use here.
		$wc_tax_location = [
			WC()->countries->get_base_country(),
			WC()->countries->get_base_state(),
			WC()->countries->get_base_postcode(),
			WC()->countries->get_base_city(),
		];
		/**
		 * Filter Tax Location to apply before product loop
		 *
		 * @param array $tax_location
		 *
		 * @since 3.3.0
		 */
		$tax_location = apply_filters('woo_feed_tax_location_data', $wc_tax_location);
		if ( ! is_array($tax_location) || (is_array($tax_location) && 4 !== count($tax_location)) ) {
			$tax_location = $wc_tax_location;
		}

		return $tax_location;
	}
}

if ( ! function_exists('woo_feed_get_dynamic_discounted_product_price') ) {

	/**
	 * Get price with dynamic discount
	 *
	 * @param WC_Product|WC_Product_Variable $product product object
	 * @param $price
	 * @param $config
	 * @param bool $tax product taxable or not
	 * @return mixed $price
	 */
	function woo_feed_get_dynamic_discounted_product_price( $price, $product, $config, $tax ) {
		$base_price = $price;
		$discount_plugin_activate = false;


		/**
		 * PLUGIN: Discount Rules for WooCommerce
		 * URL: https://wordpress.org/plugins/woo-discount-rules/
		 */
		if ( is_plugin_active('woo-discount-rules/woo-discount-rules.php') ) {
			$discount_plugin_activate = true;
			if ( class_exists('Wdr\App\Controllers\Configuration') ) {
				$config = Wdr\App\Controllers\Configuration::getInstance()->getConfig('calculate_discount_from', 'sale_price');

				if ( isset($config) && ! empty($config) ) {
					if ( 'regular_price' === $config ) {
						$price = $product->get_regular_price();
					} else {
						$price = $product->get_price();
					}
				} else {
					$price = $product->get_price();
				}

				if ( $product->is_type('variable') ) {
					$min = $product->get_variation_price('min', false);
					$max = $product->get_variation_price('max', false);

					$price = $min;
					if ( $max === $base_price ) {
						$price = $max;
					}
				}

				$price = apply_filters('advanced_woo_discount_rules_get_product_discount_price_from_custom_price', false, $product, 1, $price, 'discounted_price', true, true);

				if ( empty($price) ) {
					$price = $base_price;
				}

				if ( ! isset( $feedConfig['feedCurrency'] ) ) {
					$feedConfig['feedCurrency'] = get_woocommerce_currency();
				}

				$price = apply_filters('wcml_raw_price_amount', $price, $feedConfig['feedCurrency'] );

			}
		}

		/**
		 * PLUGIN: Dynamic Pricing With Discount Rules for WooCommerce
		 * URL: https://wordpress.org/plugins/aco-woo-dynamic-pricing/
		 *
		 * This plugin does not apply discount on product page.
		 *
		 * Don't apply discount manually.
		 */
//        if (is_plugin_active('aco-woo-dynamic-pricing/start.php')) {
//            $discount_plugin_activate = true;
//            if (class_exists('AWDP_Discount')) {
//                $price = AWDP_Discount::instance()->calculate_discount($product->get_price(), $product);
//            }
//        }

		/**
		 * PLUGIN: Conditional Discounts for WooCommerce
		 * URL: https://wordpress.org/plugins/woo-advanced-discounts/
		 *
		 * NOTE:* Automatically apply discount to $product->get_sale_price() method.
		 */
//        if (is_plugin_active('woo-advanced-discounts/wad.php')) {
//            $discount_plugin_activate = true;
//            $discount_amount = 0;
//            global $wad_discounts;
//            if (isset($wad_discounts["product"])) {
//                foreach ($wad_discounts["product"] as $discount_id => $discount_obj) {
//                    if ($discount_obj->is_applicable($product->get_id())) {
//                        $wad_obj = new WAD_Discount($discount_id);
//                        if (isset($wad_obj->settings)) {
//                            $settings = $wad_obj->settings;
//                            $discount_type = $wad_obj->settings['action'];
//                            if (false !== strpos($discount_type, 'fixed')) {
//                                $discount_amount = (float) $wad_obj->get_discount_amount($price);
//                            } elseif (false !== strpos($discount_type, 'percentage')) {
//                                $percentage = $settings['percentage-or-fixed-amount'];
//                                $discount_amount = ($product->get_price() * ($percentage / 100));
//                            }
//                        }
//                    }
//                }
//                $price = (float) $product->get_price() - (float) $discount_amount;
//            }
//        }

		/**
		 * PLUGIN: Pricing Deals for WooCommerce
		 * URL: https://wordpress.org/plugins/pricing-deals-for-woocommerce/
		 */
		if ( is_plugin_active('pricing-deals-for-woocommerce/vt-pricing-deals.php') ) {
			$discount_plugin_activate = true;
			if ( class_exists('VTPRD_Controller') ) {
				global $vtprd_rules_set;
				$vtprd_rules_set = get_option('vtprd_rules_set');
				if ( ! empty($vtprd_rules_set) && is_array($vtprd_rules_set) ) {
					foreach ( $vtprd_rules_set as $vtprd_rule_set ) {
						$status = $vtprd_rule_set->rule_on_off_sw_select;
						if ( 'on' === $status || 'onForever' === $status ) {
							$discount_type = $vtprd_rule_set->rule_deal_info[0]['discount_amt_type'];
							$discount = $vtprd_rule_set->rule_deal_info[0]['discount_amt_count'];
							if ( 'currency' === $discount_type || 'fixedPrice' === $discount_type ) {
								$price = $product->get_price() - $discount;
							} elseif ( 'percent' === $discount_type ) {
								$price = $product->get_price() - (($product->get_price() * $discount) / 100);
							}
						}
					}
				}
			}
		}

		//######################### YITH #########################################################
		/**
		 * PLUGIN: YITH WOOCOMMERCE DYNAMIC PRICING AND DISCOUNTS
		 * URL: hhttps://yithemes.com/themes/plugins/yith-woocommerce-dynamic-pricing-and-discounts/
		 *
		 * NOTE:*  YITH Automatically apply discount to $product->get_sale_price() method.
		 */
		//######################### RightPress ###################################################
		/**
		 * PLUGIN: WooCommerce Dynamic Pricing & Discounts
		 * URL: https://codecanyon.net/item/woocommerce-dynamic-pricing-discounts/7119279
		 *
		 * RightPress dynamic pricing supported. Filter Hooks applied to "woo_feed_apply_hooks_before_product_loop"
		 * to get the dynamic discounted price via $product->ger_sale_price(); method.
		 */
		//###################### Dynamic Pricing ##################################################
		/**
		 * PLUGIN: Dynamic Pricing
		 * URL: https://woocommerce.com/products/dynamic-pricing/
		 *
		 * Dynamic Pricing plugin doesn't show the options or any price change on your frontend.
		 * So a user will not even notice the discounts until he reaches the checkout.
		 * No need to add the compatibility.
		 */



		// Get Price with tax
		if ( $discount_plugin_activate && $tax ) {
			$price = woo_feed_get_price_with_tax($price, $product);
		}

		return ( isset($base_price) || ($price > 0) && ($price < $base_price) ) ? $price : $base_price;
	}
}