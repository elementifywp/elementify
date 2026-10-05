/**
 * PostCSS config.
 *
 * Mirrors the `@wordpress/scripts` default: autoprefixer (via .browserslistrc)
 * everywhere, plus cssnano for production builds. A project-level config
 * disables the built-in defaults, so both have to be listed here.
 */
const isProduction = process.env.NODE_ENV === 'production';

module.exports = {
	plugins: [
		...require( '@wordpress/postcss-plugins-preset' ),
		...( isProduction
			? [
					require( 'cssnano' )( {
						preset: [
							'default',
							{ discardComments: { removeAll: true } },
						],
					} ),
				]
			: [] ),
	],
};
