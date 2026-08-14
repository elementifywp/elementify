<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Elementify
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> data-link-type="2">

<?php
/**
 * Hook for entry content.
 *
 * @hooked elementify/loop/post/entry_header - 10
 * @hooked elementify/loop/post/entry_content - 15
 * @hooked elementify/loop/post/entry_footer - 20
 */
do_action( 'elementify/loop/post/entry_content' );
?>
</article><!-- #post-<?php the_ID(); ?> -->