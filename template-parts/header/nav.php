<?php

/**
 * Template part for displaying a header navigation
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Elementify
 */
?>
<header id="masthead" class="site-header ele-position-relative ele-w-100 ele-site-header">
	<div class="ele-header-row ele-position-absolute-before ele-header-row-main ele-d-flex ele-align-items-center ele-position-absolute-after" data-row="main">
		<div class="ele-container ele-mx-auto ele-position-relative ele-z-10 ele-z-20">
			<div class="ele-builder-column ele-builder-column-2">
				<div class="ele-builder-column-items ele-d-flex ele-flex-wrap ele-builder-col-0">
					<div class="ele-header-logo-wrap ele-d-flex">
						<div class="site-branding ele-site-branding ele-d-flex ele-flex-wrap">
							<div class="ele-site-identity">
								<?php
								the_custom_logo();
								if (is_front_page() && is_home()) :
								?>
									<h1 class="ele-site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
								<?php
								else :
								?>
									<p class="ele-site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></p>
								<?php
								endif;
								$elementify_description = get_bloginfo('description', 'display');
								if ($elementify_description || is_customize_preview()) :
								?>
									<p class="ele-site-description"><?php echo $elementify_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
																	?></p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				<div class="ele-builder-column-items ele-d-flex ele-flex-wrap ele-builder-col-2">
					<div class="ele-header-menu-1-wrap ele-d-flex">
						<?php echo elementify_primary_navigation(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</header><!-- #masthead -->