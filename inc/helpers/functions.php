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

if ( ! function_exists( 'bizness_get_nav_menus' ) ) {
	/**
	 * Returns an array of navigation menus.
	 *
	 * @access public
	 * @param string $value_field The value to be stored in options. Accepted values: id|slug.
	 * @return array
	 */
	function bizness_get_nav_menus( $value_field = 'id' ) {
		$choices   = [];
		$nav_menus = wp_get_nav_menus();

		foreach ( $nav_menus as $term ) {
			$choices[ 'slug' === $value_field ? $term->slug : $term->term_id ] = $term->name;
		}

		return $choices;
	}
}

/*
--------------------------------------------------------------
// Site Title
--------------------------------------------------------------*/
if ( ! function_exists( 'elementify_site_title' ) ) {
	/**
	 * Displays the site title dynamically.
	 *
	 * @param array   $args  Arguments for displaying the site title.
	 * @param boolean $echo  Echo or return the HTML.
	 * @return string $html  Compiled HTML based on our arguments.
	 * @since 1.0.0
	 */
	function elementify_site_title( $args = [], $echo = true ) {
		// Allow dynamic text, otherwise fallback to bloginfo
		$default_text = get_bloginfo( 'name' );

		$defaults = [
			'title'       => '<a href="%1$s">%2$s</a>',
			'title_class' => 'ele-site-title',
			'wrapper'     => '<div class="%1$s" itemprop="name">%2$s</div>',
			'text'        => $default_text, // NEW: Allows overriding the text
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
		$wrap      = $args['wrapper']; // Wrapper is usually safe from sprintf if controlled by dev

		$html = sprintf( $wrap, $classname, $contents );

		/**
		 * Filters the final HTML for `elementify_site_title()`.
		 */
		$html = apply_filters( 'elementify_site_title_html', $html, $args, $classname, $contents );

		if ( ! $echo ) {
			return $html;
		}

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/*
--------------------------------------------------------------
// Site Description
--------------------------------------------------------------*/
if ( ! function_exists( 'elementify_site_description' ) ) {
	/**
	 * Displays the site description dynamically.
	 *
	 * @param array   $args  Arguments for displaying the site description.
	 * @param boolean $echo  Echo or return the HTML.
	 * @return string $html  The HTML to display.
	 * @since 1.0.0
	 */
	function elementify_site_description( $args = [], $echo = true ) {
		$default_text = get_bloginfo( 'description' );

		$defaults = [
			'class'   => 'ele-site-description',
			'wrapper' => '<p class="%1$s" itemprop="description">%2$s</p>',
			'text'    => $default_text, // NEW: Allows overriding the text
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

		if ( ! $echo ) {
			return $html;
		}

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/*
--------------------------------------------------------------
// Site Logo
--------------------------------------------------------------*/
if ( ! function_exists( 'elementify_site_logo' ) ) {
	/**
	 * Displays the site logo dynamically.
	 *
	 * @param array   $args  Arguments for displaying the site logo.
	 * @param boolean $echo  Echo or return the HTML.
	 * @return string $html  The HTML to display.
	 * @since 1.0.0
	 */
	function elementify_site_logo( $args = [], $echo = true ) {
		$defaults = [
			'class'     => 'ele-site-logo',
			'wrapper'   => '<div class="%1$s">%2$s</div>',
			'logo_id'   => get_theme_mod( 'custom_logo' ), // NEW: Allows passing a specific logo ID
			'logo_size' => 'full', // NEW: Allows dynamic image sizes (e.g., 'thumbnail', 'medium')
			'fallback'  => '', // NEW: HTML to show if no logo is set
		];

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filters the arguments for `elementify_site_logo()`.
		 */
		$args = apply_filters( 'elementify_site_logo_args', $args, $defaults );

		// If no logo ID is provided or set, return fallback or empty
		if ( empty( $args['logo_id'] ) ) {
			$html = $args['fallback'];
		} else {
			// Get logo HTML, optionally with a specific size
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

		if ( ! $echo ) {
			return $html;
		}

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
/*
--------------------------------------------------------------
// Site Identity
--------------------------------------------------------------*/
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
--------------------------------------------------------------
// Primary Navigation
--------------------------------------------------------------*/
if ( ! function_exists( 'elementify_primary_navigation' ) ) {
	/**
	 * Displays the primary navigation: the small-screen toggle and the menu-1 location.
	 *
	 * The markup (nav#ele-header-menu-1, .menu-toggle and ul#primary-menu) is
	 * what assets/src/js/main.js binds to, so callers can change the menu's
	 * look and behaviour through $args but not those hooks.
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
			]
		);

		$main_navigation = [ 'main-navigation', 'ele-left-0', 'ele-z-20' ];
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

		<nav id="ele-header-menu-1" class="<?php echo esc_attr( implode( ' ', $main_navigation ) ); ?>" data-layout="<?php echo esc_attr( $args['layout'] ); ?>" aria-label="<?php esc_attr_e( 'Primary menu', 'elementify' ); ?>"<?php echo $nav_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?>>
			<button type="button" class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'elementify' ); ?></span>
				<span class="ele-trigger-menu ele-d-block ele-z-30" aria-hidden="true">
					<span class="ele-hamburger-menu"><span></span><span></span><span></span><span></span></span>
				</span>
			</button>
			<?php
			wp_nav_menu(
				[
					'theme_location' => 'menu-1',
					'menu_id'        => 'primary-menu',
					'menu_class'     => $menu_class . ' ele-dropdown-reveal-' . sanitize_html_class( $args['reveal'] ),
					'container'      => false,
					'items_wrap'     => '<ul id="primary-menu" class="%2$s">%3$s</ul>',
					'link_after'     => $args['link_after'],
					'depth'          => absint( $args['depth'] ),
					'fallback_cb'    => $args['fallback_cb'],
				]
			);
			?>
		</nav>
		<?php
	}
}

/*
--------------------------------------------------------------
// Primary menu fallback
--------------------------------------------------------------*/
if ( ! function_exists( 'elementify_menu_fallback' ) ) {
	/**
	 * Menu fallback for primary menu.
	 *
	 * Contains wp_list_pages to display pages created,
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	function elementify_menu_fallback() {
		$output  = '';
		$output .= '<ul id="primary-menu" class="ele-main-menu ele-d-flex ele-flex-wrap ele-list-style-none">';

		$output .= wp_list_pages(
			[
				'echo'     => false,
				'title_li' => false,
			]
		);

		$output .= '</ul>';

        // @codingStandardsIgnoreStart
        echo $output;
        // @codingStandardsIgnoreEnd
	}
}

/*
--------------------------------------------------------------
// Collapsable menu fallback
--------------------------------------------------------------*/
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
--------------------------------------------------------------
// Get SVG Code
--------------------------------------------------------------*/
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
--------------------------------------------------------------
// Pagination
--------------------------------------------------------------*/
if ( ! function_exists( 'elemetify_pagination' ) ) {

	/**
	 * Elemetify Pagination.
	 *
	 * @return void
	 */
	function elemetify_pagination() {

		$links = paginate_links(
			[
				'mid_size'           => 1,
				'prev_text'          => elementify_get_the_svg( 'ui', 'chevron-left', 16 ) . '<span class="ele-pagination-label">' . esc_html__( 'Previous', 'elementify' ) . '</span>',
				'next_text'          => '<span class="ele-pagination-label">' . esc_html__( 'Next', 'elementify' ) . '</span>' . elementify_get_the_svg( 'ui', 'chevron-right', 16 ),
				/* translators: Hidden text before a page number in the posts pagination, e.g. "Page 2". */
				'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Page', 'elementify' ) . ' </span>',
			]
		);

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
				esc_attr__( 'Posts', 'elementify' ),
				esc_html__( 'Posts navigation', 'elementify' ),
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
	 * Display trail breadcrumb
	 *
	 * @return void
	 */
	function elementify_breadcrumb() {
		$defaults = [
			'show_browse' => false,
			'echo'        => true,
		];
		$args     = apply_filters( 'breadcrumb_trail_args', $defaults );

		$breadcrumb = apply_filters( 'breadcrumb_trail_object', null, $args );

		if ( ! is_object( $breadcrumb ) ) {

			$breadcrumb = new Elementify\Inc\Breadcrumb_Trail( $args );
		}

		return $breadcrumb->trail();
	}
}

/*
--------------------------------------------------------------
// Add svg icon for the menu item if they have submenu items.
--------------------------------------------------------------*/
if ( ! function_exists( 'elementify_submenu_icon' ) ) {
	/**
	 * Filters a menu item's starting output.
	 *
	 * Append the dropdown arrow to links with submenus.
	 *
	 * @param string  $item_output The menu item's starting HTML output.
	 * @param WP_Post $item        Menu item data object.
	 * @param int     $depth       Depth of the menu item, relative to its parent.
	 * @param object  $args        Menu arguments, from wp_nav_menu().
	 * @return string The item output, with the arrow appended when the item has children.
	 */
	function elementify_submenu_icon( $item_output, $item, $depth, $args ) {
		$has_children = in_array( 'menu-item-has-children', $item->classes );
		if ( $has_children ) {
			$item_output = str_replace(
				'</a>',
				'<span class="ele-submenu-icon">' . elementify_get_the_svg( 'ui', 'angle-down', 15 ) . '</span></a>',
				$item_output
			);
		}
		return $item_output;
	}
}
// add_filter( 'walker_nav_menu_start_el', 'elementify_submenu_icon', 10, 4 );

/*
--------------------------------------------------------------
// Add classes in Sub Menu
--------------------------------------------------------------*/
if ( ! function_exists( 'elementify_submenu_classes' ) ) {
	/**
	 * Filters the CSS classes of a submenu's sub-menu element.
	 *
	 * Adds `ele-transition-normal` to any `sub-menu` class, enabling the
	 * theme's submenu open/close transition.
	 *
	 * @param array  $classes The submenu's CSS classes.
	 * @param object $args    Menu arguments, from wp_nav_menu().
	 * @param int    $depth   Depth of the submenu, relative to its parent.
	 * @return array The classes, with the transition class added.
	 */
	function elementify_submenu_classes( $classes, $args, $depth ) {
		foreach ( $classes as $key => $class ) {
			if ( $class == 'sub-menu' ) {
				$classes[ $key ] = 'sub-menu ele-transition-normal';
			}
		}
		return $classes;
	}
}
// add_filter( 'nav_menu_submenu_css_class', 'elementify_submenu_classes', 10, 3 );

/**
 * Detects the social network from a URL and returns the SVG code for its icon.
 *
 * @since Twenty Twenty-One 1.0
 *
 * @param string $uri  Social link.
 * @param int    $size The icon size in pixels.
 * @return string
 */
function twenty_twenty_one_get_social_link_svg( $uri, $size = 24 ) {
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
function twenty_twenty_one_nav_menu_social_icons( $item_output, $item, $depth, $args ) {
	// Change SVG icon inside social links menu if there is supported URL.
	if ( 'footer' === $args->theme_location ) {
		$svg = twenty_twenty_one_get_social_link_svg( $item->url, 24 );
		if ( ! empty( $svg ) ) {
			$item_output = str_replace( $args->link_before, $svg, $item_output );
		}
	}

	return $item_output;
}

add_filter( 'walker_nav_menu_start_el', 'twenty_twenty_one_nav_menu_social_icons', 10, 4 );

add_filter(
	'nav_menu_link_attributes',
	function ( $attr, $item, $args, $depth ) {

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
	4
);

// /**
// * Filters the CSS class(es) applied to a menu list element.
// *
// * @param array $classes Array of the CSS classes that are applied to the menu `<ul>` element.
// * @return array
// */
// add_filter( 'nav_menu_submenu_css_class', function( $classes ) {
// return [ 'wp-block-navigation__container' ];
// } );

// /**
// * Filters the CSS classes applied to a menu item's list item element.
// *
// * @param array $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
// * @return array
// */
// add_filter( 'nav_menu_css_class', function( $classes ) {
// $item_classes = [ 'wp-block-navigation-link' ];
// if ( in_array( 'current-menu-item', $classes ) ) {
// $item_classes[] = 'current-menu-item';
// }
// if ( in_array( 'menu-item-has-children', $classes ) ) {
// $item_classes[] = 'has-child';
// }
// return $item_classes;
// } );

// /**
// * Filters the HTML attributes applied to a menu item's anchor element.
// *
// * @param array $atts The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
// * @return array
// */
// add_filter( 'nav_menu_link_attributes', function( $atts ) {
// $atts['class'] = 'wp-block-navigation-link__content';
// return $atts;
// } );

// /**
// * Filters a menu item's title.
// *
// * @param string $title The menu item's title.
// * @return string
// */
// add_filter( 'nav_menu_item_title', function( $title ) {
// return '<span class="wp-block-navigation-link__label">' . $title . '</span>';
// } );

/**
 * Filters a menu item's starting output.
 *
 * Append the dropdown arrow to links with submenus.
 *
 * @param string   $item_output The menu item's starting HTML output.
 * @param WP_Post  $item        Menu item data object.
 * @return string
 */
// add_filter( 'walker_nav_menu_start_el', function( $item_output, $item ) {
// $has_children = in_array( 'menu-item-has-children', $item->classes );
// if ( $has_children ) {
// $item_output = str_replace(
// '</a>',
// '</a><span class="wp-block-navigation-link__submenu-icon"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" transform="rotate(90)"><path d="M8 5v14l11-7z"></path><path d="M0 0h24v24H0z" fill="none"></path></svg></span>',
// $item_output
// );
// }
// return $item_output;
// }, 10, 2 );


if ( ! function_exists( 'elementify_blog_id' ) ) {
	/**
	 * Get blog id, support multisite
	 *
	 * @param null $slug
	 *
	 * @return string
	 */
	function elementify_blog_id( $slug = null ) {
		global $blog_id;

		$prefix = ( is_multisite() && $blog_id > 1 ) ? 'ele-blog-' . $blog_id : 'ele-blog';

		return $slug === null ? $prefix : $prefix . '-' . $slug;
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
	 * @param bool            $active  Whether the section is currently active.
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
		// Check for legacy function and use it if available
		if ( function_exists( 'elementify_framework_get_post_id' ) ) {
			return (int) elementify_framework_get_post_id( $post_id_override );
		}

		// Handle override early
		if ( ! empty( $post_id_override ) && is_numeric( $post_id_override ) ) {
			return (int) apply_filters( 'elementify_get_post_id', (int) $post_id_override, $post_id_override );
		}

		// Use WordPress core function for singular posts
		if ( is_singular() && ( $current_id = get_the_ID() ) !== false ) {
			return (int) apply_filters( 'elementify_get_post_id', $current_id, $post_id_override );
		}

		// Handle specific page types
		$post_id = 0; // Default value

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



/**
 * Page menu walker that marks the current page and its ancestors.
 *
 * Wraps each page in an <li> whose classes come from the core `page_css_class`
 * filter, and gives the anchor an `ele-menu-link` class.
 */
class Elementify_Walker_Page_Menu extends Walker_Page {

	/**
	 * Render the opening markup for a single page link.
	 *
	 * Appends an <li> carrying the core `current_page_item`,
	 * `current_page_parent` and `current_page_ancestor` classes, followed by
	 * an anchor with the `ele-menu-link` class. Appends the page date when
	 * $args['show_date'] is set.
	 *
	 * @param string  $output       Walker output, passed by reference and appended to.
	 * @param WP_Post $page         The page being rendered.
	 * @param int     $depth        Depth of the page, relative to its parent.
	 * @param array   $args         Menu arguments, including an optional 'show_date' key.
	 * @param int     $current_page ID of the page currently being viewed, if any.
	 * @return void
	 */
	function start_el( &$output, $page, $depth = 0, $args = [], $current_page = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$css_class = [ 'page_item', 'page-item-' . $page->ID ];
		if ( ! empty( $current_page ) ) {
			$_current_page = get_post( $current_page );
			if ( in_array( $page->ID, $_current_page->ancestors ) ) {
				$css_class[] = 'current_page_ancestor';
			}
			if ( $page->ID == $current_page ) {
				$css_class[] = 'current_page_item';
			} elseif ( $_current_page && $page->ID == $_current_page->post_parent ) {
				$css_class[] = 'current_page_parent';
			}
		} elseif ( $page->ID == get_option( 'page_for_posts' ) ) {
			$css_class[] = 'current_page_parent';
		}

		$css_class = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

		// Here we add the custom class 'ele-menu-link' to the anchor tag
		$output .= $indent . sprintf(
			'<li class="%s"><a href="%s" class="ele-menu-link">%s</a>',
			$css_class,
			esc_url( get_permalink( $page->ID ) ),
			apply_filters( 'the_title', $page->post_title, $page->ID )
		);

		if ( ! empty( $args['show_date'] ) ) {
			if ( 'modified' == $args['show_date'] ) {
				$time = $page->post_modified;
			} else {
				$time = $page->post_date;
			}
			$output .= ' ' . mysql2date( get_option( 'date_format' ), $time );
		}
	}
}
