<?php

namespace CTXFeed\V5\API\V1;

use CTXFeed\V5\API\RestController;
use WP_REST_Server;
use CTXFeed\V5\Common\DropDownOptions;
use \WP_Error;

class DropDown extends RestController {

	/**
	 * @var $dropdown
	 */
	protected $dropdown;
	/**
	 * The single instance of the class
	 *
	 * @var DropDown
	 *
	 */
	protected static $_instance = null;

	private function __construct() {
		parent::__construct();

		$this->rest_base = 'drop_down';
		$this->dropdown  = DropDownOptions::instance();

	}

	/**
	 * Main DropDownOptionsApi Instance.
	 *
	 * Ensures only one instance of DropDownOptionsApi is loaded or can be loaded.
	 *
	 * @return DropDown Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				/**
				 * @endpoint: wp-json/ctxfeed/v1/dropdown/?type=feed_country
				 *
				 * @param $type String  will be DropDownOptions class\'s method name
				 */
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_item' ],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
					'args'                => [
						'type' => [
							'description' => __( 'Dropdown type name. $type will be DropDownOptions class\'s method name. Example: wp-json/ctxfeed/v1/dropdown/?type=feed_country. Here fee_country is DropDownOptions method name.' ),
							'type'        => 'string',
							'required'    => true
						],
					],
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);
	}


	/**
	 * Get dropdown based on type params. If parameter 'type' is not passed then it will give error.
	 * $type will be DropDownOptions class's method name.
	 *
	 * @param $request
	 *
	 * @return void|\WP_Error|\WP_REST_Response
	 */
	public function get_item( $request ) {

		$param = $request->get_param( 'type' );

		if ( method_exists( $this->dropdown, $param ) ) {
			if('product_attributes' === $param) {
				$this->response['data'] = $this->dropdown::$param( '', false, 'woo_feed_product_attribute_dropdown_array' );
			}else{
				$this->response['data'] = $this->dropdown::$param( '', false );
			}
		} else {
			return $this->error( __( 'Method Does not exist !', 'woo-feed' ) );
		}

		$response = $this->success( $this->response['data'] );
		$response->header( 'X-WP-Total', count( $this->response['data'] ) );

		return $response;
	}


	/**
	 * Retrieves the contact schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'dropdown',
			'type'       => 'array',
			'properties' => [
				'dropdown' => [
					'description' => __( 'Unique identifier for the object.' ),
					'type'        => 'array',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => false,
				],
			]
		];

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		unset( $params['search'] );

		return $params;
	}

}
