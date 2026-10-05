<?php

/**
 * Register Menus
 *
 * @package Elementify
 */

namespace Elementify\Inc;

use Elementify\Inc\Traits\Singleton;

/**
 * Registers the theme's nav menu locations and provides menu lookup helpers.
 */
class Menus {


	use Singleton;

	/**
	 * Register the menu hooks when the singleton is first created.
	 *
	 * @return void
	 */
	public function __construct() {

		// load class.
		$this->setup_hooks();
	}

	/**
	 * Hook menu location registration into `init`.
	 *
	 * @return void
	 */
	private function setup_hooks() {

		/**
		 * Actions.
		 */
		add_action( 'init', [ $this, 'register_menus' ] );
	}

	/**
	 * Register the Primary, Secondary, Sidebar and Footer menu locations
	 * (menu-1 to menu-4).
	 *
	 * @return void
	 */
	public function register_menus() {
		register_nav_menus(
			[
				'menu-1' => esc_html__( 'Primary Menu', 'elementify' ),
				'menu-2' => esc_html__( 'Secondary Menu', 'elementify' ),
				'menu-3' => esc_html__( 'Sidebar Menu', 'elementify' ),
				'menu-4' => esc_html__( 'Footer Menu', 'elementify' ),
			]
		);
	}

	/**
	 * Return the ID of the menu assigned to a theme location.
	 *
	 * @param string $location Menu location slug, e.g. 'menu-1'.
	 * @return int|string Menu term ID, or an empty string when no menu is
	 *                    assigned to the location.
	 */
	public function get_menu_id( $location ) {

		// Get all locations
		$locations = get_nav_menu_locations();

		// Get object id by location.
		$menu_id = ! empty( $locations[ $location ] ) ? $locations[ $location ] : '';

		return ! empty( $menu_id ) ? $menu_id : '';
	}

	/**
	 * Return the menu items whose parent is the given menu item.
	 *
	 * Only direct children are returned; deeper descendants are not.
	 *
	 * @param array $menu_array Menu item objects, e.g. from wp_get_nav_menu_items().
	 * @param int   $parent_id  Menu item ID of the parent (strict integer match
	 *                          against each item's menu_item_parent).
	 * @return array Matching child menu item objects, in their original order.
	 */
	public function get_child_menu_items( $menu_array, $parent_id ) {

		$child_menus = [];

		if ( ! empty( $menu_array ) && is_array( $menu_array ) ) {

			foreach ( $menu_array as $menu ) {
				if ( intval( $menu->menu_item_parent ) === $parent_id ) {
					array_push( $child_menus, $menu );
				}
			}
		}

		return $child_menus;
	}
}
