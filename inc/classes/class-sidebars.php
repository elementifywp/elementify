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
 * Class Sidebars
 */
class Sidebars {

	use Singleton;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Initialize hooks.
	 */
	private function setup_hooks() {

		/**
		 * Actions
		 */
		add_action( 'widgets_init', [ $this, 'register_sidebars' ] );
	}

	/**
	 * Register widgets.
	 *
	 * @action widgets_init
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
				'name'          => esc_html__( 'Popup Area', 'elementify-framework' ),
				'id'            => 'popup-1',
				'description'   => esc_html__( 'Add widgets here.', 'elementify-framework' ),
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
				'name'          => sprintf( esc_html__( 'Header Area #%d', 'elementify-framework' ), $i ),
				'id'            => 'header-' . $i,
				'description'   => esc_html__( 'Add widgets here.', 'elementify-framework' ),
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
				'name'          => sprintf( esc_html__( 'Footer Area #%d', 'elementify-framework' ), $i ),
				'id'            => 'footer-' . $i,
				'description'   => esc_html__( 'Add widgets here.', 'elementify-framework' ),
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
