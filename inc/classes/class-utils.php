<?php
/**
 * Theme utils.
 *
 * @package Elementify
 */

namespace Elementify\Inc;

use Elementify\Inc\Traits\Singleton;

class Utils {

	use Singleton;

	/**
	 * A utility for constructing className strings conditionally.
	 *
	 * @param ...$args
	 *
	 * @return string
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
	 * @param ...$args
	 */
	public static function the_clsx( ...$args ) {
		echo esc_attr( self::clsx( ...$args ) );
	}

	/**
	 * Render attribute string
	 *
	 * @param $attributes
	 *
	 * @return string
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
	 * @param $attributes
	 */
	public static function print_attribute_string( $attributes ) {
		echo self::render_attribute_string( $attributes );
	}

	/**
	 * Encode uri component
	 *
	 * @param $str
	 *
	 * @return string
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
	 * @param $array
	 * @param $depth
	 *
	 * @return array
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
	 * @param $array
	 *
	 * @return array
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
	 * @param $key
	 * @param $arr
	 *
	 * @return array
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
	 * @param $arr
	 * @param $path
	 * @param null $default
	 *
	 * @return mixed|null
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
	 * @return string
	 */
	public static function rand_key() {
		return md5( time() . '-' . uniqid( wp_rand(), true ) . '-' . wp_rand() );
	}

	/**
	 * Polyfill for `str_contains()` function added in PHP 8.0.
	 *
	 * @param $haystack
	 * @param $needle
	 *
	 * @return bool
	 */
	public static function str_contains( $haystack, $needle ) {
		return ( '' === $needle || false !== strpos( $haystack, $needle ) );
	}

	/**
	 * Polyfill for `str_starts_with()` function added in PHP 8.0.
	 *
	 * @param $haystack
	 * @param $needle
	 *
	 * @return bool
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
	 * @param $haystack
	 * @param $needle
	 *
	 * @return bool
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
	 * @param $location
	 *
	 * @return string
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
	 * @param string $context The element to target.
	 * @return string Our final attribute to add to the element.
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
	 * Print attribute string
	 *
	 * @param $attributes
	 */
	public static function the_microdata( $attributes ) {
		echo self::schema_org_definitions( $attributes );
	}

	/**
	 * Return attribute string
	 *
	 * @param $attributes
	 */
	public static function microdata( $attributes ) {
		return self::schema_org_definitions( $attributes );
	}
}
