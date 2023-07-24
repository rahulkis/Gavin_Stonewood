<?php

namespace CTXFeed\V5\Product;

use CTXFeed\V5\Helper\ProductHelper;
use CTXFeed\V5\Output\AttributeMapping;
use CTXFeed\V5\Output\CategoryMapping;
use CTXFeed\V5\Output\DynamicAttributes;
use CTXFeed\V5\Override\OverrideFactory;
use WC_Abstract_Legacy_Product;
use WC_Product;
use WC_Product_External;
use WC_Product_Grouped;
use WC_Product_Variable;
use WC_Product_Variation;

class AttributeValueByType {

	/**
	 * Advance Custom Field (ACF) Prefix
	 *
	 * @since 3.1.18
	 * @var string
	 */
	const PRODUCT_ACF_FIELDS = 'acf_fields_';
	/**
	 * Post meta prefix for dropdown item
	 *
	 * @since 3.1.18
	 * @var string
	 */
	const POST_META_PREFIX = 'wf_cattr_';
	/**
	 * Product Attribute (taxonomy & local) Prefix
	 *
	 * @since 3.1.18
	 * @var string
	 */
	const PRODUCT_ATTRIBUTE_PREFIX = 'wf_attr_';
	/**
	 * Product Taxonomy Prefix
	 *
	 * @since 3.1.18
	 * @var string
	 */
	const PRODUCT_TAXONOMY_PREFIX = 'wf_taxo_';
	/**
	 * Product Category Mapping Prefix
	 *
	 * @since 3.1.18
	 * @var string
	 */
	const PRODUCT_CATEGORY_MAPPING_PREFIX = 'wf_cmapping_';
	/**
	 * Product Dynamic Attribute Prefix
	 *
	 * @since 3.1.18
	 * @var string
	 */
	const PRODUCT_DYNAMIC_ATTRIBUTE_PREFIX = 'wf_dattribute_';
	/**
	 * WordPress Option Prefix
	 *
	 * @since 3.1.18
	 * @var string
	 */
	const WP_OPTION_PREFIX = 'wf_option_';
	/**
	 * Extra Attribute Prefix
	 *
	 * @since 3.2.20
	 */
	const PRODUCT_EXTRA_ATTRIBUTE_PREFIX = 'wf_extra_';
	/**
	 * Product Attribute Mappings Prefix
	 *
	 * @since 3.3.2*
	 */
	const PRODUCT_ATTRIBUTE_MAPPING_PREFIX = 'wp_attr_mapping_';
	/**
	 * Product Custom Field Prefix
	 *
	 * @since 3.1.18
	 * @var string
	 */
	const PRODUCT_CUSTOM_IDENTIFIER = 'woo_feed_';


	/**
	 * WP Option Name
	 *
	 * @since 6.1.1
	 * @var string
	 */
	const WP_OPTION_NAME = 'wpfp_option';

	private $attribute;
	private $merchant_attribute;
	private $product;
	private $productInfo;
	private $config;

	/**
	 * @param $attribute
	 * @param $merchant_attribute
	 * @param $product
	 * @param $config
	 */
	public function __construct( $attribute, $product, $config, $merchant_attribute = null ) {
		$this->attribute          = $attribute;
		$this->merchant_attribute = $merchant_attribute;
		$this->product            = $product;
		$this->config             = $config;
		$this->productInfo        = new ProductInfo( $this->product, $this->config );

		// Load Merchant Template Override File.
		// OverrideFactory::init( $config );
	}

	/**
	 * Get product attribute value by attribute type.
	 *
	 * @return mixed|void
	 */
	public function get_value( $attr = '' ) {

		if ( ! empty( $attr ) ) {
			$this->attribute = $attr;
		}

		if ( method_exists( $this->productInfo, $this->attribute ) ) {
			$attribute = $this->attribute;
			$output    = $this->productInfo->$attribute();
		} elseif ( false !== strpos( $this->attribute, self::PRODUCT_EXTRA_ATTRIBUTE_PREFIX ) ) {
			$attribute = str_replace( self::PRODUCT_EXTRA_ATTRIBUTE_PREFIX, '', $this->attribute );

			/**
			 * Filter output for extra attribute, which can be added via 3rd party plugins.
			 *
			 * @param string                                                                                                          $output  the output
			 * @param WC_Product|WC_Product_Variable|WC_Product_Variation|WC_Product_Grouped|WC_Product_External|WC_Product_Composite $product Product Object.
			 *
			 *
			 * @since 3.3.5
			 */
			return apply_filters( "woo_feed_get_extra_{$attribute}_attribute", '', $this->product, $this->config );
		} elseif ( false !== strpos( $this->attribute, 'csv_tax_' ) ) {
			$key    = str_replace( 'csv_tax_', '', $this->attribute );
			$output = $this->productInfo->tax( (string) $key );
		} elseif ( false !== strpos( $this->attribute, 'csv_shipping_' ) ) {
			$key    = str_replace( 'csv_shipping_', '', $this->attribute );
			$output = $this->productInfo->shipping( (string) $key );
		} elseif ( false !== strpos( $this->attribute, self::PRODUCT_ACF_FIELDS ) ) {
			$output = ProductHelper::get_acf_field( $this->product, $this->attribute );
		} elseif ( false !== strpos( $this->attribute, self::PRODUCT_ATTRIBUTE_MAPPING_PREFIX ) ) {
			//$output = ProductHelper::get_attribute_mapping( $this->product, $this->attribute, $this->merchant_attribute, $this->config );
			$output = AttributeMapping::getMappingValue( $this->product, $this->attribute, $this->merchant_attribute, $this->config );
		} elseif ( false !== strpos( $this->attribute, self::PRODUCT_DYNAMIC_ATTRIBUTE_PREFIX ) ) {
			//$output = ProductHelper::get_dynamic_attribute( $this->product, $this->attribute, $this->merchant_attribute, $this->config );
			$output = DynamicAttributes::getDynamicAttributeValue(  $this->attribute, $this->merchant_attribute, $this->product, $this->config );
		} elseif ( false !== strpos( $this->attribute, self::PRODUCT_CUSTOM_IDENTIFIER ) || woo_feed_strpos_array( array(
				'_identifier_gtin',
				'_identifier_ean',
				'_identifier_mpn'
			), $this->attribute ) ) {
			$output = ProductHelper::get_custom_filed( $this->attribute, $this->product, $this->config );
		} elseif ( false !== strpos( $this->attribute, self::PRODUCT_ATTRIBUTE_PREFIX ) ) {
			$this->attribute = str_replace( self::PRODUCT_ATTRIBUTE_PREFIX, '', $this->attribute );
			$output          = ProductHelper::get_product_attribute( $this->attribute, $this->product, $this->config );
		} elseif ( false !== strpos( $this->attribute, self::POST_META_PREFIX ) ) {
			$this->attribute = str_replace( self::POST_META_PREFIX, '', $this->attribute );
			$output          = ProductHelper::get_product_meta( $this->attribute, $this->product, $this->config );
		} elseif ( false !== strpos( $this->attribute, self::PRODUCT_TAXONOMY_PREFIX ) ) {
			$this->attribute = str_replace( self::PRODUCT_TAXONOMY_PREFIX, '', $this->attribute );
			$output          = ProductHelper::get_product_taxonomy( $this->attribute, $this->product, $this->config );
		} elseif ( false !== strpos( $this->attribute, self::PRODUCT_CATEGORY_MAPPING_PREFIX ) ) {
			$id     = $this->product->is_type( 'variation' ) ? $this->product->get_parent_id() : $this->product->get_id();
			//$output = ProductHelper::get_category_mapping( $this->attribute, $id );
			$output = CategoryMapping::getCategoryMappingValue( $this->attribute, $id );
		} elseif ( false !== strpos( $this->attribute, self::WP_OPTION_PREFIX ) ) {
			$optionName = str_replace( self::WP_OPTION_PREFIX, '', $this->attribute );
			$output     = get_option( $optionName );
		} elseif ( strpos( $this->attribute, 'image_' ) === 0 ) {
			// For additional image method images() will be used with extra parameter - image number
			$imageKey = explode( '_', $this->attribute );
			if ( empty( $imageKey[1] ) || ! is_numeric( $imageKey[1] ) ) {
				$imageKey[1] = '';
			}
			$output = $this->productInfo->images( $imageKey[1] );
		} else {
			// return the attribute so multiple attribute can be used with separator to make custom attribute.
			$output = $this->attribute;
		}

		// Json encode if value is an array
		if ( is_array( $output ) ) {
			$output = wp_json_encode( $output );
		}

		return $this->apply_filters_to_attribute_value( $output, $this->merchant_attribute );
	}

	/**
	 *  Apply Filter to Attribute value
	 *
	 * @param $output
	 * @param $merchant_attribute
	 *
	 * @return mixed|void
	 */
	protected function apply_filters_to_attribute_value( $output, $merchant_attribute ) {
		$product_attribute = $this->attribute;
		/**
		 * Filter attribute value
		 *
		 * @param string                     $output  the output
		 * @param WC_Abstract_Legacy_Product $product Product Object.
		 * @param array feed config/rule
		 *
		 * @since 3.4.3
		 *
		 */
		$output = apply_filters( 'woo_feed_get_attribute', $output, $this->product, $this->config, $product_attribute, $merchant_attribute );

		/**
		 * Filter attribute value before return based on product attribute name
		 *
		 * @param string                     $output  the output
		 * @param WC_Abstract_Legacy_Product $product Product Object.
		 * @param array feed config/rule
		 *
		 * @since 3.3.5
		 *
		 */

		$output = apply_filters( "woo_feed_get_{$product_attribute}_attribute", $output, $this->product, $this->config, $product_attribute, $merchant_attribute );

		/**
		 * Filter attribute value before return based on merchant name
		 *
		 * @param string                     $output  the output
		 * @param WC_Abstract_Legacy_Product $product Product Object.
		 * @param array feed config/rule
		 *
		 * @since 3.3.5
		 *
		 */

		$output = apply_filters( "woo_feed_get_{$this->config->provider}_attribute", $output, $this->product, $this->config, $product_attribute, $merchant_attribute );

		/**
		 * Filter attribute value before return based on merchant and merchant attribute name
		 *
		 * @param string                     $output  the output
		 * @param WC_Abstract_Legacy_Product $product Product Object.
		 * @param array feed config/rule
		 *
		 * @since 3.3.7
		 *
		 */
		$merchant_attribute = str_replace( [ ' ', 'g:' ], '', $merchant_attribute );

		//$output = "woo_feed_get_{$this->config->provider}_{$merchant_attribute}_attribute";
		return apply_filters( "woo_feed_get_{$this->config->provider}_{$merchant_attribute}_attribute", $output, $this->product, $this->config, $product_attribute, $merchant_attribute );
	}


}
