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
	 * Functions hooked into elementify_before_footer action
	 */
	do_action( 'elementify_before_footer' );
?>

<?php
	/**
	 * Functions hooked into elementify_footer action
	 *
	 * @hooked elementify_footer - 10
	 */
	do_action( 'elementify_footer' );
?>

<?php
	/**
	 * Functions hooked into elementify_after_footer action
	 */
	do_action( 'elementify_after_footer' );
?>

</div><!-- #page -->


<?php
/**
 * Functions hooked into elementify_body_bottom action
 *
 * @hooked elementify_framework_go_top_button - 10
 */
do_action( 'elementify_body_bottom' );
?>

<?php wp_footer(); ?>

</body>

</html>