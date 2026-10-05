<?php
/**
 * Theme utils.
 *
 * @package Elementify
 */

namespace Elementify\Inc;

use Elementify\Inc\Traits\Singleton;

/**
 * Static helpers shared across the theme.
 *
 * Covers class-name building, attribute string rendering, array manipulation,
 * PHP 8 string function polyfills, Customizer deep links, and schema.org
 * microdata attributes. All methods are static and safe to call without
 * booting the singleton.
 */
class Utils {

	use Singleton;

	/**
	 * A utility for constructing className strings conditionally.
	 *
	 * Accepts strings, and arrays whose values are either class name strings or
	 * booleans keyed by class name, where `true` means "include this class".
	 *
	 * @param mixed ...$args Class name strings, or arrays as described above.
	 * @return string The collected class names joined by a single space.
	 */
	public static function clsx( ...$args ) {
		$classNames = [];

		foreach ( $args as $arg ) {
			if ( is_string( $arg ) && $arg !== '' ) {
				$classNames[] = $arg;
			} elseif ( is_array( $arg ) ) {
				foreach ( $arg as $k => $v ) {
					if ( is_string( $v ) ) {
						$classNames[] = $v;
					} elseif ( is_bool( $v ) && $v === true ) {
						$classNames[] = $k;
					}
				}
			}
		}

		return implode( ' ', $classNames );
	}

	/**
	 * Echo version for clsx
	 *
	 * @param mixed ...$args Class name strings, or arrays as described in clsx().
	 * @return void
	 */
	public static function the_clsx( ...$args ) {
		echo esc_attr( self::clsx( ...$args ) );
	}

	/**
	 * Render attribute string
	 *
	 * @param array $attributes Attribute values keyed by attribute name.
	 * @return string The attributes as a space-separated `name="value"` string.
	 */
	public static function render_attribute_string( $attributes ) {
		$attrs = [];

		foreach ( $attributes as $attr => $value ) {
			$attrs[] = $attr . '=' . '"' . esc_attr( $value ) . '"';
		}

		return implode( ' ', $attrs );
	}

	/**
	 * Print attribute string
	 *
	 * @param array $attributes Attribute values keyed by attribute name.
	 * @return void
	 */
	public static function print_attribute_string( $attributes ) {
		echo self::render_attribute_string( $attributes );
	}

	/**
	 * Encode uri component
	 *
	 * rawurlencode() per RFC 3986, then restores the sub-delims that
	 * JavaScript's encodeURIComponent() leaves unescaped.
	 *
	 * @param string $str The string to encode.
	 * @return string The encoded string.
	 */
	public static function encode_uri_component( $str ) {
		$revert = [
			'%21' => '!',
			'%2A' => '*',
			'%27' => "'",
			'%28' => '(',
			'%29' => ')',
		];

		return strtr( rawurlencode( $str ), $revert );
	}

	/**
	 * Flatten a multi-dimensional array into a single level.
	 *
	 * @See: https://github.com/laravel/framework
	 *
	 * @param array    $array The array to flatten.
	 * @param int|float $depth How many levels of nesting to descend. INF
	 *                     flattens fully; the default is the float INF.
	 * @return array The flattened values, re-indexed from zero.
	 */
	public static function array_flatten( $array, $depth = INF ) {
		$result = [];

		foreach ( $array as $item ) {

			if ( ! is_array( $item ) ) {
				$result[] = $item;
			} else {
				$values = $depth === 1
					? array_values( $item )
					: self::array_flatten( $item, $depth - 1 );

				foreach ( $values as $value ) {
					$result[] = $value;
				}
			}
		}

		return $result;
	}

	/**
	 * Collapse an array of arrays into a single array.
	 *
	 * @See: https://github.com/laravel/framework
	 *
	 * @param array $array An array whose values may themselves be arrays.
	 * @return array All values merged into a single flat array.
	 */
	public static function array_collapse( $array ) {
		$results = [];

		foreach ( $array as $values ) {
			if ( ! is_array( $values ) ) {
				continue;
			}

			$results[] = $values;
		}

		return array_merge( [], ...$results );
	}

	/**
	 * Just like array_pluck function in laravel
	 *
	 * @param string|int $key Key to read from each item in $arr.
	 * @param array      $arr Items to pluck from.
	 * @return array The plucked values, in the order of $arr.
	 */
	public static function array_pluck( $key, $arr ) {
		return array_map(
			function ( $item ) use ( $key ) {
				return $item[ $key ];
			},
			$arr
		);
	}

	/**
	 * Find value in an array using a string path
	 *
	 * Supports a `[]` segment to map the rest of the path over every value at
	 * that level, e.g. 'items[].id'.
	 *
	 * @param array  $arr     The array to search.
	 * @param string $path    Dot-delimited path to the value.
	 * @param mixed  $default Value to return when the path cannot be resolved.
	 * @return mixed The value at $path, or $default.
	 */
	public static function array_path( $arr, $path, $default = null ) {
		$keys   = explode( '.', $path );
		$source = $arr;

		while ( count( $keys ) > 0 ) {
			$key = array_shift( $keys );

			// collect value
			if ( $key === '[]' ) {
				$result = [];

				foreach ( $source as $item ) {
					$result[] = self::array_path( $item, implode( '.', $keys ), $default );
				}

				return $result;
			}

			if ( is_array( $source ) && isset( $source[ $key ] ) ) {
				$source = $source[ $key ];
			} else {
				// current key doesn't exist, stop loop and return default value
				return $default;
			}
		}

		// we have reached the end of the path
		return $source;
	}

	/**
	 * Generate rand key
	 *
	 * @return string A 32-character hex string, seeded from time(), uniqid() and wp_rand().
	 */
	public static function rand_key() {
		return md5( time() . '-' . uniqid( wp_rand(), true ) . '-' . wp_rand() );
	}

	/**
	 * Polyfill for `str_contains()` function added in PHP 8.0.
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The substring to search for.
	 * @return bool Whether $needle occurs in $haystack. An empty needle always matches.
	 */
	public static function str_contains( $haystack, $needle ) {
		return ( '' === $needle || false !== strpos( $haystack, $needle ) );
	}

	/**
	 * Polyfill for `str_starts_with()` function added in PHP 8.0.
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The prefix to look for.
	 * @return bool Whether $haystack begins with $needle. An empty needle always matches.
	 */
	public static function str_starts_with( $haystack, $needle ) {
		if ( function_exists( 'str_starts_with' ) ) {
			return str_starts_with( $haystack, $needle );
		}

		if ( '' === $needle ) {
			return true;
		}

		return 0 === strpos( $haystack, $needle );
	}

	/**
	 * Polyfill for `str_ends_with()` function added in PHP 8.0.
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The suffix to look for.
	 * @return bool Whether $haystack ends with $needle. An empty needle always matches.
	 */
	public static function str_ends_with( $haystack, $needle ) {
		if ( function_exists( 'str_ends_with' ) ) {
			return str_ends_with( $haystack, $needle );
		}

		if ( '' === $haystack && '' !== $needle ) {
			return false;
		}
		$len = strlen( $needle );

		return 0 === substr_compare( $haystack, $needle, - $len, $len );
	}

	/**
	 * Get customizer_url
	 *
	 * @param string $location Control or section to focus in the Customizer.
	 * @return string The Customizer admin URL with the focus query arg set.
	 */
	public static function customizer_url( $location ) {
		$query                     = [];
		$query['lotta_auto_focus'] = $location;

		return add_query_arg( $query, admin_url( 'customize.php' ) );
	}

	/**
	 * Echo version for customizer_url
	 *
	 * @param $location
	 *
	 * @return void
	 */
	public static function the_customizer_url( $location ) {
		echo esc_url( self::customizer_url( $location ) );
	}

	/**
	 * Get any necessary schema definition.
	 *
	 * Recognised contexts are html, header, navigation, logo, article,
	 * post-author, comment-body, comment-author, sidebar, footer and video.
	 * The html and article types are filterable.
	 *
	 * @param string $context The element to target.
	 * @return string|null Our final attribute to add to the element, or null
	 *                     when the context has no definition.
	 */
	public static function schema_org_definitions( $context ) {
		$data = false;

		if ( 'html' === $context ) {
			$type = 'WebPage';

			if ( class_exists( 'woocommerce' ) && is_product() ) {
				$type = 'IndividualProduct';
			} elseif ( is_home() || is_archive() || is_attachment() || is_tax() || is_single() ) {
				$type = 'Blog';
			} elseif ( is_author() ) {
				$type = 'ProfilePage';
			}

			if ( is_search() ) {
				$type = 'SearchResultsPage';
			}

			$type = apply_filters( 'elementify_html_itemtype', $type );

			$data = sprintf(
				'itemtype="https://schema.org/%s" itemscope',
				esc_html( $type )
			);
		}

		if ( 'header' === $context ) {
			$data = 'itemtype="https://schema.org/WPHeader" itemscope';
		}

		if ( 'navigation' === $context ) {
			$data = 'itemtype="https://schema.org/SiteNavigationElement" itemscope';
		}

		if ( 'logo' === $context ) {
			$data = 'itemtype="https://schema.org/Organization" itemscope';
		}

		if ( 'article' === $context ) {
			$type = apply_filters( 'elementify_article_itemtype', 'CreativeWork' );

			$data = sprintf(
				'itemtype="https://schema.org/%s" itemscope',
				esc_html( $type )
			);
		}

		if ( 'post-author' === $context ) {
			$data = 'itemprop="author" itemtype="https://schema.org/Person" itemscope';
		}

		if ( 'comment-body' === $context ) {
			$data = 'itemtype="https://schema.org/Comment" itemscope';
		}

		if ( 'comment-author' === $context ) {
			$data = 'itemprop="author" itemtype="https://schema.org/Person" itemscope';
		}

		if ( 'sidebar' === $context ) {
			$data = 'itemtype="https://schema.org/WPSideBar" itemscope';
		}

		if ( 'footer' === $context ) {
			$data = 'itemtype="https://schema.org/WPFooter" itemscope';
		}
		if ( 'video' === $context ) {
			$data = 'itemprop="video" itemtype="http://schema.org/VideoObject" itemscope';
		}

		if ( $data ) {
			return apply_filters( "elementify_{$context}_schema", $data );
		}
	}
	/**
	 * Echo the schema.org microdata attributes for an element.
	 *
	 * Despite the parameter name, the value is the element context passed
	 * straight to schema_org_definitions().
	 *
	 * @param string $attributes The element context, e.g. 'header'.
	 * @return void
	 */
	public static function the_microdata( $attributes ) {
		echo self::schema_org_definitions( $attributes );
	}

	/**
	 * Return the schema.org microdata attributes for an element.
	 *
	 * Despite the parameter name, the value is the element context passed
	 * straight to schema_org_definitions().
	 *
	 * @param string $attributes The element context, e.g. 'header'.
	 * @return string|false The attributes, or false when the context is unknown.
	 */
	public static function microdata( $attributes ) {
		return self::schema_org_definitions( $attributes );
	}
}
