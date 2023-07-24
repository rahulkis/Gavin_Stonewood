<?php
namespace CTXFeed\V5\Query;
class WCReviewQuery implements QueryInterface {
	private $config;
	private $arguments;

	public function __construct( $config ) {
		$this->config    = $config;
		$this->arguments = $this->get_query_arguments();
	}
	
	/**
	 * @return string
	 */
	public function get_product_types(){
		return 'product';
	}

	public function get_query_arguments() {
		$args = array(
			'post_type'     => $this->get_product_types(),
			'status'        => $this->get_product_status(),
			'fields'        => 'ids',
			'comment_count' => array(
				'value'   => 0,
				'compare' => '>',
			),
		);


		return apply_filters( 'ctx_filter_arguments_for_product_with_review_query', $args, 'review' );
	}

	public function get_product_status() {
		$status = $this->config->get_post_status_to_include();

		return ( $status ) ?: "publish";
	}

	public function product_ids() {
		return get_posts( $this->arguments );
	}
}