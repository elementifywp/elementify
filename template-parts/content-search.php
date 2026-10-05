<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Elementify
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'ele-column' ); ?>>

	<?php
	/**
	 * Hook for entry content.
	 *
	 * @hooked elementify/loop/entry_post_card - 20
	 */
	do_action( 'elementify/loop/entry_content' );
	?>
</article><!-- #post-<?php the_ID(); ?> -->