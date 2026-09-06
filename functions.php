<?php
/**
 * Elementify functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Elementify
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ELEMENTIFY_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'ELEMENTIFY_VERSION', wp_get_theme()->get( 'Version' ) );
}

if ( ! defined( 'ELEMENTIFY_DIR_PATH' ) ) {
	define( 'ELEMENTIFY_DIR_PATH', untrailingslashit( get_template_directory() ) );
}

if ( ! defined( 'ELEMENTIFY_DIR_URI' ) ) {
	define( 'ELEMENTIFY_DIR_URI', untrailingslashit( get_template_directory_uri() ) );
}

if ( ! defined( 'ELEMENTIFY_BUILD_PATH' ) ) {
	define( 'ELEMENTIFY_BUILD_PATH', ELEMENTIFY_DIR_PATH . '/build' );
}

if ( ! defined( 'ELEMENTIFY_BUILD_URI' ) ) {
	define( 'ELEMENTIFY_BUILD_URI', ELEMENTIFY_DIR_URI . '/build' );
}

if ( ! defined( 'ELEMENTIFY_BUILD_JS_DIR_PATH' ) ) {
	define( 'ELEMENTIFY_BUILD_JS_DIR_PATH', ELEMENTIFY_BUILD_PATH . '/js' );
}

if ( ! defined( 'ELEMENTIFY_BUILD_JS_URI' ) ) {
	define( 'ELEMENTIFY_BUILD_JS_URI', ELEMENTIFY_BUILD_URI . '/js' );
}

if ( ! defined( 'ELEMENTIFY_BUILD_CSS_DIR_PATH' ) ) {
	define( 'ELEMENTIFY_BUILD_CSS_DIR_PATH', ELEMENTIFY_BUILD_PATH . '/css' );
}

if ( ! defined( 'ELEMENTIFY_BUILD_CSS_URI' ) ) {
	define( 'ELEMENTIFY_BUILD_CSS_URI', ELEMENTIFY_BUILD_URI . '/css' );
}

if ( ! defined( 'ELEMENTIFY_BUILD_LIB_URI' ) ) {
	define( 'ELEMENTIFY_BUILD_LIB_URI', ELEMENTIFY_BUILD_URI . '/library' );
}

if ( ! defined( 'ELEMENTIFY_IMG_URI' ) ) {
	define( 'ELEMENTIFY_IMG_URI', ELEMENTIFY_BUILD_URI . '/images' );
}

if ( ! defined( 'ELEMENTIFY_ARCHIVE_POST_PER_PAGE' ) ) {
	define( 'ELEMENTIFY_ARCHIVE_POST_PER_PAGE', 9 );
}

if ( ! defined( 'ELEMENTIFY_SEARCH_RESULTS_POST_PER_PAGE' ) ) {
	define( 'ELEMENTIFY_SEARCH_RESULTS_POST_PER_PAGE', 9 );
}

require_once ELEMENTIFY_DIR_PATH . '/inc/helpers/autoloader.php';
require_once ELEMENTIFY_DIR_PATH . '/inc/helpers/template-functions.php';
require_once ELEMENTIFY_DIR_PATH . '/inc/helpers/template-tags.php';
require_once ELEMENTIFY_DIR_PATH . '/inc/helpers/functions.php';

if ( ! function_exists( 'elementify_get_theme_instance' ) ) {
	/**
	 * Bootstraps the theme and returns the main theme instance.
	 *
	 * The instance is created on the first call and cached, so the theme's
	 * hooks are registered exactly once even if this is called again.
	 *
	 * @return \Elementify\Inc\Elementify
	 */
	function elementify_get_theme_instance() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new \Elementify\Inc\Elementify();
		}

		return $instance;
	}
}

elementify_get_theme_instance();
