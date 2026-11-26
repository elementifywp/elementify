<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Elementify
 */

?>

<section class="no-results not-found">
	<div class="ele-no-results-inner">
		<span class="ele-no-results-icon" aria-hidden="true">
			<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
		</span>

		<header class="page-header">
			<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'elementify' ); ?></h1>
		</header><!-- .page-header -->

		<div class="page-content">
			<?php
			if ( is_home() && current_user_can( 'publish_posts' ) ) :

				printf(
					'<p>' . wp_kses(
						/* translators: 1: link to WP admin new post page. */
						__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'elementify' ),
						[
							'a' => [
								'href' => [],
							],
						]
					) . '</p>',
					esc_url( admin_url( 'post-new.php' ) )
				);

			elseif ( is_search() ) :
				$elementify_search_query = get_search_query();
				?>

				<p>
					<?php
					if ( '' !== $elementify_search_query ) {
						printf(
							/* translators: %s: the search terms. */
							esc_html__( 'Sorry, nothing matched %s. Please try again with some different keywords.', 'elementify' ),
							'<strong class="ele-no-results-query">&ldquo;' . esc_html( $elementify_search_query ) . '&rdquo;</strong>'
						);
					} else {
						esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'elementify' );
					}
					?>
				</p>
				<?php
				get_search_form();

			else :
				?>

				<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'elementify' ); ?></p>
				<?php
				get_search_form();

			endif;
			?>

			<?php if ( ! ( is_home() && current_user_can( 'publish_posts' ) ) ) : ?>
				<p class="ele-no-results-home">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the homepage', 'elementify' ); ?></a>
				</p>
			<?php endif; ?>
		</div><!-- .page-content -->
	</div>
</section><!-- .no-results -->
