<?php

namespace CTXFeed\V5\Common;

use CTXFeed\V5\Download\FileDownload;
use CTXFeed\V5\Utility\Config;
use RuntimeException;

class ExportFeed {
	
	public function __construct() {
		add_action( 'admin_post_wf_export_feed', [ $this, 'export_feed' ], 10 );
	}
	
	/**
	 * @return void
	 */
	public function export_feed() {
		if ( isset( $_REQUEST['feed'], $_REQUEST['_wpnonce'] ) && ! empty( $_REQUEST['feed'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'wpf-export' ) ) {
			$feed      = sanitize_text_field( wp_unslash( $_REQUEST['feed'] ) );
			$feed      = str_replace( [ 'wf_feed_', 'wf_config' ], '', $feed );
			$feed_info = maybe_unserialize( get_option( 'wf_feed_' . $feed ) );
			$config    = new Config( $feed_info );
			
			$file_name = sprintf(
				'%s-%s.wpf',
				sanitize_title( $config->get_feed_file_name() ),
				time()
			);
			$feed      = wp_json_encode( $config->get_feed_rules() );
			$meta      = wp_json_encode( [
				'version'   => WOO_FEED_FREE_VERSION,
				'file_name' => $file_name,
				'hash'      => md5( $feed ),
			] );
			$bin       = pack( 'VA*VA*', strlen( $meta ), $meta, strlen( $feed ), $feed );
			$feed      = gzdeflate( $bin, 9 );
			
			$fileDownload = FileDownload::createFromString( $feed );
			$fileDownload->sendDownload( $file_name );
		} else {
			throw new RuntimeException( esc_html__( 'Invalid Request', 'woo-feed' ) );
		}
	}
	
}