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
		 */
		do_action( 'elementify/frontend/before_register_scripts' );

		// Enqueue Styles.
		wp_enqueue_style( 'elementify-style', get_stylesheet_uri(), [], ELEMENTIFY_VERSION );
		// wp_style_add_data( 'elementify-style', 'rtl', 'replace' );

		$style_asset = $this->get_asset_meta( 'css/main' );
		wp_enqueue_style(
			'elementify-main',
			ELEMENTIFY_DIR_URI . '/assets/build/css/main.css',
			[],
			$style_asset['version'],
			'all'
		);
		wp_style_add_data( 'elementify-main', 'rtl', 'replace' );

		// Enqueue Scripts.
		$script_asset = $this->get_asset_meta( 'js/main' );
		wp_enqueue_script(
			'elementify-main',
			ELEMENTIFY_DIR_URI . '/assets/build/js/main.js',
			$script_asset['dependencies'],
			$script_asset['version'],
			[
				'in_footer' => true,
				'strategy'  => 'defer',
			]
		);

		// Translatable labels for the submenu toggle buttons main.js adds.
		wp_add_inline_script(
			'elementify-main',
			'window.elementifyMenu = ' . wp_json_encode(
				[
					/* translators: %s: Parent menu item title. */
					'expand'   => __( 'Show submenu for %s', 'elementify' ),
					/* translators: %s: Parent menu item title. */
					'collapse' => __( 'Hide submenu for %s', 'elementify' ),
				]
			) . ';',
			'before'
		);

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		/**
		 * Functions hooked into elementify/frontend/before_register_scripts action
		 *
		 * Fires after Elementify frontend css & js are registered.
		 */
		do_action( 'elementify/frontend/after_register_scripts' );
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 */
	public function customize_preview_assets() {

		/**
		 * Functions hooked into elementify/frontend/before_register_customize_preview_scripts action
		 *
		 * Fires before Elementify frontend css & js are registered.
		 */
		do_action( 'elementify/frontend/before_register_customize_preview_scripts' );

		// Enqueue scripts.
		$customizer_asset = $this->get_asset_meta( 'js/customizer' );
		wp_enqueue_script(
			'elementify-customizer-preview',
			ELEMENTIFY_DIR_URI . '/assets/build/js/customizer.js',
			array_merge( [ 'customize-preview', 'jquery' ], $customizer_asset['dependencies'] ),
			$customizer_asset['version'],
			true
		);

		/**
		 * Functions hooked into elementify/frontend/after_register_customize_preview_scripts action
		 *
		 * Fires after Elementify frontend css & js are registered.
		 */
		do_action( 'elementify/frontend/after_register_customize_preview_scripts' );
	}

	/**
	 * Reads the dependency manifest that @wordpress/scripts writes next to each build file.
	 *
	 * Falls back to the theme version when the build has not been run yet, so a
	 * missing build never triggers PHP warnings on the front end.
	 *
	 * @param string $entry Entry path relative to assets/build, without extension (e.g. 'js/main').
	 *
	 * @return array{dependencies: string[], version: string} Script dependencies and cache-busting version.
	 */
	private function get_asset_meta( $entry ) {
		$asset_file = ELEMENTIFY_DIR_PATH . '/assets/build/' . $entry . '.asset.php';
		$asset      = is_readable( $asset_file ) ? require $asset_file : [];

		return [
			'dependencies' => isset( $asset['dependencies'] ) ? $asset['dependencies'] : [],
			'version'      => isset( $asset['version'] ) ? $asset['version'] : ELEMENTIFY_VERSION,
		];
	}
}
