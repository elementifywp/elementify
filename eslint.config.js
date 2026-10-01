/**
 * ESLint flat config (ESLint 10).
 *
 * Extends the `@wordpress/scripts` defaults and adds the globals and test
 * overrides this theme needs.
 */
const wpScriptsConfig = require( '@wordpress/scripts/config/eslint.config.cjs' );

module.exports = [
	...wpScriptsConfig,
	{
		ignores: [ 'artifacts/**', 'assets/build/**' ],
	},
	{
		// Customizer preview runs inside WordPress, which provides these globals.
		files: [ 'assets/src/js/**/*.js' ],
		languageOptions: {
			globals: {
				wp: 'readonly',
				jQuery: 'readonly',
			},
		},
	},
	{
		// Node-side tooling configs.
		files: [ '*.config.js' ],
		languageOptions: {
			sourceType: 'commonjs',
			globals: {
				require: 'readonly',
				module: 'writable',
				process: 'readonly',
				__dirname: 'readonly',
			},
		},
	},
];
