<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Elementify
 */

namespace Elementify;

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
	 * Functions hooked into elementify/before_content action
	 */
	do_action( 'elementify/before_content' );
	?>

<main id="main" class="site-main">

	<?php
	/**
	 * Functions hooked into elementify/page/content/before_loop action
	 */
	do_action( 'elementify/page/content/before_loop' );

	while ( have_posts() ) :
		the_post();

		/**
		 * Functions hooked into elementify/page/before_content action
		 */
		do_action( 'elementify/page/before_content' );

		get_template_part( 'template-parts/content', 'page' );

		/**
		 * Functions hooked into elementify/page/after_content action
		 *
		 * @hooked elementify_page_after_content_elements  - 10
		 */
		do_action( 'elementify/page/after_content' );

	endwhile; // End of the loop.

	/**
	 * Functions hooked into elementify/page/content/after_loop action
	 */
	do_action( 'elementify/page/content/after_loop' );
	?>

</main><!-- #main -->

	<?php
	/**
	 * Functions hooked into elementify/after_content action
	 *
	 * @hooked elementify_sidebar    - 10
	 */
	do_action( 'elementify/after_content' );
	?>

	<?php
endif;

get_footer();