<?php
/**
 * Elementor Page Builder compatibility.
 *
 * @package Elementify
 */

namespace Elementify\Inc;

use Elementify\Inc\Traits\Singleton;

/**
 * Lets the Elementor Page Builder take over the markup a theme would
 * otherwise render around it.
 *
 * Two separate mechanisms live here, because Elementor splits its own API
 * the same way:
 *
 * 1. Pages built with Elementor, which works with the free plugin.
 *    `the_content()` already renders an Elementor document on its own, so the
 *    only work left is standing the theme's own #content / .ele-container /
 *    #primary wrapper and its page-title band out of the way so Elementor's
 *    sections run full-bleed. Detection reads the document's
 *    is_built_with_elementor(), not the post meta, because Elementor\DB's
 *    equivalent helper has been deprecated since 3.2.0.
 *
 * 2. Theme Builder locations (header, footer, single, archive), which are an
 *    Elementor *Pro* feature. `elementor_theme_do_location()`,
 *    `register_all_core_location()` and the `elementor/theme/register_locations`
 *    action do not exist in Elementor core, so every call to them is guarded
 *    with function_exists() and the theme falls back to its own markup.
 */
class Elementor {

	use Singleton;

	/**
	 * Memoised result of is_elementor_page().
	 *
	 * @var bool|null
	 */
	private $is_elementor_page = null;

	/**
	 * Registers the hooks when the singleton is first created.
	 *
	 * @return void
	 */
	public function __construct() {

		$this->setup_hooks();
	}

	/**
	 * Hook Elementor support and the render-time overrides.
	 *
	 * The three elementify/* callbacks run at priority 5, ahead of the theme's
	 * own callbacks, which template-functions.php registers at priority 10 and 15.
	 *
	 * @return void
	 */
	private function setup_hooks() {

		/**
		 * Actions.
		 */
		add_action( 'after_setup_theme', [ $this, 'theme_support' ] );
		add_action( 'elementor/theme/register_locations', [ $this, 'register_locations' ] );
		add_action( 'elementify/before_content', [ $this, 'maybe_suppress_content_wrapper' ], 5 );
		add_action( 'elementify/after_content', [ $this, 'maybe_suppress_content_wrapper' ], 5 );
		add_action( 'elementify/header', [ $this, 'maybe_render_header_location' ], 5 );
		add_action( 'elementify/footer', [ $this, 'maybe_render_footer_location' ], 5 );
	}

	/**
	 * Declares support for the Elementor Page Builder.
	 *
	 * Elementor core 4.x does not read this flag itself, but it is the flag
	 * Elementor's own documentation asks themes to declare and the one
	 * third-party add-ons test for, so it sits alongside the theme's other
	 * feature support.
	 *
	 * @return void
	 */
	public function theme_support() {

		add_theme_support( 'elementor' );
	}

	/**
	 * Registers Elementor's core Theme Builder locations.
	 *
	 * Hooked to `elementor/theme/register_locations`, which only Elementor Pro
	 * fires, so this does not run on a free install.
	 *
	 * @param object $locations_manager Elementor's locations manager.
	 * @return void
	 */
	public function register_locations( $locations_manager ) {

		$locations_manager->register_all_core_location();
	}

	/**
	 * Whether the Elementor plugin is loaded.
	 *
	 * @return bool
	 */
	public function is_active() {

		return did_action( 'elementor/loaded' ) > 0;
	}

	/**
	 * Whether the current request is an Elementor page.
	 *
	 * True for a singular post built with Elementor, and also for requests
	 * where a Theme Builder location has already been rendered by
	 * do_content_location(). Memoised, because it is read once per template
	 * and detection instantiates an Elementor document object.
	 *
	 * @return bool
	 */
	public function is_elementor_page() {

		if ( null !== $this->is_elementor_page ) {
			return $this->is_elementor_page;
		}

		$this->is_elementor_page = false;

		if ( $this->is_active() && is_singular() ) {
			$post_id = get_queried_object_id();

			if ( $post_id && $this->is_built_with_elementor( $post_id ) ) {
				$this->is_elementor_page = true;
			}
		}

		return $this->is_elementor_page;
	}

	/**
	 * Whether a post was built with Elementor.
	 *
	 * Uses the Document API rather than the post meta directly, and guards
	 * against both a missing plugin and the manager returning false for a post
	 * it has no document type for.
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	private function is_built_with_elementor( $post_id ) {

		if ( ! class_exists( '\Elementor\Plugin' ) || ! isset( \Elementor\Plugin::$instance ) ) {
			return false;
		}

		$documents = \Elementor\Plugin::$instance->documents;

		if ( ! $documents ) {
			return false;
		}

		$document = $documents->get( $post_id );

		return (bool) $document && $document->is_built_with_elementor();
	}

	/**
	 * Renders a Theme Builder location.
	 *
	 * `elementor_theme_do_location()` both checks for an assigned template and
	 * prints it, so this returns false and prints nothing when the location is
	 * unassigned, Pro is not installed, or the function does not exist.
	 *
	 * @param string $location Location slug, for example `header`.
	 * @return bool Whether a template was printed.
	 */
	public function do_location( $location ) {

		if ( ! function_exists( 'elementor_theme_do_location' ) ) {
			return false;
		}

		return (bool) elementor_theme_do_location( $location );
	}

	/**
	 * Renders a content location from a root template, for example `single`
	 * or `archive`.
	 *
	 * Templates call this before their own markup and skip that markup when it
	 * returns true. Rendering the location also flags the request as an
	 * Elementor page, so the theme's container stays out of the way.
	 *
	 * @param string $location Location slug, for example `archive`.
	 * @return bool Whether a template was printed.
	 */
	public function do_content_location( $location ) {

		$rendered = $this->do_location( $location );

		if ( $rendered ) {
			$this->is_elementor_page = true;
		}

		return $rendered;
	}

	/**
	 * Stands the theme's content wrapper down on Elementor pages.
	 *
	 * Removes all three callbacks together. elementify_site_content_start()
	 * opens #content, .ele-container and #primary and elementify_site_content_end()
	 * closes them again and loads the sidebar, so unhooking either half on its
	 * own would leave unbalanced markup.
	 *
	 * @return void
	 */
	public function maybe_suppress_content_wrapper() {

		if ( ! $this->is_elementor_page() ) {
			return;
		}

		remove_action( 'elementify/before_content', 'elementify_site_content_start', 10 );
		remove_action( 'elementify/before_content', 'elementify_before_content_hero', 15 );
		remove_action( 'elementify/after_content', 'elementify_site_content_end', 10 );
	}

	/**
	 * Replaces the theme header with a Theme Builder header.
	 *
	 * Also drops the header separator, which is a spacer that only makes sense
	 * beneath the theme's own header markup.
	 *
	 * @return void
	 */
	public function maybe_render_header_location() {

		if ( ! $this->do_location( 'header' ) ) {
			return;
		}

		remove_action( 'elementify/header', 'elementify_header', 10 );
		remove_action( 'elementify/after_header', 'elementify_header_separator', 10 );
	}

	/**
	 * Replaces the theme footer with a Theme Builder footer.
	 *
	 * @return void
	 */
	public function maybe_render_footer_location() {

		if ( ! $this->do_location( 'footer' ) ) {
			return;
		}

		remove_action( 'elementify/footer', 'elementify_footer', 10 );
	}
}
