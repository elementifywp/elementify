<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Elementify
 */

namespace Elementify;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php
/**
 * Functions hooked into elementify/before_content action
 */
do_action( 'elementify/before_content' );
?>

<main id="main" class="site-main">

	<section class="error-404 not-found">
		<?php
		/**
		 * Functions hooked into elementify_404_content_top action
		 */
		do_action( 'elementify/404/entry_header' );
		?>

		<?php
		/**
		 * Functions hooked into elementify/404/entry_content action
		 *
		 * @hooked elementify_404_conent - 10
		 */
		do_action( 'elementify/404/entry_content' );
		?>

		<?php
		/**
		 * Functions hooked into elementify_404_content_bottom action
		 */
		do_action( 'elementify/404/entry_footer' );
		?>
	</section><!-- .error-404 -->

</main><!-- #main -->

<?php
/**
 * Functions hooked into elementify/after_content action
 *
 * @hooked elementify_sidebar    - 10
 */
do_action( 'elementify/after_content' );
?>

<?php get_footer(); ?>