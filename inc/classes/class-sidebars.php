<?php
/**
 * Theme Sidebars.
 *
 * @package Elementify
 */

namespace Elementify\Inc;

use Elementify\Inc\Utils;
use Elementify\Inc\Traits\Singleton;

/**
 * Registers the theme's widget areas: the main sidebar, a popup area, two
 * header areas and six footer areas.
 */
class Sidebars {

	use Singleton;

	/**
	 * Register the widget area hooks when the singleton is first created.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Hook widget area registration into `widgets_init`.
	 *
	 * @return void
	 */
	private function setup_hooks() {

		/**
		 * Actions
		 */
		add_action( 'widgets_init', [ $this, 'register_sidebars' ] );
	}

	/**
	 * Register the theme's widget areas.
	 *
	 * Builds sidebar-1, popup-1, header-1 to header-2 and footer-1 to
	 * footer-6, passes the list through the `elementify_register_sidebar_args`
	 * filter, and registers whatever remains. Nothing is registered if the
	 * filter returns an empty array.
	 *
	 * @action widgets_init
	 *
	 * @return void
	 */
	public function register_sidebars() {

		$args = [
			'sidebar-1' => [
				'name'          => esc_html__( 'Sidebar', 'elementify' ),
				'id'            => 'sidebar-1',
				'description'   => esc_html__( 'Add widgets here.', 'elementify' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			],
			'popup-1'   => [
				'name'          => esc_html__( 'Popup Area', 'elementify' ),
				'id'            => 'popup-1',
				'description'   => esc_html__( 'Add widgets here.', 'elementify' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			],
		];

		// Header Widgets Area
		for ( $i = 1; $i <= 2; $i++ ) {
			$args[ 'header-' . $i ] = [
				/* translators: 1: Widget number. */
				'name'          => sprintf( esc_html__( 'Header Area #%d', 'elementify' ), $i ),
				'id'            => 'header-' . $i,
				'description'   => esc_html__( 'Add widgets here.', 'elementify' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			];
		}
		// Footer Widgets Area
		for ( $i = 1; $i <= 6; $i++ ) {
			$args[ 'footer-' . $i ] = [
				/* translators: 1: Widget number. */
				'name'          => sprintf( esc_html__( 'Footer Area #%d', 'elementify' ), $i ),
				'id'            => 'footer-' . $i,
				'description'   => esc_html__( 'Add widgets here.', 'elementify' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			];
		}

		$args = apply_filters( 'elementify_register_sidebar_args', $args );
		if ( empty( $args ) ) {
			return;
		}
		foreach ( $args as $key => $sidebar ) {
			register_sidebar( $sidebar );
		}
	}
}
