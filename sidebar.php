<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Elementify
 */

namespace Elementify;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}

/**
 * Functions hooked into elementify_before_sidebar action
 */
do_action( 'elementify_before_sidebar' );
?>

<?php
/**
 * Functions hooked into elementify_sidebar action
 */
do_action( 'elementify_sidebar' );
?>

<?php
/**
 * Functions hooked into elementify_after_sidebar action
 */
do_action( 'elementify_after_sidebar' );

