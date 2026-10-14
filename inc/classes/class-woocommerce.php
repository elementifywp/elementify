<?php
/**
 * WooCommerce compatibility.
 *
 * @package Elementify
 */

namespace Elementify\Inc;

use Elementify\Inc\Traits\Singleton;

/**
 * Makes the theme's content shell, sidebar and styling wrap WooCommerce.
 *
 * The class is bootstrapped from functions.php only when the WooCommerce
 * plugin is active, so none of these hooks exist on a plain install. The
 * integration follows the same pattern as Astra, Blocksy and Kadence:
 * WooCommerce keeps rendering its own templates on top of the theme's
 * header/footer, and the theme swaps WooCommerce's default content wrapper
 * for its own #content / .ele-container / #primary shell (and its page-title
 * band) so store pages look like the rest of the site.
 */
class WooCommerce {

	use Singleton;

	/**
	 * Register the WooCommerce hooks when the singleton is first created.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Hook the theme-support, wrapper, sidebar and asset registrations.
	 *
	 * @return void
	 */
	private function setup_hooks() {

		/**
		 * Actions.
		 */
		add_action( 'after_setup_theme', [ $this, 'theme_support' ] );
		add_action( 'init', [ $this, 'setup' ] );
		add_action( 'widgets_init', [ $this, 'register_sidebars' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ], 20 );

		/**
		 * Filters.
		 */
		add_filter( 'body_class', [ $this, 'body_class' ] );
		add_filter( 'elementify_get_sidebar_id', [ $this, 'single_product_sidebar' ] );
		add_filter( 'woocommerce_output_related_products_args', [ $this, 'related_products_args' ] );
		add_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'cart_link_fragment' ] );

		/*
		 * Replace WooCommerce's default stylesheets with the theme's own, so a
		 * WooCommerce core update cannot restyle the store.
		 *
		 * @link https://woocommerce.com/document/disable-the-default-stylesheet/
		 */
		add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
	}

	/**
	 * Declare theme support for WooCommerce and its product gallery features.
	 *
	 * Sets the catalogue and single-product image widths and the shop grid
	 * defaults shown under Customizer > WooCommerce > Product Catalog.
	 *
	 * @link https://github.com/woocommerce/woocommerce/wiki/Declaring-WooCommerce-support-in-themes
	 * @link https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)
	 *
	 * @action after_setup_theme
	 *
	 * @return void
	 */
	public function theme_support() {

		add_theme_support(
			'woocommerce',
			[
				'thumbnail_image_width' => 300,
				'single_image_width'    => 600,
				'product_grid'          => [
					'default_rows'    => 3,
					'min_rows'        => 1,
					'default_columns' => 4,
					'min_columns'     => 1,
					'max_columns'     => 6,
				],
			]
		);
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}

	/**
	 * Swap WooCommerce's default markup hooks for the theme's own.
	 *
	 * @action init
	 *
	 * @return void
	 */
	public function setup() {

		// The theme's page-title band replaces WooCommerce's shop title.
		add_filter( 'woocommerce_show_page_title', '__return_false' );

		// The theme renders its own breadcrumbs.
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

		// Replace WooCommerce's wrapper and sidebar with the theme shell.
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

		add_action( 'woocommerce_before_main_content', [ $this, 'content_wrapper_start' ], 10 );
		add_action( 'woocommerce_after_main_content', [ $this, 'content_wrapper_end' ], 10 );

		// Theme pagination replaces WooCommerce's below product loops.
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
		add_action( 'woocommerce_after_shop_loop', [ $this, 'pagination' ], 10 );

		// Group the result count and sorting dropdown into one toolbar row.
		add_action( 'woocommerce_before_shop_loop', [ $this, 'shop_toolbar_start' ], 15 );
		add_action( 'woocommerce_before_shop_loop', [ $this, 'shop_toolbar_end' ], 35 );

		// Wrap the sale badge and thumbnail so the card image can be cropped and zoomed.
		add_action( 'woocommerce_before_shop_loop_item_title', [ $this, 'product_media_start' ], 9 );
		add_action( 'woocommerce_before_shop_loop_item_title', [ $this, 'product_media_end' ], 11 );

		// Wrap the gallery and summary so the summary only sticks within that row.
		add_action( 'woocommerce_before_single_product_summary', [ $this, 'product_hero_start' ], 5 );
		add_action( 'woocommerce_after_single_product_summary', [ $this, 'product_hero_end' ], 5 );

		// A single product carries its own title, so drop the theme hero there.
		add_action( 'elementify_before_content', [ $this, 'single_product_suppress_hero' ], 5 );
		add_action( 'woocommerce_before_single_product', [ $this, 'single_product_breadcrumb' ], 5 );
	}

	/**
	 * Open the theme's content shell around WooCommerce output.
	 *
	 * Reuses the theme's own content-wrapper callbacks (which open #content,
	 * .ele-container and #primary, and print the page-title band) so store
	 * pages share the site's layout.
	 *
	 * @return void
	 */
	public function content_wrapper_start() {

		do_action( 'elementify_before_content' );

		echo '<main id="main" class="site-main ele-woocommerce-main">';
	}

	/**
	 * Close the theme's content shell around WooCommerce output.
	 *
	 * @return void
	 */
	public function content_wrapper_end() {

		echo '</main><!-- #main -->';

		do_action( 'elementify_after_content' );
	}

	/**
	 * Print the theme's numbered pagination below a product loop.
	 *
	 * Mirrors the arguments woocommerce_pagination() builds, so it works for
	 * the shop and product archives (pretty /page/N/ links) as well as for
	 * paginated product shortcodes and blocks (?product-page=N).
	 *
	 * @return void
	 */
	public function pagination() {

		if ( ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) {
			return;
		}

		$args = [
			'total'              => (int) wc_get_loop_prop( 'total_pages' ),
			'current'            => max( 1, (int) wc_get_loop_prop( 'current_page' ) ),
			'base'               => esc_url_raw( add_query_arg( 'product-page', '%#%', false ) ),
			'format'             => '?product-page=%#%',
			'aria_label'         => __( 'Products', 'elementify' ),
			'screen_reader_text' => __( 'Products navigation', 'elementify' ),
		];

		if ( ! wc_get_loop_prop( 'is_shortcode' ) ) {
			global $wp_rewrite;

			$args['base'] = esc_url_raw( str_replace( '999999999', '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );

			if ( $wp_rewrite->using_permalinks() ) {
				$args['format'] = user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' );

				if ( ! $wp_rewrite->use_trailing_slashes ) {
					$args['format'] = '/' . ltrim( $args['format'], '/' );
				}
			} else {
				$args['format'] = false !== strpos( $args['base'], '?paged=%#%' ) ? '?paged=%#%' : '&paged=%#%';
			}

			$args['base'] = str_replace( $args['format'], '%_%', $args['base'] );
		}

		if ( $args['total'] <= 1 ) {
			return;
		}

		elementify_pagination( $args );
	}

	/**
	 * Open the toolbar row around the result count and sorting dropdown.
	 *
	 * @return void
	 */
	public function shop_toolbar_start() {

		echo '<div class="ele-shop-toolbar">';
	}

	/**
	 * Close the toolbar row opened by shop_toolbar_start().
	 *
	 * @return void
	 */
	public function shop_toolbar_end() {

		echo '</div><!-- .ele-shop-toolbar -->';
	}

	/**
	 * Open the image frame of a product card.
	 *
	 * @return void
	 */
	public function product_media_start() {

		echo '<div class="ele-product-media">';
	}

	/**
	 * Close the image frame opened by product_media_start().
	 *
	 * @return void
	 */
	public function product_media_end() {

		echo '</div>';
	}

	/**
	 * Open the gallery + summary row of a single product.
	 *
	 * Runs before the sale badge (10) and gallery (20); closed after the
	 * summary, before the tabs (10), upsells (15) and related products (20).
	 *
	 * @return void
	 */
	public function product_hero_start() {

		echo '<div class="ele-product-hero">';
	}

	/**
	 * Close the row opened by product_hero_start().
	 *
	 * @return void
	 */
	public function product_hero_end() {

		echo '</div><!-- .ele-product-hero -->';
	}

	/**
	 * Drop the theme's page-title band on single product pages.
	 *
	 * WooCommerce renders the product title, rating, excerpt and gallery in
	 * its own summary, so the theme's singular hero would only duplicate the
	 * title and featured image and add unrelated author/date meta.
	 *
	 * @return void
	 */
	public function single_product_suppress_hero() {

		if ( function_exists( 'is_product' ) && is_product() ) {
			remove_action( 'elementify_before_content', 'elementify_before_content_hero', 15 );
		}
	}

	/**
	 * Print the breadcrumb trail above a single product.
	 *
	 * WooCommerce's own breadcrumb is removed in setup(); this replaces it
	 * with the theme's trail, which is otherwise only printed by the hero
	 * that is suppressed on single product pages.
	 *
	 * @return void
	 */
	public function single_product_breadcrumb() {

		echo '<div class="ele-woocommerce-breadcrumb">';
		elementify_breadcrumb();
		echo '</div><!-- .ele-woocommerce-breadcrumb -->';
	}

	/**
	 * Render single product pages full width, without a sidebar.
	 *
	 * The gallery and summary need the full content width. An id with no
	 * registered widget area makes is_active_sidebar() false, so sidebar.php
	 * prints nothing and the body gets `no-sidebar` / data-sidebar="none".
	 *
	 * @param string $sidebar_id The sidebar id for the current view.
	 * @return string The filtered sidebar id.
	 */
	public function single_product_sidebar( $sidebar_id ) {

		if ( is_product() ) {
			return '';
		}

		return $sidebar_id;
	}

	/**
	 * Register the dedicated WooCommerce widget area.
	 *
	 * @action widgets_init
	 *
	 * @return void
	 */
	public function register_sidebars() {

		register_sidebar(
			[
				'name'          => esc_html__( 'WooCommerce Sidebar', 'elementify' ),
				'id'            => 'sidebar-woocommerce',
				'description'   => esc_html__( 'Shown on shop, product, cart, checkout and account pages.', 'elementify' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			]
		);
	}

	/**
	 * Add the WooCommerce body classes.
	 *
	 * `woocommerce-active` is added on every view while the plugin is active;
	 * `ele-woocommerce` only on WooCommerce screens.
	 *
	 * @param array $classes An array of body classes.
	 * @return array The filtered body classes.
	 */
	public function body_class( $classes ) {

		$classes[] = 'woocommerce-active';

		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			$classes[] = 'ele-woocommerce';
		}

		return $classes;
	}

	/**
	 * Enqueue the WooCommerce integration stylesheet.
	 *
	 * WooCommerce's own stylesheets are disabled (see setup_hooks()), so this
	 * loads on every front-end view: product blocks and shortcodes, the header
	 * cart and star ratings in widgets can all appear outside store screens.
	 * The `star` icon font that the rating styles use is declared inline,
	 * pointing at the copy shipped with the WooCommerce plugin.
	 *
	 * @action wp_enqueue_scripts
	 *
	 * @return void
	 */
	public function enqueue_assets() {

		$asset = $this->get_asset_meta( 'css/woocommerce' );

		wp_enqueue_style(
			'elementify-woocommerce',
			ELEMENTIFY_DIR_URI . '/assets/build/css/woocommerce.css',
			[ 'elementify-main' ],
			$asset['version'],
			'all'
		);
		wp_style_add_data( 'elementify-woocommerce', 'rtl', 'replace' );

		$font_path   = WC()->plugin_url() . '/assets/fonts/';
		$inline_font = sprintf(
			'@font-face{font-family:"star";src:url("%1$s") format("woff"),url("%2$s") format("truetype");font-weight:normal;font-style:normal;font-display:swap;}',
			esc_url_raw( $font_path . 'star.woff' ),
			esc_url_raw( $font_path . 'star.ttf' )
		);

		wp_add_inline_style( 'elementify-woocommerce', $inline_font );
	}

	/**
	 * Limit related products to a single row of three.
	 *
	 * @param array $args Related products query and display args.
	 * @return array The filtered args.
	 */
	public function related_products_args( $args ) {

		return array_merge(
			$args,
			[
				'posts_per_page' => 3,
				'columns'        => 3,
			]
		);
	}

	/**
	 * Refresh the header cart link when products are added via AJAX.
	 *
	 * @param array $fragments Fragments keyed by the selector they replace.
	 * @return array The filtered fragments.
	 */
	public function cart_link_fragment( $fragments ) {

		ob_start();
		$this->cart_link();
		$fragments['a.cart-contents'] = ob_get_clean();

		return $fragments;
	}

	/**
	 * Print the header cart link: a bag icon with an item-count badge.
	 *
	 * The subtotal and item count are also spelled out for screen readers,
	 * since the badge alone is just a number.
	 *
	 * @return void
	 */
	public function cart_link() {

		if ( ! WC()->cart ) {
			return;
		}

		$count = WC()->cart->get_cart_contents_count();

		$item_count_text = sprintf(
			/* translators: %d: number of items in the mini cart. */
			_n( '%d item', '%d items', $count, 'elementify' ),
			$count
		);
		?>
		<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'elementify' ); ?>">
			<?php elementify_the_svg( 'ui', 'shopping-bag', 22 ); ?>
			<span class="count" aria-hidden="true"><?php echo esc_html( number_format_i18n( $count ) ); ?></span>
			<span class="screen-reader-text">
				<?php echo esc_html( $item_count_text ); ?>,
				<span class="amount"><?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?></span>
			</span>
		</a>
		<?php
	}

	/**
	 * Print the header cart: the cart link plus the mini-cart widget.
	 *
	 * Called from template-parts/header/nav.php when WooCommerce is active.
	 *
	 * @return void
	 */
	public function header_cart() {

		$class = is_cart() ? 'current-menu-item' : '';
		?>
		<ul id="site-header-cart" class="site-header-cart">
			<li class="<?php echo esc_attr( $class ); ?>">
				<?php $this->cart_link(); ?>
			</li>
			<li>
				<?php the_widget( 'WC_Widget_Cart', [ 'title' => '' ] ); ?>
			</li>
		</ul>
		<?php
	}

	/**
	 * Read the dependency manifest for the WooCommerce stylesheet.
	 *
	 * Falls back to the theme version when the build has not been run yet.
	 *
	 * @param string $entry Entry path relative to assets/build, without the
	 *                      extension, e.g. 'css/woocommerce'.
	 * @return array{version: string} The cache-busting version.
	 */
	private function get_asset_meta( $entry ) {

		$asset_file = ELEMENTIFY_DIR_PATH . '/assets/build/' . $entry . '.asset.php';
		$asset      = is_readable( $asset_file ) ? require $asset_file : [];

		return [
			'version' => isset( $asset['version'] ) ? $asset['version'] : ELEMENTIFY_VERSION,
		];
	}
}
