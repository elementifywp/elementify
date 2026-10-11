<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Elementify
 */

namespace Elementify;

use Elementify\Inc\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/**
 * Renders an Elementor Theme Builder location when one is assigned for this
 * template, and otherwise falls through to the theme's own markup.
 *
 * @see \Elementify\Inc\Elementor::do_content_location()
 */
if ( ! \Elementify\Inc\Elementor::get_instance()->do_content_location( 'single' ) ) :
	?>

	<?php
	/**
	 * Functions hooked into elementify_before_content action
	 */
	do_action( 'elementify_before_content' );
	?>

<main id="main" class="site-main ele-d-flex ele-flex-col ele-flex-column">

	<?php
	/**
	 * Functions hooked into elementify_post_content_before_loop action
	 */
	do_action( 'elementify_post_content_before_loop' );

	while ( have_posts() ) :
		the_post();

		/**
		 * Functions hooked into elementify_post_content action
		 */
		do_action( 'elementify_post_before_content' );

		get_template_part( 'template-parts/content', 'single' );

		/**
		 * Functions hooked into elementify_post_content action
		 *
		 * @hooked elementify_post_after_content_elements  - 10
		 */
		do_action( 'elementify_post_after_content' );

	endwhile; // End of the loop.

	/**
	 * Functions hooked into elementify_post_content_after_loop action
	 */
	do_action( 'elementify_post_content_after_loop' );

	?>

</main><!-- #main -->

	<?php
	/**
	 * Functions hooked into elementify_after_content action
	 *
	 * @hooked elementify_sidebar    - 10
	 */
	do_action( 'elementify_after_content' );
	?>

	<?php
endif;

get_footer();