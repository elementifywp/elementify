<?php
/**
 * Constants PHPStan needs to analyse the theme outside WordPress.
 *
 * @package Elementify
 */

define( 'ELEMENTIFY_VERSION', '1.0.0' );
define( 'ELEMENTIFY_DIR_PATH', __DIR__ . '/../..' );
define( 'ELEMENTIFY_DIR_URI', 'https://example.com/wp-content/themes/elementify' );
define( 'ELEMENTIFY_BUILD_PATH', ELEMENTIFY_DIR_PATH . '/build' );
define( 'ELEMENTIFY_BUILD_URI', ELEMENTIFY_DIR_URI . '/build' );
define( 'ELEMENTIFY_BUILD_JS_DIR_PATH', ELEMENTIFY_BUILD_PATH . '/js' );
define( 'ELEMENTIFY_BUILD_JS_URI', ELEMENTIFY_BUILD_URI . '/js' );
define( 'ELEMENTIFY_BUILD_CSS_DIR_PATH', ELEMENTIFY_BUILD_PATH . '/css' );
define( 'ELEMENTIFY_BUILD_CSS_URI', ELEMENTIFY_BUILD_URI . '/css' );
define( 'ELEMENTIFY_BUILD_LIB_URI', ELEMENTIFY_BUILD_URI . '/library' );
define( 'ELEMENTIFY_IMG_URI', ELEMENTIFY_BUILD_URI . '/images' );
define( 'ELEMENTIFY_ARCHIVE_POST_PER_PAGE', 9 );
define( 'ELEMENTIFY_SEARCH_RESULTS_POST_PER_PAGE', 9 );
