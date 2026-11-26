<?php
/**
 * Theme Customizer.
 *
 * @package Elementify
 */

namespace Elementify\Inc;

use Elementify\Inc\Traits\Singleton;

/**
 * Adds postMessage and selective refresh support for the core Customizer settings.
 */
class Customizer {

	use Singleton;

	/**
	 * Register the Customizer hooks when the singleton is first created.
	 *
	 * @return void
	 */
	public function __construct() {

		$this->setup_hooks();
	}

	/**
	 * Hook Customizer settings registration into `customize_register`.
	 *
	 * @return void
	 */
	private function setup_hooks() {

		/**
		 * Actions
		 */
		add_action( 'customize_register', [ $this, 'customizer' ] );
		// After Elementify Framework registers its panels (priority 20).
		add_action( 'customize_register', [ $this, 'move_schema_section' ], 25 );
	}

	/**
	 * Move the Schema Markup switch into the Elementify Framework
	 * "Performance" section (Customizer → Global → Performance).
	 *
	 * The section and the switch control are the plugin's, so the move only
	 * happens when both exist; without the plugin the theme's own top-level
	 * "Schema Markup" section and checkbox stay. The setting itself remains
	 * the theme's `elementify_schema_org_markup` either way.
	 *
	 * @param \WP_Customize_Manager $wp_customize Theme Customizer object.
	 * @return void
	 */
	public function move_schema_section( $wp_customize ) {
		$section_id    = 'elementify_framework_performance';
		$toggle_class  = '\\ElementifyFramework\\Modules\\Customize\\Controls\\Toggle';
		$heading_class = '\\ElementifyFramework\\Modules\\Customize\\Controls\\Blank';

		if ( ! $wp_customize->get_section( $section_id ) || ! class_exists( $toggle_class ) || ! class_exists( $heading_class ) ) {
			return;
		}

		$wp_customize->remove_control( 'elementify_schema_org_markup' );
		$wp_customize->remove_section( 'elementify_schema' );

		// The plugin's controls all bind to a setting, headings included,
		// so the heading reads the schema setting it introduces.
		$wp_customize->add_control(
			new $heading_class(
				$wp_customize,
				'elementify_schema_org_markup_heading',
				[
					'section'     => $section_id,
					'settings'    => 'elementify_schema_org_markup',
					'priority'    => 20,
					'label'       => esc_html__( 'Schema Markup', 'elementify' ),
					'input_attrs' => [
						'class' => 'uppercase text-sm font-bold text-gray-700 tracking-wider',
					],
				]
			)
		);

		$wp_customize->add_control(
			new $toggle_class(
				$wp_customize,
				'elementify_schema_org_markup',
				[
					'section'     => $section_id,
					'priority'    => 25,
					'label'       => esc_html__( 'Enable schema.org markup', 'elementify' ),
					'description' => esc_html__( 'Describe the site to search engines with schema.org microdata: the header, navigation, sidebar and footer, each post\'s headline, image, author and dates, comments and breadcrumbs. Turn this off if an SEO plugin already prints a complete schema graph.', 'elementify' ),
					'default'     => true,
				]
			)
		);
	}

	/**
	 * Add postMessage support for site title and description for the Theme Customizer.
	 *
	 * Switches the blogname, blogdescription and header_textcolor settings to
	 * postMessage transport so they preview without a full refresh, and
	 * registers the matching selective refresh partials when the manager
	 * supports them.
	 *
	 * @param \WP_Customize_Manager $wp_customize Theme Customizer object.
	 * @return void
	 */
	public function customizer( $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

		$wp_customize->add_section(
			'elementify_schema',
			[
				'title'       => esc_html__( 'Schema Markup', 'elementify' ),
				'description' => esc_html__( 'Describe the site to search engines with schema.org microdata: the header, navigation, sidebar and footer, each post\'s headline, image, author and dates, comments and breadcrumbs. Turn this off if an SEO plugin already prints a complete schema graph.', 'elementify' ),
				'priority'    => 160,
			]
		);

		$wp_customize->add_setting(
			'elementify_schema_org_markup',
			[
				// Setting defaults are typed as strings; '1' reads as on, matching
				// the `true` fallback in Utils::has_schema_org_markup().
				'default'           => '1',
				'sanitize_callback' => 'wp_validate_boolean',
			]
		);

		$wp_customize->add_control(
			'elementify_schema_org_markup',
			[
				'type'    => 'checkbox',
				'section' => 'elementify_schema',
				'label'   => esc_html__( 'Enable schema.org markup', 'elementify' ),
			]
		);

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'blogname',
				[
					'selector'        => '.ele-site-title a',
					'render_callback' => [ $this, 'customize_partial_blogname' ],
				]
			);
			$wp_customize->selective_refresh->add_partial(
				'blogdescription',
				[
					'selector'        => '.ele-site-description',
					'render_callback' => [ $this, 'customize_partial_blogdescription' ],
				]
			);
		}
	}

	/**
	 * Render the site title for the selective refresh partial.
	 *
	 * @return void
	 */
	public function customize_partial_blogname() {
		bloginfo( 'name' );
	}

	/**
	 * Render the site tagline for the selective refresh partial.
	 *
	 * @return void
	 */
	public function customize_partial_blogdescription() {
		bloginfo( 'description' );
	}
}
