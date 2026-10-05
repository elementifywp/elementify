<?php
/**
 * Page walker for the primary menu's page-list fallback.
 *
 * @package     Elementify
 * @since       1.0.0
 */

namespace Elementify\Inc;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Guarded so a child theme or plugin can declare its own walker first.
if ( ! class_exists( 'Elementify\Inc\Walker_Page' ) ) {

	/**
	 * Render pages as primary-menu items for wp_page_menu().
	 *
	 * Used by elementify_menu_fallback() when no menu is assigned to the
	 * menu-1 location. Each item gets both the core page classes
	 * (page_item, current_page_item, ...) and the nav-menu classes
	 * (menu-item, current-menu-item, ...), so CSS and assets/src/js/main.js
	 * can target pages and nav menus the same way. Parent pages get a
	 * decorative dropdown-menu-toggle caret inside their link, following
	 * Astra's page walker.
	 *
	 * @since 1.0.0
	 */
	class Walker_Page extends \Walker_Page {
		/**
		 * Open a nested list for a parent page's children.
		 *
		 * Outputs a `ul.children.sub-menu`, indented with tabs and newlines only
		 * when $args['item_spacing'] is 'preserve'. The accumulated output is
		 * then passed through the `elementify_caret_wrap_filter` filter, along
		 * with $args['sort_column'] when it is set.
		 *
		 * @since 1.0.0
		 *
		 * @see Walker::start_lvl()
		 *
		 * @param string $output Walker output, passed by reference and appended to.
		 * @param int    $depth  Optional. Depth of the nested list, used for indentation. Default 0.
		 * @param array  $args   Optional. wp_page_menu() / wp_list_pages() arguments. Default [].
		 * @return void
		 */
		public function start_lvl( &$output, $depth = 0, $args = [] ) {
			if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
				$t = "\t";
				$n = "\n";
			} else {
				$t = '';
				$n = '';
			}
			$indent  = str_repeat( $t, $depth );
			$output .= "{$n}{$indent}<ul class='children sub-menu'>{$n}";
			if ( isset( $args['sort_column'] ) ) {
				$output = apply_filters( 'elementify_caret_wrap_filter', $output, $args['sort_column'] );
			} else {
				$output = apply_filters( 'elementify_caret_wrap_filter', $output );
			}
		}

		/**
		 * Open a page's `li` and output its link.
		 *
		 * Adds page_item_has_children / menu-item-has-children and the
		 * decorative caret span to parent pages, marks the current page with
		 * current_page_item / current-menu-item and `aria-current="page"`, and
		 * marks its parent and ancestors (or the posts page when no page is
		 * current). Classes pass through the core `page_css_class` filter and
		 * the title through `the_title`. The caret is placed after
		 * $args['link_after'], inside the anchor.
		 *
		 * @since 1.0.0
		 *
		 * @see Walker::start_el()
		 *
		 * @param string   $output       Walker output, passed by reference and appended to.
		 * @param \WP_Post $page         Page being rendered.
		 * @param int      $depth        Optional. Depth of the page, relative to its parent. Default 0.
		 * @param array    $args         Optional. wp_page_menu() / wp_list_pages() arguments; uses
		 *                               `pages_with_children`, `link_before` and `link_after`. Default [].
		 * @param int      $current_page Optional. ID of the page being viewed, or 0. Default 0.
		 * @return void
		 */
		public function start_el( &$output, $page, $depth = 0, $args = [], $current_page = 0 ) {
			$css_class = [ 'page_item', 'page-item-' . $page->ID, 'menu-item' ];
			$icon      = '';

			if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
				$css_class[] = 'page_item_has_children';
				$css_class[] = 'menu-item-has-children';

				// Decorative caret rendered inside the anchor, like Astra's page walker.
				// The keyboard/touch toggle is the button added after the link by main.js.
				$icon = '<span class="dropdown-menu-toggle ele-submenu-icon" aria-hidden="true">' . elementify_get_the_svg( 'ui', 'chevron-down', 15 ) . '</span>';
			}

			$aria_current = '';

			if ( ! empty( $current_page ) ) {
				$_current_page = get_post( $current_page );
				if ( $_current_page && in_array( $page->ID, $_current_page->ancestors, true ) ) {
					$css_class[] = 'current_page_ancestor';
					$css_class[] = 'current-menu-ancestor';
				}
				if ( $page->ID === (int) $current_page ) {
					$css_class[]  = 'current_page_item';
					$css_class[]  = 'current-menu-item';
					$aria_current = ' aria-current="page"';
				} elseif ( $_current_page && $page->ID === $_current_page->post_parent ) {
					$css_class[] = 'current_page_parent';
					$css_class[] = 'current-menu-parent';
				}
			} elseif ( (int) get_option( 'page_for_posts' ) === $page->ID ) {
				$css_class[] = 'current_page_parent';
				$css_class[] = 'current-menu-parent';
			}

			$css_classes = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

			$args['link_before'] = empty( $args['link_before'] ) ? '' : $args['link_before'];
			$args['link_after']  = empty( $args['link_after'] ) ? '' : $args['link_after'];

			$output .= sprintf(
				'<li class="%s"><a href="%s" class="menu-link"%s>%s%s%s%s</a>',
				esc_attr( $css_classes ),
				esc_url( get_permalink( $page->ID ) ),
				$aria_current,
				$args['link_before'],
				apply_filters( 'the_title', $page->post_title, $page->ID ),
				$args['link_after'],
				$icon
			);
		}
	}
}

if ( ! class_exists( 'Elementify_Walker_Page' ) ) {
	/**
	 * Global-namespace alias of \Elementify\Inc\Walker_Page.
	 *
	 * Lets code outside the theme namespace reference the walker by its
	 * prefixed class name; it adds no behaviour of its own.
	 *
	 * @since 1.0.0
	 */
	class Elementify_Walker_Page extends \Elementify\Inc\Walker_Page {
	}
}
