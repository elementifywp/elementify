<?php

/**
 * Template part for displaying post entry content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Elementify
 */
$content_classes   = [ 'ele-post-content' ];
$content_classes[] = ( is_singular() ) ? 'entry-content' : 'entry-excerpt';
?>

<div class="<?php echo esc_attr( implode( ' ', $content_classes ) ); ?>">

	<?php
	if ( is_singular() ) {

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

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'elementify' ),
					[
						'span' => [
							'class' => [],
						],
					]
				),
				wp_kses_post( get_the_title() )
			),
			'<span class="edit-link">',
			'</span>'
		);
	} else {

		elementify_the_excerpt( 200, '...', false );
	}
	?>

</div><!-- .entry-content -->