<?php
/**
 * SVG Icons class
 *
 * @link    https://github.com/WordPress/twentytwentyone/blob/trunk/classes/class-twenty-twenty-one-svg-icons.php
 * @package Elementify
 * @since   1.0.0
 */

namespace Elementify\Inc;

/**
 * This class is in charge of displaying SVG icons across the site.
 *
 * Place each <svg> source on its own array key, without adding either
 * the `width` or `height` attributes, since these are added dynamically,
 * before rendering the SVG code.
 *
 * All icons are assumed to have equal width and height, hence the option
 * to only specify a `$size` parameter in the svg methods.
 *
 * @since   1.0.0
 */
class Svg_Icons {


	/**
	 * User Interface icons – svg sources.
	 *
	 * @since   1.0.0
	 *
	 * @var array
	 */
	protected static $icons = [
		// Only the icons the theme and the Elementify Framework plugin request:
		// shopping-bag (header cart), x, arrow-left and arrow-right (framework
		// popup and hero slider). Add more through the elementify_svg_icons_ui filter.
		'arrow-left'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
		'arrow-right'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>',
		'shopping-bag' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>',
		'x'            => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
	];

	/**
	 * Social Icons – svg sources.
	 *
	 * @since   1.0.0
	 *
	 * @var array
	 */
	protected static $social_icons = [];

	/**
	 * Social Icons – domain mappings.
	 *
	 * By default, each Icon ID is matched against a .com TLD. To override this behavior,
	 * specify all the domains it covers (including the .com TLD too, if applicable).
	 *
	 * @since   1.0.0
	 *
	 * @var array
	 */
	protected static $social_icons_map = [];

	/**
	 * Gets list of svg icons.
	 *
	 * @static
	 *
	 * @since   1.0.0
	 * @return array
	 */
	public static function get_svg_list() {
		return wp_parse_args( self::$social_icons, self::$icons );
	}

	/**
	 * Gets the SVG code for a given icon.
	 *
	 * @static
	 *
	 * @since   1.0.0
	 *
	 * @param string $group The icon group.
	 * @param string $icon  The icon.
	 * @param int    $size  The icon-size in pixels.
	 * @return string
	 */
	public static function get_svg( $group, $icon, $size ) {

		if ( 'ui' === $group ) {
			$arr = self::$icons;
		} elseif ( 'social' === $group ) {
			$arr = self::$social_icons;
		} else {
			$arr = [];
		}

		/**
		 * Filters Twenty Twenty-Ones's array of icons.
		 *
		 * The dynamic portion of the hook name, `$group`, refers to
		 * the name of the group of icons, either "ui" or "social".
		 *
		 * @since   1.0.0
		 *
		 * @param array $arr Array of icons.
		 */
		$arr = apply_filters( "elementify_svg_icons_{$group}", $arr );

		$svg = '';
		if ( array_key_exists( $icon, $arr ) ) {
			$repl = sprintf( '<svg class="ele-svg-icon" width="%d" height="%d" aria-hidden="true" role="img" focusable="false" ', $size, $size );

			$svg = preg_replace( '/^<svg /', $repl, trim( $arr[ $icon ] ) ); // Add extra attributes to SVG code.
		}

		return $svg;
	}

	/**
	 * Detects the social network from a URL and returns the SVG code for its icon.
	 *
	 * @static
	 *
	 * @since   1.0.0
	 *
	 * @param string $uri  Social link.
	 * @param int    $size The icon-size in pixels.
	 * @return string|null
	 */
	public static function get_social_link_svg( $uri, $size ) {
		static $regex_map; // Only compute regex map once, for performance.

		if ( ! isset( $regex_map ) ) {
			$regex_map = [];

			/**
			 * Filters Twenty Twenty-Ones's array of domain mappings for social icons.
			 *
			 * By default, each Icon ID is matched against a .com TLD. To override this behavior,
			 * specify all the domains it covers (including the .com TLD too, if applicable).
			 *
			 * @since   1.0.0
			 *
			 * @param array $social_icons_map Array of default social icons.
			 */
			$map = apply_filters( 'elementify_social_icons_map', self::$social_icons_map );

			/**
			 * Filters Twenty Twenty-One's array of social icons.
			 *
			 * @since   1.0.0
			 *
			 * @param array $social_icons Array of default social icons.
			 */
			$social_icons = apply_filters( 'elementify_svg_icons_social', self::$social_icons );

			foreach ( array_keys( $social_icons ) as $icon ) {
				$domains            = array_key_exists( $icon, $map ) ? $map[ $icon ] : [ sprintf( '%s.com', $icon ) ];
				$domains            = array_map( 'trim', $domains ); // Remove leading/trailing spaces, to prevent regex from failing to match.
				$domains            = array_map( 'preg_quote', $domains );
				$regex_map[ $icon ] = sprintf( '/(%s)/i', implode( '|', $domains ) );
			}
		}
		foreach ( $regex_map as $icon => $regex ) {
			if ( preg_match( $regex, $uri ) ) {

				return self::get_svg( 'social', $icon, $size ) . '<span class="screen-reader-text">';
			}
		}
		return null;
	}
}
