/**
 * External dependencies
 */
const path = require( 'path' );
const CopyPlugin = require( 'copy-webpack-plugin' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );

/**
 * WordPress dependencies
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const SRC_DIR = path.resolve( __dirname, 'assets/src' );
const BUILD_DIR = path.resolve( __dirname, 'assets/build' );

/**
 * Single build for the theme.
 *
 * Reuses the `@wordpress/scripts` defaults (Babel, PostCSS, RTL generation,
 * minification, `*.asset.php` dependency manifests) and only changes the
 * entry points and output layout:
 *
 * - assets/src/js/*.js   -> assets/build/js/*.js
 * - assets/src/css/*.css -> assets/build/css/*.css (+ *-rtl.css); editor.css is
 *   loaded only in the block editor
 * - assets/src/images/   -> assets/build/images/
 */
module.exports = {
	...defaultConfig,
	entry: {
		'js/main': path.join( SRC_DIR, 'js/main.js' ),
		'js/customizer': path.join( SRC_DIR, 'js/customizer.js' ),
		'css/main': path.join( SRC_DIR, 'css/main.css' ),
		'css/editor': path.join( SRC_DIR, 'css/editor.css' ),
	},
	output: {
		...defaultConfig.output,
		path: BUILD_DIR,
		clean: true,
	},
	plugins: [
		...defaultConfig.plugins,
		// Drops the empty css/main.js that webpack emits for the CSS-only entry.
		new RemoveEmptyScriptsPlugin( {
			stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
		} ),
		new CopyPlugin( {
			patterns: [
				{
					from: path.join( SRC_DIR, 'images' ),
					to: path.join( BUILD_DIR, 'images' ),
					noErrorOnMissing: true,
				},
			],
		} ),
	],
};
