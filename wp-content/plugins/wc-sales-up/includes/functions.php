<?php
/**
 * Global functions defined here.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    WC_Sales_Up
 * @subpackage WC_Sales_Up/includes
 */

if ( ! function_exists( 'wsp_get_products_from_id' ) ) {
	/**
	 * Get products from ids.
	 *
	 * @since    1.0.0
	 * @param array $wsp_products_array array of ids.
	 * @return array $product_array array of products
	 */
	function wsp_get_products_from_id( $wsp_products_array ) {
		global $wpdb;
		if ( '' != $wsp_products_array ) {
			$wsp_products_ct       = count( $wsp_products_array );
			$string_placeholders   = array_fill( 0, $wsp_products_ct, '%s' );
			$placeholders_products = implode( ', ', $string_placeholders );
			$all_products          = $wpdb->get_results( $wpdb->prepare( 'select ID,post_title from ' . $wpdb->prefix . "posts where ID in ( $placeholders_products )", $wsp_products_array ) );
			$product_array         = array();
			foreach ( $all_products as $product_d ) {
				$product_array[ $product_d->ID ] = $product_d->post_title;
			}
			return $product_array;
		}
		return '';
	}
}

if ( ! function_exists( 'wsp_get_small_image_from_id' ) ) {
	/**
	 * Get products from ids.
	 *
	 * @since    1.0.0
	 * @param int $image_id image id.
	 * @return string $image_s image link.
	 */
	function wsp_get_small_image_from_id( $image_id ) {
		global $wpdb;
		$image_size  = apply_filters( 'wsp_small_image', array( 100, 100 ) );
		$image_array = wp_get_attachment_image_src( $image_id, $image_size );
		if ( is_array( $image_array ) && ! empty( $image_array ) ) {
			$image_s = $image_array[0];
		} else {
			$image_s = wc_placeholder_img_src( $image_size );
		}
		return $image_s;
	}
}

if ( ! function_exists( 'wsp_args_kses' ) ) {
	/**
	 * Get allowed html attributes.
	 *
	 * @since    1.0.0
	 * @return array $args_kses allowed attributes
	 */
	function wsp_args_kses() {
		$args_kses = array(
			'span' => array(
				'class' => true,
				'id'    => true,
			),
			'bdi',
			'ul'   => array(
				'class' => true,
				'id'    => true,
			),
			'li'   => array(
				'class' => true,
				'id'    => true,
			),
			'div'  => array(
				'class' => true,
				'id'    => true,
			),
			'p'    => array(
				'class' => true,
				'id'    => true,
			),
			'span' => array(
				'class' => true,
				'id'    => true,
			),
			'a'    => array(
				'href'  => true,
				'class' => true,
				'id'    => true,
			),
			'b'    => array(
				'class' => true,
				'id'    => true,
			),
			'br',
		);
		return $args_kses;
	}
}

if ( ! function_exists( 'wsp_recursive_sanitize_text_field' ) ) {
	/**
	 * Recursive sanitation for an array
	 *
	 * @since    1.0.0
	 * @param array $array array.
	 * @return mixed $array
	 */
	function wsp_recursive_sanitize_text_field( $array ) {
		if ( is_array( $array ) ) {
			foreach ( $array as $key => &$value ) {
				if ( is_array( $value ) ) {
					$value = wsp_recursive_sanitize_text_field( $value );
				} else {
					if ( 'content' == $key || 'title' == $key ) {
						$value = wp_kses( $value, wsp_args_kses() );
					} else {
						$value = sanitize_text_field( $value );
					}
				}
			}
		} else {
			$array = sanitize_text_field( $array );
		}
		return $array;
	}
}
