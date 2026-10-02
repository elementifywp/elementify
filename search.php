<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Elementify
 */

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
if ( ! \Elementify\Inc\Elementor::get_instance()->do_content_location( 'archive' ) ) :
	?>

	<?php
	/**
	 * Functions hooked into elementify/before_content action
	 *
	 * @hooked elementify_site_content_start    - 10
	 */
	do_action( 'elementify/before_content' );
	?>

	<?php
	/**
	 * Functions hooked into elementify/content_top action
	 *
	 * @hooked elementify_posts_wrapper_start - 10
	 */
	do_action( 'elementify/content_top' );
	?>

	<?php if ( have_posts() ) : ?>

		<?php
		/**
		 * Functions hooked into elementify/content/before_loop action
		 *
		 * @hooked elementify_posts_page_header - 10
		 */
		do_action( 'elementify/content/before_loop' );

		/* Start the Loop */
		while ( have_posts() ) :
			the_post();

			/*
			* Include the Post-Type-specific template for the content.
			* If you want to override this in a child theme, then include a file
			* called content-___.php (where ___ is the Post Type name) and that will be used instead.
			*/
			// get_template_part( 'template-parts/content', get_post_type() );
			get_template_part( 'template-parts/content', 'search' );

		endwhile;

		/**
		 * Functions hooked into elementify/content/after_loop action
		 *
		 * @hooked elementify_posts_wrapper_end - 10
		 * @hooked elementify_posts_pagination  - 15
		 */
		do_action( 'elementify/content/after_loop' );

else :

	get_template_part( 'template-parts/content', 'none' );

endif;
?>

	<?php
	/**
	 * Functions hooked into elementify/content_bottom action
	 *
	 * @hooked elementify_posts_wrapper_end - 10
	 */
	do_action( 'elementify/content_bottom' );
	?>

	<?php
	/**
	 * Functions hooked into elementify/after_content action
	 *
	 * @hooked elementify_site_content_end  - 10
	 */
	do_action( 'elementify/after_content' );
	?>

	<?php
endif;

get_footer();
