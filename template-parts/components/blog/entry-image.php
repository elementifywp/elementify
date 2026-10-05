<?php

/**
 * Template part for displaying post/page featured image.
 *
 * @package Elementify
 */

$the_post_id = get_the_ID();

if ( post_password_required() || is_attachment() ) {
	return;
}
?>
<div class="ele-featured-image-wrap ele-w-100 ele-unavailable-image">

	<figure class="ele-featured-image ele-position-relative" data-ratio="4x3">

		<?php
		if ( is_single() || is_page() ) {

			elementify_the_post_thumbnail(
				$the_post_id,
				'large',
				[
					'class' => 'attachment-featured-large size-featured-image',
				]
			);
		} else {
			?>

			<a class="post-thumbnail ele-d-block" href="<?php echo esc_url( get_permalink() ); ?>" aria-hidden="true" tabindex="-1">
				<?php
				elementify_the_post_thumbnail(
					$the_post_id,
					'medium',
					[
						'class' => 'attachment-featured-large size-featured-image',
					]
				);
				?>
			</a><!-- .post-thumbnail -->

		<?php } ?>

		<?php
		if ( is_sticky() ) {
			printf(
				esc_html_x( '%1$s ', 'sticky post', 'elementify' ),
				'<label class="ele-sticky-label">' . esc_html__( 'Featured Post', 'elementify' ) . '</label>'
			);
		}
		?>

	</figure><!-- .ele-featured-image -->

</div><!-- .ele-featured-image-wrap -->