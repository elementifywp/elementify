/**
 * Primary navigation.
 *
 * - Toggles the small-screen menu panel and keeps aria-expanded in sync.
 * - Adds a toggle button to every parent item so submenus can be opened by
 *   touch, mouse and keyboard on small screens.
 * - Escape closes the innermost open submenu, then the panel, returning focus
 *   to the control that opened it.
 * - Closes everything when focus or a click leaves the navigation.
 * - Keeps large-screen dropdowns inside the viewport by flipping any that
 *   would overflow it (hidden dropdowns still widen the page otherwise).
 *
 * Large screens keep the CSS :hover / :focus-within dropdowns, so the menu
 * still works for keyboard users if this script fails to load.
 */
( function () {
	const labels = Object.assign(
		{ expand: 'Show submenu for %s', collapse: 'Hide submenu for %s' },
		window.elementifyMenu || {}
	);
	const desktop = window.matchMedia( '(min-width: 64em)' );

	/**
	 * Wires up one primary navigation instance. A header builder can print
	 * the menu more than once (e.g. in its desktop and mobile rows), so all
	 * state and listeners are scoped to the given nav.
	 *
	 * @param {HTMLElement} nav      The nav.ele-primary-navigation element.
	 * @param {number}      navIndex Position of the nav on the page, for unique submenu ids.
	 */
	function initNavigation( nav, navIndex ) {
		const button = nav.querySelector( '.menu-toggle' );

		if ( ! button ) {
			return;
		}

		const menu = nav.querySelector( '.ele-main-menu' );

		// Hide the toggle when no menu (and no page fallback) is rendered.
		if ( ! menu ) {
			button.hidden = true;
			return;
		}

		menu.classList.add( 'nav-menu' );

		const hamburger = button.querySelector( '.ele-hamburger-menu' );

		/**
		 * Opens or closes the small-screen menu panel.
		 *
		 * @param {boolean} open Whether the panel should be open.
		 */
		function setPanel( open ) {
			nav.classList.toggle( 'toggled', open );
			button.setAttribute( 'aria-expanded', String( open ) );

			if ( hamburger ) {
				hamburger.classList.toggle( 'cross', open );
			}

			if ( ! open ) {
				closeSubmenus( menu );
			}
		}

		/**
		 * Opens or closes one submenu and updates its toggle button.
		 *
		 * @param {HTMLElement} item Parent `li` element.
		 * @param {boolean}     open Whether the submenu should be open.
		 */
		function setSubmenu( item, open ) {
			const toggle = item.querySelector( ':scope > .ele-submenu-toggle' );

			item.classList.toggle( 'is-open', open );

			if ( toggle ) {
				toggle.setAttribute( 'aria-expanded', String( open ) );
				toggle.querySelector( '.screen-reader-text' ).textContent = (
					open ? labels.collapse : labels.expand
				).replace( '%s', toggle.dataset.title );
			}

			if ( ! open ) {
				closeSubmenus( item );
			}
		}

		/**
		 * Closes every open submenu inside an element.
		 *
		 * @param {HTMLElement} root Element to search within.
		 */
		function closeSubmenus( root ) {
			root.querySelectorAll( '.is-open' ).forEach( ( item ) =>
				setSubmenu( item, false )
			);
		}

		// Add a toggle button after the link of every item that has a submenu.
		menu.querySelectorAll(
			'.menu-item-has-children, .page_item_has_children'
		).forEach( ( item, index ) => {
			const link = item.querySelector( ':scope > a' );
			const submenu = item.querySelector( ':scope > ul' );

			if ( ! link || ! submenu ) {
				return;
			}

			submenu.id =
				submenu.id || `ele-submenu-${ navIndex + 1 }-${ index + 1 }`;

			const toggle = document.createElement( 'button' );
			toggle.type = 'button';
			toggle.className = 'ele-submenu-toggle';
			toggle.dataset.title = link.textContent.trim();
			toggle.setAttribute( 'aria-controls', submenu.id );
			toggle.setAttribute( 'aria-expanded', 'false' );
			toggle.innerHTML =
				'<span class="screen-reader-text"></span><span class="ele-submenu-toggle-icon" aria-hidden="true"></span>';
			toggle.querySelector( '.screen-reader-text' ).textContent =
				labels.expand.replace( '%s', toggle.dataset.title );

			toggle.addEventListener( 'click', () =>
				setSubmenu( item, ! item.classList.contains( 'is-open' ) )
			);

			link.after( toggle );
		} );

		button.addEventListener( 'click', () =>
			setPanel( ! nav.classList.contains( 'toggled' ) )
		);

		nav.addEventListener( 'keydown', ( event ) => {
			if ( event.key !== 'Escape' ) {
				return;
			}

			// Close the submenu that contains focus, if any, before the panel.
			const openItem = event.target.closest( '.is-open' );

			if ( openItem && nav.contains( openItem ) ) {
				setSubmenu( openItem, false );
				openItem
					.querySelector( ':scope > .ele-submenu-toggle' )
					.focus();
			} else if ( nav.classList.contains( 'toggled' ) ) {
				setPanel( false );
				button.focus();
			}
		} );

		// Close everything when keyboard focus moves outside the navigation.
		nav.addEventListener( 'focusout', ( event ) => {
			if (
				event.relatedTarget &&
				! nav.contains( event.relatedTarget )
			) {
				setPanel( false );
			}
		} );

		document.addEventListener( 'click', ( event ) => {
			if ( ! nav.contains( event.target ) ) {
				setPanel( false );
			}
		} );

		/**
		 * Flips large-screen dropdowns that would run past the viewport edge.
		 * Hidden dropdowns keep their layout box, so they can be measured
		 * before they open. Parents come first in document order, so each
		 * nested list is measured after its parent has been placed.
		 */
		function fitSubmenus() {
			const submenus = menu.querySelectorAll( 'ul' );

			submenus.forEach( ( submenu ) =>
				submenu.classList.remove(
					'ele-submenu-align-right',
					'ele-submenu-align-left'
				)
			);

			if ( ! desktop.matches ) {
				return;
			}

			const viewport = document.documentElement.clientWidth;

			submenus.forEach( ( submenu ) => {
				const rect = submenu.getBoundingClientRect();

				if ( rect.right > viewport ) {
					submenu.classList.add( 'ele-submenu-align-right' );
				} else if ( rect.left < 0 ) {
					submenu.classList.add( 'ele-submenu-align-left' );
				}
			} );
		}

		let fitFrame = 0;

		window.addEventListener( 'resize', () => {
			window.cancelAnimationFrame( fitFrame );
			fitFrame = window.requestAnimationFrame( fitSubmenus );
		} );

		fitSubmenus();

		// Reset small-screen state when the viewport grows to the desktop layout.
		desktop.addEventListener( 'change', ( event ) => {
			if ( event.matches ) {
				setPanel( false );
			}

			fitSubmenus();
		} );
	}

	document
		.querySelectorAll( '.ele-primary-navigation' )
		.forEach( ( nav, navIndex ) => initNavigation( nav, navIndex ) );
} )();
