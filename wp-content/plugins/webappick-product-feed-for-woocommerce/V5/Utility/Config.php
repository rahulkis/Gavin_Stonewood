<?php

namespace CTXFeed\V5\Utility;
/**
 * This class contain feed information.
 */

use CTXFeed\V5\Helper\FeedHelper;

class Config {
	/**
	 * @var array|bool
	 */
	public $feedInfo;

	/**
	 * @var array|bool
	 */
	private $config;
	/**
	 * @var mixed
	 */
	private $context;

	/**
	 * @param array  $feedInfo
	 * @param string $context
	 */
	public function __construct( $feedInfo, $context = 'view' ) {

		$this->feedInfo = $feedInfo;
		$this->context  = $context;
		$config         = isset( $this->feedInfo['feedrules'] ) ? $this->feedInfo['feedrules'] : $this->feedInfo;
		$this->set_config( $config );
	}

	public function __isset( $name ) {
		if ( isset( $this->config[ $name ] ) ) {
			return true;
		}

		return false;
	}

	public function __get( $name ) {
		return $this->config[ $name ];
	}

	public function __set( $name, $value ) {
		return $this->config[ $name ] = $value;
	}

	public function __unset( $name ) {
		unset( $this->config[ $name ] );
	}

	/**
	 *
	 * @return array
	 */
	public function get_feed_rules() {
		return isset( $this->feedInfo['feedrules'] ) ? $this->feedInfo['feedrules'] : $this->feedInfo;
	}

	/**
	 *  Get Feed name.
	 *
	 * @return string
	 */
	public function get_feed_id() {
		if ( isset( $this->config['feed_id'] ) && ! empty( $this->config['feed_id'] ) ) {
			return $this->config['feed_id'];
		}

		return false;
	}

	/**
	 *  Get Feed name.
	 *
	 * @return string
	 */
	public function get_feed_name() {
		if ( isset( $this->config['filename'] ) && ! empty( $this->config['filename'] ) ) {
			return $this->config['filename'];
		}

		return false;
	}

	/**
	 *  Get Feed file name.
	 *
	 * @return string
	 */
	public function get_feed_file_name( $array = false ) {
		if ( isset( $this->feedInfo['url'] ) && ! empty( $this->feedInfo['url'] ) ) {
			$fileInfo = pathinfo( $this->feedInfo['url'] );
			if ( $array ) {
				return $fileInfo;
			}

			return $fileInfo['basename'];
		}

		return false;
	}

	/**
	 *  Get Feed Template.
	 *
	 * @return string
	 */
	public function get_feed_template() {
		if ( isset( $this->config['provider'] ) ) {
			return $this->config['provider'];
		}

		return false;
	}

	/**
	 *  Get Feed Language.
	 *
	 * @return string
	 */
	public function get_feed_language() {
		if ( isset( $this->config['feedLanguage'] ) && ! empty( $this->config['feedLanguage'] ) ) {
			return $this->config['feedLanguage'];
		}

		return false;
	}

	/**
	 *  Get Feed Currency.
	 *
	 * @return string
	 */
	public function get_feed_currency() {
		if ( isset( $this->config['feedCurrency'] ) ) {
			return $this->config['feedCurrency'];
		}

		$attributes = $this->config['attributes'];
		$priceAttrs = [ 'price', 'current_price', 'price_with_tax', 'current_price_with_tax' ];
		foreach ( $priceAttrs as $price_attr ) {
			$key = array_search( $price_attr, $attributes, true );
			if ( $key ) {
				break;
			}
		}

		return isset( $this->config['suffix'][ $key ] ) ? $this->config['suffix'][ $key ] : get_woocommerce_currency();
	}

	/**
	 *  Get Feed Country.
	 *
	 * @return string
	 */
	public function get_feed_country() {
		if ( isset( $this->config['feed_country'] ) && ! empty( $this->config['feed_country'] ) ) {
			return $this->config['feed_country'];
		}

		return false;
	}

	/**
	 *  Get Feed File Type.
	 *
	 * @return string
	 */
	public function get_feed_file_type() {
		if ( isset( $this->config['feedType'] ) && ! empty( $this->config['feedType'] ) ) {
			return $this->config['feedType'];
		}

		return false;
	}

	/**
	 *  Get Feed File Type.
	 *
	 * @return string
	 */
	public function get_delimiter() {
		if ( isset( $this->config['delimiter'] ) && $this->config['delimiter'] !== "" ) {
			if ( 'tsv' === $this->get_feed_file_type() ) {
				$this->config['delimiter'] = "\t";

				return $this->config['delimiter'];
			}

			if ( ' ' === $this->config['delimiter'] ) {
				$this->config['delimiter'] = "\s";
			}

			return $this->config['delimiter'];
		}

		return false;
	}

	/**
	 *  Get Feed File Type.
	 *
	 * @return string
	 */
	public function get_enclosure() {
		if ( isset( $this->config['enclosure'] ) && ! empty( $this->config['enclosure'] ) && in_array( $this->config['enclosure'], [
				'double',
				'single'
			] ) ) {
			return ( 'double' === $this->config['enclosure'] ) ? '"' : "'";
		}

		return false;
	}

	/**
	 *  Get Feed items wrapper.
	 *
	 * @return string
	 */
	public function get_feed_items_wrapper() {
		if ( isset( $this->config['itemsWrapper'] ) && ! empty( $this->config['itemsWrapper'] ) ) {
			return $this->config['itemsWrapper'];
		}

		return false;
	}

	/**
	 *  Get Feed item wrapper.
	 *
	 * @return string
	 */
	public function get_feed_item_wrapper() {
		if ( isset( $this->config['itemWrapper'] ) && ! empty( $this->config['itemWrapper'] ) ) {
			return $this->config['itemWrapper'];
		}

		return false;
	}

	/**
	 *  Get Feed Extra Header.
	 *
	 * @return string
	 */
	public function get_feed_extra_header() {
		if ( isset( $this->config['extraHeader'] ) && ! empty( $this->config['extraHeader'] ) ) {
			return $this->config['extraHeader'];
		}

		return false;
	}

	/**
	 *  Get Feed Shipping Country.
	 *
	 * @return string
	 */
	public function get_shipping_country() {
		if ( isset( $this->config['shipping_country'] ) && ! empty( $this->config['shipping_country'] ) ) {
			return $this->config['shipping_country'];
		}

		return false;
	}

	/**
	 *  Get Feed Tax Country.
	 *
	 * @return string
	 */
	public function get_tax_country() {
		if ( isset( $this->config['tax_country'] ) && ! empty( $this->config['tax_country'] ) ) {
			return $this->config['tax_country'];
		}

		return false;
	}

	/**
	 *  Get String Replace config
	 *
	 * @return array|bool
	 */
	public function get_string_replace() {
		if ( ! empty( $this->config['str_replace'] ) ) {
			return $this->config['str_replace'];
		}

		return false;
	}

	/**
	 *  Get URL campaign parameter.
	 *
	 * @return array|bool
	 */
	public function get_campaign_parameters() {
		if ( isset( $this->config['campaign_parameters'] ) && ! empty( $this->config['campaign_parameters'] ) ) {
			return wp_parse_args(
				$this->config['campaign_parameters'],
				array(
					'utm_source'   => '',
					'utm_medium'   => '',
					'utm_campaign' => '',
					'utm_term'     => '',
					'utm_content'  => '',
				)
			);
		}

		return false;
	}

	/**
	 *  Status to remove backorder products.
	 *
	 * @return bool
	 */
	public function remove_backorder_product() {
		if ( isset( $this->config['is_backorder'] ) ) {
			return 'y' === $this->config['is_backorder'];
		}

		return false;
	}

	/**
	 *  Status to remove outofstock products.
	 *
	 * @return bool
	 */
	public function remove_outofstock_product() {
		if ( isset( $this->config['is_outOfStock'] ) ) {
			return 'y' === $this->config['is_outOfStock'];
		}

		return false;
	}

	/**
	 *  Status to remove empty description products.
	 *
	 * @return bool
	 */
	public function remove_empty_title() {
		if ( isset( $this->config['is_emptyTitle'] ) ) {
			return 'y' === $this->config['is_emptyTitle'];
		}

		return false;
	}

	/**
	 *  Status to remove empty description products.
	 *
	 * @return bool
	 */
	public function remove_empty_description() {
		if ( isset( $this->config['is_emptyDescription'] ) ) {
			return 'y' === $this->config['is_emptyDescription'];
		}

		return false;
	}

	/**
	 *  Status to remove empty image products.
	 *
	 * @return bool
	 */
	public function remove_empty_image() {
		if ( isset( $this->config['is_emptyImage'] ) ) {
			return 'y' === $this->config['is_emptyImage'];
		}

		return false;
	}

	/**
	 *  Status to remove empty price products.
	 *
	 * @return bool
	 */
	public function remove_empty_price() {
		if ( isset( $this->config['is_emptyPrice'] ) ) {
			return 'y' === $this->config['is_emptyPrice'];
		}

		return false;
	}

	public function remove_hidden_products() {
		if ( isset( $this->config['product_visibility'] ) ) {
			return $this->config['product_visibility'];
		}

		return false;
	}

	/**
	 *  Get Number Format.
	 *
	 * @return bool|array
	 */
	public function get_number_format() {
		if ( isset( $this->config['decimal_separator'] ) ) {
			$number_format = [
				'decimal_separator'  => apply_filters( 'ctx_feed_number_format_decimal_separator', $this->config['decimal_separator'], $this->config ),
				'thousand_separator' => apply_filters( 'ctx_feed_number_format_thousand_separator', $this->config['thousand_separator'], $this->config ),
				'decimals'           => apply_filters( 'ctx_feed_number_format_decimals', $this->config['decimals'], $this->config ),
			];

			return apply_filters( 'ctx_feed_number_format', $number_format, $this->config );
		}

		return false;
	}

	/**
	 * Get product Ids to exclude.
	 *
	 * @return array|bool
	 */
	public function get_products_to_exclude() {
		if ( isset( $this->config['filter_mode'] ) ) {
			$mode = $this->config['filter_mode'];
			if ( 'exclude' === $mode['product_ids'] && ! empty( $this->config['product_ids'] ) ) {
				return explode( ',', $this->config['product_ids'] );
			}
		}

		return false;
	}

	/**
	 * Get product Ids to include.
	 *
	 * @return array|bool
	 */
	public function get_products_to_include() {

		if ( isset( $this->config['filter_mode'] ) ) {
			$mode = $this->config['filter_mode'];
			if ( 'include' === $mode['product_ids'] && ! empty( $this->config['product_ids'] ) ) {
				return explode( ',', $this->config['product_ids'] );
			}
		}

		return false;
	}

	/**
	 * Get categories to exclude.
	 *
	 * @return mixed
	 */
	public function get_categories_to_exclude() {

		if ( isset( $this->config['filter_mode'] ) ) {
			$mode = $this->config['filter_mode'];
			if ( 'exclude' === $mode['categories'] && ! empty( $this->config['categories'] ) ) {
				return $this->config['categories'];
			}
		}

		return false;
	}

	/**
	 * Get categories to include.
	 *
	 * @return mixed
	 */
	public function get_categories_to_include() {

		if ( isset( $this->config['filter_mode'] ) ) {
			$mode = $this->config['filter_mode'];
			if ( 'include' === $mode['categories'] && ! empty( $this->config['categories'] ) ) {
				return $this->config['categories'];
			}
		}

		return false;
	}


	/**
	 * Get post statuses to include.
	 *
	 * @return mixed
	 */
	public function get_post_status_to_include() {
		$status = [ 'draft', 'pending', 'private', 'publish' ];
		if ( isset( $this->config['filter_mode'], $this->config['post_status'] ) && ! empty( $this->config['post_status'] ) ) {
			$mode = $this->config['filter_mode'];
			if ( 'include' === $mode['post_status'] ) {
				return $this->config['post_status'];
			}

			if ( 'exclude' === $mode['post_status'] ) {
				return array_unique( array_merge( array_diff( $status, $this->config['post_status'] ), array_diff( $status, $this->config['post_status'] ) ) );
			}
		}

		return false;
	}

	/**
	 * Get post statuses to include.
	 *
	 * @return string|bool
	 */
	public function get_vendors_to_include() {

		if ( isset( $this->config['vendors'] ) && ! empty( $this->config['vendors'] ) ) {
			return implode( ',', $this->config['vendors'] );
		}

		return false;
	}

	/**
	 * Get post statuses to include.
	 *
	 * @return bool
	 */
	public function get_variations_to_include() {

		return isset( $this->config['is_variations'] ) && in_array( $this->config['is_variations'], [ 'y', 'both' ] );
	}

	/**
	 * Get Advance Filter Config.
	 *
	 * @return array|bool
	 */
	public function get_advance_filters() {
		if ( isset( $this->config['fattribute'] ) ) {
			return [
				"fattribute"    => $this->config['fattribute'],
				"condition"     => $this->config['condition'],
				"filterCompare" => $this->config['filterCompare'],
				"concatType"    => $this->config['concatType'],
			];
		}

		return false;
	}

	/**
	 * Get FTP Config.
	 *
	 * @return array|bool
	 */
	public function get_ftp_config() {
		if ( isset( $this->config['ftpenabled'] ) && $this->config['ftpenabled'] ) {

//			if ( '0' === $this->config['ftpenabled'] ) {
//				return false;
//			}

			return [
				"type"     => $this->config['ftporsftp'],
				"host"     => $this->config['ftphost'],
				"port"     => $this->config['ftpport'],
				"username" => $this->config['ftpuser'],
				"password" => $this->config['ftppassword'],
				"path"     => $this->config['ftppath'],
				"mode"     => $this->config['ftpmode'],
			];
		}

		return false;
	}

	/**
	 * Get variable product config.
	 *
	 * @return array|bool
	 */
	public function get_variable_config() {
		if ( isset( $this->config['is_variations'] ) ) {
			return [
				"is_variations"     => $this->config['is_variations'],
				"variable_price"    => isset( $this->config['variable_price'] ) ? $this->config['variable_price'] : '',
				"variable_quantity" => isset( $this->config['variable_quantity'] ) ? $this->config['variable_quantity'] : '',
			];
		}

		return false;
	}

	/**
	 * Get composite product price settings.
	 *
	 * @return mixed
	 */
	public function get_composite_price_type() {
		if ( isset( $this->config['composite_price'] ) ) {
			return $this->config['composite_price'];
		}

		return false;
	}

	/**
	 * Get Feed Info
	 *
	 * @return array
	 */
	public function get_feed() {
		return $this->feedInfo;
	}

	/**
	 * Get Feed Configuration.
	 *
	 * @return array
	 */
	public function get_config() {
		return $this->config;
	}

	/**
	 * Set Feed Configuration.
	 *
	 * @return array
	 */
	private function set_config( $config ) {

		$defaults = array(
			'provider'              => '',
			'feed_country'          => '',
			'filename'              => '',
			'feedType'              => '',
			'ftpenabled'            => 0,
			'ftporsftp'             => 'ftp',
			'ftphost'               => '',
			'ftpport'               => '21',
			'ftpuser'               => '',
			'ftppassword'           => '',
			'ftppath'               => '',
			'ftpmode'               => 'active',
			'is_variations'         => 'y', // Only Variations (All Variations)
			'variable_price'        => 'first',
			'variable_quantity'     => 'first',
			'feedLanguage'          => apply_filters( 'wpml_current_language', null ),
			'feedCurrency'          => apply_filters( 'woocommerce_currency', get_option( 'woocommerce_currency' ) ),
			'itemsWrapper'          => 'products',
			'itemWrapper'           => 'product',
			'delimiter'             => ',',
			'enclosure'             => 'double',
			'extraHeader'           => '',
			'vendors'               => array(),
			// Feed Config
			'mattributes'           => array(), // merchant attributes
			'prefix'                => array(), // prefixes
			'type'                  => array(), // value (attribute) types
			'attributes'            => array(), // product attribute mappings
			'default'               => array(), // default values (patterns) if value type set to pattern
			'suffix'                => array(), // suffixes
			'output_type'           => array(), // output type (output filter)
			'limit'                 => array(), // limit or command
			// filters tab
			'composite_price'       => 'all_product_price',
			'product_ids'           => '',
			'categories'            => array(),
			'post_status'           => array( 'publish' ),
			'filter_mode'           => array(),
			'campaign_parameters'   => array(),
			'is_outOfStock'         => 'n',
			'is_backorder'          => 'n',
			'is_emptyDescription'   => 'n',
			'is_emptyTitle'         => 'n',
			'is_emptyImage'         => 'n',
			'is_emptyPrice'         => 'n',
			'product_visibility'    => 0,
			'shipping_country'      => '',
			'tax_country'           => '',
			// include hidden ? 1 yes 0 no
			'outofstock_visibility' => 0,
			// override wc global option for out-of-stock product hidden from catalog? 1 yes 0 no
			'ptitle_show'           => '',
			'decimal_separator'     => apply_filters( 'wc_get_price_decimal_separator', get_option( 'woocommerce_price_decimal_sep' ) ),
			'thousand_separator'    => stripslashes( apply_filters( 'wc_get_price_thousand_separator', get_option( 'woocommerce_price_thousand_sep' ) ) ),
			'decimals'              => absint( apply_filters( 'wc_get_price_decimals', get_option( 'woocommerce_price_num_decimals', 2 ) ) ),
		);

		$this->config                = wp_parse_args( $config, $defaults );
		$this->config['filter_mode'] = wp_parse_args(
			$this->config['filter_mode'],
			array(
				'product_ids' => 'include',
				'categories'  => 'include',
				'post_status' => 'include',
			)
		);

		if ( ! empty( $this->config['provider'] ) && is_string( $this->config['provider'] ) ) {
			/**
			 * filter parsed rules for provider
			 *
			 * @param array  $rules
			 * @param string $context
			 *
			 * @since 3.3.7
			 */
			$this->config = apply_filters( "woo_feed_{$this->config['provider']}_parsed_rules", $this->config, $this->context );
		}

		/**
		 * filter parsed rules
		 *
		 * @param array  $rules
		 * @param string $context
		 *
		 * @since 3.3.7 $provider parameter removed
		 */
		$this->config = apply_filters( 'woo_feed_parsed_rules', $this->config, $this->context );

		return $this->config;
	}

	public function save_feed( $data, $feed_option_name = null, $configOnly = true ) {
		if ( ! is_array( $data ) ) {
			return false;
		}
		if ( ! isset( $data['filename'], $data['feedType'], $data['provider'] ) ) {
			return false;
		}
		// unnecessary form fields to remove
		$removables = [ 'closedpostboxesnonce', '_wpnonce', '_wp_http_referer', 'save_feed_config', 'edit-feed' ];
		foreach ( $removables as $removable ) {
			if ( isset( $data[ $removable ] ) ) {
				unset( $data[ $removable ] );
			}
		}
		// parse rules
		$data = $this->set_config( $data );
		// Sanitize Fields
		$data = FeedHelper::sanitize_form_fields( $data );
		if ( empty( $feed_option_name ) ) {
			$feed_option_name = generate_unique_feed_file_name(
				$data['filename'],
				$data['feedType'],
				$data['provider'] );
		} else {
			$feed_option_name = FeedHelper::get_feed_option_name( $feed_option_name );
		}

		// get old config
		$old_data = get_option( 'wf_config' . $feed_option_name, array() );
		$update   = false;
		if ( is_array( $old_data ) && ! empty( $old_data ) ) {
			$update = true;
		}

		/**
		 * Filters feed data just before it is inserted into the database.
		 *
		 * @param array  $data             An array of sanitized config
		 * @param array  $old_data         An array of old feed data
		 * @param string $feed_option_name Option name
		 *
		 * @since 3.3.3
		 *
		 */
		$data = apply_filters( 'woo_feed_insert_feed_data', $data, $old_data, 'wf_config' . $feed_option_name );

		if ( $update ) {
			/**
			 * Before Updating Config to db
			 *
			 * @param array  $data             An array of sanitized config
			 * @param string $feed_option_name Option name
			 */
			do_action( 'woo_feed_before_update_config', $data, 'wf_config' . $feed_option_name );
		} else {
			/**
			 * Before inserting Config to db
			 *
			 * @param array  $data             An array of sanitized config
			 * @param string $feed_option_name Option name
			 */
			do_action( 'woo_feed_before_insert_config', $data, 'wf_config' . $feed_option_name );
		}
		$updated = ( $data === $old_data );
		if ( false === $updated ) {
			// Store Config.
			$updated = update_option( 'wf_config' . $feed_option_name, $data, false );
		}
		// update wf_feed if wp_config update ok...
		if ( $updated && false === $configOnly ) {
			$old_feed  = maybe_unserialize( get_option( 'wf_feed_' . $feed_option_name ) );
			$feed_data = array(
				'feedrules'    => $data,
				'url'          => woo_feed_get_file_url( $feed_option_name, $data['provider'], $data['feedType'] ),
				'last_updated' => date( 'Y-m-d H:i:s', strtotime( current_time( 'mysql' ) ) ),
				'status'       => isset( $old_feed['status'] ) && 1 === (int) $old_feed['status'] ? 1 : 0,
				// set old status or disable auto update.
			);

			$saved2 = update_option( 'wf_feed_' . $feed_option_name, maybe_serialize( $feed_data ), false );
		}

		if ( $update ) {
			/**
			 * After Updating Config to db
			 *
			 * @param array  $data             An array of sanitized config
			 * @param string $feed_option_name Option name
			 */
			do_action( 'woo_feed_after_update_config', $data, 'wf_config' . $feed_option_name );
		} else {
			/**
			 * After inserting Config to db
			 *
			 * @param array  $data             An array of sanitized config
			 * @param string $feed_option_name Option name
			 */
			do_action( 'woo_feed_after_insert_config', $data, 'wf_config' . $feed_option_name );
		}

		// return filename on success or update status
		return $updated ? $feed_option_name : $updated;
	}

	/**
	 * Get Feed URL.
	 *
	 * @return array|bool
	 */
	public function get_feed_url() {
		if ( isset( $this->feedInfo['url'] ) ) {
			return $this->feedInfo['url'];
		}

		return false;
	}

	/**
	 * Get Feed File Path.
	 *
	 * @return string|bool
	 */
	public function get_feed_path() {
		$upload_dir = wp_get_upload_dir();
		$url        = $this->get_feed_url();
		$file_name  = basename( $url );

		if ( ! isset( $this->config['provider'] ) && ! isset( $this->config['feedType'] ) ) {
			return false;
		}

		return sprintf( '%s/woo-feed/%s/%s/%s', $upload_dir['basedir'], $this->config['provider'], $this->config['feedType'], $file_name );
	}


	/**
	 * Get Feed Status.
	 *
	 * @return array|bool
	 */
	public function get_feed_status() {
		if ( isset( $this->feedInfo['status'] ) ) {
			return $this->feedInfo['status'];
		}

		return false;
	}
}
