<?php

/**
 * Template part for displaying post entry meta
 *
 * @package Elementify
 */

$the_post_id = get_the_ID();
$default     = array( 'author', 'date', 'comment' );
$metas       = ! empty( $args['desktop'] ) ? $args['desktop'] : $default;

// Check whether the post type is allowed to output post meta.
if ( in_array( get_post_type( $the_post_id ), array( 'page' ), true ) || empty( $metas ) ) {
	return;
}

?>
<div class="ele-post-metas-wrap">
	<ul class="ele-post-metas ele-d-inline-flex ele-flex-wrap ele-align-items-center ele-list-style-none ele-p-0 ele-m-0">

		<?php
		foreach ( $metas as $key => $value ) {
			$key++;

			if ( 'author' === $value ) {
				// Resolve the author explicitly from the post ID instead of
				// relying on global $authordata / $post being set up, so this
				// renders correctly regardless of where the partial is called from.
				$author_id = (int) get_post_field( 'post_author', $the_post_id );

				if ( $author_id ) {
					$author_email = get_the_author_meta( 'user_email', $author_id );
					$author_name  = get_the_author_meta( 'display_name', $author_id );
					$author_url   = get_author_posts_url( $author_id );
					$has_avatar   = elementify_has_gravatar( $author_email );
					$avatar       = get_avatar(
						$author_email,
						24,
						'',
						'',
						array(
							'class'   => 'ele-avatar-icon',
							'default' => '404',
						)
					);
					?>
					<li class="ele-post-meta post-author ele-d-flex ele-flex-wrap ele-align-items-center">
						<span class="screen-reader-text"><?php esc_html_e( 'Post Author', 'elementify' ); ?></span>
						<span class="ele-meta-text">
							<?php
							printf(
								/* translators: %s: Author name link. */
								esc_html_x( '%s', 'Post Author', 'elementify' ),
								'<a href="' . esc_url( $author_url ) . '">' . esc_html( $author_name ) . '</a>'
							);
							?>
						</span><!-- .ele-meta-text -->
					</li>
					<?php
				}
			}

			if ( 'date' === $value ) {
				$date_format    = get_option( 'date_format' );
				$published_date = esc_html( get_the_date( $date_format, $the_post_id ) );
				?>
				<li class="ele-post-meta post-published-date ele-d-flex ele-flex-wrap ele-align-items-center">
					<span class="screen-reader-text"><?php esc_html_e( 'Published Date', 'elementify' ); ?></span>
					<span class="ele-meta-text">
						<?php
						$publish_date = '<a href="' . esc_url( get_month_link( get_the_time( 'Y', $the_post_id ), get_the_time( 'm', $the_post_id ) ) ) . '">' . esc_html( $published_date ) . '</a>';
						printf(
							/* translators: %s: post date. */
							esc_html_x( '%s', 'Post Date', 'elementify' ),
							$publish_date
						);
						?>
					</span><!-- .ele-meta-text -->
				</li>
				<?php
			}

			if ( 'category' === $value ) {
				?>
				<li class="ele-post-meta post-categories ele-d-flex ele-flex-wrap ele-align-items-center">
					<span class="screen-reader-text"><?php esc_html_e( 'Categories', 'elementify' ); ?></span>
					<?php
					// Hide category and tag text for pages.
					if ( 'post' === get_post_type( $the_post_id ) ) {
						/* translators: used between list items, there is a space after the comma */
						$categories_list = get_the_category_list( esc_html__( ', ', 'elementify' ), '', $the_post_id );
						if ( $categories_list ) {
							/* translators: 1: list of categories. */
							printf( '<span class="ele-meta-text ele-d-flex ele-flex-wrap ele-align-items-center">%1$s</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
					}
					?>
				</li><!-- .ele-post-meta -->
				<?php
			}

			if ( 'comment' === $value && ( ! post_password_required( $the_post_id ) && ( comments_open( $the_post_id ) || get_comments_number( $the_post_id ) ) ) ) {
				?>
				<li class="ele-post-meta post-comments ele-d-flex ele-flex-wrap ele-align-items-center">
					<span class="screen-reader-text"><?php esc_html_e( 'Comments', 'elementify' ); ?></span>
					<?php
					echo '<span class="ele-meta-text">';
					echo '<span class="comments-link">';
					comments_popup_link(
						sprintf(
							wp_kses(
								/* translators: %s: post title */
								__( 'No Comment<span class="screen-reader-text"> on %s</span>', 'elementify' ),
								array(
									'span' => array(
										'class' => array(),
									),
								)
							),
							wp_kses_post( get_the_title( $the_post_id ) )
						)
					);
					echo '</span>';
					echo '</span>';
					?>
				</li><!-- .ele-post-meta -->
				<?php
			}

			if ( $key < count( $metas ) ) {
				echo '<li class="ele-meta-separator ele-dash-icon"></li>';
			}
		}
		?>

	</ul><!-- .ele-post-metas -->
</div><!-- .ele-post-metas-wrap -->