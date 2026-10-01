<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Elementify
 */

namespace Elementify;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<?php
/**
 * Functions hooked into elementify/before_comments action
 */
do_action( 'elementify/before_comments' );
?>

<div id="comments" class="comments-area">

	<?php
	/**
	 * Functions hooked into elementify/comments_top action
	 */
	do_action( 'elementify/comments_top' );
	?>

	<?php
	/**
	 * Functions hooked into elementify/comments action
	 *
	 * @hooked elementify_comments_element - 10
	 */
	do_action( 'elementify/comments' );
	?>

	<?php
	/**
	 * Functions hooked into elementify/comments_bottom action
	 */
	do_action( 'elementify/comments_bottom' );
	?>

</div><!-- #comments -->

<?php
/**
 * Functions hooked into elementify/after_comments action
 */
do_action( 'elementify/after_comments' );
?>