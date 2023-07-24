<?php
namespace CTXFeed\V5\Price;
use CTXFeed\V5\Common\Config;
use WC_Product;
use WC_Product_Variable;
use WC_Product_Variation;
class WCCompositeProductPrice implements PriceInterface {

	private $product;
	private $config;

	/**
	 * @param WC_Product $product
	 * @param Config     $config
	 */
	public function __construct( $product, $config ) {

		$this->product = $product;
		$this->config  = $config;
	}

	/**
	 * Get Composite Price.
	 *
	 * @param bool $tax
	 *
	 * @return float|int
	 */
	public function composite_price( $price_type = 'price', $tax = false ) {

		// Parent Component Price
		$price = $this->product->$price_type();

		if ( ! class_exists( 'WC_Product_Composite' ) ) {
			return $price;
		}

		if ( isset( $this->config['composite_price'] ) && 'all_product_price' === $this->config['composite_price'] ) {

			$composite = new WC_Product_Composite( $this->product );

			if ( 'regular_price' === $price_type ) {
				$price = $composite->get_composite_regular_price();
			} else {
				$price = $composite->get_composite_price();
			}

			// Get WooCommerce Multi language Price by Currency.
			$price = $this->convert_currency( $price, $price_type );

			// Get Price with tax
			$price = $this->add_tax( $price, $tax );
		}

		return $price > 0 ? $price : '';
	}

	/**
	 * Get Regular Price.
	 *
	 * @param bool $tax
	 *
	 * @return float|int|string
	 */
	public function regular_price( $tax = false ) {
		return $this->composite_price( 'regular_price', $tax );
	}

	/**
	 * Get Price.
	 *
	 * @param bool $tax
	 *
	 * @return float|int|string
	 */
	public function price( $tax = false ) {
		return $this->composite_price( 'price', $tax );
	}

	/**
	 * Get Sale Price.
	 *
	 * @param bool $tax
	 *
	 * @return float|int|string
	 */
	public function sale_price( $tax = false ) {
		return $this->composite_price( 'sale_price', $tax );
	}

	/**
	 * Convert Currency.
	 *
	 * @param $price
	 * @param string $price_type price type (regular_price|price|sale_price)
	 *
	 * @return mixed|void
	 */
	public function convert_currency( $price, $price_type ) {

		return apply_filters( 'woo_feed_wcml_price',
			$price, $this->product->get_id(), $this->config->get_feed_currency(), '_' . $price_type
		);
	}

	/**
	 * Get Price with Tax.
	 *
	 * @return float|int
	 */
	public function add_tax( $price, $tax = false ) {
		if ( true === $tax ) {
			return woo_feed_get_price_with_tax( $price, $this->product );
		}

		return $price;
	}
}