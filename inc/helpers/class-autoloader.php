<?php
/**
 * Autoloader file for theme.
 *
 * @package GetsheildedTheme
 */

namespace GetsheildedTheme\Inc\Helpers;

/**
 * Auto loader function.
 *
 * @param string $resource Source namespace.
 *
 * @return void
 */
function autoloader( $resource = '' ) {
	$resource_path  = false;
	$namespace_root = 'GetsheildedTheme\\Inc\\';
	$resource       = trim( $resource, '\\' );

	if ( empty( $resource ) || strpos( $resource, '\\' ) === false || strpos( $resource, $namespace_root ) !== 0 ) {
		// Not our namespace, bail out.
		return;
	}

	// Remove our root namespace.
	$resource = str_replace( $namespace_root, '', $resource );

	$path = explode( '\\', $resource );

	/**
	 * Time to determine which type of resource path it is,
	 * so that we can deduce the correct file path for it.
	 */
	if ( empty( $path[0] ) || empty( $path[1] ) ) {
		return;
	}


	$directory = '';
	$file_name = '';

	switch ( strtolower( $path[0] ) ) {
		case 'traits':
			$directory = 'traits';
			$file_name = sprintf( 'trait-%s', trim( strtolower( str_replace( '_', '-', $path[1] ) ) ) );
			break;

		case 'admin':
			$directory = 'admin';
			$file_name = sprintf( 'class-%s', trim( strtolower( str_replace( '_', '-', $path[1] ) ) ) );
			break;

		case 'helpers':
			$directory = 'helpers';
			$file_name = sprintf( 'class-%s', trim( strtolower( str_replace( '_', '-', $path[1] ) ) ) );
			break;

		case 'classes':
		default:
			$directory = 'classes';
			$file_name = sprintf( 'class-%s', trim( strtolower( str_replace( '_', '-', $path[1] ) ) ) );
			break;
	}

	// Get the theme directory by going up from the helpers directory
	$theme_dir = dirname( dirname( __DIR__ ) );
	$resource_path = sprintf( '%s/inc/%s/%s.php', $theme_dir, $directory, $file_name );


	if ( ! empty( $resource_path ) && file_exists( $resource_path ) ) {
		// We already making sure that file is exists and valid.
		require_once( $resource_path ); // phpcs:ignore
	}

}

spl_autoload_register( '\GetsheildedTheme\Inc\Helpers\autoloader' );