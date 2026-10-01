/**
 * Playwright config.
 *
 * Extends the `@wordpress/scripts` defaults (global login via REST, artifacts
 * folder, wp-env web server) and points them at this theme's specs. Tests run
 * against the wp-env *tests* site (http://localhost:8889), which has its own
 * database, so they never touch content on the development site.
 */
const { defineConfig } = require( '@playwright/test' );

const baseConfig = require( '@wordpress/scripts/config/playwright.config' );

module.exports = defineConfig( {
	...baseConfig,
	testDir: './tests/e2e/specs',
	projects: baseConfig.projects.map( ( project ) => ( {
		...project,
		use: {
			...project.use,
			// e.g. PLAYWRIGHT_CHANNEL=chrome to use the installed Google Chrome
			// where Playwright ships no Chromium build (macOS 13 and older).
			channel: process.env.PLAYWRIGHT_CHANNEL || project.use.channel,
		},
	} ) ),
	webServer: {
		...baseConfig.webServer,
		command: 'pnpm run env:start',
	},
} );
