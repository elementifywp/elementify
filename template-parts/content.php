<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Elementify
 */

use Elementify\Inc\Utils;

$the_post_id = get_the_ID();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ele-column' ); ?>>

	<?php
	/**
	 * Hook for entry content.
	 *
	 * @hooked elementify_loop_entry_post_card - 20
	 */
	do_action( 'elementify_loop_entry_content' );
	?>

</article><!-- #post-<?php the_ID(); ?> -->