<?php

/**
 * Template part for displaying post/page featured image.
 *
 * @package Elementify
 */

$the_post_id   = get_the_ID();

if (post_password_required() || is_attachment()) {
	return;
}
// add class for now featured images
$wrapper_classes 	= ['ele-featured-image-wrap ele-overflow-hidden'];
if (! has_post_thumbnail()) {
	$wrapper_classes[] = 'ele-unavailable-image';
}
?>
<div class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>">

	<figure class="ele-featured-image ele-position-relative ele-position-absolute-after" data-ratio="4x3">

		<?php
		if (is_single() || is_page()) {

			elementify_the_post_thumbnail(
				$the_post_id,
				'large',
				[
					'class' => 'attachment-featured-large size-featured-image lazyloaded'
				]
			);
		} else { ?>

			<a class="post-thumbnail ele-d-block" href="<?php echo esc_url(get_permalink()); ?>" aria-hidden="true" tabindex="-1">
				<?php
				elementify_the_post_thumbnail(
					$the_post_id,
					'medium',
					[
						'class' => 'attachment-featured-large size-featured-image lazyloaded'
					]
				);
				?>
			</a><!-- .post-thumbnail -->

		<?php } ?>

		<?php
		if (is_sticky()) {
			printf(
				esc_html_x('%1$s ', 'sticky post', 'elementify'),
				'<label class="ele-sticky-label">' . esc_html__('Featured Post', 'elementify') . '</label>'
			);
		}
		?>

	</figure><!-- .ele-featured-image -->

</div><!-- .ele-featured-image-wrap -->