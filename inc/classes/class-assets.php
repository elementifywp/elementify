<?php
/**
 * Enqueue theme assets
 *
 * @package Elementify
 */

namespace Elementify\Inc;

use Elementify\Inc\Traits\Singleton;

/**
 * Class Assets.
 */
class Assets {

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
		 * Actions.
		 */
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_assets' ] );
		add_action( 'customize_preview_init', [ $this, 'customize_preview_assets' ] );
	}

	/**
	 * Enqueue css & js in frontend
	 */
	public function frontend_assets() {

		/**
		 * Functions hooked into elementify/frontend/before_register_scripts action
		 * 
		 * Fires before Elementify frontend css & js are registered.
		 *
		 */
		do_action('elementify/frontend/before_register_scripts');

		// Enqueue Styles.
		wp_enqueue_style( 'elementify-style', get_stylesheet_uri(), [], ELEMENTIFY_VERSION );
		//wp_style_add_data( 'elementify-style', 'rtl', 'replace' );

		wp_enqueue_style(
			'elementify-main',
			trailingslashit(ELEMENTIFY_DIR_URI) . 'assets/build/css/main.css',
			[],
			filemtime(ELEMENTIFY_DIR_PATH . '/assets/build/css/main.css'),
			'all'
		);
		wp_style_add_data( 'elementify-main', 'rtl', 'replace' );

		// Enqueue Scripts.
		wp_enqueue_script(
			'elementify-main',
			trailingslashit(ELEMENTIFY_DIR_URI) . 'assets/build/js/main.js',
			[],
			filemtime(ELEMENTIFY_DIR_PATH . '/assets/build/js/main.js'),
			true
		);

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		/**
		 * Functions hooked into elementify/frontend/before_register_scripts action
		 * 
		 * Fires after Elementify frontend css & js are registered.
		 *
		 */
		do_action('elementify/frontend/after_register_scripts');
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 */
	public function customize_preview_assets() {

		/**
		 * Functions hooked into elementify/frontend/before_register_customize_preview_scripts action
		 * 
		 * Fires before Elementify frontend css & js are registered.
		 *
		 */
		do_action('elementify/frontend/before_register_customize_preview_scripts');

		// Enqueue scripts.
		wp_enqueue_script(
			'elementify-customizer-preview',
			trailingslashit(ELEMENTIFY_DIR_URI) . 'assets/build/js/customizer.js',
			['customize-preview'],
			filemtime(ELEMENTIFY_DIR_PATH . '/assets/build/js/customizer.js'),
			true
		);
		
		/**
		 * Functions hooked into elementify/frontend/after_register_customize_preview_scripts action
		 * 
		 * Fires after Elementify frontend css & js are registered.
		 *
		 */
		do_action('elementify/frontend/after_register_customize_preview_scripts');
	}
}