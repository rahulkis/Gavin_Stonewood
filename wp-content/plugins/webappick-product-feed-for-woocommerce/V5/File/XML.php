<?php

namespace CTXFeed\V5\File;

use DOMDocument;
use SimpleXMLElement;

/**
 * Make XML Feed.
 *
 * @package    CTXFeed
 * @subpackage CTXFeed\V5\File
 */
class XML implements FileInterface {
	/**
	 * @var
	 */
	private $data;
	/**
	 * @var
	 */
	private $config;
	private $feedBody;
	
	/**
	 * @param $data
	 * @param $config
	 */
	public function __construct( $data, $config ) {
		
		$this->data   = $data;
		$this->config = $config;
	}
	
	/**
	 * Make Header & Footer.
	 *
	 * @return array
	 */
	public function make_header_footer() {
		$HF = $this->get_header_footer( $this->config );
		
		return apply_filters( "ctx_make_{$this->config->feedType}_feed_header_footer", $HF, $this->data, $this->config );
	}
	
	/**
	 * Convert an array to XML.
	 *
	 * @param array $array array to convert
	 * @param mixed $xml   xml object
	 */
	public function array_to_xml( $array, &$xml ) {
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				if ( ! is_numeric( $key ) ) {
					$this->feedBody .= "<$key>" . PHP_EOL;
					self::array_to_xml( $value, $child );
					$this->feedBody .= "</$key>" . PHP_EOL;
				} else {
					self::array_to_xml( $value, $xml );
				}
			} else {
				if ( ! in_array( $key, [ 'g:tax', 'g:shipping' ], true ) ) {
					$value = htmlentities( $value, ENT_XML1 | ENT_QUOTES, 'UTF-8' );
				}
				$this->feedBody .= "<$key>" . $value . "</$key>" . PHP_EOL;
			}
		}
	}
	
	/**
	 * Make XML body.
	 *
	 * @return false|string
	 */
	public function make_body() {
		// create simpleXML object
		
		$xml = '';
		$this->array_to_xml( $this->data, $xml );
		
		return apply_filters( "ctx_make_{$this->config->feedType}_feed_body", $this->feedBody, $this->data, $this->config );
	}
	
	/**
	 * Create XML File Header and Footer.
	 *
	 * @param $config
	 *
	 * @return array
	 */
	private function get_header_footer( $config ) {
		
		$xml_wrapper['header'] = $this->makeHeader( $config );
		$xml_wrapper['footer'] = "\n" . $this->makeFooter( $config );
		
		
		$config->itemWrapper  = str_replace( ' ', '_', $config->itemWrapper );
		$config->itemsWrapper = str_replace( ' ', '_', $config->itemsWrapper );
		
		if ( file_exists( WOO_FEED_FREE_ADMIN_PATH . 'partials/templates/' . $config->provider . '.txt' ) ) {
			$txt = file_get_contents( WOO_FEED_FREE_ADMIN_PATH . 'partials/templates/' . $config->provider . '.txt' );
			$txt = trim( $txt );
			$txt = explode( '{separator}', $txt );
			if ( 2 === count( $txt ) ) {
				$xml_wrapper['header'] = $this->makeHeader( $config, trim( $txt[0] ) );
				$xml_wrapper['footer'] = "\n" . $this->makeFooter( $config, trim( $txt[1] ) );
			}
		}
		
		return $xml_wrapper;
	}
	
	/**
	 * Replace template variables.
	 *
	 *
	 * @param $header
	 * @param $config
	 *
	 * @return array|string|string[]
	 */
	private function replaceTemplateVariable( $header, $config ) {
		
		$variables = [
			'{DateTimeNow}'     => gmdate( 'Y-m-d H:i:s', strtotime( current_time( 'mysql' ) ) ),
			'{BlogName}'        => get_bloginfo( 'name' ),
			'{BlogURL}'         => get_bloginfo( 'url' ),
			'{BlogDescription}' => "CTX Feed - This product feed is generated with the CTX Feed - WooCommerce Product Feed Manager plugin by WebAppick.com. For all your support questions check out our plugin Docs on https://webappick.com/docs or e-mail to: support@webappick.com",
			'{BlogEmail}'       => get_bloginfo( 'admin_email' ),
		];
		
		$variables = apply_filters( 'ctx_xml_header_template_variables', $variables, $config );
		
		return str_replace( array_keys( $variables ), array_values( $variables ), $header );
	}
	
	/**
	 * Make XML Header.
	 *
	 * @param $config
	 * @param $override
	 *
	 * @return mixed|void
	 */
	private function makeHeader( $config, $override = '' ) {
		$config->itemsWrapper = str_replace( ' ', '_', $config->itemsWrapper );
		if ( ! empty( $override ) ) {
			$header = $override;
		} else {
			$header = '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL . "<" . wp_unslash( $config->itemsWrapper ) . ">";
		}
		
		if ( ! empty( $config->extraHeader ) ) {
			$header .= PHP_EOL . wp_unslash( $config->extraHeader );
		}
		
		// replace template variables.
		$header = $this->replaceTemplateVariable( $header, $config );
		
		return apply_filters( 'ctx_make_xml_header', $header, $config );
	}
	
	/**
	 * Make XML Footer.
	 *
	 * @param $config
	 * @param $override
	 *
	 * @return mixed|void
	 */
	private function makeFooter( $config, $override = '' ) {
		if ( ! empty( $override ) ) {
			$footer = $override;
		} else {
			$footer = '</' . $config->itemsWrapper . '>';
		}
		
		return apply_filters( 'ctx_make_xml_footer', $footer, $config );
	}
	
	/**
	 * @param $feed
	 *
	 * @return array|string|string[]
	 */
	private function removeHeaderFooter( $feed ) {
		return str_replace(
			[ '<?xml version="1.0" encoding="utf-8"?>', '<?xml version="1.0"?>', '<products>', '</products>' ],
			'',
			$feed
		);
	}
}
