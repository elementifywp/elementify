<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Elementify
 */

namespace Elementify;

use Elementify\Inc\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!doctype html>

<?php
/**
 * Functions hooked into elementify_before_html action
 */
do_action( 'elementify_before_html' );
?>

<html <?php language_attributes(); ?> <?php elementify_html_attributes(); ?> <?php Utils::the_microdata( 'html' ); ?>>

<head>

	<?php
	/**
	 * Functions hooked into elementify_head_top action
	 */
	do_action( 'elementify_head_top' );
	?>

	<?php
	/**
	 * Functions hooked into elementify_head action
	 *
	 * @hooked elementify_head_meta - 10
	 */
	do_action( 'elementify_head' );
	?>

	<?php
	/**
	 * Functions hooked into elementify_head_bottom action
	 *
	 * @hooked elementify_wp_head - 10
	 */
	do_action( 'elementify_head_bottom' );
	?>

</head>

<body 
<?php
		body_class();
		/**
		 * Functions hooked into elementify_body_attributes action
		 *
		 * @hooked elementify_body_attributes - 10
		 */
		do_action( 'elementify_body_attributes' );
?>
		>

	<?php
	/**
	 * Functions hooked into elementify_body_top action
	 *
	 * @hooked elementify_wp_body_open - 10
	 */
	do_action( 'elementify_body_top' );
	?>
	<div id="page" class="site ele-position-relative ele-position-absolute-after">

		<?php
		/**
		 * Functions hooked into elementify_before_header action
		 *
		 * @hooked elementify_skip_link - 10
		 */
		do_action( 'elementify_before_header' );
		?>

		<?php
		/**
		 * Functions hooked into elementify_header action
		 *
		 * @hooked elementify_header - 10
		 */
		do_action( 'elementify_header' );
		?>

		<?php
		/**
		 * Functions hooked into elementify_after_header action
		 */
		do_action( 'elementify_after_header' );