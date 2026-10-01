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

// echo Utils::has_container_gaps();

get_header();
?>

<?php
/**
 * Functions hooked into elementify/before_content action
 */
do_action( 'elementify/before_content' );
?>

<main id="main" class="site-main ele-d-flex ele-flex-col ele-flex-column">

	<?php
	/**
	 * Functions hooked into elementify/post/content/before_loop action
	 */
	do_action( 'elementify/post/content/before_loop' );

	while ( have_posts() ) :
		the_post();

		/**
		 * Functions hooked into elementify_post_content action
		 */
		do_action( 'elementify/post/before_content' );

		get_template_part( 'template-parts/content', 'single' );

		/**
		 * Functions hooked into elementify_post_content action
		 *
		 * @hooked elementify_post_after_content_elements  - 10
		 */
		do_action( 'elementify/post/after_content' );

	endwhile; // End of the loop.

	/**
	 * Functions hooked into elementify/post/content/after_loop action
	 */
	do_action( 'elementify/post/content/after_loop' );

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

<?php get_footer(); ?>