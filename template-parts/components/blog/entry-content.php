<?php

/**
 * Template part for displaying post entry content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Elementify
 */

?>

<div class="entry-content ele-post-content">

	<?php
	if ( is_single() ) {

		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'elementify' ),
					[
						'span' => [
							'class' => [],
						],
					]
				),
				wp_kses_post( get_the_title() )
			)
		);

		wp_link_pages(
			[
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'elementify' ),
				'after'  => '</div>',
			]
		);
	} else {

		elementify_the_excerpt( 200, '...', false );
	}
	?>

</div><!-- .entry-content -->