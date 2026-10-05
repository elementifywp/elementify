<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Elementify
 */

namespace Elementify;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
	/**
	 * Functions hooked into elementify/before_footer action
	 */
	do_action( 'elementify/before_footer' );
?>

<?php
	/**
	 * Functions hooked into elementify/footer action
	 *
	 * @hooked elementify_footer - 10
	 */
	do_action( 'elementify/footer' );
?>

<?php
	/**
	 * Functions hooked into elementify/after_footer action
	 */
	do_action( 'elementify/after_footer' );
?>

</div><!-- #page -->


<?php
/**
 * Functions hooked into elementify/body_bottom action
 *
 * @hooked elementify_framework_go_top_button - 10
 */
do_action( 'elementify/body_bottom' );
?>

<?php wp_footer(); ?>

</body>

</html>