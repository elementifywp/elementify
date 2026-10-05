/**
 * External dependencies
 */
const path = require( 'path' );

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test.describe( 'Featured image', () => {
	let media;
	const created = [];

	test.beforeAll( async ( { requestUtils } ) => {
		media = await requestUtils.uploadMedia(
			path.join( __dirname, '../../../assets/src/images/404.webp' )
		);
	} );

	test.afterAll( async ( { requestUtils } ) => {
		for ( const id of created ) {
			await requestUtils.rest( {
				method: 'DELETE',
				path: `/wp/v2/pages/${ id }`,
				params: { force: true },
			} );
		}
		await requestUtils.deleteMedia( media.id );
	} );

	test( 'is shown on a page that has one', async ( {
		page,
		requestUtils,
	} ) => {
		const withImage = await requestUtils.createPage( {
			title: 'Page with featured image',
			status: 'publish',
			featured_media: media.id,
		} );
		created.push( withImage.id );

		await page.goto( withImage.link );

		const image = page.locator( '.ele-featured-image img' );
		await expect( image ).toBeVisible();
		await expect( image ).toHaveAttribute( 'src', /404/ );
		expect(
			await image.evaluate( ( img ) => img.naturalWidth )
		).toBeGreaterThan( 0 );
	} );

	test( 'leaves no empty placeholder on a page without one', async ( {
		page,
		requestUtils,
	} ) => {
		const withoutImage = await requestUtils.createPage( {
			title: 'Page without featured image',
			status: 'publish',
		} );
		created.push( withoutImage.id );

		await page.goto( withoutImage.link );

		await expect( page.locator( '.ele-featured-image-wrap' ) ).toHaveCount(
			0
		);
	} );
} );
