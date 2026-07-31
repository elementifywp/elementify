<?php

/**
 * Bootstraps the Theme.
 *
 * @package Elementify
 */

namespace Elementify\Inc;

class Elementify
{

	public function __construct()
	{

		Assets::get_instance();
		Utils::get_instance();
		Customizer::get_instance();
		Menus::get_instance();
		Sidebars::get_instance();

		$this->setup_hooks();
	}

	private function setup_hooks()
	{

		/**
		 * Actions.
		 */
		add_action('after_setup_theme', [$this, 'setup_theme']);
	}

	/**
	 * Setup theme.
	 *
	 * @return void
	 */
	public function setup_theme()
	{

		/**
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support('title-tag');

		/**
		 * Custom logo.
		 *
		 * @see Adding custom logo
		 * @link https://developer.wordpress.org/themes/functionality/custom-logo/#adding-custom-logo-support-to-your-theme
		 */
		add_theme_support(
			'custom-logo',
			[
				'header-text' => [
					'site-title',
					'site-description',
				],
				'height'      => 100,
				'width'       => 400,
				'flex-height' => true,
				'flex-width'  => true,
			]
		);

		/**
		 * Adds Custom background panel to customizer.
		 * Default color matches --ele-light-base-color in style.css / palette
		 * slug "ele-base" in theme.json — keep these three in sync if the
		 * brand's base color ever changes.
		 *
		 * @see Enable Custom Backgrounds
		 * @link https://developer.wordpress.org/themes/functionality/custom-backgrounds/#enable-custom-backgrounds
		 */
		add_theme_support(
			'custom-background',
			[
				'default-color' => 'ffffff',
				'default-image' => '',
				'default-repeat' => 'no-repeat',
			]
		);

		/**
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * Adding this will allow you to select the featured image on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support('post-thumbnails');

		add_theme_support('post-formats', array('aside', 'gallery'));

		add_post_type_support('page', 'excerpt'); //change page with your post type slug.


		/**
		 * Register image sizes.
		 */
		//add_image_size( 'featured-thumbnail', 350, 233, true );


		// Add theme support for selective refresh for widgets.
		/**
		 * WordPress 4.5 includes a new Customizer framework called selective refresh
		 *
		 * Selective refresh is a hybrid preview mechanism that has the performance benefit of not having to refresh the entire preview window.
		 *
		 * @link https://make.wordpress.org/core/2016/03/22/implementing-selective-refresh-support-for-widgets/
		 */
		add_theme_support('customize-selective-refresh-widgets');

		// Add default posts and comments RSS feed links to head.
		add_theme_support('automatic-feed-links');

		/**
		 * Switch default core markup for search form, comment form, comment-list, gallery, caption, script and style
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			[
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
				'navigation-widgets',
			]
		);

		/**
		 * Makes embeds (YouTube, Twitter, etc.) fluid inside .ele-container
		 * instead of overflowing at fixed widths. Standard for any theme
		 * pairing classic templates with block-editor content.
		 *
		 * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
		 */
		add_theme_support('responsive-embeds');

		// Gutenberg theme support.

		/**
		 * Some blocks in Gutenberg like tables, quotes, separator benefit from structural styles (margin, padding, border etc…)
		 * They are applied visually only in the editor (back-end) but not on the front-end to avoid the risk of conflicts with the styles wanted in the theme.
		 * If you want to display them on front to have a base to work with, in this case, you can add support for wp-block-styles, as done below.
		 * @see Theme Styles.
		 * @link https://make.wordpress.org/core/2018/06/05/whats-new-in-gutenberg-5th-june/, https://developer.wordpress.org/block-editor/developers/themes/theme-support/#default-block-styles
		 */
		add_theme_support('wp-block-styles');

		/**
		 * Some blocks such as the image block have the possibility to define
		 * a “wide” or “full” alignment by adding the corresponding classname
		 * to the block’s wrapper ( alignwide or alignfull ). A theme can opt-in for this feature by calling
		 * add_theme_support( 'align-wide' ), like we have done below.
		 *
		 * Paired with theme.json's settings.layout.contentSize (var(--ele-container-max-width))
		 * and wideSize ("100%") — this is what actually generates the wide/full CSS widths;
		 * this add_theme_support call is what exposes the align buttons in the block toolbar.
		 *
		 * @see Wide Alignment
		 * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#wide-alignment
		 */
		add_theme_support('align-wide');

		/**
		 * Loads the editor styles in the Gutenberg editor.
		 *
		 * Editor Styles allow you to provide the CSS used by WordPress’ Visual Editor so that it can match the frontend styling.
		 * If we don't add this, the editor styles will only load in the classic editor ( tiny mice )
		 *
		 * @see https://developer.wordpress.org/block-editor/developers/themes/theme-support/#editor-styles
		 */
		add_theme_support('editor-styles');

		/**
		 * Path to our editor stylesheet.
		 *
		 * NOTE: this loads the full compiled frontend stylesheet (main.css) into
		 * the block editor iframe. Since main.css includes header/footer/nav/modal
		 * chrome that never renders inside the editor canvas, consider splitting
		 * out a slimmer build/css/editor.css containing just the content-facing
		 * rules (typography, .entry-content, .ele-button-fill/.ele-button-outline,
		 * tables, etc.) so block previews match the frontend without paying for
		 * unused chrome CSS in the editor.
		 *
		 * theme.json already supplies color/typography/spacing/border tokens to
		 * the editor automatically — don't duplicate those here via
		 * add_theme_support('editor-color-palette') / ('editor-font-sizes'), or
		 * they'll conflict with the palette/fontSizes defined in theme.json.
		 *
		 * @see add_editor_style()
		 * @link https://developer.wordpress.org/reference/functions/add_editor_style/
		 */
		add_editor_style('build/css/main.css');

		// Remove the core block patterns
		remove_theme_support('core-block-patterns');

		/**
		 * Set the maximum allowed width for any content in the theme,
		 * like oEmbeds and images added to posts.
		 *
		 * Kept in sync with --ele-container-max-width (style.css) and
		 * settings.layout.contentSize in theme.json — update all three
		 * together if this value ever changes.
		 *
		 * @see Content Width
		 * @link https://codex.wordpress.org/Content_Width
		 */
		global $content_width;
		if (! isset($content_width)) {
			$content_width = 1280;
		}
	}
}