<?php
/**
 * Template part for displaying post entry header
 *
 * @package Elementify
 */

$the_post_id = get_the_ID();
?>
<header class="entry-header">
	
	<?php get_template_part( 'template-parts/components/blog/entry-image' ); // Entry image. ?>

	<?php get_template_part( 'template-parts/components/blog/entry-title' ); // Entry title. ?>

	<?php get_template_part( 'template-parts/components/blog/entry-meta' ); // Entry meta. ?>

</header><!-- .entry-header -->
