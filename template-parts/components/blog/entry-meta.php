<?php

/**
 * Template part for displaying post entry meta
 *
 * @package Elementify
 */

$the_post_id	= get_the_ID();
$default 		= ['author', 'date', 'comment'];
$metas 			= !empty($args['desktop']) ? $args['desktop'] : $default;

// Check whether the post type is allowed to output post meta.
if (in_array(get_post_type($the_post_id), array('page'), true) || empty($metas)) {
	return;
}

?>
<div class="ele-post-metas-wrap">
	<ul
		class="ele-post-metas ele-d-inline-flex ele-flex-wrap ele-align-items-center ele-list-style-none ele-p-0 ele-m-0">

		<?php foreach ($metas as $key => $value) {

			if ($value === 'author') {
				$author_email = get_the_author_meta('user_email');
				$has_avatar   = elementify_has_gravatar($author_email);
				$avatar       = get_avatar($author_email, 24, '', '', ['class'   => 'ele-avatar-icon', 'default' => '404']);
		?>
				<li class="ele-post-meta post-author ele-d-flex ele-flex-wrap ele-align-items-center">
					<span class="screen-reader-text"><?php esc_html_e('Post Author', 'elementify'); ?></span>
					<span class="ele-meta-icon">
						<?php
						if (! empty($has_avatar)) {
							echo wp_kses_post($avatar);
						}
						?>
					</span><!-- .ele-meta-icon -->

					<span class="ele-meta-text">
						<?php printf(
							/* translators: %s: Author name. */
							esc_html_x('%s', 'Post Author', 'elementify'),
							'<a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author_meta('display_name')) . '</a>'
						); ?>
					</span><!-- .ele-meta-text -->
				</li>
			<?php
			}

			if ($value === 'date') {
				$date_format    = get_option('date_format');
				$published_date = esc_html(get_the_date($date_format));
			?>
				<li class="ele-post-meta post-published-date ele-d-flex ele-flex-wrap ele-align-items-center">
					<span class="screen-reader-text"><?php esc_html_e('Published Date', 'elementify'); ?></span>
					<span class="ele-meta-text">
						<?php
						$publish_date = '<a href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '">' . esc_html($published_date) . '</a>';
						printf(
							/* translators: %s: post date. */
							esc_html_x('%s', 'Post Date', 'elementify'),
							$publish_date
						); ?>
					</span><!-- .ele-meta-text -->
				</li>
			<?php

			}

			if ($value === 'category') {
			?>
				<li class="ele-post-meta post-categories ele-d-flex ele-flex-wrap ele-align-items-center">
					<span class="screen-reader-text"><?php esc_html_e('Categories', 'elementify'); ?></span>
					<?php
					// Hide category and tag text for pages.
					if ('post' === get_post_type()) {
						/* translators: used between list items, there is a space after the comma */
						$categories_list = get_the_category_list(esc_html__(', ', 'elementify'));
						if ($categories_list) {
							/* translators: 1: list of categories. */
							printf('<span class="ele-meta-text ele-d-flex ele-align-items-center">%1$s</span>', $categories_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
					}
					?>
				</li><!-- .ele-post-meta -->
		<?php
			}
		} ?>

	</ul><!-- .ele-post-metas -->
</div><!-- .ele-post-metas-wrap -->