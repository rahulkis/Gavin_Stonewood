<?php

namespace CTXFeed\V5\Helper;

use CTXFeed\V5\Common\Helper;
use \WP_Error;
class FeedHelper {


	/** Sanitize Feed Configs
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public static function sanitize_form_fields( $data ) {
		foreach ( $data as $k => $v ) {
			if ( true === apply_filters( 'woo_feed_sanitize_form_fields', true, $k, $v, $data ) ) {
				if ( is_array( $v ) ) {
					$v = self::sanitize_form_fields( $v );
				} else {
					// $v = sanitize_text_field( $v ); #TODO should not trim Prefix and Suffix field
				}
			}
			$data[ $k ] = apply_filters( 'woo_feed_sanitize_form_field', $v, $k );
		}

		return $data;
	}


	/**
	 * Generate Unique file Name.
	 * This will insure unique slug and file name for a single feed.
	 *
	 * @param string $filename
	 * @param string $type
	 * @param string $provider
	 *
	 * @return string|string[]
	 */
	public static function generate_unique_feed_file_name( $filename, $type, $provider ) {

		$feedDir      = Helper::get_file_dir( $provider, $type );
		$raw_filename = sanitize_title( $filename, '', 'save' );
		// check option name uniqueness ...
		$raw_filename = self::unique_feed_slug( $raw_filename, 'wf_feed_' );
		$raw_filename = sanitize_file_name( $raw_filename . '.' . $type );
		$raw_filename = wp_unique_filename( $feedDir, $raw_filename );
		$raw_filename = str_replace( '.' . $type, '', $raw_filename );

		return - 1 !== (int) $raw_filename ? $raw_filename : false;
	}

	/**
	 * Generate Unique slug for feed.
	 * This function only check database for existing feed for generating unique slug.
	 * Use generate_unique_feed_file_name() for complete unique slug name.
	 *
	 * @param string $slug slug for checking uniqueness.
	 * @param string $prefix prefix to check with. Optional.
	 * @param int $option_id option id. Optional option id to exclude specific option.
	 *
	 * @return string
	 * @see wp_unique_post_slug()
	 *
	 */
	public  static  function unique_feed_slug( $slug, $prefix = '', $option_id = null ) {
		$slug = CommonHelper::unique_option_name($slug, $prefix , $option_id );

		return $slug;
	}

	/**
	 * Sanitize And Save Feed config data (array) to db (option table)
	 *
	 * @param array $data data to be saved in db
	 * @param null $feed_option_name feed (file) name. optional, if empty or null name will be auto generated
	 * @param bool $configOnly save only wf_config or both wf_config and wf_feed_. default is only wf_config
	 *
	 * @return bool|string          return false if failed to update. return filename if success
	 */
	public static function save_feed_config_data( $data, $feed_option_name = null, $configOnly = true ) {
		if ( ! is_array( $data ) ) {
			return false;
		}
		if ( ! isset( $data['filename'], $data['feedType'], $data['provider'] ) ) {
			return false;
		}
		// unnecessary form fields to remove
		$removables = array( 'closedpostboxesnonce', '_wpnonce', '_wp_http_referer', 'save_feed_config', 'edit-feed' );
		foreach ( $removables as $removable ) {
			if ( isset( $data[ $removable ] ) ) {
				unset( $data[ $removable ] );
			}
		}
		// parse rules
		$data = self::parse_feed_rules( $data );
		// Sanitize Fields
		$data = self::sanitize_form_fields( $data );
		if ( empty( $feed_option_name ) ) {
			$feed_option_name = self::generate_unique_feed_file_name(
				$data['filename'],
				$data['feedType'],
				$data['provider']
			);
		} else {
			$feed_option_name = Helper::extract_feed_option_name( $feed_option_name );
		}

		// get old config
		$old_data = get_option( 'wf_config' . $feed_option_name, array() );
		$update   = false;
		$updated  = false;
		if ( is_array( $old_data ) && ! empty( $old_data ) ) {
			$update = true;
		}

		/**
		 * Filters feed data just before it is inserted into the database.
		 *
		 * @param array $data An array of sanitized config
		 * @param array $old_data An array of old feed data
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
			 * @param array $data An array of sanitized config
			 * @param string $feed_option_name Option name
			 */
			do_action( 'woo_feed_before_update_config', $data, 'wf_config' . $feed_option_name );
		} else {
			/**
			 * Before inserting Config to db
			 *
			 * @param array $data An array of sanitized config
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
				'url'          => Helper::get_file_url( $feed_option_name, $data['provider'], $data['feedType'] ),
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
			 * @param array $data An array of sanitized config
			 * @param string $feed_option_name Option name
			 */
			do_action( 'woo_feed_after_update_config', $data, 'wf_config' . $feed_option_name );
		} else {
			/**
			 * After inserting Config to db
			 *
			 * @param array $data An array of sanitized config
			 * @param string $feed_option_name Option name
			 */
			do_action( 'woo_feed_after_insert_config', $data, 'wf_config' . $feed_option_name );
		}

		// return filename on success or update status
		return $updated ? $feed_option_name : $updated;
	}


	private static  function parse_feed_rules( $rules = array(), $context = 'view' ) {

		if ( empty( $rules ) ) {
			$rules = array();
		}

		if ( Helper::is_pro() ) {
			$defaults = self::pro_default_feed_rules();
		}else{
			$defaults = self::free_default_feed_rules();
		}

		$rules                = wp_parse_args( $rules, $defaults );
		$rules['filter_mode'] = wp_parse_args(
			$rules['filter_mode'],
			array(
				'product_ids' => 'include',
				'categories'  => 'include',
				'post_status' => 'include',
			)
		);

		$rules['campaign_parameters'] = wp_parse_args(
			$rules['campaign_parameters'],
			array(
				'utm_source'   => '',
				'utm_medium'   => '',
				'utm_campaign' => '',
				'utm_term'     => '',
				'utm_content'  => '',
			)
		);

		if ( ! empty( $rules['provider'] ) && is_string( $rules['provider'] ) ) {
			/**
			 * filter parsed rules for provider
			 *
			 * @param array $rules
			 * @param string $context
			 *
			 * @since 3.3.7
			 *
			 */
			$rules = apply_filters( "woo_feed_{$rules['provider']}_parsed_rules", $rules, $context );
		}

		/**
		 * filter parsed rules
		 *
		 * @param array $rules
		 * @param string $context
		 *
		 * @since 3.3.7 $provider parameter removed
		 *
		 */
		return apply_filters( 'parsed_rules', $rules, $context );
	}

	/**
	 * Get pro version feed default rules.
	 * @param $rules
	 *
	 * @return mixed|null
	 */
	private static function free_default_feed_rules( $rules = [] ) {
		$defaults = array(
			'provider'            => '',
			'filename'            => '',
			'feedType'            => '',
			'feed_country'        => '',
			'ftpenabled'          => 0,
			'ftporsftp'           => 'ftp',
			'ftphost'             => '',
			'ftpport'             => '21',
			'ftpuser'             => '',
			'ftppassword'         => '',
			'ftppath'             => '',
			'ftpmode'             => 'active',
			'is_variations'       => 'y',
			'variable_price'      => 'first',
			'variable_quantity'   => 'first',
			'feedLanguage'        => apply_filters( 'wpml_current_language', null ),
			'feedCurrency'        => get_woocommerce_currency(),
			'itemsWrapper'        => 'products',
			'itemWrapper'         => 'product',
			'delimiter'           => ',',
			'enclosure'           => 'double',
			'extraHeader'         => '',
			'vendors'             => array(),
			// Feed Config
			'mattributes'         => array(), // merchant attributes
			'prefix'              => array(), // prefixes
			'type'                => array(), // value (attribute) types
			'attributes'          => array(), // product attribute mappings
			'default'             => array(), // default values (patterns) if value type set to pattern
			'suffix'              => array(), // suffixes
			'output_type'         => array(), // output type (output filter)
			'limit'               => array(), // limit or command
			// filters tab
			'composite_price'     => '',
			'shipping_country'    => '',
			'tax_country'         => '',
			'product_ids'         => '',
			'categories'          => array(),
			'post_status'         => array( 'publish' ),
			'filter_mode'         => array(),
			'campaign_parameters' => array(),

			'ptitle_show'        => '',
			'decimal_separator'  => wc_get_price_decimal_separator(),
			'thousand_separator' => wc_get_price_thousand_separator(),
			'decimals'           => wc_get_price_decimals(),
		);
		$rules    = wp_parse_args( $rules, $defaults );

		return apply_filters( 'woo_feed_free_default_feed_rules', $rules );
	}

	/**
	 * Get pro version feed default rules.
	 * @param $rules
	 *
	 * @return mixed|null
	 */
	private static function pro_default_feed_rules( $rules = [] ) {
		$defaults             = array(
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
			'feedCurrency'          => get_woocommerce_currency(),
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
			'is_emptyImage'         => 'n',
			'is_emptyPrice'         => 'n',
			'product_visibility'    => 0,
			// include hidden ? 1 yes 0 no
			'outofstock_visibility' => 0,
			// override wc global option for out-of-stock product hidden from catalog? 1 yes 0 no
			'ptitle_show'           => '',
			'decimal_separator'     => wc_get_price_decimal_separator(),
			'thousand_separator'    => wc_get_price_thousand_separator(),
			'decimals'              => wc_get_price_decimals(),
		);
		$rules    = wp_parse_args( $rules, $defaults );

		return apply_filters( 'woo_feed_pro_default_feed_rules', $rules );
	}



	/**
	 * @param $item
	 * @param $request
	 *
	 * @return void|\WP_Error|\WP_REST_Response
	 */
	public static function prepare_item_for_response( $item ) {
		$item['option_value'] = maybe_unserialize( get_option( $item['option_name'] ) );

		return $item;
	}


	public static function prepare_all_feeds( $feed_lists, $status ) {
		$lists = [];
		foreach ( $feed_lists as $feed ) {
			$item = self::prepare_item_for_response( $feed );
			if ( $status ) {
				if ( is_object( $item['option_value'] ) ) {
					$lists[] = $item;
					continue;
				}
				if ( 'active' === $status && 1 === $item['option_value']['status'] ) {
					$lists[] = $item;
				}
				if ( 'inactive' === $status && 0 === $item['option_value']['status'] ) {
					$lists[] = $item;
				}
			} else {
				$lists[] = $item;
			}
		}

		return $lists;
	}

	/**
	 * Remove Feed Option Name Prefix and return the slug
	 *
	 * @param string $feed
	 *
	 * @return string
	 */
	public static function get_feed_option_name( $feed ) {
		return str_replace( [ 'wf_feed_', 'wf_config' ], '', $feed );
	}


	/**
	 * Get Schedule Intervals
	 * @return mixed
	 */
	public static  function get_schedule_interval_options() {
		return apply_filters(
			'woo_feed_schedule_interval_options',
			array(
				WEEK_IN_SECONDS      => esc_html__( '1 Week', 'woo-feed' ),
				DAY_IN_SECONDS       => esc_html__( '24 Hours', 'woo-feed' ),
				12 * HOUR_IN_SECONDS => esc_html__( '12 Hours', 'woo-feed' ),
				6 * HOUR_IN_SECONDS  => esc_html__( '6 Hours', 'woo-feed' ),
				HOUR_IN_SECONDS      => esc_html__( '1 Hours', 'woo-feed' ),
			)
		);
	}

	public static function get_minimum_interval_option() {
		$intervals = array_keys( self::get_schedule_interval_options() );
		if ( ! empty( $intervals ) ) {
			return end( $intervals );
		}

		return 15 * MINUTE_IN_SECONDS;
	}


}
