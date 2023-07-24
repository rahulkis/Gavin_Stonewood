<?php
namespace CTXFeed\V5\Output;

use CTXFeed\V5\Utility\Config;
use CTXFeed\V5\Helper\CommonHelper;
use CTXFeed\V5\Helper\ProductHelper;

class FormatOutput {
	private $product;
	/**
	 * @var Config $config
	 */
	private $config;
	private $attribute;

	public function __construct( $product, $config, $attribute ) {
		$this->product   = $product;
		$this->config    = $config;
		$this->attribute = $attribute;
	}

	/**
	 * Set price formate
	 * @param $output
	 *
	 * @return void
	 */
	private function get_price_format( $output ) {
		if ( ! empty( $output ) && $output > 0 ) {
			$decimals           = $this->config->get_number_format()['decimals'];
			$decimal_separator  = $this->config->get_number_format()['decimal_separator'];
			$thousand_separator = $this->config->get_number_format()['thousand_separator'];
			$output             = (float) $output;

			// TODO: Remove this and make a hook.
			if ( 'idealo' === $this->config->get_feed_template() ) {
				$output = number_format( $output, 2, '.', '' );
			} else {
				$output = number_format( $output, $decimals, wp_specialchars_decode( wp_unslash( $decimal_separator ) ), wp_specialchars_decode( wp_unslash( $thousand_separator ) ) );
			}
		}
		return $output;
	}



	/**
	 * Delete Space
	 * @param $output
	 *
	 * @return array|string|string[]|null
	 */
	private function delete_space( $output ) {
		$output = htmlentities( $output, null, 'utf-8' );
		$output = str_replace( '&nbsp;', ' ', $output );
		$output = html_entity_decode( $output );
		return preg_replace( '/\\s+/', ' ', $output );
	}




	/**
	 * Get the parent value on current empty value
	 *
	 * @param $output
	 * @param $outputTypes
	 *
	 * @return mixed
	 */
	private function get_parent_lang_child_is_empty( $output, $outputTypes ) {
		$id = $this->product->get_id();

		//check if the format type is `parent` or `parent_lang_if_empty`
		if ( in_array( 23, $outputTypes, true ) ) {
			$force_parent = true;
		} elseif ( in_array( 24, $outputTypes, true ) ) {
			$force_parent = empty( $output );
		}

		/**
		 * when format type is `parent` then force getting parent value
		 * when format type is `parent_lang_if_empty` then get the parent value on current empty value
		 */
		if ( $force_parent ) {
			//when wpml plugin is activated, get parent language post id
			if ( class_exists( 'SitePress', false ) ) {
				$parent_id = woo_feed_wpml_get_original_post_id( $id );

				//remove wpml term filter
				global $sitepress;
				remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1 );
				remove_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ), 1 );
			}

			// when polylang plugin is activated, get parent language post id
			if ( defined( 'POLYLANG_BASENAME' ) || function_exists( 'PLL' ) ) {
				$parent_id = woo_feed_pll_get_original_post_id( $id );
			}

			//get attribute value of parent language post id
			if ( ! empty( $parent_id ) ) {
				$parentProduct = wc_get_product( $parent_id );
				$output        = ProductHelper::getAttributeValueByType( $this->attribute, $parentProduct, $this->config );
			}
		}

		return $output;
	}
	/**
	 * Get Output
	 *
	 * @param $output
	 * @param $outputTypes
	 *
	 * @return array|false|int|mixed|string|string[]|null
	 */

	public function get_output( $output, $outputTypes ) {

		if ( ! empty( $outputTypes ) && is_array( $outputTypes ) ) {
			// Format Output According to output type
			if ( in_array( 2, $outputTypes, true ) ) { // Strip Tags
				//return $outputTypes;
				$output = CommonHelper::woo_feed_strip_all_tags( html_entity_decode( $output ) );
			}

			if ( in_array( 4, $outputTypes, true ) ) { // htmlentities
				$output = htmlentities( $output, ENT_QUOTES, 'UTF-8' );
			}

			if ( in_array( 5, $outputTypes, true ) ) { // Integer
				$output = (int) $output;
			}

			if ( in_array( 6, $outputTypes, true ) ) { // Format Price
				$this->get_price_format( $output );
			}

			if ( ! empty( $output ) && $output > 0 && in_array( 7, $outputTypes, true ) ) { // Rounded Price
				$output = round( $output );
				$output = number_format( $output, 2, '.', '' );
			}

			if ( in_array( 8, $outputTypes, true ) ) { // Delete Space
				$output = $this->delete_space( $output );
			}

			if ( in_array( 10, $outputTypes, true ) ) { // Remove Invalid Character
				$output = CommonHelper::woo_feed_stripInvalidXml( $output );
			}

			if ( in_array( 11, $outputTypes, true ) ) {  // Remove ShortCodes
				$output = CommonHelper::remove_shortcodes( $output );
			}

			if ( in_array( 12, $outputTypes, true ) ) {
				$output = ucwords( mb_strtolower( $output ) );
			}

			if ( in_array( 13, $outputTypes, true ) ) {
				$output = ucfirst( mb_strtolower( $output ) );
			}

			if ( in_array( 14, $outputTypes, true ) ) {
				$output = mb_strtoupper( strtolower( $output ) );
			}

			if ( in_array( 15, $outputTypes, true ) ) {
				$output = mb_strtolower( $output );
			}

			if ( in_array( 16, $outputTypes, true ) && strpos( $output, 'http' ) === 0 ) {
				$output = str_replace( 'http://', 'https://', $output );
			}

			if ( in_array( 17, $outputTypes, true ) && strpos( $output, 'http' ) === 0 ) {
				$output = str_replace( 'https://', 'http://', $output );
			}

			if ( in_array( 18, $outputTypes, true ) ) { // only parent
				$output = $this->get_only_parent( $output );
			}

			if ( in_array( 19, $outputTypes, true ) ) { // child if parent empty
				$output = $this->get_parent( $output );
			}

			if ( in_array( 20, $outputTypes, true ) ) { // parent if child empty
				$output = $this->get_parent_if_empty( $output );
			}

			if ( 'xml' === $this->config->get_feed_file_type() && in_array( 9, $outputTypes, true ) ) { // Add CDATA
				$output = '<![CDATA[' . $output . ']]>';
			}

			if ( in_array( 23, $outputTypes, true ) || in_array( 24, $outputTypes, true ) ) { // parent lang if child empty
				$output = $this->get_parent_lang_child_is_empty( $output, $outputTypes );
			}
		}

		return $output;
	}

	/**
	 * Get Variable Product Value.
	 *
	 * @param string $output
	 *
	 * @return string
	 */
	protected function get_only_parent( $output ) {
		if ( $this->product->is_type( 'variation' ) ) {
			$id     = $this->product->get_parent_id();
			$parent = wc_get_product( $id );
			$output = ProductHelper::getAttributeValueByType( $this->attribute, $parent, $this->config );
		}

		return $output;
	}

	/**
	 * Get variation value if Variable Product Value empty.
	 *
	 * @param string $output
	 *
	 * @return string
	 */
	protected function get_parent( $output ) {
		if ( $this->product->is_type( 'variation' ) ) {
			$id            = $this->product->get_parent_id();
			$parentProduct = wc_get_product( $id );
			$output        = ProductHelper::getAttributeValueByType( $this->attribute, $parentProduct, $this->config );
			if ( empty( $output ) ) {
				$output = ProductHelper::getAttributeValueByType( $this->attribute, $this->product, $this->config );
			}
		}

		return $output;
	}

	/**
	 * Get Variable Product Value if variation value empty.
	 *
	 * @param string $output
	 *
	 * @return string
	 */
	protected function get_parent_if_empty( $output ) {
		if ( $this->product->is_type( 'variation' ) ) {
			$output = ProductHelper::getAttributeValueByType( $this->attribute, $this->product, $this->config );
			if ( empty( $output ) ) {
				$id     = $this->product->get_parent_id();
				$parent = wc_get_product( $id );
				$output = ProductHelper::getAttributeValueByType( $this->attribute, $parent, $this->config );
			}
		}

		return $output;
	}
}
