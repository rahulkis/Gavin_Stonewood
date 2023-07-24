<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    WC_Sales_Up
 * @subpackage WC_Sales_Up/admin
 */

if ( ! class_exists( 'WC_Sales_Up_Admin' ) ) {
	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the admin-specific stylesheet and JavaScript.
	 *
	 * @package    WC_Sales_Up
	 * @subpackage WC_Sales_Up/admin
	 */
	class WC_Sales_Up_Admin {

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		private $version;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param      string $plugin_name       The name of this plugin.
		 * @param      string $version    The version of this plugin.
		 */
		public function __construct( $plugin_name, $version ) {

			$this->plugin_name = $plugin_name;
			$this->version     = $version;
			add_action( 'wp_ajax_sort_cho_priority', array( &$this, 'sort_cho_priority' ) );
			add_action( 'pre_get_posts', array( &$this, 'change_order' ), 1 );
			add_action( 'save_post_checkout-offers', array( &$this, 'add_priority_on_save' ), 10 );
			add_action( 'post_row_actions', array( &$this, 'duplicate_offers' ), 10, 2 );
			add_action( 'admin_action_wsp_duplicate_offer', array( &$this, 'wsp_duplicate_offer' ) );		}

		/**
		 * Register the stylesheets for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {

			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in WC_Sales_Up_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The WC_Sales_Up_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */
			if ( ! wp_script_is( 'select2', 'enqueued' ) ) {
				wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
			}
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc-sales-up-admin.css', array(), $this->version, 'all' );

		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {
			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in WC_Sales_Up_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The WC_Sales_Up_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */

			if ( ! wp_script_is( 'select2', 'enqueued' ) ) {
				wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
			}
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_media();
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wc-sales-up-admin.js', array( 'select2', 'jquery' ), $this->version, false );
			global $wp_query;
			if ( is_array( $wp_query->posts ) ) {
				$cho_posts = array_map(
					function ( $post ) {
						return $post->ID;
					},
					$wp_query->posts
				);
			} else {
				$cho_posts = array();
			}

			wp_localize_script(
				$this->plugin_name,
				'ajax_object',
				array(
					'default_img' => content_url() . '/uploads/woocommerce-placeholder-100x100.png',
					'cho_posts'   => $cho_posts,
					'wsp_nonce'   => wp_create_nonce( 'wsp_nonce' ),
				)
			);

		}

		/**
		 * Change order of offers.
		 *
		 * @since    1.0.0
		 * @param object $query global query object.
		 */
		public function change_order( $query ) {
			if ( is_admin() && $query->is_main_query() ) {
				if ( 'checkout-offers' == $query->get( 'post_type' ) ) {
					$query->set(
						'meta_query',
						array(
							'relation' => 'OR',
							array(
								'key'     => 'priority',
								'compare' => 'EXISTS',
							),
							array(
								'key'     => 'priority',
								'compare' => 'NOT EXISTS',
							),
						)
					);
					$query->set( 'orderby', 'meta_value title' );
					$query->set( 'order', 'asc' );
				}
			}
		}

		/**
		 * Update order of offers.
		 *
		 * @since    1.0.0
		 */
		public function sort_cho_priority() {
			$wsp_nonce = isset( $_POST['wsp_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_nonce'] ) ) : 0;
			if ( wp_verify_nonce( $wsp_nonce, 'wsp_nonce' ) ) {
				$old_posts   = isset( $_REQUEST['posts'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['posts'] ) ) : array();
				$list_data_a = isset( $_REQUEST['list_data'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['list_data'] ) ) : '';
				$list_data   = str_replace( 'post-', '', $list_data_a );
				$new_posts   = explode( ',', $list_data );

				foreach ( $new_posts as $i => $new_post_id ) {
					$old_post_id = $old_posts[ array_search( $new_post_id, $new_posts, true ) ];
					if ( $old_post_id == $new_post_id ) {
						continue;
					}
					$this->update_cho_priority( $new_post_id, $i );
				}
			}
			exit();
		}

		/**
		 * Update Priority.
		 *
		 * @since    1.0.0
		 * @param int $post_id Post Id.
		 */
		public function add_priority_on_save( $post_id ) {
			global $wpdb;
			$old_priority = get_post_meta( $post_id, 'priority', true );
			if ( '' == $old_priority ) {
				$query    = "SELECT max(cast(meta_value as unsigned)) as max_prio FROM $wpdb->postmeta WHERE meta_key='priority'";
				$max_prio = $wpdb->get_var( $query );
				if ( $max_prio > 0 ) {
					++$max_prio;
				} else {
					$max_prio = 1;
				}
				update_post_meta( $post_id, 'priority', $max_prio );
			}
		}

		/**
		 * Update order of offers.
		 *
		 * @since    1.0.0
		 * @param int $offer_id Offer Id.
		 * @param int $priority Priority.
		 */
		public function update_cho_priority( $offer_id, $priority ) {
			update_post_meta( $offer_id, 'priority', $priority );
		}

		/**
		 * Get order of offers.
		 *
		 * @since    1.0.0
		 * @param int $offer_id Offer Id.
		 */
		public function get_cho_priority( $offer_id ) {
			return (int) get_post_meta( $offer_id, 'priority', true );
		}

		/**
		 * Add menu for product offer.
		 *
		 * @since    1.0.0
		 */
		public function add_menu() {
			global $wsp_screen_option_page;
			$wsp_screen_option_page = add_menu_page( esc_html__( 'Sales Up', 'wc-sales-up' ), esc_html__( 'Sales Up', 'wc-sales-up' ), 'manage_options', 'WC_Sales_Up', array( $this, 'WC_Sales_Up_cblk' ) );
			add_submenu_page( 'WC_Sales_Up', esc_html__( 'On Product', 'wc-sales-up' ), esc_html__( 'On Product', 'wc-sales-up' ), 'manage_options', 'woo_on_product', array( $this, 'on_product_cblk' ), 0 );
		}

		/**
		 * Create post type Checkout offers.
		 *
		 * @since    1.0.0
		 */
		public function create_post_type() {
			$wsp_co_labels = array(
				'name'               => _x( 'Checkout Offer', 'Checkout Offer', 'wc-sales-up' ),
				'singular_name'      => _x( 'Checkout Offer', 'Checkout Offer', 'wc-sales-up' ),
				'menu_name'          => __( 'Checkout Offer', 'wc-sales-up' ),
				'parent_item_colon'  => __( 'Parent Checkout', 'wc-sales-up' ),
				'all_items'          => __( 'On Cart/Checkout', 'wc-sales-up' ),
				'view_item'          => __( 'View Checkout Offer', 'wc-sales-up' ),
				'add_new_item'       => __( 'Add New Checkout Offer', 'wc-sales-up' ),
				'add_new'            => __( 'Add New', 'wc-sales-up' ),
				'edit_item'          => __( 'Edit Checkout Offer', 'wc-sales-up' ),
				'update_item'        => __( 'Update Checkout Offer', 'wc-sales-up' ),
				'search_items'       => __( 'Search Checkout Offer', 'wc-sales-up' ),
				'not_found'          => __( 'Not Found', 'wc-sales-up' ),
				'not_found_in_trash' => __( 'Not found in Trash', 'wc-sales-up' ),
			);

			$wsp_co_args = array(
				'label'               => __( 'checkout-offers', 'wc-sales-up' ),
				'labels'              => $wsp_co_labels,
				'supports'            => array( 'title' ),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'WC_Sales_Up',
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 5,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'post',
				'show_in_rest'        => true,
			);

			// Registering checkout offers Post Type.
			register_post_type( 'checkout-offers', $wsp_co_args );

		}

		/**
		 * Include on product page offer.
		 *
		 * @since    1.0.0
		 */
		public function on_product_cblk() {
			$wsp_on_product_data = get_option( 'wsp_on_product_data' );
			$wsp_pro_design      = isset( $wsp_on_product_data['design'] ) ? $wsp_on_product_data['design'] : 'layout-1';
			$wsp_pro_offer_title = isset( $wsp_on_product_data['offer_title'] ) ? $wsp_on_product_data['offer_title'] : esc_html__( 'Frequently Bought Together', 'wc-sales-up' );
			$wsp_pro_link        = isset( $wsp_on_product_data['link'] ) ? $wsp_on_product_data['link'] : '';
			$wsp_pro_cart        = isset( $wsp_on_product_data['cart'] ) ? $wsp_on_product_data['cart'] : '';
			$wsp_pro_enable_ajax = isset( $wsp_on_product_data['enable_ajax'] ) ? $wsp_on_product_data['enable_ajax'] : '';

			$wsp_pro_offer_title_color = isset( $wsp_on_product_data['offer_title_color'] ) ? $wsp_on_product_data['offer_title_color'] : '';
			$wsp_pro_box_bg            = isset( $wsp_on_product_data['box_bg'] ) ? $wsp_on_product_data['box_bg'] : '';
			$wsp_pro_price_color       = isset( $wsp_on_product_data['price_color'] ) ? $wsp_on_product_data['price_color'] : '';
			$wsp_pro_discount_color    = isset( $wsp_on_product_data['discount_color'] ) ? $wsp_on_product_data['discount_color'] : '';
			$wsp_pro_button_color      = isset( $wsp_on_product_data['button_color'] ) ? $wsp_on_product_data['button_color'] : '';
			$wsp_pro_button_bg_color   = isset( $wsp_on_product_data['button_bg_color'] ) ? $wsp_on_product_data['button_bg_color'] : '';
			$wsp_pro_arrow_color       = isset( $wsp_on_product_data['arrow_color'] ) ? $wsp_on_product_data['arrow_color'] : '';

			include 'partials/wsp-on-product-offer.php';
		}

		/**
		 * Get all products.
		 *
		 * @since    1.0.0
		 */
		public function get_all_products() {
			global $wpdb;
			$wsp_nonce = isset( $_POST['wsp_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_nonce'] ) ) : 0;
			if ( wp_verify_nonce( $wsp_nonce, 'wsp_nonce' ) ) {
				$search          = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
				$all_products    = $wpdb->get_results( $wpdb->prepare( 'select ID,post_title from ' . $wpdb->prefix . 'posts where post_type = "product" and post_status = "publish" and post_title like %s', '%' . $search . '%' ) );
				$return_products = array();
				$results         = array();
				$k               = 0;
				foreach ( $all_products as $single_product ) {
					$return_products[ $k ]['text'] = $single_product->post_title;
					$return_products[ $k ]['id']   = $single_product->ID;
					$k++;
				}
				$results['results'] = $return_products;
				echo wp_json_encode( $results );
			}
			exit();
		}

		/**
		 * Create offer on checkout page.
		 *
		 * @since    1.0.0
		 * @param object $post post object.
		 */
		public function create_checkout_offer( $post ) {
			global $wpdb;
			if ( 'checkout-offers' == $post->post_type ) {
				$post_id                = $post->ID;
				$wsp_cho                = get_post_meta( $post_id, 'wsp_cho', true );
				$wsp_cho_display_page   = isset( $wsp_cho['display_page'] ) ? $wsp_cho['display_page'] : 'both';
				$wsp_cho_display_page   = isset( $wsp_cho['display_page'] ) ? $wsp_cho['display_page'] : array( 'cart', 'checkout_bump' );
				if($wsp_cho_display_page == 'both') {
					$wsp_cho_display_page = array( 'cart', 'checkout_bump' );
				}
				if($wsp_cho_display_page == 'checkout') {
					$wsp_cho_display_page = array( 'checkout_bump');
				}
				if($wsp_cho_display_page == 'cart') {
					$wsp_cho_display_page = array( 'cart');
				}
				$wsp_cho_display_on     = isset( $wsp_cho['display_on'] ) ? $wsp_cho['display_on'] : '';
				$wsp_cho_categories_all = isset( $wsp_cho['categories_all'] ) ? $wsp_cho['categories_all'] : '';
				$wsp_cho_categories     = isset( $wsp_cho['categories'] ) ? $wsp_cho['categories'] : array();
				$wsp_cho_products_all   = isset( $wsp_cho['products_all'] ) ? $wsp_cho['products_all'] : '';
				$wsp_cho_products       = isset( $wsp_cho['products'] ) ? $wsp_cho['products'] : array();

				$wsp_cho_offer     = isset( $wsp_cho['offer'] ) ? $wsp_cho['offer'] : '';
				$wsp_cho_offer_amt = isset( $wsp_cho['offer_amt'] ) ? $wsp_cho['offer_amt'] : '';
				$wsp_cho_offer_pre = isset( $wsp_cho['offer_pre'] ) ? $wsp_cho['offer_pre'] : '';

				$wsp_cho_box_width = isset( $wsp_cho['box_width'] ) ? $wsp_cho['box_width'] : '500';
				$wsp_cho_box_width_pre = isset( $wsp_cho['box_width_pre'] ) ? $wsp_cho['box_width_pre'] : 'px';

				$wsp_cho_box_bg               = isset( $wsp_cho['box_bg'] ) ? $wsp_cho['box_bg'] : '';
				$wsp_cho_offer_title_color    = isset( $wsp_cho['offer_title_color'] ) ? $wsp_cho['offer_title_color'] : '';
				$wsp_cho_offer_title_bg_color = isset( $wsp_cho['offer_title_bg_color'] ) ? $wsp_cho['offer_title_bg_color'] : '';
				$wsp_cho_offer_text_color     = isset( $wsp_cho['offer_text_color'] ) ? $wsp_cho['offer_text_color'] : '';
				$wsp_cho_price_color          = isset( $wsp_cho['price_color'] ) ? $wsp_cho['price_color'] : '';
				$wsp_cho_display_position     = isset( $wsp_cho['display_position'] ) ? $wsp_cho['display_position'] : 'before_order_button';
				$wsp_cho_display_image        = isset( $wsp_cho['display_image'] ) ? $wsp_cho['display_image'] : '';
				$wsp_cho_display_price        = isset( $wsp_cho['display_price'] ) ? $wsp_cho['display_price'] : '';
				$wsp_cho_display_link         = isset( $wsp_cho['display_link'] ) ? $wsp_cho['display_link'] : '';
				$wsp_cho_img                  = isset( $wsp_cho['img'] ) ? $wsp_cho['img'] : '';
				$wsp_cho_img_src              = '';

				$wsp_cho_title   = isset( $wsp_cho['title'] ) ? $wsp_cho['title'] : '';
				$wsp_cho_content = isset( $wsp_cho['content'] ) ? $wsp_cho['content'] : '';

				if ( '' == $wsp_cho_title ) {
					$wsp_cho_title = esc_html__( 'Yes! I want to add this offer to my order', 'wc-sales-up' );
				}

				if ( '' == $wsp_cho_content ) {
					$wsp_cho_content = esc_html__( "One time offer! Offer ends soon. Get flat discount. Order now and save 50%. Hurry up before offer expires.", 'wc-sales-up' );
				}

				if ( '' == $wsp_cho_img ) {
					$wsp_cho_img_src = wc_placeholder_img_src( array( 300, 300 ) );
				} else {
					$image_s         = wp_get_attachment_image_src( $wsp_cho_img, array( 300, 300 ) );
					$wsp_cho_img_src = $image_s[0];
				}
				$offer_image = plugin_dir_url( __FILE__ ).'images/free-offer.jpg';
				include 'partials/wsp-checkout-offer.php';
			}
		}

		/**
		 * Update offer checkout meta.
		 *
		 * @since    1.0.0
		 * @param int $post_id post id.
		 */
		public function cho_update_meta( $post_id ) {
			global $wpdb;
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			$wsp_nonce = isset( $_POST['wsp_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_nonce'] ) ) : 0;
			if ( wp_verify_nonce( $wsp_nonce, 'wsp_nonce' ) ) {
				if ( isset( $_POST['wsp_cho'] ) ) {
					$wsp_cho = isset( $_POST['wsp_cho'] ) ? wsp_recursive_sanitize_text_field( wp_unslash( $_POST['wsp_cho'] ) ) : '';
					update_post_meta( $post_id, 'wsp_cho', $wsp_cho );
				}
			}
		}

		/**
		 * Get offer data.
		 *
		 * @since    1.0.0
		 */
		public function offer_data() {
			global $wpdb;
			$wsp_nonce = isset( $_POST['wsp_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_nonce'] ) ) : 0;
			if ( wp_verify_nonce( $wsp_nonce, 'wsp_nonce' ) ) {
				$product_id                            = isset( $_POST['wsp_cho_offer'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_cho_offer'] ) ) : '';
				$wsp_cho_offer_amt                     = isset( $_POST['wsp_cho_offer_amt'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_cho_offer_amt'] ) ) : '';
				$wsp_cho_offer_pre                     = isset( $_POST['wsp_cho_offer_pre'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_cho_offer_pre'] ) ) : '';
				$product_data                          = wc_get_product( $product_id );
				$return_product_data                   = array();
				$price                                 = $product_data->get_price();
				$return_product_data['original_price'] = $price;
				if ( $price > 0 ) {
					if ( 'fix' == $wsp_cho_offer_pre ) {
						$new_price = $price - $wsp_cho_offer_amt;
					} else {
						$new_price = $price - $price * $wsp_cho_offer_amt / 100;
					}
					$return_product_data['price']          = wc_price( $new_price );
					$return_product_data['original_price'] = wc_price( $price );
				} else {
					$return_product_data['price']          = wc_price( 0 );
					$return_product_data['original_price'] = wc_price( 0 );
				}
				$image_id = $product_data->get_image_id();
				if ( $image_id > 0 ) {
					$return_product_data['image_id'] = $image_id;
					$image_s                         = wp_get_attachment_image_src( $image_id, array( 300, 300 ) );
					$return_product_data['image']    = $image_s[0];
				} else {
					$return_product_data['image_id'] = '';
					$return_product_data['image']    = '';
				}
				echo wp_json_encode( $return_product_data );
			}
			exit();
		}

		/**
		 * Update offer data.
		 *
		 * @since    1.0.0
		 */
		public function pro_update_data() {
			$wsp_nonce = isset( $_POST['wsp_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_nonce'] ) ) : 0;
			if ( wp_verify_nonce( $wsp_nonce, 'wsp_nonce' ) ) {
				if ( isset( $_POST['wsp_pro'] ) ) {
					$wsp_on_product_data = wsp_recursive_sanitize_text_field( wp_unslash( $_POST['wsp_pro'] ) );
					update_option( 'wsp_on_product_data', $wsp_on_product_data );
				}
			}
		}

		/**
		 * Update offer checkout meta.
		 *
		 * @since    1.0.0
		 * @param array $product_data_tabs global data tabs.
		 * @return array $product_data_tabs updated data tabs.
		 */
		public function single_product_tab_title( $product_data_tabs ) {
			$product_data_tabs['wsp_together_product_settings'] = array(
				'label'  => __( 'Sales Up', 'wc-sales-up' ),
				'target' => 'wsp_together_product_settings_cblk',
			);
			return $product_data_tabs;
		}

		/**
		 * Display product tab content.
		 *
		 * @since    1.0.0
		 */
		public function single_product_tab_content() {
			global $woocommerce, $post, $wpdb;
			$wsp_tp_products_array  = '';
			$wsp_acc_products_array = '';
			$post_id                = $post->ID;
			$wsp_tp                 = get_post_meta( $post_id, 'wsp_tp', true );

			$wsp_tp_products  = isset( $wsp_tp['products'] ) ? $wsp_tp['products'] : array();
			$wsp_tp_discount  = isset( $wsp_tp['discount'] ) ? $wsp_tp['discount'] : '';
			$wsp_tp_offer_pre = isset( $wsp_tp['offer_pre'] ) ? $wsp_tp['offer_pre'] : 'percent';
			if ( is_array( $wsp_tp_products ) && ! empty( $wsp_tp_products ) ) {
				$wsp_cho_products_string = implode( ',', $wsp_tp_products );
				$wsp_tp_products_array   = wsp_get_products_from_id( $wsp_tp_products );
			}

			$wsp_aac_products = get_post_meta( $post_id, 'wsp_aac_products', true );
			if ( is_array( $wsp_aac_products ) && ! empty( $wsp_aac_products ) ) {
				$wsp_aac_products_string = implode( ',', $wsp_aac_products );
				$wsp_acc_products_array  = wsp_get_products_from_id( $wsp_aac_products );
			}

			include plugin_dir_path( __FILE__ ) . 'partials/wsp-single-product.php';
		}

		/**
		 * Update product tab content.
		 *
		 * @since    1.0.0
		 * @param int $post_id post id.
		 */
		public function single_product_tab_content_save( $post_id ) {
			$wsp_nonce = isset( $_POST['wsp_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wsp_nonce'] ) ) : 0;
			if ( wp_verify_nonce( $wsp_nonce, 'wsp_nonce' ) ) {
				if ( isset( $_POST['wsp_tp'] ) ) {
					$wsp_tp = wsp_recursive_sanitize_text_field( wp_unslash( $_POST['wsp_tp'] ) );
					update_post_meta( $post_id, 'wsp_tp', $wsp_tp );
				}
			}
		}

		/**
		 * Add new column for sorting.
		 *
		 * @since    1.0.0
		 * @param array $columns array of columns.
		 * @return array $columns array of columns.
		 */
		public function add_columns_header( $columns ) {
			$columns['wsp-sort'] = '';
			return $columns;
		}

		/**
		 * Add new column for sorting.
		 *
		 * @since    1.0.0
		 * @param string $column column name.
		 */
		public function add_columns_data( $column ) {
			switch ( $column ) {

				case 'wsp-sort':
					echo '<span class="dashicons dashicons-menu wsp_sortable"></span>';
					break;
			}
		}


		/**
		 * Duplicate offers.
		 *
		 * @since    1.0.0
		 * @param array  $actions plugin actions.
		 * @param object $post post.
		 * @return array $actions plugin actions.
		 */
		public function duplicate_offers( $actions, $post ) {
			if ( 'checkout-offers' == $post->post_type ) {
				$actions['duplicate'] = '<a href="' . wp_nonce_url( 'admin.php?action=wsp_duplicate_offer&post=' . $post->ID, basename( __FILE__ ), 'wsp_duplicate_nonce' ) . '" title="" rel="permalink">Duplicate</a>';
				if ( isset( $actions['view'] ) ) {
					unset( $actions['view'] );
				}
			}
			return $actions;
		}

		/**
		 * Duplicate offer.
		 *
		 * @since    1.0.0
		 */
		public function wsp_duplicate_offer() {
			global $wpdb;
			if ( ! ( isset( $_GET['post'] ) || isset( $_POST['post'] ) || ( isset( $_REQUEST['action'] ) && 'wsp_duplicate_offer' == $_REQUEST['action'] ) ) ) {
				wp_die( 'No Offer to duplicate has been supplied!' );
			}

			/*
			* Nonce verification
			*/
			$wsp_duplicate_nonce = isset( $_GET['wsp_duplicate_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['wsp_duplicate_nonce'] ) ) : 0;
			if ( ! isset( $_GET['wsp_duplicate_nonce'] ) || ! wp_verify_nonce( $wsp_duplicate_nonce, basename( __FILE__ ) ) ) {
				return;
			}

			/*
			* get the original post id
			*/
			$post_id = ( isset( $_GET['post'] ) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );

			/*
			* and all the original post data then
			*/
			$post = get_post( $post_id );

			/*
			* if you don't want current user to be the new post author,
			* then change next couple of lines to this: $new_post_author = $post->post_author;
			*/
			$current_user    = wp_get_current_user();
			$new_post_author = $current_user->ID;

			/*
			* if post data exists, create the post duplicate
			*/
			if ( isset( $post ) && null != $post ) {

				/*
				* new post data array
				*/
				$args = array(
					'comment_status' => $post->comment_status,
					'ping_status'    => $post->ping_status,
					'post_author'    => $new_post_author,
					'post_content'   => $post->post_content,
					'post_excerpt'   => $post->post_excerpt,
					'post_name'      => $post->post_name,
					'post_parent'    => $post->post_parent,
					'post_password'  => $post->post_password,
					'post_status'    => 'draft',
					'post_title'     => $post->post_title,
					'post_type'      => $post->post_type,
					'to_ping'        => $post->to_ping,
					'menu_order'     => $post->menu_order,
				);

				/*
				* insert the post by wp_insert_post() function
				*/
				$new_post_id = wp_insert_post( $args );

				/*
				* duplicate all post meta just in two SQL queries
				*/
				$post_meta_infos = $wpdb->get_results( $wpdb->prepare( 'select meta_key,meta_value from ' . $wpdb->postmeta . ' where post_id = %d', $post_id ) );
				if ( 0 != count( $post_meta_infos ) ) {
					$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
					foreach ( $post_meta_infos as $meta_info ) {
						$meta_key = $meta_info->meta_key;
						if ( 'wsp_impressions' == $meta_key || 'wsp_clicks' == $meta_key || 'wsp_purchases' == $meta_key ) {
							$meta_value = 0;
						} else {
							$meta_value = addslashes( $meta_info->meta_value );
						}
						$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
					}
					$sql_query .= implode( ' UNION ALL ', $sql_query_sel );
					$wpdb->query( $sql_query );
				}

				/*
				* finally, redirect to the edit post screen for the new draft
				*/
				wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
					exit;
			} else {
				wp_die( 'Post creation failed, could not find original post: ' . absint( $post_id ) );
			}

		}

	}
}
