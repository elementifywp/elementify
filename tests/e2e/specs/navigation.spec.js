/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

/**
 * Primary menu: Home, Services > ( Web Design, SEO Audits > Local SEO ), Contact.
 */
test.describe( 'Primary navigation', () => {
	let menuId;

	test.beforeAll( async ( { requestUtils } ) => {
		const menu = await requestUtils.rest( {
			method: 'POST',
			path: '/wp/v2/menus',
			data: { name: 'E2E Primary', locations: [ 'menu-1' ] },
		} );
		menuId = menu.id;

		const addItem = ( title, parent = 0 ) =>
			requestUtils.rest( {
				method: 'POST',
				path: '/wp/v2/menu-items',
				data: {
					title,
					url: `${ requestUtils.baseURL }/#${ title }`,
					menus: menuId,
					parent,
					status: 'publish',
				},
			} );

		await addItem( 'Home' );
		const services = await addItem( 'Services' );
		await addItem( 'Web Design', services.id );
		const seo = await addItem( 'SEO Audits', services.id );
		await addItem( 'Local SEO', seo.id );
		await addItem( 'Contact' );
	} );

	test.afterAll( async ( { requestUtils } ) => {
		await requestUtils.rest( {
			method: 'DELETE',
			path: `/wp/v2/menus/${ menuId }`,
			params: { force: true },
		} );
	} );

	test.describe( 'small screens', () => {
		test.use( { viewport: { width: 390, height: 844 } } );

		test( 'toggle opens a panel above the content and is keyboard operable', async ( {
			page,
		} ) => {
			await page.goto( '/' );

			const toggle = page.getByRole( 'button', {
				name: 'Menu',
				exact: true,
			} );
			await expect( toggle ).toHaveAttribute( 'aria-expanded', 'false' );
			await expect(
				page.getByRole( 'link', { name: 'Home', exact: true } )
			).toBeHidden();

			await toggle.focus();
			await page.keyboard.press( 'Enter' );
			await expect( toggle ).toHaveAttribute( 'aria-expanded', 'true' );

			// The first link after the toggle is the first menu item.
			await page.keyboard.press( 'Tab' );
			const home = page.getByRole( 'link', {
				name: 'Home',
				exact: true,
			} );
			await expect( home ).toBeFocused();

			// The open panel is not covered by page content.
			const box = await home.boundingBox();
			const topElement = await page.evaluate(
				( [ x, y ] ) => document.elementFromPoint( x, y )?.textContent,
				[ box.x + box.width / 2, box.y + box.height / 2 ]
			);
			expect( topElement ).toBe( 'Home' );

			// Submenu toggle expands Services inline.
			const services = page.getByRole( 'button', {
				name: 'Show submenu for Services',
			} );
			await services.click();
			await expect(
				page.getByRole( 'button', {
					name: 'Hide submenu for Services',
				} )
			).toHaveAttribute( 'aria-expanded', 'true' );
			await expect(
				page.getByRole( 'link', { name: 'Web Design' } )
			).toBeVisible();

			// Escape closes the submenu first, then the panel.
			await page.getByRole( 'link', { name: 'Web Design' } ).focus();
			await page.keyboard.press( 'Escape' );
			await expect(
				page.getByRole( 'button', {
					name: 'Show submenu for Services',
				} )
			).toBeFocused();
			await page.keyboard.press( 'Escape' );
			await expect( toggle ).toBeFocused();
			await expect( toggle ).toHaveAttribute( 'aria-expanded', 'false' );
		} );
	} );

	test.describe( 'large screens', () => {
		test.use( { viewport: { width: 1280, height: 800 } } );

		test( 'every level of the dropdown is reachable with Tab', async ( {
			page,
		} ) => {
			await page.goto( '/' );

			const services = page.getByRole( 'link', {
				name: 'Services',
				exact: true,
			} );
			await services.focus();

			for ( const name of [ 'Web Design', 'SEO Audits', 'Local SEO' ] ) {
				// No waiting between key presses: submenus must be focusable immediately.
				await page.keyboard.press( 'Tab' );
				await expect(
					page.getByRole( 'link', { name, exact: true } )
				).toBeFocused();
			}

			await page.keyboard.press( 'Tab' );
			await expect(
				page.getByRole( 'link', { name: 'Contact', exact: true } )
			).toBeFocused();
		} );
	} );
} );
