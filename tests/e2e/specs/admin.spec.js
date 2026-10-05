/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test.describe( 'Admin', () => {
	test( 'logged-in admin reaches the dashboard', async ( {
		admin,
		page,
	} ) => {
		await admin.visitAdminPage( '/' );

		await expect(
			page.getByRole( 'heading', { name: 'Dashboard', level: 1 } )
		).toBeVisible();
	} );

	test( 'Elementify is the active theme', async ( { requestUtils } ) => {
		const themes = await requestUtils.rest( {
			path: '/wp/v2/themes',
			params: { status: 'active' },
		} );

		expect( themes[ 0 ].stylesheet ).toBe( 'elementify' );
	} );
} );
