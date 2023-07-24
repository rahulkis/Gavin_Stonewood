<?php
namespace CTXFeed\V5\Utility;
use WP_Error;

class FileSystem {

	/**
	 * Check Filesystem connection.
	 *
	 * @param $url
	 * @param $method
	 * @param $context
	 * @param $fields
	 *
	 * @return bool
	 */
	public static function connect_fs( $url, $method, $context, $fields = null ) {
		global $wp_filesystem;
		if ( false === ( $credentials = request_filesystem_credentials( $url, $method, false, $context, $fields ) ) ) {
			return false;
		}

		//check if credentials are correct or not.
		if ( ! WP_Filesystem( $credentials ) ) {
			request_filesystem_credentials( $url, $method, true, $context );

			return false;
		}

		return true;
	}

	/**
	 * @param $content
	 * @param $path
	 * @param $filename
	 * @param string $admin_url
	 * @param string $nonce
	 *
	 * @return mixed|WP_Error
	 */
	public static function WriteFile( $content, $path, $filename, $admin_url = 'admin.php?page=webappick-manage-feeds', $nonce = 'wpf_feed_nonce' ) {
		global $wp_filesystem;

		$url = wp_nonce_url( $admin_url, $nonce );
		if ( self::connect_fs( $url, "", $path ) ) {
			$dir  = $wp_filesystem->find_folder( $path );
			$file = trailingslashit( $dir ) . $filename;
//			print_r($content);die();
			// Delete the file first when possible.
//			self::DeleteFile( $path, $filename );

			return $wp_filesystem->put_contents( $file, $content, FS_CHMOD_FILE );
		}

		return new WP_Error( "filesystem_error", "Cannot initialize filesystem" );
	}

	/**
	 * Read file from directory.
	 *
	 * @param $path
	 * @param $filename
	 * @param string $admin_url
	 * @param string $nonce
	 *
	 * @return string|WP_Error
	 */
	public static function ReadFile( $path, $filename, $admin_url = 'admin.php?page=webappick-new-feed', $nonce = 'wpf_feed_nonce' ) {
		global $wp_filesystem;

		$url = wp_nonce_url( $admin_url, $nonce );

		if ( self::connect_fs( $url, "", $path ) ) {
			$dir  = $wp_filesystem->find_folder( $path );
			$file = trailingslashit( $dir ) . $filename;

			if ( $wp_filesystem->exists( $file ) ) {
				$text = $wp_filesystem->get_contents( $file );
				if ( ! $text ) {
					return "";
				}

				return $text;
			}

			return new WP_Error( "filesystem_error", "File doesn't exist" );
		}

		return new WP_Error( "filesystem_error", "Cannot initialize filesystem" );
	}

	/**
	 * Delete file from directory.
	 *
	 * @param $path
	 * @param $filename
	 * @param string $admin_url
	 * @param string $nonce
	 *
	 * @return string|WP_Error
	 */
	public static function DeleteFile( $path, $filename, $admin_url = 'admin.php?page=webappick-new-feed', $nonce = 'wpf_feed_nonce' ) {
		global $wp_filesystem;

		$url = wp_nonce_url( $admin_url, $nonce );

		if ( self::connect_fs( $url, "", $path ) ) {
			$dir  = $wp_filesystem->find_folder( $path );
			$file = trailingslashit( $dir ) . $filename;

			if ( $wp_filesystem->exists( $file ) ) {
				return $wp_filesystem->delete( $file );
			}

			return new WP_Error( "filesystem_error", "File doesn't exist" );
		}

		return new WP_Error( "filesystem_error", "Cannot initialize filesystem" );
	}

}