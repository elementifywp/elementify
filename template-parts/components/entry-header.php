<?php

/**
 * Template part for displaying page title
 *
 * @package Elementify
 */

$page_title 	= false;
$classes 		= ['ele-hero-section ele-position-relative ele-position-absolute-after ele-overflow-hidden'];
$header_preset 	= ['desktop'	=> '1'];
if (is_singular()) {
	$page_title 	= true;
	$classes[] 		= 'ele-hero-single-post';
	$elements 		= is_single() ? ['title','breadcrumbs', 'post-meta', 'thumbnail'] : ['title', 'breadcrumbs'];
} else {
	$classes[] 		= 'ele-hero-archive-posts';
	$home_enable 	= false;
	$blog_enable 	= ['desktop' => true];
	$search_enable 	= ['desktop' => true];
	$archive_enable	= ['desktop' => true];
	$elements 		= ['title', 'breadcrumbs'];

	if ($home_enable && is_home() && is_front_page()) {
		$page_title = true;
	}
	if ($blog_enable && is_home() && !is_front_page()) {
		$page_title = true;
	}
	if ($search_enable && is_search()) {
		$page_title = true;
	}
	if ($archive_enable && is_archive()) {
		$page_title = true;
	}
}

if ($page_title) : ?>
	<div class="<?php echo esc_attr(implode(' ', $classes)); ?>"
		data-type="<?php echo esc_attr($header_preset['desktop']); ?>">

		<?php
		if (! empty($elements)) {

			// Remove thumbnail if conditions are met
			if (($key = array_search('thumbnail', $elements)) !== false) {
				if (is_singular() && !has_post_thumbnail($post->ID)) {
					unset($elements[$key]);
				}
			}

			if ($header_preset && array_key_exists('desktop', $header_preset) && in_array($header_preset['desktop'], ['2'])) {
				$container_width = ['desktop'	=> 'default'];
				$containerClasses = ['ele-container ele-mx-auto'];
				if (in_array($container_width['desktop'], ['narrow', 'wide', 'contain'])) {
					$containerClasses[] = 'ele-container-' . sanitize_text_field($container_width['desktop']);
				}
				echo '<div class="' . esc_attr(implode(' ', $containerClasses)) . '">';
			}
		?>

			<header class="entry-header ele-d-flex ele-flex-column">
				<?php
				$content_open = false;
				foreach ($elements as $key => $value) {
					// thumbnail
					if ($value == 'thumbnail') {
						if ($content_open) {
							$content_open = false;
							echo '</div>';
						}
						get_template_part('template-parts/components/entry-image');
					} else {
						if (! $content_open) {
							$content_open = true;
							echo '<div class="ele-card-content ele-d-flex ele-flex-column ele-flex-grow">';
						}
					}

					// title
					if ($value == 'title') {
						$title_tags = ['desktop'	=> 'h2'];
						if (is_home() && is_front_page()) {
							$title_text = ['desktop'	=> (function_exists('is_shop') && is_shop()) ? esc_html__('Products', 'elementify-framework') : esc_html__('Home', 'elementify-framework')];
							if ($title_text && array_key_exists('desktop', $title_text)) {
								printf(
									'<%1$s class="page-title ele-w-100">%2$s</%1$s><!-- .page-title -->',
									esc_attr($title_tags['desktop']),
									esc_html($title_text['desktop'])
								);
							}
						} elseif (is_archive()) {
							printf(
								'<%1$s class="page-title ele-w-100">%2$s</%1$s><!-- .page-title -->',
								esc_attr($title_tags['desktop']),
								wp_kses_post(get_the_archive_title())
							);
						} elseif (is_search()) {
							printf(
								'<%1$s class="page-title ele-w-100">%2$s</%1$s><!-- .page-title -->',
								esc_attr($title_tags['desktop']),
								wp_kses_post(get_search_query())
							);
						} elseif (is_singular()) {
							printf(
								'<%1$s class="page-title ele-w-100">%2$s</%1$s><!-- .page-title -->',
								esc_attr($title_tags['desktop']),
								wp_kses_post(get_the_title())
							);
						}
					}

					// description
					if ($value == 'excerpt') {
						the_archive_description('<div class="archive-description">', '</div>');
					}

					// breadcrumb
					if ($value == 'breadcrumbs') {
						elementify_breadcrumb();
					}
					// Post Meta
					if ($value == 'post-meta') {
						$args 	= [];
						$elements 	= ['author', 'date', 'comment'];
						$avatar 	= false;
						$date		= ['desktop'	=> 'F j, Y'];
						$taxonomy	= ['desktop'	=> 'cats'];
						$prefix		= ['desktop'	=> 'none'];
						$sept		= ['desktop'	=> 'dash'];
						// Items
						$args['items']	= ! empty($elements) ? map_deep(wp_unslash($elements), 'sanitize_text_field') : [];
						// Enable Avatar
						if ($avatar) {
							$args['avatar-enable']	= true;
						}
						// date formate
						$args['date-format'] 	= $date['desktop'] != '' ? esc_html($date['desktop']) : 'F j, Y';
						// Taxonomy Type
						$args['taxonomy-type']	= $taxonomy['desktop'] != '' ? esc_html($taxonomy['desktop']) : 'cats';
						// Item Prefix
						$args['items-prefix'] 	= $prefix['desktop'] != '' ? esc_html($prefix['desktop']) : 'none';
						// Item Prefix
						$args['items-sept'] 	= $sept['desktop'] != '' ? esc_html($sept['desktop']) : 'dash';

						get_template_part('template-parts/components/entry-meta', null, $args);
					}

					// Taxomony
					if ($value == 'taxonomy') {
						$args 	= [];
						$taxonomy	= ['desktop'	=> 'cats'];
						$prefix		= ['desktop'	=> 'none'];
						$args['items-prefix'] 	= $prefix['desktop'] != '' ? esc_html($prefix['desktop']) : 'none';
						if ($taxonomy['desktop'] == 'cats') {
							get_template_part('template-parts/components/entry-cats', null, $args);
						} else {
							get_template_part('template-parts/components/entry-tags', null, $args);
						}
					}
				}
				?>
			</header><!-- .entry-header -->

		<?php if ($header_preset && array_key_exists('desktop', $header_preset) && in_array($header_preset['desktop'], ['2'])) {
				echo '</div><!-- .ele-container --->';
			}
		} ?>

	</div><!-- .ele-hero-section -->
<?php
endif;
