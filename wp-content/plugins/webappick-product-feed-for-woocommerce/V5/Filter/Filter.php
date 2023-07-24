<?php /** @noinspection ALL */

namespace CTXFeed\V5\Filter;

use CTXFeed\V5\Utility\Config;
use WC_Product;

class Filter {
	/**
	 * @var WC_Product $product
	 */
	private $product;
	/**
	 * @var Config $config
	 */
	private $config;

	public function __construct( $product, $config ) {
		$this->product = $product;
		$this->config  = $config;
	}

	/**
	 * @return bool
	 */
	public function exclude() {
		$exclude = false;

		// Remove Out Of Stock Product.
		$exclude = $this->exclude_out_of_stock();

		// Remove On Backorder Product.
		$exclude = $this->exclude_back_order();

		//Remove Hidden Product.
		$exclude = $this->exclude_hidden_products();

		//Remove empty title product.
		$exclude = $this->exclude_empty_title_products();

		// Remove empty description product.
		$exclude = $this->exclude_empty_description_products();

		// Remove empty image product.
		$exclude = $this->exclude_empty_image_products();

		// Remove empty price product.
		$exclude = $this->exclude_empty_price_products();

		// Exclude for variation
		if ( $this->product->is_type( 'variation' ) ) {
			$exclude = $this->exclude_variation( $exclude );
		}


		return apply_filters( 'ctx_feed_filter_product', $exclude, $this->product, $this->config );
	}

	/**
	 * Remove out of stock products.
	 *
	 * @return bool
	 */
	public function exclude_out_of_stock_products() {
		if ( ! $this->config->remove_outofstock_product() || $this->product->get_stock_status() !== 'outofstock' && $this->product->get_stock_quantity() !== 0 ) {
			return false;
		}

		return true;
	}

	//TODO Out of stock visibility.

	/**
	 * Remove back order products.
	 *
	 * @return bool
	 */
	public function exclude_back_order_products() {
		if ( $this->config->remove_backorder_product() && $this->product->get_stock_status() === 'onbackorder' ) {
			return true;
		}

		return false;
	}

	/**
	 * Remove empty title products.
	 *
	 * @return bool
	 */
	public function exclude_empty_title_products() {
		if ( $this->config->remove_empty_title() && empty( $this->product->get_name() ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Remove hidden products.
	 *
	 * @return bool
	 */
	public function exclude_hidden_products() {
		if ( $this->config->remove_hidden_products() && $this->product->get_catalog_visibility() === 'hidden' ) {
			return true;
		}

		return false;
	}

	/**
	 * Remove empty description products.
	 *
	 * @return bool
	 */
	public function exclude_empty_description_products() {
		if ( $this->config->remove_empty_description() && empty( $this->product->get_description() ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Remove empty image products.
	 *
	 * @return bool
	 */
	public function exclude_empty_image_products() {
		if ( $this->config->remove_empty_image() && empty( $this->product->get_image( 'woocommerce_thumbnail', [], false ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Return Empty Price products.
	 *
	 * @return bool
	 */
	public function exclude_empty_price_products() {
		if ( $this->config->remove_empty_price() && empty( $this->product->get_price() ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Exclude Variations.
	 *
	 * @param $exclude
	 *
	 * @return bool|mixed
	 */
	public function exclude_variation( $exclude ) {
		$id = $this->product->get_id();
		if ( $this->product->is_type( 'variation' ) ) {
			$id = $this->product->get_parent_id();
		}

		// Remove products which are set to exclude.
		$exclude = $this->exclude_variation_products();

		// Only add products which are set to include.
		$exclude = $this->include_variation_products();

		// Remove categories which are set to exclude.
		$exclude = $this->exclude_variation_category( $id );

		// Only add categories which are set to include.
		$exclude = $this->include_variation_category( $id );

		// Only add product status which are set to include.
		$exclude = $this->include_variation_status();

		return $exclude;
	}

	public function exclude_variation_products() {
		$exclude_products = $this->config->get_products_to_exclude();
		if ( $exclude_products && in_array( $this->product->get_id(), $exclude_products, false ) ) {
			return true;
		}

		return false;
	}

	public function include_variation_products() {
		$include_products = $this->config->get_products_to_include();
		if ( $include_products && ! in_array( $this->product->get_id(), $include_products, true ) ) {
			return true;
		}

		return false;
	}

	public function exclude_variation_category( $id ) {
		$exclude_categories = $this->config->get_categories_to_exclude();
		if ( $exclude_categories && has_term( $exclude_categories, 'product_cat', $id ) ) {
			return true;
		}

		return false;
	}

	public function include_variation_category( $id ) {
		 $include_categories = $this->config->get_categories_to_include();
		if ( $include_categories && ! has_term( $include_categories, 'product_cat', $id ) ) {
			return true;
		}

		return false;
	}

	public function include_variation_status() {
		$variation_status = $this->config->get_post_status_to_include();
		if ( in_array( $this->product->get_status(), $variation_status ) ) {
			return true;
		}

		return false;
	}

	// TODO String Replace
	// TODO Number Format
	// TODO UTM Parameter
}
