<?php
namespace CTXFeed\V5\Template;
use CTXFeed\V5\Utility\Config;
use CTXFeed\V5\Structure\CustomStructure;

class TemplateFactory {

	public static function MakeFeed( $ids, $config ) {
		$class = "\CTXFeed\V5\Template\\".ucfirst( $config->get_feed_template() ) . 'Feed';

		if ( ! class_exists( $class ) && in_array( $config->provider, self::get_grouped_templates(), true ) ) {
			$class = array_search( $config->provider, self::get_grouped_templates(), true );

			return new Template( new $class( $ids, $config ) );
		}

		if ( class_exists( $class ) ) {
			return new Template( new $class( $ids, $config ) );
		}

		return new Template( new CustomTemplate( $ids, $config ) );
	}

	public static function get_structure( $config ) {

		$template  = ucfirst( str_replace( [ '_', '.' ], '', $config->provider ) );
		$file_type = strtoupper( $config->feedType );
		$class     = "\CTXFeed\V5\Structure\\".$template . 'Structure';
		$method    = 'get' . $file_type . 'Structure';

		if ( class_exists( $class ) && method_exists( $class, $method ) ) {
			return ( new $class( $config ) )->$method();
		}

		return ( new CustomStructure( $config ) )->$method();
	}

	public static function get_feed_config( $config ) {
		return new Config( $config );
	}

	public static function get_grouped_templates() {
		return [
			'google'    => [ 'google_shopping_action', 'google_local', 'google_local_inventory' ],
			'custom2'   => [ 'custom2', 'admarkt', 'yandex_xml', 'glami' ],
			'pinterest' => [ 'pinterest', 'pinterest_rss' ],
		];
	}
}
