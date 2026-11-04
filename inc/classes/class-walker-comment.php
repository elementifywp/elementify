<?php
/**
 * Comment walker that adds schema.org microdata.
 *
 * @package Elementify
 */

namespace Elementify\Inc;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Guarded so a child theme or plugin can declare its own walker first.
if ( ! class_exists( 'Elementify\Inc\Walker_Comment' ) ) {

	/**
	 * Core's HTML5 comment markup, annotated as a schema.org Comment.
	 *
	 * Rather than copying core's html5_comment() template, this captures its
	 * output and adds the microdata attributes to the elements core already
	 * prints, so markup and class names stay identical to core (and to what
	 * main.css styles) across WordPress versions. An element that core stops
	 * printing simply loses its attribute.
	 */
	class Walker_Comment extends \Walker_Comment {

		/**
		 * Outputs a comment in the HTML5 format, with microdata.
		 *
		 * @param \WP_Comment $comment Comment to display.
		 * @param int         $depth   Depth of the current comment.
		 * @param array       $args    An array of arguments.
		 * @return void
		 */
		protected function html5_comment( $comment, $depth, $args ) {
			if ( ! Utils::has_schema_org_markup() ) {
				parent::html5_comment( $comment, $depth, $args );
				return;
			}

			ob_start();
			parent::html5_comment( $comment, $depth, $args );
			$html = (string) ob_get_clean();

			$replacements = [
				// The Comment item, as a "comment" of the enclosing post or page.
				'class="comment-body"'         => 'class="comment-body" ' . Utils::schema_org_definitions( 'comment-body' ),
				// Its author.
				'class="comment-author vcard"' => 'class="comment-author vcard" ' . Utils::schema_org_definitions( 'comment-author' ),
				'<b class="fn">'               => '<b class="fn" ' . Utils::schema_org_definitions( 'author-name' ) . '>',
				// The author's website, when they gave one. Older cores quote
				// this attribute with single quotes.
				'class="url"'                  => 'class="url" ' . Utils::schema_org_definitions( 'author-url' ),
				"class='url'"                  => "class='url' " . Utils::schema_org_definitions( 'author-url' ),
				// Date and body.
				'<time datetime='              => '<time ' . Utils::schema_org_definitions( 'comment-date' ) . ' datetime=',
				'class="comment-content"'      => 'class="comment-content" ' . Utils::schema_org_definitions( 'comment-text' ),
			];

			foreach ( $replacements as $search => $replace ) {
				$position = strpos( $html, $search );

				// Only the first match: nested replies are rendered by their
				// own html5_comment() call.
				if ( false !== $position ) {
					$html = substr_replace( $html, $replace, $position, strlen( $search ) );
				}
			}

			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core's escaped comment markup plus escaped microdata attributes.
		}
	}
}
