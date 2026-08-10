<?php

/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Elementify
 */

add_action('wp_head', 'elementify_pingback_header');
add_filter('body_class', 'elementify_body_classes');

if (! function_exists('elementify_pingback_header')) {
	/**
	 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
	 */
	function elementify_pingback_header()
	{
		if (is_singular() && pings_open()) {
			printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
		}
	}
}

if (! function_exists('elementify_body_classes')) {
	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array
	 */
	function elementify_body_classes($classes)
	{
		// Adds a class of hfeed to non-singular pages.
		if (! is_singular()) {
			$classes[] = 'hfeed';
		}

		// Adds a class of no-sidebar when there is no sidebar present.
		if (! is_active_sidebar('sidebar-1') || is_404()) {
			$classes[] = 'no-sidebar';
		}

		//$classes[] = 'ele-position-absolute-after';

		return $classes;
	}
}

/*
|--------------------------------------------------------------------------
| Header Hooks
|--------------------------------------------------------------------------
|
*/

add_action('elementify/head', 'elementify_head_meta', 			10);
add_action('elementify/head_bottom', 'elementify_wp_head', 	10);
add_action('elementify/body_attributes', 'elementify_body_attributes', 10);
add_action('elementify/body_top', 'elementify_wp_body_open', 	10);
add_action('elementify/before_header', 'elementify_skip_link', 10);

if (! function_exists('elementify_head_meta')) {

	/**
	 * Add custom data in the head tag on the front end.
	 */
	function elementify_head_meta()
	{
?>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php
	}
}

if (! function_exists('elementify_wp_head')) {

	/**
	 * Prints scripts or data in the head tag on the front end.
	 */
	function elementify_wp_head()
	{
		wp_head();
	}
}

if (! function_exists('elementify_body_attributes')) {

	/**
	 * Body tag attributes
	 */
	function elementify_body_attributes()
	{

		$prefix = 'blog';

		if (is_singular()) {
			$prefix = is_single() ? 'single-post' : 'single-page';
		}

		if (! $prefix) {
			return; // no value
		}
		print ' data-prefix="' . esc_attr($prefix) . '" data-sidebar="right"';
	}
}

if (! function_exists('elementify_wp_body_open')) {

	/**
	 * Triggered after the opening body tag.
	 */
	function elementify_wp_body_open()
	{
		wp_body_open();
	}
}

if (! function_exists('elementify_skip_link')) {

	/**
	 * Skip to content links.
	 */
	function elementify_skip_link()
	{
	?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'elementify'); ?></a>
<?php
	}
}

/*
|--------------------------------------------------------------------------
| Header Hooks
|--------------------------------------------------------------------------
|
*/
add_action('elementify/header', 'elementify_header', 						10);
add_action('elementify/after_header', 'elementify_header_separator', 		10);
//add_action( 'elementify/body_bottom', 'elementify_header_site_overlay', 	10 );

if (! function_exists('elementify_header')) {

	/**
	 * Site main header content.
	 */
	function elementify_header()
	{

		get_template_part('template-parts/header/nav');
	}
}

if (! function_exists('elementify_header_separator')) {

	/**
	 * Add header separator div
	 *
	 * @since   1.0.0
	 * @return  string HTML display
	 */
	function elementify_header_separator()
	{
		echo '<div class="ele-after-header"></div>';
	}
}

if (! function_exists('elementify_header_site_overlay')) {

	/**
	 * Add site overlay div after header
	 *
	 * @since   1.0.0
	 * @return  string HTML display
	 */
	function elementify_header_site_overlay()
	{
		$overlay_div    = ['ele-site-overlay'];
		// Header Transition
		// $settings   = elementify_customizer_controls_io();
		// if ( !empty( $settings['header_transition'] ) ) {
		// 	$overlay_div[] = esc_attr( $settings['header_transition'] );
		// }

		echo '<div class="' . esc_attr(implode(' ', $overlay_div)) . '"></div>';
	}
}

/*
|--------------------------------------------------------------------------
| Site Content Hooks
|--------------------------------------------------------------------------
|
*/
add_action('elementify/before_content', 'elementify_site_content_start', 		10);
add_action('elementify/before_content', 'elementify_before_content_hero', 		15);
add_action('elementify/after_content', 'elementify_site_content_end', 			10);
//add_filter( 'post_class', 'elementify_set_post_class' );

if (! function_exists('elementify_site_content_start')) {

	/**
	 * Site Content Start
	 */
	function elementify_site_content_start()
	{
		echo '<div id="content" class="site-content ele-position-relative ele-position-absolute-after">';
		echo '<div class="ele-container ele-mx-auto">';
		echo '<div id="primary" class="content-area primary ele-position-relative ele-z-10">';
	}
}

if (! function_exists('elementify_before_content_hero')) {

	/**
	 * page header
	 */
	function elementify_before_content_hero()
	{
		get_template_part('template-parts/components/entry-header');
	}
}

if (! function_exists('elementify_site_content_end')) {

	/**
	 * Site Content End
	 */
	function elementify_site_content_end()
	{
		echo '</div><!-- #primary -->';
		get_sidebar();
		echo '</div><!-- .ele-container -->';
		echo '</div><!-- #content -->';
	}
}

if (! function_exists('elementify_set_post_class')) {

	/**
	 * Post class
	 */
	function elementify_set_post_class($classes)
	{
		$classes[] = 'ele-column'; //add a custom class to highlight this row in the table
		$classes[] = 'ele-text-left';
		// Return the array
		return apply_filters('elementify_set_post_class', $classes);
	}
}

/*
|--------------------------------------------------------------------------
| Site Sidebar Hooks
|--------------------------------------------------------------------------
|
*/
add_action('elementify/sidebar', 'elementify_sidebar_area', 		10);

if (! function_exists('elementify_sidebar_area')) {

	/**
	 * Sidebar Area
	 */
	function elementify_sidebar_area()
	{

		echo '<aside class="widget-area" data-layout="1" data-title-type="1" data-link-type="1">';
		echo '<div class="ele-sidebar ele-position-relative">';

		/**
		 * Functions hooked into elementify/sidebar_top action
		 *
		 */
		do_action('elementify/sidebar_top');

		dynamic_sidebar('sidebar-1');

		/**
		 * Functions hooked into elementify/sidebar_bottom action
		 *
		 */
		do_action('elementify/sidebar_bottom');

		echo '</div><!-- .ele-sidebar -->';
		echo '</aside><!-- .widget-area -->';
	}
}


/*
|--------------------------------------------------------------------------
| Blog Page Hooks
|--------------------------------------------------------------------------
|
*/
add_action('elementify/content_top', 'elementify_posts_wrapper_start', 		10);
add_action('elementify/content_bottom', 'elementify_posts_wrapper_end', 		10);
add_action('elementify/content_bottom', 'elementify_posts_pagination', 		15);
add_action('elementify/loop/entry_content', 'elementify_loop_entry_post_card', 20);

if (! function_exists('elementify_posts_wrapper_start')) {

	/**
	 * Posts wrapper Start
	 */
	function elementify_posts_wrapper_start()
	{
		echo '<main id="main" class="site-main ele-posts-wrap ele-d-grid ele-align-items-initial" data-layout="3" data-cards="simple">';
	}
}

if (! function_exists('elementify_posts_wrapper_end')) {

	/**
	 * Posts wrapper End
	 */
	function elementify_posts_wrapper_end()
	{
		echo '</main><!-- #main -->';
	}
}

if (! function_exists('elementify_posts_pagination')) {

	/**
	 * Posts navigation
	 */
	function elementify_posts_pagination()
	{
		elemetify_pagination();
	}
}

if (! function_exists('elementify_loop_entry_thumbnail')) {

	/**
	 * Entry Thumbnail
	 */
	function elementify_loop_entry_thumbnail()
	{

		get_template_part('template-parts/components/entry-image');
	}
}

if (! function_exists('elementify_loop_entry_post_card')) {

	/**
	 * Entry Post Card
	 */
	function elementify_loop_entry_post_card()
	{

		$elements = ['thumbnail', 'title', 'metas', 'excerpt', 'more'];

		if (!empty($elements)) {

			$last_component = count($elements) - 1;

			$inserted = array('ghost'); // not necessarily an array, see manual quote

			array_splice($elements, $last_component, 0, $inserted); // splice in at position n

			echo '<div class="ele-d-flex ele-flex-column ele-column-content ele-position-relative ele-h-100 ele-w-100">';

			$content_open = false;

			foreach ($elements as $key => $value) {

				// Thubmail
				if ($value == 'thumbnail') {
					if ($content_open) {
						$content_open = false;
						echo '</div>';
					}
					get_template_part('template-parts/components/entry-image');
				} else {
					if (! $content_open) {
						$content_open = true;
						echo '<div class="ele-card-content ele-d-flex ele-flex-column ele-justify-content-inherit">';
					}
				}
				// Categories
				if ($value == 'cats') {
					get_template_part('template-parts/components/entry-cats');
				}
				// Title
				if ($value == 'title') {
					get_template_part('template-parts/components/entry-title');
				}
				// Metas
				if ($value == 'metas') {
					get_template_part('template-parts/components/entry-meta');
				}
				// Excerpt
				if ($value == 'excerpt') {
					get_template_part('template-parts/components/entry-content');
				}
				// Read More
				if ($value == 'more') {
					echo elementify_excerpt_more(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				// Tags
				if ($value == 'tags') {
					get_template_part('template-parts/components/entry-tags');
				}
				// Divider
				if ($value == 'div1') {
					echo '<div class="ele-entry-divider ele-boundless"></div><!-- .ele-entry-divider -->';
				}
				// Ghost
				if ($value == 'ghost') {
					echo '<div class="ele-ghost"></div><!-- .ele-ghost -->';
				}
			}

			echo '</div><!-- .ele-column-content -->';
		}
	}
}

/*
|--------------------------------------------------------------------------
| Single Post Hooks
|--------------------------------------------------------------------------
|
*/
// add_action('elementify/loop/post/entry_content', 'elementify_single_post_title_elements', 10);
add_action('elementify/loop/post/entry_content', 'elementify_single_post_content', 15);
add_action('elementify/post/after_content', 'elementify_post_after_content_elements', 10);
if (! function_exists('elementify_single_post_title_elements')) {

	/**
	 * Post Title
	 */
	function elementify_single_post_title_elements()
	{
		get_template_part('template-parts/components/entry-header');
	}
}
if (! function_exists('elementify_single_post_content')) {

	/**
	 * Post Content
	 */
	function elementify_single_post_content()
	{
		get_template_part('template-parts/components/entry-content');
	}
}

if (! function_exists('elementify_post_after_content_elements')) {

	/**
	 * Single post after content 
	 */
	function elementify_post_after_content_elements()
	{
		the_post_navigation(
			array(
				'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'elementify') . '</span> <span class="nav-title">%title</span>',
				'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'elementify') . '</span> <span class="nav-title">%title</span>',
			)
		);

		// If comments are open or we have at least one comment, load up the comment template.
		if (comments_open() || get_comments_number()) :
			comments_template();
		endif;
	}
}


/*
|--------------------------------------------------------------------------
| Single Page Hooks
|--------------------------------------------------------------------------
|
*/
add_action('elementify/page/after_content', 'elementify_page_after_content_elements', 10);

if (! function_exists('elementify_page_after_content_elements')) {

	/**
	 * Single page after content 
	 */
	function elementify_page_after_content_elements()
	{

		// If comments are open or we have at least one comment, load up the comment template.
		if (comments_open() || get_comments_number()) :
			comments_template();
		endif;
	}
}


/*
|--------------------------------------------------------------------------
| Search Page Hooks
|--------------------------------------------------------------------------
|
*/

/*
|--------------------------------------------------------------------------
| Comment Hooks
|--------------------------------------------------------------------------
|
*/
add_action('elementify/comments', 'elementify_comments_element', 10);
if (! function_exists('elementify_comments_element')) {

	/**
	 * Comment section elements.
	 */
	function elementify_comments_element()
	{

		// You can start editing here -- including this comment!
		if (have_comments()) {
			$position 	= is_single()
				? get_theme_mod('elementify_framework_single_post_comments_form_position', ['desktop' => 'default'])
				: get_theme_mod('elementify_framework_single_page_comments_form_position', ['desktop' => 'default']);
		?>
<h2 class="comments-title">
    <?php
				$elementify_comment_count = get_comments_number();
				if (1 == $elementify_comment_count) {
					echo esc_html__('One Comment', 'elementify');
				} else {
					/* translators: %s: The count of comments */
					printf(esc_html__('%s Comments', 'elementify'), $elementify_comment_count);
				}
				?>
</h2><!-- .comments-title -->

<?php
			if ($position['desktop'] === 'above') {
				comment_form();
			}
			?>

<?php the_comments_navigation(); ?>

<ol class="comment-list">
    <?php
				wp_list_comments(
					array(
						'style'      => 'ol',
						'short_ping' => true,
					)
				);
				?>
</ol><!-- .comment-list -->

<?php
			the_comments_navigation();

			// If comments are closed and there are comments, let's leave a little note, shall we?
			if (! comments_open()) :
			?>
<p class="no-comments"><?php esc_html_e('Comments are closed.', 'elementify'); ?></p>
<?php
			endif;

			if ($position['desktop'] === 'default') {
				comment_form();
			}
		} // Check for have_comments().
		else {
			comment_form();
		}
	}
}

/*
|--------------------------------------------------------------------------
| 404 Page Hooks
|--------------------------------------------------------------------------
|
*/
add_action('elementify/404/entry_content', 'elementify_404_conent', 		10);
if (! function_exists('elementify_404_conent')) {

	/**
	 * 404 page content.
	 */
	function elementify_404_conent()
	{
		?>
<div class="page-content ele-d-grid ele-align-items-initial" data-columns="1" data-columns-md="2" data-columns-lg="2">

    <div
        class="ele-column ele-card-content ele-d-flex ele-flex-column ele-justify-content-initial ele-align-items-initial">
        <h6 class="entry-sub-title">ERROR CODE: 404</h6><!-- .entry-title -->
        <h1 class="entry-title">OOOPS!!</h1><!-- .entry-title -->
        <p><?php esc_html_e('This is not the page you are looking for', 'elementify'); ?>
        </p>
    </div>
    <div class="ele-column ele-featured-image-wrap ele-overflow-hidden">
        <figure class="ele-featured-image ele-position-relative ele-position-absolute-after" data-ratio="4x3">
            <img class="post-thumbnail ele-d-block"
                src="<?php echo esc_url(get_template_directory_uri() . '/assets/build/images/404.webp'); ?>">
        </figure><!-- .ele-featured-image -->
    </div>

</div>

<?php
	}
}


/*
|--------------------------------------------------------------------------
| Footer Hooks
|--------------------------------------------------------------------------
|
*/
add_action('elementify/footer', 'elementify_footer', 10);
if (! function_exists('elementify_footer')) {

	/**
	 * Site footer content.
	 */
	function elementify_footer()
	{
		get_template_part('template-parts/footer/copyright');
	}
}