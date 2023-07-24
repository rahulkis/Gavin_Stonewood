<?php
/**
 * Frequently Bought Together functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    WC_Sales_Up
 * @subpackage WC_Sales_Up/public
 */

if ( ! class_exists( 'WC_Sales_Up_Product' ) ) {
	/**
	 * WC_Sales_Up_Product class.
	 */
	class WC_Sales_Up_Product {
		/**
		 * Array for global options for fbq.
		 *
		 * @var array
		 */
		public static $global_options = array();

		/**
		 * Apply necessary hooks.
		 */
		public function __construct() {
			add_action( 'woocommerce_before_single_product', array( &$this, 'set_global_options' ), 10 );
			add_action( 'template_redirect', array( &$this, 'fbt_add_to_cart' ), 10 );
			add_action( 'wp_head', array( &$this, 'apply_css' ), 2 );
			add_action( 'woocommerce_after_single_product_summary', array( &$this, 'display_box' ), 5 );
			add_action( 'wp_ajax_submit_fbt', array( &$this, 'submit_fbt' ), 5 );
			add_action( 'wp_ajax_nopriv_submit_fbt', array( &$this, 'submit_fbt' ), 5 );
			add_action( 'wp_ajax_change_offer_price', array( &$this, 'change_offer_price' ), 5 );
			add_action( 'wp_ajax_nopriv_change_offer_price', array( &$this, 'change_offer_price' ), 5 );
			add_action( 'wp_ajax_change_single_product_price', array( &$this, 'change_single_product_price' ), 5 );
			add_action( 'wp_ajax_nopriv_change_single_product_price', array( &$this, 'change_single_product_price' ), 5 );
		}

		/**
		 * Set global options for fbq.
		 */
		public static function set_global_options() {
			global $wpdb;
			self::$global_options = get_option( 'wsp_on_product_data' );
		}

		/**
		 * Get fbq product ids from main product id without cart filter.
		 *
		 * @param int $product_id_t contains the id of the main product to get fbq product ids.
		 * @return array $wps_product_ids
		 */
		public static function get_product_ids_original( $product_id_t ) {
			$pros            = get_post_meta( $product_id_t, 'wsp_tp', true );
			$wps_product_ids = isset( $pros['products'] ) ? $pros['products'] : array();

			// Hide out of stock products and not purchasable products.
			if ( is_array( $wps_product_ids ) && ! empty( $wps_product_ids ) ) {
				foreach ( $wps_product_ids as $wsp_pro_id ) {
					if ( $wsp_pro_id > 0 ) {
						$wsp_pro = wc_get_product( $wsp_pro_id );
						if ( ! $wsp_pro->is_in_stock() || ! $wsp_pro->is_purchasable() ) {
							$key = array_search( $wsp_pro_id, $wps_product_ids );
							if ( false !== $key ) {
								unset( $wps_product_ids[ $key ] );
							}
						}
					}
				}
			}

			$wps_product_ids = apply_filters( 'wps_tp_product_ids', $wps_product_ids );
			return $wps_product_ids;
		}

		/**
		 * Get fbq product ids from main product id.
		 *
		 * @param int $product_id_t contains the id of the main product to get fbq product ids.
		 * @return array $wps_product_ids
		 */
		public static function get_product_ids( $product_id_t ) {
			$pros            = get_post_meta( $product_id_t, 'wsp_tp', true );
			$wps_product_ids = isset( $pros['products'] ) ? $pros['products'] : array();
			// Remove product if in cart.
			if ( isset( self::$global_options['cart'] ) ) {
				if ( is_single() ) {
					$cart_items = WC()->cart->get_cart();
					foreach ( $cart_items as $cart_item ) {
						$key = array_search( $cart_item['product_id'], $wps_product_ids );
						if ( false !== $key ) {
							unset( $wps_product_ids[ $key ] );
						}
					}
				}
			}

			// Hide out of stock products and not purchasable products.
			if ( is_array( $wps_product_ids ) && ! empty( $wps_product_ids ) ) {
				foreach ( $wps_product_ids as $wsp_pro_id ) {
					if ( $wsp_pro_id > 0 ) {
						$wsp_pro = wc_get_product( $wsp_pro_id );
						if ( ! $wsp_pro->is_in_stock() || ! $wsp_pro->is_purchasable() ) {
							$key = array_search( $wsp_pro_id, $wps_product_ids );
							if ( false !== $key ) {
								unset( $wps_product_ids[ $key ] );
							}
						}
					}
				}
			}
			$wps_product_ids = apply_filters( 'wps_tp_product_ids', $wps_product_ids );
			return $wps_product_ids;
		}

		/**
		 * Get products from product ids.
		 *
		 * @param array $wps_product_ids contains the ids to get array of products.
		 * @return array $wps_products
		 */
		public static function get_products( $wps_product_ids ) {
			if ( is_array( $wps_product_ids ) && ! empty( $wps_product_ids ) ) {
				foreach ( $wps_product_ids as $wsp_pro_id ) {
					if ( $wsp_pro_id > 0 ) {
						$wsp_pro                     = wc_get_product( $wsp_pro_id );
						$wps_products[ $wsp_pro_id ] = $wsp_pro;
					}
				}
				return $wps_products;
			}
		}

		/**
		 * Display fbq block.
		 */
		public static function display_box() {
			global $product;
			$product_id      = $product->get_id();
			$wps_product_ids = self::get_product_ids( $product_id );

			$wps_product_ids_original = self::get_product_ids_original( $product_id );
			$wps_products             = self::get_products( $wps_product_ids );
			if ( is_array( $wps_products ) && ! empty( $wps_products ) ) {
				$total_original_price = self::total_product_original_price( $wps_products );
				$discounted_price     = $total_original_price;
				$discount             = 0;

				if ( ! array_diff( $wps_product_ids_original, $wps_product_ids ) ) {
					$discount = self::get_wsp_fbq_discounted_price( $product_id, $wps_product_ids );
					if ( $discount > 0 && $total_original_price > $discount ) {
						$discounted_price = $total_original_price - $discount;
					}
				}
				$total_original_price_f = wc_price( $total_original_price );
				$discount_f             = wc_price( $discount );
				$discounted_price_f     = wc_price( $discounted_price );
				$wsp_on_product_data    = self::$global_options;
				$link                   = isset( $wsp_on_product_data['link'] ) ? $wsp_on_product_data['link'] : '';
				$enable_ajax_class      = isset( $wsp_on_product_data['enable_ajax'] ) ? 'wsp_fbt_add_to_cart_ajax' : '';
				$wsp_pro_design         = isset( $wsp_on_product_data['design'] ) ? $wsp_on_product_data['design'] : 'layout-1';
				include plugin_dir_path( __FILE__ ) . 'partials/wc-sales-up-product-display.php';
			}
		}

		/**
		 * Get total price from products.
		 *
		 * @param array $wps_products contains products array.
		 * @return float $original_price
		 */
		public static function total_product_original_price( $wps_products ) {
			$original_price = 0;
			if ( is_array( $wps_products ) && ! empty( $wps_products ) ) {
				foreach ( $wps_products as $wps_product ) {
					if ( $wps_product->get_price() > 0 ) {
						$original_price = $original_price + $wps_product->get_price();
					}
				}
			}
			return $original_price;
		}

		/**
		 * Add products to cart.
		 */
		public function fbt_add_to_cart() {
			if ( isset( $_POST['wsp_fbt_add_to_cart'] ) ) {
				$wsp_nonce = isset( $_POST['wsp_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_nonce'] ) ) : 0;
				if ( wp_verify_nonce( $wsp_nonce, 'wsp_nonce' ) ) {
					if ( isset( $_POST['fbq_main_product'] ) && '' != $_POST['fbq_main_product'] ) {
						global $woocommerce;
						self::set_global_options();
						$main_product_id = isset( $_POST['fbq_main_product'] ) ? intval( $_POST['fbq_main_product'] ) : '';
						$main_product_id = sanitize_text_field( wp_unslash( $main_product_id ) );
						$variable_product_id = isset( $_POST['fbq_variable_product'] ) ? intval( $_POST['fbq_variable_product'] ) : '';
						$variable_product_id = sanitize_text_field( wp_unslash( $variable_product_id ) );
						$get_product_ids = isset( $_POST['fbq_products'] ) ? wsp_recursive_sanitize_text_field( wp_unslash( $_POST['fbq_products'] ) ) : array();
						$products        = array();
						if ( is_array( $get_product_ids ) ) {
							$products[ $main_product_id ] = 1;
							if($variable_product_id > 0) {
								$woocommerce->cart->add_to_cart( $main_product_id, 1, $variable_product_id, '' );
							}
							else {
								$woocommerce->cart->add_to_cart( $main_product_id, 1, '', '' );
							}							
							foreach ( $get_product_ids as $get_product_id ) {
								if ( $get_product_id > 0 ) {
									if ( 'product_variation' == get_post_type( $get_product_id ) ) {
										$parent_id = wp_get_post_parent_id( $get_product_id );
										$woocommerce->cart->add_to_cart( $parent_id, 1, $get_product_id, '', array( 'wsp_fbq_discount' => $main_product_id ) );
										$products[ $get_product_id ] = 1;
									} else {
										$woocommerce->cart->add_to_cart( $get_product_id, 1, '', '', array( 'wsp_fbq_discount' => $main_product_id ) );
										$products[ $get_product_id ] = 1;
									}
								}
							}
							wc_add_to_cart_message( $products );
							wp_safe_redirect( wc_get_cart_url() );
							exit();
						}
					}
				}
			}
		}

		/**
		 * Add products to cart from ajax.
		 */
		public function submit_fbt() {
			$wsp_nonce = isset( $_POST['wsp_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_nonce'] ) ) : 0;
			if ( wp_verify_nonce( $wsp_nonce, 'wsp_nonce' ) ) {
				if ( isset( $_POST['main_product_id'] ) && '' != $_POST['main_product_id'] ) {
					$main_product_id = isset( $_POST['main_product_id'] ) ? intval( $_POST['main_product_id'] ) : '';
					$main_product_id = sanitize_text_field( wp_unslash( $main_product_id ) );
					$variable_product_id = isset( $_POST['fbq_variable_product'] ) ? intval( $_POST['fbq_variable_product'] ) : '';
					$variable_product_id = sanitize_text_field( wp_unslash( $variable_product_id ) );
					$get_product_ids = isset( $_POST['products'] ) ? wsp_recursive_sanitize_text_field( wp_unslash( $_POST['products'] ) ) : array();
					global $woocommerce;
					self::set_global_options();
					$products = array();
					if ( is_array( $get_product_ids ) ) {
						$products[ $main_product_id ] = 1;
						if($variable_product_id > 0) {
							$woocommerce->cart->add_to_cart( $main_product_id, 1, $variable_product_id, '' );
						}
						else {
							$woocommerce->cart->add_to_cart( $main_product_id, 1, '', '');
						}	
						foreach ( $get_product_ids as $get_product_id ) {
							if ( $get_product_id > 0 ) {
								if ( 'product_variation' == get_post_type( $get_product_id ) ) {
									$parent_id = wp_get_post_parent_id( $get_product_id );
									$woocommerce->cart->add_to_cart( $parent_id, 1, $get_product_id, '', array( 'wsp_fbq_discount' => $main_product_id ) );
									$products[ $get_product_id ] = 1;
								} else {
									$woocommerce->cart->add_to_cart( $get_product_id, 1, '', '', array( 'wsp_fbq_discount' => $main_product_id ) );
									$products[ $get_product_id ] = 1;
								}
							}
						}
						wc_add_to_cart_message( $products );
						WC_AJAX::get_refreshed_fragments();
					}
				}
			}
			exit();
		}

		/**
		 * Check if fbq is valid.
		 *
		 * @param int   $fbq_main_product contains main product id.
		 * @param array $cart_product_ids contains array of products ids.
		 * @return boolean
		 */
		public static function is_wsp_fbq_discount_valid( $fbq_main_product, $cart_product_ids ) {
			// get discount apply product ids.
			$wps_product_ids    = self::get_product_ids_original( $fbq_main_product );
			$parent_product_ids = array();
			if ( empty( $wps_product_ids ) ) {
				return false;
			}
			foreach ( $cart_product_ids as $cart_product_id ) {
				$cart_product = wc_get_product( $cart_product_id );

				if ( ! $cart_product || ! $cart_product->is_in_stock() || ! $cart_product->is_purchasable() ) {
					continue;
				}

				if ( in_array( $cart_product_id, $wps_product_ids ) ) {
					$parent_product_ids[] = $cart_product_id;
				} else {
					if ( in_array( $cart_product->get_parent_id(), $wps_product_ids ) ) {
						$parent_product_ids[] = $cart_product->get_parent_id();
					}
				}
			}
			if ( array_diff( $wps_product_ids, $parent_product_ids ) ) {
				return false;
			} else {
				return true;
			}
		}

		/**
		 * Get discounted price from products.
		 *
		 * @param int   $fbq_main_product contains main product id.
		 * @param array $cart_product_ids contains array of products ids.
		 * @return float $discounted_price discounted price
		 */
		public static function get_wsp_fbq_discounted_price( $fbq_main_product, $cart_product_ids ) {
			$original_price   = 0;
			$discounted_price = 0;
			// get original price.
			foreach ( $cart_product_ids as $wps_product_id ) {
				if ( $wps_product_id > 0 ) {
					$wps_product = wc_get_product( $wps_product_id );
					$price       = wc_get_price_to_display( $wps_product );
					if ( $price > 0 ) {
						$original_price = $original_price + $price;
					}
				}
			}
			if ( is_array( $cart_product_ids ) && ! empty( $cart_product_ids ) ) {
				// calculate discounted price.
				$wsp_tp = get_post_meta( $fbq_main_product, 'wsp_tp', true );
				if ( is_array( $wsp_tp ) && ! empty( $wsp_tp ) && $original_price > 0 ) {
					$discount  = $wsp_tp['discount'];
					$offer_pre = $wsp_tp['offer_pre'];
					if ( $discount > 0 ) {
						if ( 'fix' == $offer_pre ) {
							$discounted_price = $discount;
						} else {
							$discounted_price = $original_price * $discount / 100;
						}
					}
				}
				return $discounted_price;
			}

		}

		/**
		 * Add css.
		 */
		public function apply_css() {
			if ( is_product() ) {
				$wsp_on_product_data       = get_option( 'wsp_on_product_data' );
				$wsp_pro_offer_title_color = isset( $wsp_on_product_data['offer_title_color'] ) ? $wsp_on_product_data['offer_title_color'] : '';
				$wsp_pro_box_bg            = isset( $wsp_on_product_data['box_bg'] ) ? $wsp_on_product_data['box_bg'] : '';
				$wsp_pro_price_color       = isset( $wsp_on_product_data['price_color'] ) ? $wsp_on_product_data['price_color'] : '';
				$wsp_pro_discount_color    = isset( $wsp_on_product_data['discount_color'] ) ? $wsp_on_product_data['discount_color'] : '';
				$wsp_pro_button_color      = isset( $wsp_on_product_data['button_color'] ) ? $wsp_on_product_data['button_color'] : '';
				$wsp_pro_button_bg_color   = isset( $wsp_on_product_data['button_bg_color'] ) ? $wsp_on_product_data['button_bg_color'] : '';
				$wsp_pro_arrow_color       = isset( $wsp_on_product_data['arrow_color'] ) ? $wsp_on_product_data['arrow_color'] : '';
				?>
			<style>
				.wsp_fbt_box {
					background: <?php echo esc_attr( $wsp_pro_box_bg ); ?>;
				}
				.wsp_fbt_total, .wsp_fbt_total span {
					color: <?php echo esc_attr( $wsp_pro_price_color ); ?>;
				}
				.wsp_fbt_total_save {
					color: <?php echo esc_attr( $wsp_pro_discount_color ); ?>;
				}
				.wsp_fbt_images_s:after {
					color: <?php echo esc_attr( $wsp_pro_arrow_color ); ?>;
				}
				input[type="submit"].wsp_button {
					color: <?php echo esc_attr( $wsp_pro_button_color ); ?>;
					background: <?php echo esc_attr( $wsp_pro_button_bg_color ); ?>;
				}
				h2.wsp_fbt_head {
					color: <?php echo esc_attr( $wsp_pro_offer_title_color ); ?>
				}
			</style>
				<?php
			}
		}

		/**
		 * Get variations from products.
		 *
		 * @param object $wps_product contains product object.
		 * @return array $variations variations
		 */
		public static function get_variations_from_product( $wps_product ) {
			$attributes       = array_reverse( $wps_product->get_variation_attributes() );
			$attributes_array = array();

			foreach ( $attributes as $key => $val ) {
				$attributes_array[ 'attribute_' . sanitize_title( $key ) ] = $val;
			}
			$cartesian  = wc_array_cartesian( $attributes_array );
			$variations = array();

			foreach ( $cartesian as $attributes ) {
				$attributes = array_map( 'strval', $attributes );

				// get variation id from attributes and product id.
				$variation_id = self::get_variation_id_from_attributes( $wps_product->get_ID(), $attributes );

				if ( $variation_id ) {
					$variation = wc_get_product( $variation_id );
					if ( is_object( $variation ) && $variation->is_purchasable() && $variation->is_in_stock() ) {
						$variations[] = array(
							'variation_id' => $variation->get_ID(),
							'attributes'   => $attributes,
						);
					}
				}
			}
			return $variations;
		}

		/**
		 * Get variation id from product id and selected attributes.
		 *
		 * @param int   $product_id contains product id.
		 * @param array $attributes selected attributes.
		 * @return int
		 */
		public static function get_variation_id_from_attributes( $product_id, $attributes ) {
			return ( new WC_Product_Data_Store_CPT() )->find_matching_product_variation(
				new wc_product( $product_id ),
				$attributes
			);
		}

		/**
		 * Get placeholder from product.
		 *
		 * @param object $wps_product contains product object.
		 * @return string
		 */
		public static function get_attribute_placeholder( $wps_product ) {
			$attributes           = array_reverse( $wps_product->get_variation_attributes() );
			$attribute_labels_ary = array();

			foreach ( $attributes as $atribute_key => $atribute_value ) {
				if ( 'pa_' === substr( $atribute_key, 0, 3 ) ) {
					$taxonomy               = get_taxonomy( $atribute_key );
					$attribute_labels_ary[] = $taxonomy->labels->singular_name;
				} else {
					$attribute_labels_ary[] = $atribute_key;
				}
			}
			$attribute_labels_ary = array_reverse( $attribute_labels_ary );
			return implode( ' - ', $attribute_labels_ary );
		}

		/**
		 * Get json object.
		 */
		public static function change_offer_price() {
			$wsp_nonce = isset( $_POST['wsp_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_nonce'] ) ) : 0;
			if ( wp_verify_nonce( $wsp_nonce, 'wsp_nonce' ) ) {
				global $woocommerce;

				self::set_global_options();
				$main_product_id = isset( $_POST['main_product_id'] ) ? intval( $_POST['main_product_id'] ) : '';

				$wps_product_ids      = isset( $_POST['fbq_products'] ) ? wsp_recursive_sanitize_text_field( wp_unslash( $_POST['fbq_products'] ) ) : array();
				$wps_products         = self::get_products( $wps_product_ids );
				$total_original_price = self::total_product_original_price( $wps_products );
				$discount             = 0;
				$discounted_price     = $total_original_price;
				if ( self::is_wsp_fbq_discount_valid( $main_product_id, $wps_product_ids ) ) {
					$discount = self::get_wsp_fbq_discounted_price( $main_product_id, $wps_product_ids );
					if ( $discount > 0 && $total_original_price > $discount ) {
						$discounted_price = $total_original_price - $discount;
					}
				}
				$total_original_price_f = wc_price( $total_original_price );
				$discount_f             = wc_price( $discount );
				$discounted_price_f     = wc_price( $discounted_price );
				$return_array           = array(
					'total_original_price' => $total_original_price_f,
					'discount'             => $discount_f,
					'discounted_price'     => $discounted_price_f,
				);
				echo wp_json_encode( $return_array );
			}
			exit();
		}

		/**
		 * Get json object.
		 */
		public static function change_single_product_price() {
			$wsp_nonce = isset( $_POST['wsp_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_nonce'] ) ) : 0;
			if ( wp_verify_nonce( $wsp_nonce, 'wsp_nonce' ) ) {
				global $woocommerce;
				self::set_global_options();
				$wps_product_ids        = isset( $_POST['variation_id'] ) ? wsp_recursive_sanitize_text_field( wp_unslash( $_POST['variation_id'] ) ) : array();
				$wps_products           = self::get_products( $wps_product_ids );
				$total_original_price   = self::total_product_original_price( $wps_products );
				$total_original_price_f = wc_price( $total_original_price );
				$return_array           = array(
					'total_original_price' => $total_original_price_f,
				);
				echo wp_json_encode( $return_array );
			}
			exit();
		}
	}
	$aa = new WC_Sales_Up_Product();
}
