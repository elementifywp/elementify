<?php
/**
 * Template part for displaying post entry header
 *
 * @package Elementify
 */

$the_post_id = get_the_ID();
?>
<header class="entry-header">
	
	<?php get_template_part( 'template-parts/components/blog/entry-image' ); // entry image ?>

	<?php get_template_part( 'template-parts/components/blog/entry-title' ); // entry title ?>

	<?php get_template_part( 'template-parts/components/blog/entry-meta' ); // entry meta ?>

	<?php // get_template_part( 'template-parts/components/blog/entry-meta', '', [ 'desktop' => ['author', 'date', 'category'] ] ); // entry meta ?>

</header><!-- .entry-header -->
