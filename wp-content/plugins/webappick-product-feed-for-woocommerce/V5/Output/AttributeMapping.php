<?php

namespace CTXFeed\V5\Output;


use CTXFeed\V5\Helper\CommonHelper;
use CTXFeed\V5\Product\AttributeValueByType;
use CTXFeed\V5\Utility\Config;
use WC_Product;
/**
 * Class AttributeMapping
 *
 * @package    CTXFeed
 * @subpackage CTXFeed\V5\Output
 * @author     Ohidul Islam <wahid0003@gmail.com>
 * @link       https://webappick.com
 * @license    https://opensource.org/licenses/gpl-license.php GNU Public License
 * @category   Output
 */
class AttributeMapping {

	/**
	 *  Get Attribute Mapping Value.
	 *
	 * @param            $attribute
	 * @param            $merchant_attribute
	 * @param WC_Product $product
	 * @param Config     $config
	 *
	 * @return string
	 */
	public function getMappingValue( $attribute, $merchant_attribute, $product, $config ) {
		$getAttributeValueByType = new AttributeValueByType( $attribute, $product, $config, $merchant_attribute );
		$attributes              = get_option( $attribute );
		$glue                    = ! empty( $attributes['glue'] ) ? $attributes['glue'] : " ";
		$output                  = '';

		if ( isset( $attributes['mapping'] ) ) {
			foreach ( $attributes['mapping'] as $map ) {
				$get_value = $getAttributeValueByType->get_value( $map );
				if ( ! empty( $get_value ) ) {
					$output .= $glue . $get_value;
				}
			}
		}

		//trim extra glue
		$output = trim( $output, $glue );

		// remove extra whitespace
		$output = preg_replace( '!\s\s+!', ' ', $output );

		return apply_filters( 'woo_feed_filter_attribute_mapping', $output, $attribute, $product, $config );
	}

	/**
	 * Get Attribute Mapping.
	 *
	 * @param $attribute
	 *
	 * @return false|mixed|null
	 */
	public function getMapping( $attribute ) {
		if ( strpos( $attribute, AttributeValueByType::PRODUCT_ATTRIBUTE_MAPPING_PREFIX ) === false ) {
			$attribute = AttributeValueByType::PRODUCT_ATTRIBUTE_MAPPING_PREFIX . $attribute;
		}

		return get_option( $attribute );
	}


	public function getMappings() {

		$mappings = CommonHelper::get_options( AttributeValueByType::PRODUCT_ATTRIBUTE_MAPPING_PREFIX );
		$data     = [];
		if ( ! empty( $mappings ) ) {
			foreach ( $mappings as $mapping ) {
				$data[ $mapping->option_name ] = get_option( $mapping->option_name );
			}

			return $data;
		}

		return false;
	}

	/**
	 * Save Attribute mapping.
	 *
	 * @param array $mappingConfig
	 *
	 * @return bool
	 */
	public function saveMapping( $mappingConfig ) {

		$data = array();

		$data['name'] = '';
		if ( isset( $mappingConfig['mapping_name'] ) ) {
			$data['name'] = sanitize_text_field( $mappingConfig['mapping_name'] );
		}

		// Set Multiple Attributes or texts.
		if ( isset( $mappingConfig['value'] ) ) {
			foreach ( $mappingConfig['value'] as $item ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if ( ' ' === $item ) {
					$data['mapping'][] = $item;
				} elseif ( '' !== $item ) {
					$data['mapping'][] = sanitize_text_field( $item );
				}
			}
			$data['mapping'] = array_filter( $data['mapping'] );
		}

		// Set Glue.
		if ( isset( $mappingConfig['mapping_glue'] ) ) {
			$data['glue'] = $mappingConfig['mapping_glue'];
		} else {
			$data['glue'] = '';
		}

		// Set Option Name.
		if ( isset( $mappingConfig['option_name'] ) &&
		     ! empty( $mappingConfig['option_name'] ) &&
		     false !== strpos( $mappingConfig['option_name'], AttributeValueByType::PRODUCT_ATTRIBUTE_MAPPING_PREFIX ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		) {
			$option = sanitize_text_field( $mappingConfig['option_name'] );
		} else {
			// generate unique one.
			$option = CommonHelper::unique_option_name( AttributeValueByType::PRODUCT_ATTRIBUTE_MAPPING_PREFIX . $data['name'] );
		}

		return update_option( $option, $data );
	}

	/**
	 * Save Attribute mapping.
	 *
	 * @param array $mappingConfig
	 *
	 * @return bool
	 */
	public function updateMapping( $mappingConfig ) {

		$data = array();

		$data['name'] = '';
		if ( isset( $mappingConfig['mapping_name'] ) ) {
			$data['name'] = sanitize_text_field( $mappingConfig['mapping_name'] );
		}

		// Set Multiple Attributes or texts.
		if ( isset( $mappingConfig['value'] ) ) {
			foreach ( $mappingConfig['value'] as $item ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if ( ' ' === $item ) {
					$data['mapping'][] = $item;
				} elseif ( '' !== $item ) {
					$data['mapping'][] = sanitize_text_field( $item );
				}
			}
			$data['mapping'] = array_filter( $data['mapping'] );
		}

		// Set Glue.
		if ( isset( $mappingConfig['mapping_glue'] ) ) {
			$data['glue'] = $mappingConfig['mapping_glue'];
		} else {
			$data['glue'] = '';
		}

		// Set Option Name.
		if ( isset( $mappingConfig['option_name'] ) &&
		     ! empty( $mappingConfig['option_name'] ) &&
		     false !== strpos( $mappingConfig['option_name'], AttributeValueByType::PRODUCT_ATTRIBUTE_MAPPING_PREFIX ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		) {
			$option = sanitize_text_field( $mappingConfig['option_name'] );
		} else {
			// generate unique one.
			$option = CommonHelper::unique_option_name( AttributeValueByType::PRODUCT_ATTRIBUTE_MAPPING_PREFIX . $data['name'] );
		}

		return update_option( $option, $data );
	}

	/**
	 * Delete Attribute Mapping.
	 *
	 * @param $attribute
	 *
	 * @return bool
	 */
	public function deleteMapping( $attribute ) {
		if ( strpos( $attribute, AttributeValueByType::PRODUCT_ATTRIBUTE_MAPPING_PREFIX ) === false ) {
			$attribute = AttributeValueByType::PRODUCT_ATTRIBUTE_MAPPING_PREFIX . $attribute;
		}

		return delete_option( $attribute );
	}


}
