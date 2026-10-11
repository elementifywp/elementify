<?php
/**
 * Template part for displaying page title
 *
 * @package Elementify
 */

$page_title    = false;
$classes       = [ 'ele-hero-section ele-position-relative ele-position-absolute-after ele-overflow-hidden' ];
$header_preset = [ 'desktop' => '1' ];
if ( is_singular() ) {
	$page_title = true;
	$classes[]  = 'ele-hero-single-post';
	// Posts also show their meta; every singular view (posts, pages, custom post
	// types) shows its featured image. 'thumbnail' is dropped below when none is set.
	$elements = is_single() ? [ 'title', 'breadcrumbs', 'post-meta', 'thumbnail' ] : [ 'title', 'breadcrumbs', 'thumbnail' ];
} else {
	$classes[]      = 'ele-hero-archive-posts';
	$home_enable    = false;
	$blog_enable    = [ 'desktop' => true ];
	$search_enable  = [ 'desktop' => true ];
	$archive_enable = [ 'desktop' => true ];
	$elements       = [ 'title', 'breadcrumbs' ];

	if ( $home_enable && is_home() && is_front_page() ) {
		$page_title = true;
	}
	if ( $blog_enable && is_home() && ! is_front_page() ) {
		$page_title = true;
	}
	if ( $search_enable && is_search() ) {
		$page_title = true;
	}
	if ( $archive_enable && is_archive() ) {
		$page_title = true;
	}
}

if ( $page_title ) : ?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
		data-type="<?php echo esc_attr( $header_preset['desktop'] ); ?>">

		<?php
		if ( ! empty( $elements ) ) {

			// Remove the thumbnail if the conditions are met.
			$key = array_search( 'thumbnail', $elements, true );
			if ( false !== $key ) {
				if ( is_singular() && ! has_post_thumbnail() ) {
					unset( $elements[ $key ] );
				}
			}

			if ( $header_preset && array_key_exists( 'desktop', $header_preset ) && in_array( $header_preset['desktop'], [ '2' ], true ) ) {
				$container_width   = [ 'desktop' => 'default' ];
				$container_classes = [ 'ele-container ele-mx-auto' ];
				if ( in_array( $container_width['desktop'], [ 'narrow', 'wide', 'contain' ], true ) ) {
					$container_classes[] = 'ele-container-' . sanitize_text_field( $container_width['desktop'] );
				}
				echo '<div class="' . esc_attr( implode( ' ', $container_classes ) ) . '">';
			}
			?>

			<header class="entry-header ele-d-flex ele-flex-column">
				<?php
				$content_open = false;
				foreach ( $elements as $key => $value ) {
					// Thumbnail.
					if ( 'thumbnail' === $value ) {
						if ( $content_open ) {
							$content_open = false;
							echo '</div>';
						}
						get_template_part( 'template-parts/components/entry-image' );
					} elseif ( ! $content_open ) {
							$content_open = true;
							echo '<div class="ele-card-content ele-d-flex ele-flex-column ele-flex-grow">';
					}

					// Title.
					if ( 'title' === $value ) {
						$title_tags = [ 'desktop' => 'h2' ];
						if ( is_home() && is_front_page() ) {
							$title_text = [ 'desktop' => ( function_exists( 'is_shop' ) && is_shop() ) ? esc_html__( 'Products', 'elementify' ) : esc_html__( 'Home', 'elementify' ) ];
							if ( $title_text && array_key_exists( 'desktop', $title_text ) ) {
								printf(
									'<%1$s class="page-title ele-w-100">%2$s</%1$s><!-- .page-title -->',
									esc_attr( $title_tags['desktop'] ),
									esc_html( $title_text['desktop'] )
								);
							}
						} elseif ( is_archive() ) {
							printf(
								'<%1$s class="page-title ele-w-100">%2$s</%1$s><!-- .page-title -->',
								esc_attr( $title_tags['desktop'] ),
								wp_kses_post( get_the_archive_title() )
							);
						} elseif ( is_search() ) {
							printf(
								'<%1$s class="page-title ele-w-100">%2$s</%1$s><!-- .page-title -->',
								esc_attr( $title_tags['desktop'] ),
								wp_kses_post( get_search_query() )
							);
						} elseif ( is_singular() ) {
							printf(
								'<%1$s class="page-title ele-w-100">%2$s</%1$s><!-- .page-title -->',
								esc_attr( $title_tags['desktop'] ),
								wp_kses_post( get_the_title() )
							);
						}
					}

					// Description.
					if ( 'excerpt' === $value ) {
						the_archive_description( '<div class="archive-description">', '</div>' );
					}

					// Breadcrumb.
					if ( 'breadcrumbs' === $value ) {
						elementify_breadcrumb();
					}
					// Post meta.
					if ( 'post-meta' === $value ) {
						$args       = [];
						$elements   = [ 'author', 'date', 'comment' ];
						$avatar     = false;
						$date       = [ 'desktop' => 'F j, Y' ];
						$taxonomies = [ 'desktop' => 'cats' ];
						$prefix     = [ 'desktop' => 'none' ];
						$sept       = [ 'desktop' => 'dash' ];
						// Items.
						$args['items'] = ! empty( $elements ) ? map_deep( wp_unslash( $elements ), 'sanitize_text_field' ) : [];
						// Enable avatar.
						if ( $avatar ) {
							$args['avatar-enable'] = true;
						}
						// Date format.
						$args['date-format'] = '' !== $date['desktop'] ? esc_html( $date['desktop'] ) : 'F j, Y';
						// Taxonomy type.
						$args['taxonomy-type'] = '' !== $taxonomies['desktop'] ? esc_html( $taxonomies['desktop'] ) : 'cats';
						// Item prefix.
						$args['items-prefix'] = '' !== $prefix['desktop'] ? esc_html( $prefix['desktop'] ) : 'none';
						// Item separator.
						$args['items-sept'] = '' !== $sept['desktop'] ? esc_html( $sept['desktop'] ) : 'dash';

						get_template_part( 'template-parts/components/entry-meta', null, $args );
					}

					// Taxonomy.
					if ( 'taxonomy' === $value ) {
						$args                 = [];
						$taxonomies           = [ 'desktop' => 'cats' ];
						$prefix               = [ 'desktop' => 'none' ];
						$args['items-prefix'] = '' !== $prefix['desktop'] ? esc_html( $prefix['desktop'] ) : 'none';
						if ( 'cats' === $taxonomies['desktop'] ) {
							get_template_part( 'template-parts/components/entry-cats', null, $args );
						} else {
							get_template_part( 'template-parts/components/entry-tags', null, $args );
						}
					}
				}
				?>
			</header><!-- .entry-header -->

			<?php
			if ( $header_preset && array_key_exists( 'desktop', $header_preset ) && in_array( $header_preset['desktop'], [ '2' ], true ) ) {
				echo '</div><!-- .ele-container --->';
			}
		}
		?>

	</div><!-- .ele-hero-section -->
	<?php
endif;
