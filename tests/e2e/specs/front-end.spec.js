/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test.describe( 'Front end', () => {
	test( 'home page loads with theme assets and no console errors', async ( {
		page,
	} ) => {
		const errors = [];
		page.on( 'console', ( message ) => {
			if ( message.type() === 'error' ) {
				errors.push( message.text() );
			}
		} );

		const response = await page.goto( '/' );

		expect( response.ok() ).toBe( true );
		await expect( page.locator( '#elementify-main-css' ) ).toHaveCount( 1 );
		await expect( page.locator( '#elementify-main-js' ) ).toHaveCount( 1 );
		expect( errors ).toEqual( [] );
	} );
} );
