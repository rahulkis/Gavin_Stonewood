<?php

namespace CTXFeed\V5\Tax;


/**
 * Class Tax
 *
 * @package    CTXFeed\V5\Tax
 * @subpackage CTXFeed\V5\Tax
 */
class Tax {
	private $tax;
	
	public function __construct( TaxInterface $tax ) {
		$this->tax = $tax;
	}
	
	public function get_tax() {
		return $this->tax->get_tax();
	}
	
	public function get_taxes() {
		return $this->tax->get_taxes();
	}
	
	public function merchant_formatted_tax($key) {
		return $this->tax->merchant_formatted_tax($key);
	}
	
}