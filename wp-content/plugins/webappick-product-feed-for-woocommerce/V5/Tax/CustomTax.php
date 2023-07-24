<?php

namespace CTXFeed\V5\Tax;

use CTXFeed\V5\Utility\Cache;
use WC_Tax;

class CustomTax implements TaxInterface {
	private $product;
	private $confg;
	
	public function __construct( $product, $config ) {
		$this->product = $product;
		$this->confg   = $config;
	}
	
	public function get_tax() {
		$taxes = $this->get_taxes();
		
		return $taxes[0];
	}
	
	public function get_taxes() {
		
		$taxes = Cache::get( 'ctx_feed_tax_info' );
		if ( ! $taxes ) {
			$all_tax_rates = [];
			
			// Retrieve all tax classes.
			$tax_classes = WC_Tax::get_tax_classes();
			
			// Make sure "Standard rate" (empty class name) is present.
			if ( ! in_array( '', $tax_classes, true ) ) {
				array_unshift( $tax_classes, '' );
			}
			
			// For each tax class, get all rates.
			if ( ! empty( $tax_classes ) ) {
				foreach ( $tax_classes as $tax_class ) {
					$taxes = WC_Tax::get_rates_for_tax_class( $tax_class );
					if ( ! empty( $taxes ) ) {
						foreach ( $taxes as $key => $tax ) {
							$tax_class_name                                              = ( '' === $tax_class ) ? 'standard-rate' : $tax->tax_rate_class;
							$all_tax_rates [ $tax_class_name ][ $key ]['id']             = $tax->tax_rate_id;
							$all_tax_rates [ $tax_class_name ][ $key ]['country']        = $tax->tax_rate_country;
							$all_tax_rates [ $tax_class_name ][ $key ]['state']          = $tax->tax_rate_state;
							$all_tax_rates [ $tax_class_name ][ $key ]['postcode']       = is_array( $tax->postcode ) ? implode( ',', $tax->postcode ) : $tax->postcode;
							$all_tax_rates [ $tax_class_name ][ $key ]['postcode_count'] = $tax->postcode_count;
							$all_tax_rates [ $tax_class_name ][ $key ]['city']           = is_array( $tax->city ) ? implode( ',', $tax->city ) : $tax->city;
							$all_tax_rates [ $tax_class_name ][ $key ]['city_count ']    = $tax->city_count;
							$all_tax_rates [ $tax_class_name ][ $key ]['rate']           = number_format( $tax->tax_rate, 2 );
							$all_tax_rates [ $tax_class_name ][ $key ]['name']           = $tax->tax_rate_name;
							$all_tax_rates [ $tax_class_name ][ $key ]['shipping']       = $tax->tax_rate_shipping;
							$all_tax_rates [ $tax_class_name ][ $key ]['priority']       = $tax->tax_rate_priority;
						}
					}
				}
			}
			
			$taxes = ! empty( $all_tax_rates ) ? $all_tax_rates : false;
			Cache::set( 'ctx_feed_tax_info', $taxes );
		}
		
		return $taxes;
	}
	
	public function merchant_formatted_tax($key) {}
	
}