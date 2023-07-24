<?php

namespace CTXFeed\V5\Shipping;

use CTXFeed\V5\Utility\Settings;

class CustomShipping extends Shipping {
	
	/**
	 * @var \CTXFeed\V5\Utility\Config $config
	 */
	private $config;
	
	public function __construct( $product, $config ) {
		parent::__construct( $product, $config );
		$this->config = $config;
	}
	
	/**
	 * @throws \Exception
	 */
	public function get_shipping_info() {
		$this->get_shipping_zones();
		
		return $this->shipping;
	}
	
	/**
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function get_shipping( $key = '' ) {
		$this->get_shipping_zones();
		$str = "";
		foreach ( $this->shipping as $shipping ) {
			
			$allow_all_shipping = Settings::get( 'allow_all_shipping' );
			if ( 'no' === $allow_all_shipping ) {
				$country = $this->config->get_shipping_country();
				
				if ( $shipping['country'] !== $country ) {
					continue;
				}
			}
			
			$currency = $this->config->get_feed_currency();
			$str      .= "<shipping>" . PHP_EOL;
			$str      .= "<country>" . $shipping['country'] . "</country>" . PHP_EOL;
			$str      .= ( empty( $shipping['state'] ) ) ? "" : "<region>" . $shipping['state'] . "</region>" . PHP_EOL;
			$str      .= ( empty( $shipping['postcode'] ) ) ? "" : "<postal_code>" . $shipping['postcode'] . "</postal_code>" . PHP_EOL;
			$str      .= ( empty( $shipping['service'] ) ) ? "" : "<service>" . $shipping['service'] . "</service>" . PHP_EOL;
			$str      .= "<price>" . $shipping['price'] . " " . $currency . "</price>" . PHP_EOL;
			$str      .= "</shipping>" . PHP_EOL;
		}
		
		return $str;
	}
}