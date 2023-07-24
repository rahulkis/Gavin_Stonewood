<?php

namespace CTXFeed\V5\Override;
class GoogleTemplate {
	public function __construct() {
		add_filter( 'woo_feed_get_google_color_attribute', [
			$this,
			'woo_feed_get_google_color_size_attribute_callback'
		], 10, 5 );
		
		add_filter( 'woo_feed_get_google_size_attribute', [
			$this,
			'woo_feed_get_google_color_size_attribute_callback'
		], 10, 5 );
		
		add_filter( 'woo_feed_get_google_attribute', [
			$this,
			'woo_feed_get_google_attribute_callback'
		], 10, 5 );
	}
	
	public function woo_feed_get_google_color_size_attribute_callback( $output ) {
		return str_replace( [ ' ', ',' ], [ '', '/' ], $output );
	}
	
	public function woo_feed_get_google_attribute_callback( $output, $product, $config, $product_attribute, $merchant_attribute ) {
		$weightAttributes    = [ 'product_weight', 'shipping_weight' ];
		$dimensionAttributes = [
			'product_length',
			'product_width',
			'product_height',
			'shipping_length',
			'shipping_width',
			'shipping_height'
		];
		
		$wc_unit  = '';
		$override = false;
		if ( in_array( $product_attribute, $weightAttributes, true ) ) {
			$override = true;
			$wc_unit  = ' ' . get_option( 'woocommerce_weight_unit' );
		}
		
		if ( in_array( $product_attribute, $dimensionAttributes, true ) ) {
			$override = true;
			$wc_unit  = ' ' . get_option( 'woocommerce_dimension_unit' );
		}
		
		if ( ! $override ) {
			return $output;
		}
		
		$attributes = ( $config->attributes ) ?: false;
		
		if ( ! $attributes ) {
			return $output;
		}
		
		$key = array_search( $product_attribute, $attributes, true );
		if ( isset( $config->suffix ) && ! empty( $key ) && array_key_exists( $key, $config->suffix ) ) {
			$unit = $config->suffix[ $key ];
			
			if ( empty( $unit ) && ! empty( $output ) ) {
				$output .= $wc_unit;
			}
		}
		
		
		return $output;
	}
}

