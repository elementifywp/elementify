<?php
/**
 * Template part for displaying post entry tags
 *
 * @package Elementify
 */

$the_post_id = get_the_ID();

// Check whether the post type is allowed to output post meta.
if ( in_array( get_post_type( $the_post_id ), [ 'page' ], true ) ) {
	return;
}

?>
<div class="ele-post-tags-wrap">
	<ul class="ele-post-metas ele-d-inline-flex ele-flex-wrap ele-align-items-center ele-list-style-none ele-p-0 ele-m-0">
		<li class="ele-post-meta post-tags ele-d-flex ele-flex-wrap ele-align-items-center">
			<span class="screen-reader-text"><?php esc_html_e( 'Tags', 'elementify' ); ?></span>
			<?php
			// Hide category and tag text for pages.
			if ( 'post' === get_post_type() ) {
				$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'elementify' ) );
				if ( $tags_list ) {
					/* translators: 1: list of tags. */
					printf( '<span class="ele-meta-text ele-d-flex ele-flex-wrap ele-align-items-center">%1$s</span>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
			?>
		</li><!-- .ele-post-meta -->
	</ul><!-- .ele-post-metas -->
</div><!-- .ele-post-tags-wrap -->

