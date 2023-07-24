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

if ( ! class_exists( 'WC_Sales_Up_Checkout' ) ) {
	/**
	 * The public-facing functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the public-facing stylesheet and JavaScript.
	 *
	 * @package    WC_Sales_Up
	 * @subpackage WC_Sales_Up/public
	 */
	class WC_Sales_Up_Checkout {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			add_action( 'wp_ajax_add_offer_to_cart', array( &$this, 'add_offer_to_cart' ) );
			add_action( 'wp_ajax_nopriv_add_offer_to_cart', array( &$this, 'add_offer_to_cart' ) );
			add_action( 'woocommerce_review_order_before_submit', array( &$this, 'after_checkout' ), 10 );
			add_action( 'woocommerce_review_order_after_submit', array( &$this, 'after_checkout' ), 10 );
			add_action( 'woocommerce_before_calculate_totals', array( &$this, 'apply_custom_price_to_cart_item' ), 99 );
			add_filter( 'woocommerce_cart_item_name', array( &$this, 'add_remove_from_cart_btn' ), 10, 3 );
			add_action( 'woocommerce_cart_collaterals', array( &$this, 'after_checkout' ), 0 );
			add_action( 'wp_ajax_change_cho_offer_price', array( &$this, 'change_cho_offer_price' ) );
			add_action( 'wp_ajax_nopriv_change_cho_offer_price', array( &$this, 'change_cho_offer_price' ) );
		}

		/**
		 * Apply discounted price to cart products.
		 *
		 * @since    1.0.0
		 * @param object $cart cart object.
		 */
		public function apply_custom_price_to_cart_item( $cart ) {
			if ( ! WC()->session->__isset( 'reload_checkout' ) ) {

				foreach ( $cart->get_cart() as $item ) {
					if ( isset( $item['custom_price'] ) ) {
						$item['data']->set_price( $item['custom_price'] );
					}
				}
			}
		}

		/**
		 * Get cart product ids.
		 *
		 * @since    1.0.0
		 * @return array $cart_product_ids cart product ids.
		 */
		public function get_cart_product_ids() {
			$cart_items       = WC()->cart->get_cart();
			$cart_product_ids = array();
			foreach ( $cart_items as $cart_item ) {
				$cart_product_ids[] = $cart_item['product_id'];
			}
			return $cart_product_ids;
		}

		/**
		 * Get cart category ids.
		 *
		 * @since    1.0.0
		 * @return array $cart_cat_ids cart category ids.
		 */
		public function get_cart_category_ids() {
			$cart_items     = $this->get_cart_product_ids();
			$cart_cat_t_ids = array();
			foreach ( $cart_items as $product_id ) {
				$product_cats = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );
				foreach ( $product_cats as $cat_id ) {
					$cart_cat_t_ids[] = $cat_id;
				}
			}
			$cart_cat_ids = array_unique( $cart_cat_t_ids );
			return $cart_cat_ids;
		}

		/**
		 * Get matched offer id.
		 *
		 * @since    1.0.0
		 * @param string $page current page.
		 * @return id $offers_id best offer id.
		 */
		public function get_one_cho_offer( $page = 'checkout') {
			// get all offers.
			$offers_array = $this->get_active_cho_offers();

			// get one product from.
			foreach ( $offers_array as $offers_id ) {
				if ( $this->is_valid_offer_to_display( $offers_id, $page ) ) {
					return $offers_id;
				}
			}
		}

		/**
		 * Get all active offers.
		 *
		 * @since    1.0.0
		 * @return array $offers_array all matched offers.
		 */
		public function get_active_cho_offers() {
			global $wpdb;
			$offers_array = array();
			$args         = array(
				'meta_query' => array(
					'relation' => 'OR',
						array(
							'key' => 'priority',
							'compare' => 'EXISTS'
						),
					   array(
							 'key' => 'priority',
							 'compare' => 'NOT EXISTS'
						 )
					 ),
				'post_type'      => 'checkout-offers',
				'orderby'        => 'meta_value_num title',
				'order'          => 'asc',
				'posts_per_page' => -1,
			);
			$the_query    = new WP_Query( $args );

			// The Loop.
			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$cho_offer_id   = get_the_id();
					$offers_array[] = $cho_offer_id;
				}
			}

			// Reset Post Data.
			wp_reset_postdata();
			return $offers_array;
		}

		/**
		 * Check if offer is valid.
		 *
		 * @since    1.0.0
		 * @param int $cho_offer_id offer id.
		 * @param string $page current page.
		 * @return boolean $offers_array true or false.
		 */
		public function is_valid_offer_to_display( $cho_offer_id, $page ) {
			$offer_meta           = get_post_meta( $cho_offer_id, 'wsp_cho', true );
			$product_verify   = 'yes';
			$wsp_cho_display_page = isset( $offer_meta['display_page'] ) ? $offer_meta['display_page'] : array( 'cart', 'checkout_bump' );
			if($wsp_cho_display_page == 'both') {
				$wsp_cho_display_page = array( 'cart', 'checkout_bump' );
			}
			if($wsp_cho_display_page == 'checkout') {
				$wsp_cho_display_page = array( 'checkout_bump');
			}
			if($wsp_cho_display_page == 'cart') {
				$wsp_cho_display_page = array( 'cart');
			}
			if ( 'cart' == $page && ! in_array( 'cart', $wsp_cho_display_page ) ) {
				return false;
			}
			if ( 'checkout' == $page && ! in_array( 'checkout_bump', $wsp_cho_display_page ) ) {
				return false;
			}
			$offer_product_id = isset( $offer_meta['offer'] ) ? $offer_meta['offer'] : '';
			if ( ! ( $offer_product_id > 0 ) ) {
				return false;
			}
			$offer_product = wc_get_product( $offer_product_id );
			// Check if product purchasable.
			if ( ! $offer_product->is_purchasable() ) {
				return false;
			}
			// Check offer valid for specific products or categories.
			$wsp_cho_display_on = $offer_meta['display_on'];
			if ( '' == $wsp_cho_display_on ) {
				$cart_products = $this->get_cart_product_ids();
				if ( in_array( $offer_product_id, $cart_products ) ) {
					$product_verify = 'no';
				}
				else {
					$product_verify = 'yes';
				}
			} elseif ( 'specific_products' == $wsp_cho_display_on ) {
				$products_all   = $offer_meta['products_all'];
				$offer_products = isset( $offer_meta['products'] ) ? $offer_meta['products'] : '';
				if ( '' != $offer_products || ( is_array( $offer_products ) && ! empty( $offer_products ) ) ) {
					$cart_products = $this->get_cart_product_ids();
					if ( ! in_array( $offer_product_id, $cart_products ) ) {
						if ( 'any' == $products_all ) {
							$product_common = array_intersect( $offer_products, $cart_products );
							if ( is_array( $product_common ) && ! empty( $product_common ) ) {
								$product_verify = 'yes';
							}
						} elseif ( 'all' == $products_all ) {
							$product_common = array_diff( $offer_products, $cart_products );
							if ( '' == $product_common || ( is_array( $product_common ) && empty( $product_common ) ) ) {
								$product_verify = 'yes';
							}
						}
					}  else {
						$product_verify = 'no';
					}
				}
			} elseif ( 'specific_categories' == $wsp_cho_display_on ) {
				$categories_all   = $offer_meta['categories_all'];
				$offer_categories = isset( $offer_meta['categories'] ) ? $offer_meta['categories'] : '';
				if ( '' != $offer_categories || ( is_array( $offer_categories ) && ! empty( $offer_categories ) ) ) {
					$cart_categories = $this->get_cart_category_ids();
					$cart_products   = $this->get_cart_product_ids();
					if ( ! in_array( $offer_product_id, $cart_products ) ) {
						if ( 'any' == $categories_all ) {
							$cat_common = array_intersect( $offer_categories, $cart_categories );
							if ( is_array( $cat_common ) && ! empty( $cat_common ) ) {
								$product_verify = 'yes';
							}
						} elseif ( 'all' == $categories_all ) {
							$cat_common = array_diff( $offer_categories, $cart_categories );
							if ( '' == $cat_common || ( is_array( $cat_common ) && empty( $cat_common ) ) ) {
								$product_verify = 'yes';
							}
						}
					}
				} else {
					$product_verify = 'no';
				}
			}
			if('yes' == $product_verify) {
				return true;
			}
		}

		/**
		 * Include checkout offer display.
		 *
		 * @since    1.0.0
		 */
		public function after_checkout() {
			$current_filter       = current_filter();
			if ( is_cart() ) {
				$page = 'cart';
			} elseif ( is_checkout() ) {
				$page = 'checkout';
			}
			$get_one_cho_offer_id = $this->get_one_cho_offer($page);
			if ( '' == $get_one_cho_offer_id ) {
				return '';
			}
			$wsp_cho                  = get_post_meta( $get_one_cho_offer_id, 'wsp_cho', true );
			$wsp_cho_display_position = isset( $wsp_cho['display_position'] ) ? $wsp_cho['display_position'] : 'before_order_button';
			if ( ( 'before_order_button' == $wsp_cho_display_position && 'woocommerce_review_order_before_submit' == $current_filter )
			||
			( 'after_order_button' == $wsp_cho_display_position && 'woocommerce_review_order_after_submit' == $current_filter )
			||
			( 'woocommerce_cart_collaterals' == $current_filter )

			) {
				// title.
				$wsp_cho_title = isset( $wsp_cho['title'] ) ? $wsp_cho['title'] : '';
				// content.
				$wsp_cho_content = isset( $wsp_cho['content'] ) ? $wsp_cho['content'] : '';
				// image.
				$wsp_cho_img     = isset( $wsp_cho['img'] ) ? $wsp_cho['img'] : '';
				$wsp_cho_img_src = '';
				if ( '' == $wsp_cho_img ) {
					$wsp_cho_img_src = wc_placeholder_img_src( array( 100, 100 ) );
				} else {
					$image_s         = wp_get_attachment_image_src( $wsp_cho_img, array( 100, 100 ) );
					$wsp_cho_img_src = $image_s[0];
				}
				// template view.
				$wsp_cho_display_image        = isset( $wsp_cho['display_image'] ) ? $wsp_cho['display_image'] : '';
				$wsp_cho_display_price        = isset( $wsp_cho['display_price'] ) ? $wsp_cho['display_price'] : '';
				$wsp_cho_box_bg               = isset( $wsp_cho['box_bg'] ) ? $wsp_cho['box_bg'] : '';
				$wsp_cho_offer_title_bg_color = isset( $wsp_cho['offer_title_bg_color'] ) ? $wsp_cho['offer_title_bg_color'] : '';
				$wsp_cho_offer_title_color    = isset( $wsp_cho['offer_title_color'] ) ? $wsp_cho['offer_title_color'] : '';
				$wsp_cho_offer_text_color     = isset( $wsp_cho['offer_text_color'] ) ? $wsp_cho['offer_text_color'] : '';
				$wsp_cho_price_color          = isset( $wsp_cho['price_color'] ) ? $wsp_cho['price_color'] : '';
				$wsp_cho_display_link         = isset( $wsp_cho['display_link'] ) ? $wsp_cho['display_link'] : 'yes';
				$wsp_cho_box_width          = isset( $wsp_cho['box_width'] ) ? $wsp_cho['box_width'] : '500';
				$wsp_cho_box_width_pre         = isset( $wsp_cho['box_width_pre'] ) ? $wsp_cho['box_width_pre'] : 'px';
				if($wsp_cho_box_width_pre == 'percent') {
					$wsp_cho_box_width_pre = '%';
				}

				// price.
				$wsp_cho_offer     = isset( $wsp_cho['offer'] ) ? $wsp_cho['offer'] : '';
				$wsp_cho_offer_amt = isset( $wsp_cho['offer_amt'] ) ? $wsp_cho['offer_amt'] : '';
				$wsp_cho_offer_pre = isset( $wsp_cho['offer_pre'] ) ? $wsp_cho['offer_pre'] : '';

				if ( $wsp_cho_offer > 0 ) {
					$product_data = wc_get_product( $wsp_cho_offer );
					$price        = $product_data->get_price();
					if ( $price > 0 ) {
						$new_price      = $this->get_discounted_price_from_price( $wsp_cho_offer_amt, $wsp_cho_offer_pre, $price );
						$off_price      = wc_price( $new_price );
						$original_price = wc_price( $price );
					} else {
						$off_price      = wc_price( 0 );
						$original_price = wc_price( 0 );
					}
					include 'partials/wc-sales-up-product-checkout.php';
				}
			}
		}

		/**
		 * Get discounted price from original price.
		 *
		 * @since    1.0.0
		 * @param float  $wsp_cho_offer_amt discount.
		 * @param string $wsp_cho_offer_pre fix or percentage.
		 * @param float  $price original price.
		 * @return float $new_price discounted price.
		 */
		public function get_discounted_price_from_price( $wsp_cho_offer_amt, $wsp_cho_offer_pre, $price ) {
			if ( 'fix' == $wsp_cho_offer_pre ) {
				$new_price = $price - $wsp_cho_offer_amt;
			} else {
				$new_price = $price - $price * $wsp_cho_offer_amt / 100;
			}
			return $new_price;
		}

		/**
		 * Get discount.
		 *
		 * @since    1.0.0
		 * @param float  $wsp_cho_offer_amt discount.
		 * @param string $wsp_cho_offer_pre fix or percentage.
		 * @param float  $price original price.
		 * @return float $discount discounted price.
		 */
		public function get_discounted_from_product_id( $wsp_cho_offer_amt, $wsp_cho_offer_pre, $price ) {
			if ( 'fix' == $wsp_cho_offer_pre ) {
				$discount = $wsp_cho_offer_amt;
			} else {
				$discount = $price * $wsp_cho_offer_amt / 100;
			}
			return $discount;
		}

		/**
		 * Add offer to cart.
		 *
		 * @since    1.0.0
		 */
		public function add_offer_to_cart() {
			$wsp_nonce = isset( $_POST['wsp_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_nonce'] ) ) : 0;
			if ( wp_verify_nonce( $wsp_nonce, 'wsp_nonce' ) ) {
				global $woocommerce;
				$wsp_from_cart    = isset( $_POST['wsp_from_cart'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_from_cart'] ) ) : 'checkout';
				$offer_id         = $this->get_one_cho_offer($wsp_from_cart);
				$offer_product_id = $this->get_product_id_from_offer_id( $offer_id );
				$product_id       = isset( $_POST['wsp_cho_pro_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_cho_pro_id'] ) ) : '';
				$wsp_cho          = get_post_meta( $offer_id, 'wsp_cho', true );
				if ( $product_id > 0 && self::is_product_valid_for_offer( $offer_id, $product_id ) ) {
					// get discount.
					$wsp_cho_offer_amt = isset( $wsp_cho['offer_amt'] ) ? $wsp_cho['offer_amt'] : '';
					$wsp_cho_offer_pre = isset( $wsp_cho['offer_pre'] ) ? $wsp_cho['offer_pre'] : '';
					$product_data      = wc_get_product( $product_id );
					$original_price    = $product_data->get_price();
					$discounted_price  = $original_price;
					if ( $wsp_cho_offer_amt > 0 ) {
						$discounted_price = $this->get_discounted_price_from_price( $wsp_cho_offer_amt, $wsp_cho_offer_pre, $original_price );
					}
					if ( ! empty( $discounted_price ) ) {
						if ( $product_data->is_type( 'variable' ) ) {
							// add product to cart.
							$woocommerce->cart->add_to_cart(
								$offer_product_id,
								1,
								$product_id,
								'',
								array(
									'cho_discount'  => $product_id,
									'custom_price'  => $discounted_price,
									'wsp_cart_offer_id' => $offer_id,
								)
							);
						} else {
							// add product to cart.
							$woocommerce->cart->add_to_cart(
								$product_id,
								1,
								'',
								'',
								array(
									'cho_discount'  => $product_id,
									'custom_price'  => $discounted_price,
									'wsp_cart_offer_id' => $offer_id,
								)
							);
						}
					}
				}
			}
			exit();
		}

		/**
		 * Change offer price.
		 *
		 * @since    1.0.0
		 */
		public function change_cho_offer_price() {
			$wsp_nonce = isset( $_POST['wsp_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_nonce'] ) ) : 0;
			if ( wp_verify_nonce( $wsp_nonce, 'wsp_nonce' ) ) {
				echo $offer_id       = $this->get_one_cho_offer($page);
				$wsp_cho_pro_id = isset( $_POST['wsp_cho_pro_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_cho_pro_id'] ) ) : '';
				$wsp_cho        = get_post_meta( $offer_id, 'wsp_cho', true );
				print_r($wsp_cho);
				if ( $wsp_cho_pro_id > 0 && self::is_product_valid_for_offer( $offer_id, $wsp_cho_pro_id ) ) {
					$wsp_cho_offer_amt = isset( $wsp_cho['offer_amt'] ) ? $wsp_cho['offer_amt'] : '';
					$wsp_cho_offer_pre = isset( $wsp_cho['offer_pre'] ) ? $wsp_cho['offer_pre'] : '';
					$product_data      = wc_get_product( $wsp_cho_pro_id );
					$return_array      = array();
					$original_price    = $product_data->get_price();
					$discounted_price  = $original_price;
					if ( $wsp_cho_offer_amt > 0 ) {
						$discounted_price = $this->get_discounted_price_from_price( $wsp_cho_offer_amt, $wsp_cho_offer_pre, $original_price );
					}
					$return_array['original_price']   = wc_price( $original_price );
					$return_array['discounted_price'] = wc_price( $discounted_price );
					echo wp_json_encode( $return_array );
				}
			}
			exit();
		}

		/**
		 * Check if product is valid for offer.
		 *
		 * @since    1.0.0
		 * @param int $offer_id offer id.
		 * @param int $product_id product id.
		 * @return boolean true or false.
		 */
		public static function is_product_valid_for_offer( $offer_id, $product_id ) {
			if ( $offer_id > 0 ) {
				$offer_product_id = self::get_product_id_from_offer_id( $offer_id );
				if ( 'product_variation' == get_post_type( $product_id ) ) {
					$product_id = wp_get_post_parent_id( $product_id );
				}
				if ( $offer_product_id == $product_id ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Get product id from offer id.
		 *
		 * @since    1.0.0
		 * @param int $offer_id offer id.
		 * @return int $wsp_cho_offer product id.
		 */
		public static function get_product_id_from_offer_id( $offer_id ) {
			$wsp_cho       = get_post_meta( $offer_id, 'wsp_cho', true );
			$wsp_cho_offer = isset( $wsp_cho['offer'] ) ? $wsp_cho['offer'] : '';
			return $wsp_cho_offer;
		}

		/**
		 * Add remove product button.
		 *
		 * @since    1.0.0
		 * @param html   $subtotal product subtotal.
		 * @param object $cart_item cart item.
		 * @param string $cart_item_key cart item key.
		 * @return html $subtotal product subtotal.
		 */
		public function add_remove_from_cart_btn( $subtotal, $cart_item, $cart_item_key ) {
			global $woocommerce;
			if ( is_checkout() ) {
				if ( isset( $cart_item['wsp_cart_offer_id'] ) && '' != $cart_item['wsp_cart_offer_id'] ) {
					$_product    = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id  = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
					$remove_link = apply_filters(
						'woocommerce_cart_item_remove_link',
						sprintf(
							'<a href="%s" class="remove wsp_p_remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">×</a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							__( 'Remove this item', 'woocommerce' ),
							esc_attr( $product_id ),
							esc_attr( $_product->get_sku() )
						),
						$cart_item_key
					);
					$subtotal    = $remove_link . $subtotal;
				}
			}
			return $subtotal;
		}

	}
	$aa = new WC_Sales_Up_Checkout();
}
