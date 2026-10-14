<?php
/**
 * Theme helper functions
 *
 * @package Elementify
 */

use Elementify\Inc\Utils;
use Elementify\Inc\Svg_Icons;

/*
|--------------------------------------------------------------------------
| Menu Option
|--------------------------------------------------------------------------
|
| Returns an array of navigation menus.
*/

if ( ! function_exists( 'elementify_get_nav_menus' ) ) {
	/**
	 * Returns an array of navigation menus.
	 *
	 * @access public
	 * @param string $value_field The value to be stored in options. Accepted values: id|slug.
	 * @return array
	 */
	function elementify_get_nav_menus( $value_field = 'id' ) {
		$choices   = [];
		$nav_menus = wp_get_nav_menus();

		foreach ( $nav_menus as $term ) {
			$choices[ 'slug' === $value_field ? $term->slug : $term->term_id ] = $term->name;
		}

		return $choices;
	}
}

/*
 * Site title.
 */
if ( ! function_exists( 'elementify_site_title' ) ) {
	/**
	 * Displays the site title dynamically.
	 *
	 * @param array   $args   Arguments for displaying the site title.
	 * @param boolean $output Echo or return the HTML.
	 * @return string $html  Compiled HTML based on our arguments.
	 * @since 1.0.0
	 */
	function elementify_site_title( $args = [], $output = true ) {
		// Allow dynamic text; otherwise, fall back to bloginfo.
		$default_text = get_bloginfo( 'name' );

		$defaults = [
			'title'       => '<a href="%1$s">%2$s</a>',
			'title_class' => 'ele-site-title',
			'wrapper'     => '<div class="%1$s" itemprop="name">%2$s</div>',
			'text'        => $default_text, // NEW: Allows overriding the text.
			'condition'   => ( is_front_page() || is_home() ) && ! is_page(),
		];

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filters the arguments for `elementify_site_title()`.
		 */
		$args = apply_filters( 'elementify_site_title_args', $args, $defaults );

		$home_url   = esc_url( get_home_url( null, '/' ) );
		$title_text = esc_html( $args['text'] );

		$contents  = sprintf( $args['title'], $home_url, $title_text );
		$classname = esc_attr( $args['title_class'] );
		$wrap      = $args['wrapper']; // Wrapper is usually safe from sprintf if controlled by the dev.

		$html = sprintf( $wrap, $classname, $contents );

		/**
		 * Filters the final HTML for `elementify_site_title()`.
		 */
		$html = apply_filters( 'elementify_site_title_html', $html, $args, $classname, $contents );

		if ( ! $output ) {
			return $html;
		}

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/*
 * Site description.
 */
if ( ! function_exists( 'elementify_site_description' ) ) {
	/**
	 * Displays the site description dynamically.
	 *
	 * @param array   $args   Arguments for displaying the site description.
	 * @param boolean $output Echo or return the HTML.
	 * @return string $html  The HTML to display.
	 * @since 1.0.0
	 */
	function elementify_site_description( $args = [], $output = true ) {
		$default_text = get_bloginfo( 'description' );

		$defaults = [
			'class'   => 'ele-site-description',
			'wrapper' => '<p class="%1$s" itemprop="description">%2$s</p>',
			'text'    => $default_text, // NEW: Allows overriding the text.
		];

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filters the arguments for `elementify_site_description()`.
		 */
		$args = apply_filters( 'elementify_site_description_args', $args, $defaults );

		$description_text = esc_html( $args['text'] );
		$classname        = esc_attr( $args['class'] );
		$wrap             = $args['wrapper'];

		$html = sprintf( $wrap, $classname, $description_text );

		/**
		 * Filters the final HTML for `elementify_site_description()`.
		 */
		$html = apply_filters( 'elementify_site_description_html', $html, $args, $classname, $description_text );

		if ( ! $output ) {
			return $html;
		}

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/*
 * Site logo.
 */
if ( ! function_exists( 'elementify_site_logo' ) ) {
	/**
	 * Displays the site logo dynamically.
	 *
	 * @param array   $args   Arguments for displaying the site logo.
	 * @param boolean $output Echo or return the HTML.
	 * @return string $html  The HTML to display.
	 * @since 1.0.0
	 */
	function elementify_site_logo( $args = [], $output = true ) {
		$defaults = [
			'class'     => 'ele-site-logo',
			'wrapper'   => '<div class="%1$s">%2$s</div>',
			'logo_id'   => get_theme_mod( 'custom_logo' ), // NEW: Allows passing a specific logo ID.
			'logo_size' => 'full', // NEW: Allows dynamic image sizes (e.g., 'thumbnail', 'medium').
			'fallback'  => '', // NEW: HTML to show if no logo is set.
		];

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filters the arguments for `elementify_site_logo()`.
		 */
		$args = apply_filters( 'elementify_site_logo_args', $args, $defaults );

		// If no logo ID is provided or set, return the fallback or empty.
		if ( empty( $args['logo_id'] ) ) {
			$html = $args['fallback'];
		} else {
			// Get logo HTML, optionally with a specific size.
			$logo_html = wp_get_attachment_image( $args['logo_id'], $args['logo_size'], false, [ 'class' => 'custom-logo' ] );
			$logo_html = wp_kses_post( $logo_html );

			$classname = esc_attr( $args['class'] );
			$wrap      = $args['wrapper'];

			$html = sprintf( $wrap, $classname, $logo_html );
		}

		/**
		 * Filters the final HTML for `elementify_site_logo()`.
		 */
		$html = apply_filters( 'elementify_site_logo_html', $html, $args, $classname );

		if ( ! $output ) {
			return $html;
		}

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/*
 * Site identity.
 */
if ( ! function_exists( 'elementify_site_identify' ) ) {
	/**
	 * Displays the site  title and description.
	 *
	 * @return  void HTML display
	 * @since   1.0.0
	 */
	function elementify_site_identify() {
		?>
		<div class="ele-site-identity">
			<?php
			elementify_site_title(); // Site title.
			elementify_site_description(); // Site description.
			?>
		</div>
		<?php
	}
}

/*
 * Primary navigation.
 */
if ( ! function_exists( 'elementify_primary_navigation' ) ) {
	/**
	 * Displays the primary navigation: the small-screen toggle and the menu-1 location.
	 *
	 * The markup (nav.ele-primary-navigation, its .menu-toggle and the menu
	 * `ul` it controls) is what assets/src/js/main.js binds to, so callers can
	 * change the menu's look and behaviour through $args but not those hooks.
	 *
	 * Safe to call more than once per page (e.g. a header builder printing the
	 * menu in both its desktop and mobile rows): the first instance keeps the
	 * ele-header-menu-1 / primary-menu ids, later ones get a numeric suffix so
	 * ids and aria-controls stay unique.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args {
	 *     Optional. Overrides for the rendered menu.
	 *
	 *     @type string          $layout         Value of the nav's data-layout attribute. Default '1'.
	 *     @type string          $reveal         Dropdown reveal effect, added as `ele-dropdown-reveal-{$reveal}`. Default 'fade'.
	 *     @type int             $depth          Menu depth passed to wp_nav_menu(); 0 shows all levels. Default 0.
	 *     @type string          $link_after     Markup appended inside every menu link (e.g. a submenu icon). Default ''.
	 *     @type string          $menu_class     Classes for the menu `ul`, replacing the theme's default set. Default ''.
	 *     @type callable|string $fallback_cb    Callback used when no menu is assigned. Default 'elementify_menu_fallback'.
	 *     @type array           $nav_attributes Extra attributes for the `nav` element, as name => value. Default [].
	 *     @type \Walker|null    $walker         Menu walker. A \Walker_Page instance is only handed to the
	 *                                           fallback (as `page_walker`); any other walker is passed to
	 *                                           wp_nav_menu(). Default null.
	 * }
	 * @return void
	 */
	function elementify_primary_navigation( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'layout'         => '1',
				'reveal'         => 'fade',
				'depth'          => 0,
				'link_after'     => '',
				'menu_class'     => '',
				'fallback_cb'    => 'elementify_menu_fallback',
				'nav_attributes' => [],
				'walker'         => null,
			]
		);

		static $instance = 0;
		++$instance;

		$suffix  = 1 === $instance ? '' : '-' . $instance;
		$nav_id  = 'ele-header-menu-1' . $suffix;
		$menu_id = 'primary-menu' . $suffix;

		$main_navigation = [ 'main-navigation', 'ele-primary-navigation', 'ele-left-0', 'ele-z-20' ];
		$menu_class      = [ 'ele-main-menu', 'ele-list-style-none', 'ele-p-0', 'ele-m-0', 'ele-d-flex', 'ele-flex-column', 'ele-flex-lg-row' ];

		$menu_class[] = 'have-caret';

		$main_navigation[] = 'main-navigation-sm';
		$main_navigation[] = 'ele-position-sm-relative';
		$main_navigation[] = 'ele-top-sm-auto';
		$main_navigation[] = 'ele-left-sm-auto';
		$main_navigation[] = 'ele-h-sm-auto';

		$menu_class[] = 'ele-flex-sm-wrap';
		$menu_class[] = 'ele-flex-sm-row';
		$menu_class[] = 'ele-align-items-md-center';

		$menu_class = '' !== $args['menu_class'] ? $args['menu_class'] : implode( ' ', $menu_class );

		$nav_attributes = '';
		foreach ( (array) $args['nav_attributes'] as $name => $value ) {
			$nav_attributes .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( $value ) );
		}
		?>

		<nav id="<?php echo esc_attr( $nav_id ); ?>" class="<?php echo esc_attr( implode( ' ', $main_navigation ) ); ?>" data-layout="<?php echo esc_attr( $args['layout'] ); ?>" aria-label="<?php esc_attr_e( 'Primary menu', 'elementify' ); ?>"<?php echo $nav_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?>>
			<button type="button" class="menu-toggle" aria-controls="<?php echo esc_attr( $menu_id ); ?>" aria-expanded="false">
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'elementify' ); ?></span>
				<span class="ele-trigger-menu ele-d-block ele-z-30" aria-hidden="true">
					<span class="ele-hamburger-menu"><span></span><span></span><span></span><span></span></span>
				</span>
			</button>
			<?php
			$menu_args = [
				'theme_location' => 'menu-1',
				'menu_id'        => $menu_id,
				'menu_class'     => $menu_class . ' ele-dropdown-reveal-' . sanitize_html_class( $args['reveal'] ),
				'container'      => '',
				'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'link_after'     => $args['link_after'],
				'depth'          => absint( $args['depth'] ),
				'fallback_cb'    => $args['fallback_cb'],
			];
			// A page walker only fits the page-list fallback; nav menus keep core's walker.
			if ( $args['walker'] instanceof \Walker_Page ) {
				$menu_args['page_walker'] = $args['walker'];
			} elseif ( ! empty( $args['walker'] ) ) {
				$menu_args['walker'] = $args['walker'];
			}
			wp_nav_menu( $menu_args );
			?>
		</nav>
		<?php
	}
}

/*
 * Primary menu fallback.
 */
if ( ! function_exists( 'elementify_menu_fallback' ) ) {
	/**
	 * Output the page list shown when no menu is assigned to the primary location.
	 *
	 * Called by wp_nav_menu() as its fallback_cb. Renders pages through
	 * wp_page_menu() as a bare `ul` (no container) using $args['page_walker']
	 * when it is a \Walker_Page, otherwise \Elementify\Inc\Walker_Page, which
	 * puts the dropdown-menu-toggle caret inside each parent link (Astra's
	 * pattern). The list keeps the id and classes wp_nav_menu() would have
	 * used, so the navigation script and styles apply unchanged.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Optional. wp_nav_menu() arguments; `menu_id`, `menu_class`, `depth`,
	 *                    `link_before`, `link_after` and `page_walker` are used. Default [].
	 * @return void
	 */
	function elementify_menu_fallback( $args = [] ) {
		$walker = ! empty( $args['page_walker'] ) && $args['page_walker'] instanceof \Walker_Page
			? $args['page_walker']
			: new \Elementify\Inc\Walker_Page();

		wp_page_menu(
			[
				'menu_id'     => ! empty( $args['menu_id'] ) ? $args['menu_id'] : 'primary-menu',
				'menu_class'  => ! empty( $args['menu_class'] ) ? $args['menu_class'] : 'ele-main-menu ele-d-flex ele-flex-wrap ele-list-style-none',
				'container'   => 'ul',
				'before'      => '',
				'after'       => '',
				'depth'       => ! empty( $args['depth'] ) ? (int) $args['depth'] : 0,
				'link_before' => ! empty( $args['link_before'] ) ? $args['link_before'] : '',
				'link_after'  => ! empty( $args['link_after'] ) ? $args['link_after'] : '',
				'walker'      => $walker,
			]
		);
	}
}

/*
 * Collapsible menu fallback.
 */
if ( ! function_exists( 'elementify_collapsible_menu_fallback' ) ) {
	/**
	 * Menu fallback for primary menu.
	 *
	 * Contains wp_list_pages to display pages created,
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	function elementify_collapsible_menu_fallback() {
		$output  = '';
		$output .= '<div class="ele-collapsible-menu-container">';
		$output .= '<ul id="ele-collapsible-menu-list" class="ele-ele-collapsible-menu">';

		$output .= wp_list_pages(
			[
				'echo'     => false,
				'title_li' => false,
			]
		);

		$output .= '</ul>';
		$output .= '</div>';

        // @codingStandardsIgnoreStart
        echo $output;
        // @codingStandardsIgnoreEnd
	}
}

/*
 * Get SVG code.
 */
/**
 * Gets the SVG code for a given icon.
 *
 * @param string $group Icon set to look in, e.g. 'ui'.
 * @param string $icon  Icon name within the group, e.g. 'chevron-left'.
 * @param int    $size  Width and height of the icon in pixels.
 * @return string The inline SVG markup, or an empty string when unknown.
 */
function elementify_get_the_svg( $group, $icon, $size ) {
	return Svg_Icons::get_svg( $group, $icon, $size );
}

/**
 * Echo the SVG code for a given icon.
 *
 * @param string $group Icon set to look in, e.g. 'ui'.
 * @param string $icon  Icon name within the group, e.g. 'chevron-left'.
 * @param int    $size  Width and height of the icon in pixels.
 * @return void
 */
function elementify_the_svg( $group, $icon, $size ) {
	echo Svg_Icons::get_svg( $group, $icon, $size ); //phpcs:ignore WordPress.Security.EscapeOutput
}

/*
 * Pagination.
 */
if ( ! function_exists( 'elementify_pagination' ) ) {

	/**
	 * Elemetify Pagination.
	 *
	 * Prints numbered pagination for the main query. Callers paginating a
	 * different loop (e.g. WooCommerce product loops) pass `total`, `current`,
	 * `base` and `format`, which are handed to paginate_links().
	 *
	 * @param array $args {
	 *     Optional. paginate_links() arguments, plus the keys below.
	 *
	 *     @type string $aria_label         Accessible name of the nav landmark.
	 *     @type string $screen_reader_text Hidden heading inside the nav.
	 * }
	 * @return void
	 */
	function elementify_pagination( $args = [] ) {

		$args = wp_parse_args(
			$args,
			[
				'aria_label'         => __( 'Posts', 'elementify' ),
				'screen_reader_text' => __( 'Posts navigation', 'elementify' ),
				'mid_size'           => 1,
				'prev_text'          => elementify_get_the_svg( 'ui', 'chevron-left', 16 ) . '<span class="ele-pagination-label">' . esc_html__( 'Previous', 'elementify' ) . '</span>',
				'next_text'          => '<span class="ele-pagination-label">' . esc_html__( 'Next', 'elementify' ) . '</span>' . elementify_get_the_svg( 'ui', 'chevron-right', 16 ),
				/* translators: Hidden text before a page number in the posts pagination, e.g. "Page 2". */
				'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Page', 'elementify' ) . ' </span>',
			]
		);

		$aria_label         = $args['aria_label'];
		$screen_reader_text = $args['screen_reader_text'];
		unset( $args['aria_label'], $args['screen_reader_text'] );

		// Always return a string so the result can be passed through wp_kses().
		$args['type'] = 'plain';

		$links = paginate_links( $args );

		// paginate_links() returns null when there is only one page.
		if ( ! empty( $links ) ) {
			$allowed_tags = [
				'span'     => [
					'class'        => [],
					'aria-current' => [],
				],
				'a'        => [
					'class' => [],
					'href'  => [],
				],
				'svg'      => [
					'class'           => [],
					'width'           => [],
					'height'          => [],
					'aria-hidden'     => [],
					'role'            => [],
					'focusable'       => [],
					'xmlns'           => [],
					'viewbox'         => [],
					'fill'            => [],
					'stroke'          => [],
					'stroke-width'    => [],
					'stroke-linecap'  => [],
					'stroke-linejoin' => [],
				],
				'polyline' => [
					'points' => [],
				],
			];

			printf(
				'<div class="ele-pagination-wrap"><nav class="navigation pagination" aria-label="%1$s"><h2 class="screen-reader-text">%2$s</h2><div class="nav-links ele-d-flex ele-flex-wrap ele-align-items-center ele-w-100 ele-justify-content-center ele-justify-content-md-center ele-justify-content-lg-center" data-pagination-type="numbered" data-divider="none">%3$s</div></nav></div>',
				esc_attr( $aria_label ),
				esc_html( $screen_reader_text ),
				wp_kses( $links, $allowed_tags )
			);
		}
	}
}

/*
|--------------------------------------------------------------------------
| Trail Breadcrumb
|--------------------------------------------------------------------------
*/
if ( ! function_exists( 'elementify_breadcrumb' ) ) {
	/**
	 * Display trail breadcrumb.
	 *
	 * @return string|void The breadcrumb trail markup, or void when echoing.
	 */
	function elementify_breadcrumb() {
		$defaults = [
			'show_browse' => false,
			'echo'        => true,
		];
		$args     = apply_filters( 'breadcrumb_trail_args', $defaults ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Library filter.

		$breadcrumb = apply_filters( 'breadcrumb_trail_object', null, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Library filter.

		if ( ! is_object( $breadcrumb ) ) {

			$breadcrumb = new Elementify\Inc\Breadcrumb_Trail( $args );
		}

		return $breadcrumb->trail();
	}
}

/*
|--------------------------------------------------------------------------
| Sidebar selection
|--------------------------------------------------------------------------
*/

if ( ! function_exists( 'elementify_is_woocommerce_sidebar_context' ) ) {
	/**
	 * Whether the current request is a WooCommerce screen that can show the
	 * dedicated shop sidebar.
	 *
	 * @return bool
	 */
	function elementify_is_woocommerce_sidebar_context() {
		if ( ! function_exists( 'is_woocommerce' ) ) {
			return false;
		}

		return (
			is_woocommerce()
			|| ( function_exists( 'is_cart' ) && is_cart() )
			|| ( function_exists( 'is_checkout' ) && is_checkout() )
			|| ( function_exists( 'is_account_page' ) && is_account_page() )
		);
	}
}

if ( ! function_exists( 'elementify_get_sidebar_id' ) ) {
	/**
	 * Resolve the widget area the theme should render for the current view.
	 *
	 * Returns the dedicated WooCommerce sidebar on shop and store screens, and
	 * the theme's main sidebar everywhere else. Store screens never fall back
	 * to the blog sidebar: with no WooCommerce widgets they render full width.
	 *
	 * @return string The sidebar id.
	 */
	function elementify_get_sidebar_id() {
		$sidebar_id = 'sidebar-1';

		if ( elementify_is_woocommerce_sidebar_context() ) {
			$sidebar_id = 'sidebar-woocommerce';
		}

		return apply_filters( 'elementify_get_sidebar_id', $sidebar_id );
	}
}

/*
 * Add an SVG icon for the menu item if it has submenu items.
 */
if ( ! function_exists( 'elementify_submenu_icon' ) ) {
	/**
	 * Append the dropdown arrow inside the link of nav menu items with submenus.
	 *
	 * Hooked to `walker_nav_menu_start_el`. Inserts a `span.ele-submenu-icon`
	 * holding the chevron-down icon before the closing `</a>` of items carrying
	 * the menu-item-has-children class; other items are returned unchanged.
	 *
	 * @param string  $item_output The menu item's starting HTML output.
	 * @param WP_Post $item        Menu item data object.
	 * @return string The item output, with the arrow appended when the item has children.
	 */
	function elementify_submenu_icon( $item_output, $item ) {
		$has_children = in_array( 'menu-item-has-children', $item->classes, true );
		if ( $has_children ) {
			$item_output = str_replace(
				'</a>',
				'<span class="ele-submenu-icon">' . elementify_get_the_svg( 'ui', 'chevron-down', 15 ) . '</span></a>',
				$item_output
			);
		}
		return $item_output;
	}
}
add_filter( 'walker_nav_menu_start_el', 'elementify_submenu_icon', 10, 2 );

/*
 * Add classes in submenu.
 */
if ( ! function_exists( 'elementify_submenu_classes' ) ) {
	/**
	 * Filters the CSS classes of a submenu's sub-menu element.
	 *
	 * Adds `ele-transition-normal` to any `sub-menu` class, enabling the
	 * theme's submenu open/close transition.
	 *
	 * @param array $classes The submenu's CSS classes.
	 * @return array The classes, with the transition class added.
	 */
	function elementify_submenu_classes( $classes ) {
		foreach ( $classes as $key => $class ) {
			if ( 'sub-menu' === $class ) {
				$classes[ $key ] = 'sub-menu ele-transition-normal';
			}
		}
		return $classes;
	}
}

/**
 * Detects the social network from a URL and returns the SVG code for its icon.
 *
 * @since Twenty Twenty-One 1.0
 *
 * @param string $uri  Social link.
 * @param int    $size The icon size in pixels.
 * @return string
 */
function elementify_get_social_link_svg( $uri, $size = 24 ) {
	return Elementify\Inc\Svg_Icons::get_social_link_svg( $uri, $size );
}

/**
 * Displays SVG icons in the footer navigation.
 *
 * @since Twenty Twenty-One 1.0
 *
 * @param string   $item_output The menu item's starting HTML output.
 * @param WP_Post  $item        Menu item data object.
 * @param int      $depth       Depth of the menu. Used for padding.
 * @param stdClass $args        An object of wp_nav_menu() arguments.
 * @return string The menu item output with social icon.
 */
function elementify_nav_menu_social_icons( $item_output, $item, $depth, $args ) {
	// Change SVG icon inside social links menu if there is supported URL.
	if ( 'footer' === $args->theme_location ) {
		$svg = elementify_get_social_link_svg( $item->url, 24 );
		if ( ! empty( $svg ) ) {
			$item_output = str_replace( $args->link_before, $svg, $item_output );
		}
	}

	return $item_output;
}

add_filter( 'walker_nav_menu_start_el', 'elementify_nav_menu_social_icons', 10, 4 );

add_filter(
	'nav_menu_link_attributes',
	function ( $attr ) {

		$class = 'ele-menu-link';

		if ( ! isset( $attr['class'] ) ) {
			$attr['class'] = '';
		}

		$attr['class'] .= ' ' . $class;

		$attr['class'] = trim( $attr['class'] );

		// Plain links: no ARIA menu roles. Submenu state is exposed on the
		// toggle buttons that assets/src/js/main.js adds next to parent items.
		return $attr;
	},
	5,
	1
);



if ( ! function_exists( 'elementify_blog_id' ) ) {
	/**
	 * Get the blog ID; supports multisite.
	 *
	 * @param null|string $slug Optional. Multisite slug suffix.
	 *
	 * @return string
	 */
	function elementify_blog_id( $slug = null ) {
		global $blog_id;

		$prefix = ( is_multisite() && $blog_id > 1 ) ? 'ele-blog-' . $blog_id : 'ele-blog';

		return null === $slug ? $prefix : $prefix . '-' . $slug;
	}
}

if ( ! function_exists( 'elementify_html_attributes' ) ) {
	/**
	 * Output html attributes
	 */
	function elementify_html_attributes() {

		$attrs = [
			'data-save-color-scheme' => 'no',
			'data-blog-id'           => elementify_blog_id(),
			'data-theme'             => isset( $_COOKIE['darkMode'] ) ? 'dark' : 'light',
		];

		Utils::print_attribute_string( apply_filters( 'elementify_html_attributes', $attrs ) );
	}
}

if ( ! function_exists( 'elementify_footer_widgets_show' ) ) {

	/**
	 * Always show footer widgets for customize builder
	 *
	 * Forces a Customizer section active when its id belongs to a footer
	 * widgets area, so the builder never hides those panels.
	 *
	 * @param bool                 $active  Whether the section is currently active.
	 * @param WP_Customize_Section $section The section being tested.
	 * @return bool Whether the section should be active.
	 */
	function elementify_footer_widgets_show( $active, $section ) {
		if ( strpos( $section->id, 'widgets-footer-' ) ) {
			$active = true;
		}

		return $active;
	}
}
add_filter( 'customize_section_active', 'elementify_footer_widgets_show', 15, 2 );


/**
 * Helper function to get the current post ID.
 *
 * @package Elementify
 */

if ( ! function_exists( 'elementify_get_post_id' ) ) {
	/**
	 * Retrieves the current post ID based on context.
	 *
	 * @param mixed $post_id_override Optional. Override post ID. Default empty string.
	 * @return int The post ID. Returns 0 if not found.
	 */
	function elementify_get_post_id( $post_id_override = '' ) {
		// Check for the legacy function and use it if available.
		if ( function_exists( 'elementify_framework_get_post_id' ) ) {
			return (int) elementify_framework_get_post_id( $post_id_override );
		}

		// Handle the override early.
		if ( ! empty( $post_id_override ) && is_numeric( $post_id_override ) ) {
			return (int) apply_filters( 'elementify_get_post_id', (int) $post_id_override, $post_id_override );
		}

		// Use the WordPress core function for singular posts.
		$current_id = get_the_ID();
		if ( is_singular() && false !== $current_id ) {
			return (int) apply_filters( 'elementify_get_post_id', $current_id, $post_id_override );
		}

		// Handle specific page types.
		$post_id = 0; // Default value.

		if ( is_home() ) {
			$post_id = (int) get_option( 'page_for_posts', 0 );
		} elseif ( function_exists( 'is_shop' ) && is_shop() && function_exists( 'wc_get_page_id' ) ) {
			$post_id = (int) wc_get_page_id( 'shop' );
		} elseif ( is_archive() || is_tax() || is_category() || is_tag() || is_author() ) {
			$queried_object = get_queried_object();
			$post_id        = $queried_object instanceof WP_Post ? (int) $queried_object->ID : 0;
		}

		/**
		 * Filter the post ID before returning.
		 *
		 * @param int   $post_id          The post ID.
		 * @param mixed $post_id_override The override post ID.
		 */
		return (int) apply_filters( 'elementify_get_post_id', $post_id, $post_id_override );
	}
}


