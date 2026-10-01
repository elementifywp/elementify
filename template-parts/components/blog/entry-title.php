<?php
/**
 * Template part for displaying post/page entry title
 *
 * @package Elementify
 */

// Title
if ( is_single() || is_page() ) {
	printf(
		'<h1 class="entry-title ele-post-title">%1$s</h1><!-- .entry-title -->',
		wp_kses_post( get_the_title() )
	);
} else {
	printf(
		'<h2 class="entry-title ele-post-title"><a href="%1$s" rel="bookmark">%2$s</a></h2><!-- .entry-title -->',
		esc_url( get_the_permalink() ),
		wp_kses_post( get_the_title() )
	);
}
