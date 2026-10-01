<?php
/**
 * Template part for displaying a site footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Elementify
 */

$copyright  = __( 'Copyright {copyright} {current_year} {site_title}', 'elementify' );
$copyright  = str_replace( '{copyright}', '&copy;', $copyright );
$copyright  = str_replace( '{current_year}', date_i18n( _x( 'Y', 'copyright date format; check date() on php.net', 'elementify' ) ), $copyright );
$copyright  = str_replace( '{site_title}', get_bloginfo( 'name' ), $copyright );
$copyright .= sprintf(
/* translators: 1: title. */
	esc_html__( ' - WordPress Theme by %1$s', 'elementify' ),
	'<a href="' . esc_url( 'https://www.elementifythemes.com/' ) . '" rel="nofollow noopener" target="_self">' . esc_html__( 'Elementify Themes', 'elementify' ) . '</a>'
);
?>
<footer id="colophon" class="site-footer ele-site-footer">
	<div class="ele-footer-row ele-footer-row-main ele-position-absolute-after ele-d-flex ele-align-items-center" data-row="main">
		<div class="ele-container ele-mx-auto ele-position-relative ele-z-10">
			<div class="ele-builder-column ele-d-flex ele-flex-row ele-flex-wrap ele-builder-column-1">
				<div class="ele-builder-column-items ele-d-flex ele-flex-wrap ele-builder-0 ele-col-12" data-push-left="_sm-0">
					<div class="ele-builder-col-element ele-w-100 ele-d-flex" data-builder-element="copyright">
						<div class="ele-header-copyright-wrap ele-d-flex">
							<div class="ele-copyright">
								<?php echo wp_kses_post( do_shortcode( $copyright ) ); ?>
							</div>
						</div>
					</div>
				</div><!-- .ele-builder-column-items -->
			</div><!-- .ele-builder-column -->                    
		</div><!-- .ele-container -->
	</div><!-- .ele-footer-row -->
</footer><!-- #colophon -->
