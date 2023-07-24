<?php

namespace CTXFeed\V5\Common;
use CTXFeed\V5\Utility\Logs;

/**
 * Class Helper
 *
 * @package    CTXFeed\V5\Common
 * @subpackage CTXFeed\V5\Common
 */
class Helper {
	/**
	 * Object to array
	 *
	 * @param array $array |$object
	 *
	 * @return array
	 */
	public static function object_to_array( $obj ) {
		//only process if it's an object or array being passed to the function
		if ( is_object( $obj ) || is_array( $obj ) ) {
			$arr = (array) $obj;
			foreach ( $arr as &$item ) {
				//recursively process EACH element regardless of type
				$item = self::object_to_array( $item );
			}

			return $arr;
		} //otherwise (i.e. for scalar values) return without modification
		else {
			return $obj;
		}
	}

	/**
	 * Remove pro templates form merchant array.
	 *
	 * @param $merchants
	 * @param $templatesArr
	 *
	 * @return mixed
	 */
	public static function filter_merchant( $merchants ) {

		if ( WOO_FEED_PLUGIN_FILE  === 'woo-feed.php') {
			$removeTemplates = array( 'custom2' );
			foreach ( $merchants as $index => $group ) {
				foreach ( $group['options'] as $option_name => $option_value ) {
					if ( in_array( $option_name, $removeTemplates ) ) {
						unset( $merchants[ $index ]['options'][ $option_name ] );
					}
				}
			}
		}

		return $merchants;
	}

	/**
	 * Get plugin file i.e woo-feed.php or webappick-product-feed-for-woocommerce-pro.php
	 * @return false|mixed|string
	 */
	public static function get_plugin_file() {
		return WOO_FEED_PLUGIN_FILE;
	}

	/**
	 * Is the plugin is pro
	 * @return bool
	 */
	public static function is_pro() {
		if('woo-feed.php' === WOO_FEED_PLUGIN_FILE ) {
			return false;
		}

		//TODO: CHECK IF LICENSE IS ACTIVE FOR MORE TRANSPARENCY.
		if('webappick-product-feed-for-woocommerce-pro.php' === WOO_FEED_PLUGIN_FILE ) {
			return true;
		}

		return false;

	}

	/**
	 * Get Feed Directory
	 *
	 * @param string $provider
	 * @param string $feedType
	 *
	 * @return string
	 */
	public static  function get_file_dir( $provider, $feedType ) {
		$upload_dir = wp_get_upload_dir();

		return sprintf( '%s/woo-feed/%s/%s', $upload_dir['basedir'], $provider, $feedType );
	}

	/**
	 * str_replace() wrapper with trim()
	 *
	 * @param mixed $search The value being searched for, otherwise known as the needle.
	 *                          An array may be used to designate multiple needles.
	 * @param mixed $replace The replacement value that replaces found search values.
	 *                          An array may be used to designate multiple replacements.
	 * @param mixed $subject The string or array being searched and replaced on,
	 *                          otherwise known as the haystack.
	 * @param string $charlist [optional]
	 *                          Optionally, the stripped characters can also be specified using the charlist parameter.
	 *                          Simply list all characters that you want to be stripped.
	 *                          With this you can specify a range of characters.
	 *
	 * @return array|string
	 */
	public static  function str_replace_trim( $search, $replace, $subject, $charlist = " \t\n\r\0\x0B" ) {
		$replaced = str_replace( $search, $replace, $subject );
		if ( is_array( $replaced ) ) {
			return array_map(
				function ( $item ) use ( $charlist ) {
					return trim( $item, $charlist );
				},
				$replaced
			);
		} else {
			return trim( $replaced, $charlist );
		}
	}

	/**
	 * Remove Feed Option Name Prefix and return the slug
	 *
	 * @param string $feed_option_name
	 *
	 * @return string
	 */
	public static  function extract_feed_option_name( $feed_option_name ) {
		return str_replace( array( 'wf_feed_', 'wf_config' ), '', $feed_option_name );
	}


	/**
	 * Get Feed File URL
	 *
	 * @param string $fileName
	 * @param string $provider
	 * @param string $type
	 *
	 * @return string
	 */
	public  static  function get_file_url( $fileName, $provider, $type ) {
		$fileName   = self::extract_feed_option_name( $fileName );
		$upload_dir = wp_get_upload_dir();

		return esc_url(
			sprintf(
				'%s/woo-feed/%s/%s/%s.%s',
				$upload_dir['baseurl'],
				$provider,
				$type,
				$fileName,
				$type
			)
		);
	}


	/**
	 * Get Feed File URL
	 *
	 * @param string $fileName
	 * @param string $provider
	 * @param string $type
	 *
	 * @return string
	 */
	public static  function get_file( $fileName, $provider, $type ) {
		$fileName = self::extract_feed_option_name( $fileName );
		$path     = self::get_file_path( $provider, $type );

		return sprintf( '%s/%s.%s', untrailingslashit( $path ), $fileName, $type );
	}

	/**
	 * Get File Path for feed or the file upload path for the plugin to use.
	 *
	 * @param string $provider provider name.
	 * @param string $type feed file type.
	 *
	 * @return string
	 */
	public  static  function get_file_path( $provider = '', $type = '' ) {
		$upload_dir = wp_get_upload_dir();

		return sprintf( '%s/woo-feed/%s/%s/', $upload_dir['basedir'], $provider, $type );
	}

	/**
	 * Remove temporary feed files
	 *
	 * @param array $config Feed config
	 * @param string $fileName feed file name.
	 *
	 * @return void
	 */
	public static function unlink_tempFiles( $config, $fileName ) {
		$type = $config['feedType'];
		$ext  = $type;
		$path = self::get_file_dir( $config['provider'], $type );

		if ( 'csv' === $type || 'tsv' === $type || 'xls' === $type || 'xlsx' === $type ) {
			$ext = 'json';
		}
		$files = array(
			'headerFile' => $path . '/' . 'wf_store_feed_header_info_' . $fileName . '.' . $ext,
			'bodyFile'   => $path . '/' . 'wf_store_feed_body_info_' . $fileName . '.' . $ext,
			'footerFile' => $path . '/' . 'wf_store_feed_footer_info_' . $fileName . '.' . $ext,
		);

		Logs::write_log( $config['filename'], sprintf( 'Deleting Temporary Files (%s).', implode( ', ', array_values( $files ) ) ) );
		foreach ( $files as $key => $file ) {
			if ( file_exists( $file ) ) {
				unlink( $file ); // phpcs:ignore
			}
		}
	}


	/**
	 * Clear cache data.
	 *
	 * @param int _ajax_clean_nonce nonce number.
	 *
	 * @since 4.1.2
	 */
	public  static  function clear_cache_data( $cache_types  = []) {

		if ( empty( $cache_options ) ) {
			$cache_types = [ "woo_feed_attributes", "woo_feed_category_mapping", "woo_feed_dynamic_attributes", "woo_feed_attribute_mapping" ];
		}

		global $wpdb;
		//TODO add wpdb prepare statement
		$result = $wpdb->query( "DELETE FROM $wpdb->options WHERE ({$wpdb->options}.option_name LIKE '_transient_timeout___woo_feed_cache_%') OR ({$wpdb->options}.option_name LIKE '_transient___woo_feed_cache_%')" ); // phpcs:ignore


		return true;

	}

}
