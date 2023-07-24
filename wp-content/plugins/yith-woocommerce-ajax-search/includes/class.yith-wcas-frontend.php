<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Frontend class
 *
 * @author YITH
 * @package YITH WooCommerce Ajax Search
 * @version 1.1.1
 */

if ( ! defined( 'YITH_WCAS' ) ) {
	exit; } // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAS_Frontend' ) ) {
	/**
	 * Admin class.
	 * The class manage all the Frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAS_Frontend {

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			// custom styles and javascript.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 20 );
		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_styles_scripts() {
			wp_enqueue_style( 'yith_wcas_frontend' );
			wp_enqueue_script( 'yith_autocomplete' );
		}
	}
}
