<?php

namespace CTXFeed\V5\Filter;

use CTXFeed\V5\Utility\Config;
use CTXFeed\V5\Helper\CommonHelper;
use CTXFeed\V5\Utility\Logs;
use WC_Product;

/**
 *
 */
class ValidateProduct {
	/**
	 * Validate Product.
	 *
	 * @param     $product
	 * @param     $config
	 * @param int $id Product id.
	 *
	 * @return mixed|void
	 */
	public static function is_valid($product, $config, $id ) {
		$valid = true;
		// Skip for invalid products
		if ( ! is_object( $product ) ) {
			$valid = false;
			Logs::write_log( $config->filename, sprintf( 'Product with id: %s is not a valid object', $id ) );
		}

		// Skip orphaned variation.
		if ( $product->is_type( 'variation' ) && ! $product->get_parent_id() ) {
			$valid = false;
			Logs::write_log( $config->filename, sprintf( 'Orphaned Variation %s is skipped', $id ) );
		}

		// Skip for invisible products.
		if ( ! $product->is_visible() ) {
			$valid = false;
			Logs::write_log( $config->filename, sprintf( 'Product with id: %s is not visible on catalog.', $id ) );
		}

		// Remove unsupported product types.
		if ( ! in_array( $product->get_type(), CommonHelper::supported_product_types(), true ) ) {
			$valid = false;
			Logs::write_log( $config->filename, sprintf( 'Product with id: %s is a %s product. Product Type %s is not supported.', $id, $product->get_type(), $product->get_type() ) );
		}

		/**
		 * This filter hook should return false to exclude the product from feed.
		 */

		return apply_filters( 'ctx_validate_product_before_include', $valid, $product, $config );
	}
}
