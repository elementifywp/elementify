<?php

/**
 * Bootstraps the Theme.
 *
 * @package Elementify
 */

namespace Elementify\Inc;

/**
 * Boots the theme's service classes and declares theme support.
 *
 * Instantiated once per request from functions.php via
 * elementify_get_theme_instance(). Booting the singletons here is what wires
 * up their hooks, so this class is the theme's single entry point.
 */
class Elementify {


	/**
	 * Boot the singleton services, then register the theme's own hooks.
	 *
	 * @return void
	 */
	public function __construct() {

		Assets::get_instance();
		Utils::get_instance();
		Customizer::get_instance();
		Menus::get_instance();
		Sidebars::get_instance();
		Elementor::get_instance();

		$this->setup_hooks();
	}

	/**
	 * Hook theme support registration into `after_setup_theme`.
	 *
	 * @return void
	 */
	private function setup_hooks() {

		/**
		 * Actions.
		 */
		add_action( 'after_setup_theme', [ $this, 'setup_theme' ] );
	}

	/**
	 * Declare the theme's WordPress support features.
	 *
	 * Covers title-tag, custom logo and background, post thumbnails, the aside
	 * and gallery post formats, HTML5 markup, automatic feed links, wide and
	 * full block alignments, and block styles. Also loads the compiled CSS in
	 * the block editor via add_editor_style(), removes the core block
	 * patterns, enables the excerpt field for pages, and sets the global
	 * $content_width to 1280 when nothing has set it already.
	 *
	 * @return void
	 */
	public function setup_theme() {

		/**
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

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
		 *
		 * @see Enable Custom Backgrounds
		 * @link https://developer.wordpress.org/themes/functionality/custom-backgrounds/#enable-custom-backgrounds
		 */
		add_theme_support(
			'custom-background',
			[
				'default-color'  => 'ffffff',
				'default-image'  => '',
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
		add_theme_support( 'post-thumbnails' );

		add_theme_support( 'post-formats', [ 'aside', 'gallery' ] );

		add_post_type_support( 'page', 'excerpt' ); // change page with your post type slug.

		/**
		 * Register image sizes.
		 */
		// add_image_size( 'featured-thumbnail', 350, 233, true );

		// Add theme support for selective refresh for widgets.
		/**
		 * WordPress 4.5 includes a new Customizer framework called selective refresh
		 *
		 * Selective refresh is a hybrid preview mechanism that has the performance benefit of not having to refresh the entire preview window.
		 *
		 * @link https://make.wordpress.org/core/2016/03/22/implementing-selective-refresh-support-for-widgets/
		 */
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

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
			]
		);

		// Gutenberg theme support.

		/**
		 * Some blocks in Gutenberg like tables, quotes, separator benefit from structural styles (margin, padding, border etc…)
		 * They are applied visually only in the editor (back-end) but not on the front-end to avoid the risk of conflicts with the styles wanted in the theme.
		 * If you want to display them on front to have a base to work with, in this case, you can add support for wp-block-styles, as done below.
		 *
		 * @see Theme Styles.
		 * @link https://make.wordpress.org/core/2018/06/05/whats-new-in-gutenberg-5th-june/, https://developer.wordpress.org/block-editor/developers/themes/theme-support/#default-block-styles
		 */
		add_theme_support( 'wp-block-styles' );

		/**
		 * Some blocks such as the image block have the possibility to define
		 * a “wide” or “full” alignment by adding the corresponding classname
		 * to the block’s wrapper ( alignwide or alignfull ). A theme can opt-in for this feature by calling
		 * add_theme_support( 'align-wide' ), like we have done below.
		 *
		 * @see Wide Alignment
		 * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#wide-alignment
		 */
		add_theme_support( 'align-wide' );

		/**
		 * Loads the editor styles in the Gutenberg editor.
		 *
		 * Editor Styles allow you to provide the CSS used by WordPress’ Visual Editor so that it can match the frontend styling.
		 * If we don't add this, the editor styles will only load in the classic editor ( tiny mice )
		 *
		 * @see https://developer.wordpress.org/block-editor/developers/themes/theme-support/#editor-styles
		 */
		add_theme_support( 'editor-styles' );
		/**
		 * Loads the theme stylesheet in the block editor so blocks look the same
		 * as on the site, plus editor.css, which mirrors the few main.css rules
		 * that are scoped to front-end-only wrappers (.entry-content, body classes).
		 *
		 * @link https://developer.wordpress.org/reference/functions/add_editor_style/
		 */
		add_editor_style( [ 'assets/build/css/main.css', 'assets/build/css/editor.css' ] );

		// Remove the core block patterns
		remove_theme_support( 'core-block-patterns' );

		/**
		 * Set the maximum allowed width for any content in the theme,
		 * like oEmbeds and images added to posts
		 *
		 * @see Content Width
		 * @link https://codex.wordpress.org/Content_Width
		 */
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 1280;
		}
	}
}
