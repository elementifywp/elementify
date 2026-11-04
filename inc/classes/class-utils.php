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
		$class_names = [];

		foreach ( $args as $arg ) {
			if ( is_string( $arg ) && '' !== $arg ) {
				$class_names[] = $arg;
			} elseif ( is_array( $arg ) ) {
				foreach ( $arg as $k => $v ) {
					if ( is_string( $v ) ) {
						$class_names[] = $v;
					} elseif ( is_bool( $v ) && true === $v ) {
						$class_names[] = $k;
					}
				}
			}
		}

		return implode( ' ', $class_names );
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
			$attrs[] = $attr . '="' . esc_attr( $value ) . '"';
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
		echo self::render_attribute_string( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in render_attribute_string().
	}

	/**
	 * Encode uri component
	 *
	 * Encodes with rawurlencode() per RFC 3986, then restores the sub-delims
	 * that JavaScript's encodeURIComponent() leaves unescaped.
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
	 * @param array     $input The array to flatten.
	 * @param int|float $depth How many levels of nesting to descend. INF
	 *                     flattens fully; the default is the float INF.
	 * @return array The flattened values, re-indexed from zero.
	 */
	public static function array_flatten( $input, $depth = INF ) {
		$result = [];

		foreach ( $input as $item ) {

			if ( ! is_array( $item ) ) {
				$result[] = $item;
			} else {
				$values = 1 === $depth
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
	 * @param array $input An array whose values may themselves be arrays.
	 * @return array All values merged into a single flat array.
	 */
	public static function array_collapse( $input ) {
		$results = [];

		foreach ( $input as $values ) {
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
	 * @param array  $arr      The array to search.
	 * @param string $path     Dot-delimited path to the value.
	 * @param mixed  $fallback Value to return when the path cannot be resolved.
	 * @return mixed The value at $path, or $fallback.
	 */
	public static function array_path( $arr, $path, $fallback = null ) {
		$keys   = explode( '.', $path );
		$source = $arr;

		while ( ! empty( $keys ) ) {
			$key = array_shift( $keys );

			// Collect value.
			if ( '[]' === $key ) {
				$result = [];

				foreach ( $source as $item ) {
					$result[] = self::array_path( $item, implode( '.', $keys ), $fallback );
				}

				return $result;
			}

			if ( is_array( $source ) && isset( $source[ $key ] ) ) {
				$source = $source[ $key ];
			} else {
				// Current key doesn't exist; stop the loop and return the fallback value.
				return $fallback;
			}
		}

		// We have reached the end of the path.
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
	 * @param string $location Control or section to focus in the Customizer.
	 *
	 * @return void
	 */
	public static function the_customizer_url( $location ) {
		echo esc_url( self::customizer_url( $location ) );
	}

	/**
	 * Whether schema.org microdata should be printed.
	 *
	 * Controlled by the "Schema Markup" Customizer setting, and filterable so
	 * an SEO plugin that prints its own JSON-LD graph can switch it off.
	 *
	 * @return bool
	 */
	public static function has_schema_org_markup() {
		$enabled = (bool) get_theme_mod( 'elementify_schema_org_markup', true );

		/**
		 * Filters whether the theme prints schema.org microdata.
		 *
		 * @param bool $enabled Whether microdata is enabled.
		 */
		return (bool) apply_filters( 'elementify_has_schema_org_markup', $enabled );
	}

	/**
	 * The schema.org type of the whole document, printed on <html>.
	 *
	 * @return string A schema.org type name, e.g. "Blog".
	 */
	public static function schema_org_page_type() {
		$type = 'WebPage';

		if ( is_search() ) {
			$type = 'SearchResultsPage';
		} elseif ( is_author() ) {
			$type = 'ProfilePage';
		} elseif ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
			$type = 'CollectionPage';
		} elseif ( function_exists( 'is_product' ) && is_product() ) {
			// WooCommerce prints the Product itself as JSON-LD; an empty
			// microdata Product here would only add a second, invalid one.
			$type = 'ItemPage';
		} elseif ( is_home() || is_category() || is_tag() || is_date() || is_singular( 'post' ) ) {
			$type = 'Blog';
		} elseif ( is_archive() ) {
			$type = 'CollectionPage';
		}

		/**
		 * Filters the schema.org type printed on the <html> element.
		 *
		 * @param string $type Schema.org type name.
		 */
		return (string) apply_filters( 'elementify_html_itemtype', $type );
	}

	/**
	 * The schema.org type of a post's own item (cards, single post content).
	 *
	 * @param \WP_Post|int|null $post Post object or ID. Defaults to the current post.
	 * @return string A schema.org type name, e.g. "BlogPosting".
	 */
	public static function schema_org_article_type( $post = null ) {
		$post = get_post( $post );
		$type = ( $post && 'post' === $post->post_type ) ? 'BlogPosting' : 'CreativeWork';

		/**
		 * Filters the schema.org type of a post item.
		 *
		 * @param string        $type Schema.org type name.
		 * @param \WP_Post|null $post The post.
		 */
		return (string) apply_filters( 'elementify_article_itemtype', $type, $post );
	}

	/**
	 * Whether the current view wraps its main content in its own post item.
	 *
	 * True for single posts, attachments and custom post types. Pages are
	 * left out: their headline and text describe the page itself, so they
	 * attach to the WebPage item on <html>. WooCommerce products are left
	 * out because WooCommerce describes them in JSON-LD.
	 *
	 * @return bool
	 */
	public static function is_schema_org_article_view() {
		if ( ! is_singular() || is_page() ) {
			return false;
		}

		if ( function_exists( 'is_product' ) && is_product() ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the schema.org microdata attributes for a place in the markup.
	 *
	 * Modelled on Blocksy's blocksy_schema_org_definitions() and Kadence's
	 * microdata component. Item contexts open a new itemscope:
	 *
	 *   html, header, navigation, logo, sidebar, footer, breadcrumb,
	 *   breadcrumb-item, article, content-area, post-author, comment-body,
	 *   comment-author, video
	 *
	 * Property contexts add an itemprop to the nearest enclosing item:
	 *
	 *   headline, name, description, url, image, logo-image, text, excerpt,
	 *   publish-date, modified-date, author-name, author-url, comment-text,
	 *   comment-date, position, item
	 *
	 * Returns an empty array when microdata is disabled or the context is
	 * unknown.
	 *
	 * @param string $context The element to target.
	 * @return array<string, string> Attribute values keyed by attribute name.
	 *                               `itemscope` is a boolean attribute and has
	 *                               an empty value.
	 */
	public static function schema_org_attributes( $context ) {
		if ( ! self::has_schema_org_markup() ) {
			return [];
		}

		$scope = static function ( $type, $prop = '' ) {
			$attrs = [];
			if ( '' !== $prop ) {
				$attrs['itemprop'] = $prop;
			}
			$attrs['itemscope'] = '';
			$attrs['itemtype']  = 'https://schema.org/' . $type;

			return $attrs;
		};

		$props = [
			'headline'      => 'headline',
			'name'          => 'name',
			'description'   => 'description',
			'url'           => 'url',
			'image'         => 'image',
			'logo-image'    => 'logo',
			'text'          => 'text',
			'excerpt'       => 'description',
			'publish-date'  => 'datePublished',
			'modified-date' => 'dateModified',
			'author-name'   => 'name',
			'author-url'    => 'url',
			'comment-text'  => 'text',
			'comment-date'  => 'dateCreated',
			'position'      => 'position',
			'item'          => 'item',
		];

		$attrs = [];

		switch ( $context ) {
			case 'html':
				$attrs = $scope( self::schema_org_page_type() );
				break;
			case 'header':
				$attrs = $scope( 'WPHeader' );
				break;
			case 'navigation':
				$attrs = $scope( 'SiteNavigationElement' );
				break;
			case 'logo':
				$attrs = $scope( 'Organization' );
				break;
			case 'sidebar':
				$attrs = $scope( 'WPSideBar' );
				break;
			case 'footer':
				$attrs = $scope( 'WPFooter' );
				break;
			case 'breadcrumb':
				$attrs = $scope( 'BreadcrumbList' );
				break;
			case 'breadcrumb-item':
				$attrs = $scope( 'ListItem', 'itemListElement' );
				break;
			case 'article':
				$attrs = $scope( self::schema_org_article_type() );
				break;
			case 'content-area':
				// The single-view wrapper that holds the hero (title, meta,
				// image), the content and the comments.
				if ( self::is_schema_org_article_view() ) {
					$attrs = $scope( self::schema_org_article_type() );
				}
				break;
			case 'post-author':
			case 'comment-author':
				$attrs = $scope( 'Person', 'author' );
				break;
			case 'comment-body':
				$attrs = $scope( 'Comment', 'comment' );
				break;
			case 'video':
				$attrs = $scope( 'VideoObject', 'video' );
				break;
			default:
				if ( isset( $props[ $context ] ) ) {
					$attrs = [ 'itemprop' => $props[ $context ] ];
				}
		}

		/**
		 * Filters the microdata attributes for a context.
		 *
		 * @param array<string, string> $attrs   Attribute values keyed by name.
		 * @param string                $context The element context.
		 */
		return (array) apply_filters( "elementify_{$context}_schema_attributes", $attrs, $context );
	}

	/**
	 * Get the schema.org microdata attributes for a place in the markup as an
	 * HTML attribute string, e.g. `itemscope itemtype="https://schema.org/WPHeader"`.
	 *
	 * @param string $context The element to target. See schema_org_attributes().
	 * @return string The escaped attribute string, or '' when there is none.
	 */
	public static function schema_org_definitions( $context ) {
		$html = [];

		foreach ( self::schema_org_attributes( $context ) as $name => $value ) {
			$name = preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) $name );

			if ( '' === $name ) {
				continue;
			}

			$html[] = ( 'itemscope' === $name && '' === (string) $value )
				? $name
				: $name . '="' . esc_attr( (string) $value ) . '"';
		}

		$data = implode( ' ', $html );

		if ( '' === $data ) {
			return '';
		}

		/**
		 * Filters the microdata attribute string for a context.
		 *
		 * The returned string is printed inside an HTML tag unescaped.
		 *
		 * @param string $data The attribute string.
		 */
		return (string) apply_filters( "elementify_{$context}_schema", $data );
	}

	/**
	 * Echo the schema.org microdata attributes for an element.
	 *
	 * @param string $context The element context, e.g. 'header'.
	 * @return void
	 */
	public static function the_microdata( $context ) {
		echo self::schema_org_definitions( $context ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in schema_org_definitions().
	}

	/**
	 * Return the schema.org microdata attributes for an element.
	 *
	 * @param string $context The element context, e.g. 'header'.
	 * @return string The attributes, or '' when there are none.
	 */
	public static function microdata( $context ) {
		return self::schema_org_definitions( $context );
	}

	/**
	 * Return the microdata attributes with a leading space, ready to append
	 * after another attribute inside a tag built with sprintf()/echo.
	 *
	 * @param string $context The element context, e.g. 'header'.
	 * @return string ' itemprop="…"', or '' when there is none.
	 */
	public static function microdata_attr( $context ) {
		$data = self::schema_org_definitions( $context );

		return '' === $data ? '' : ' ' . $data;
	}
}
