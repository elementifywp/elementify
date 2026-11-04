<?php
/**
 * Template part for displaying post/page entry title
 *
 * @package Elementify
 */

// Title.
if ( is_singular() ) {
	printf(
		'<h1 class="entry-title ele-post-title"%2$s>%1$s</h1><!-- .entry-title -->',
		wp_kses_post( get_the_title() ),
		\Elementify\Inc\Utils::microdata_attr( 'headline' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in Utils::schema_org_definitions().
	);
} else {
	printf(
		'<h2 class="entry-title ele-post-title"%3$s><a href="%1$s" rel="bookmark"%4$s>%2$s</a></h2><!-- .entry-title -->',
		esc_url( get_the_permalink() ),
		wp_kses_post( get_the_title() ),
		\Elementify\Inc\Utils::microdata_attr( 'headline' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in Utils::schema_org_definitions().
		\Elementify\Inc\Utils::microdata_attr( 'url' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in Utils::schema_org_definitions().
	);
}
