<?php

namespace CTXFeed\V5\Common;

use CTXFeed\V5\Query\QueryFactory;
use CTXFeed\V5\Utility\Config;

class Factory {
	/**
	 * Get Product Ids by Query Type
	 *
	 * @param $config
	 *
	 * @return \CTXFeed\V5\Query\Query
	 */
	public static function get_product_ids( $config ) {
		return QueryFactory::get_ids( $config );
	}
	
	/**
	 * @param string $name Feed Name
	 *
	 * @return \CTXFeed\V5\Utility\Config
	 */
	public static function get_feed_config( $name ) {
		$name       = str_replace( [ 'wf_feed_', 'wf_config' ], '', sanitize_text_field( wp_unslash( $name ) ) );
		$feedOption = maybe_unserialize( get_option( "wf_config" . $name ) );
		
		return new Config( $feedOption );
	}
	
	/**
	 * @param string $name Feed Name
	 *
	 * @return \CTXFeed\V5\Utility\Config
	 */
	public static function get_feed_info( $name ) {
		$name       = str_replace( [ 'wf_feed_', 'wf_config' ], '', sanitize_text_field( wp_unslash( $name ) ) );
		$feedOption = maybe_unserialize( get_option( "wf_feed_" . $name ) );
		
		return new Config( $feedOption );
	}
}