<?php
/**
 * Class Cherry Attributes Utilit
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.en.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Attributes_Utilit' ) ) {

	/**
	 * Class Cherry Attributes Utilit
	 */
	class Cherry_Attributes_Utilit extends Cherry_Satellite_Utilit {

		/**
		 * Get post title.
		 *
		 * @since  1.0.0
		 * @param array  $args array of arguments.
		 * @param [type] $type - post, term.
		 * @param int    $id ID of post.
		 * @return string
		 */
		public function get_title( $args = array(), $type = 'post', $id = 0 ) {
			$object = call_user_func( array( $this, 'get_' . $type . '_object' ), $id );

			if ( 'post' === $type && empty( $object->ID ) || 'term' === $type && empty( $object->term_id ) ) {
				return '';
			}

			$default_args = array(
				'visible'      => true,
				'length'       => -1,
				'trimmed_type' => 'word',
				'ending'       => '&hellip;',
				'html'         => '<h3 %1$s><a href="%2$s" %3$s rel="bookmark">%4$s</a></h3>',
				'class'        => '',
				'title'        => '',
				'echo'         => false,
			);
			$args = wp_parse_args( $args, $default_args );
			$html = '' ;

			if ( filter_var( $args['visible'], FILTER_VALIDATE_BOOLEAN ) && 0 !== $args['length'] ) {
				$title = $title_cut = ( 'post' === $type ) ? $object->post_title : $object->name ;
				$title = ( $args['title'] ) ? 'title="' . $args['title'] . '"' : 'title="' . $title . '"' ;
				$title_cut = $this->cut_text( $title_cut, $args['length'], $args['trimmed_type'], $args['ending'] );
				$link = ( 'post' === $type ) ? $this->get_post_permalink() : $this->get_term_permalink( $object->term_id );
				$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;

				$html = sprintf( $args['html'], $html_class, $link, $title, $title_cut );
			}

			return $this->output_method( $html, $args['echo'] );
		}

		/**
		 * Get post excerpt
		 *
		 * @since  1.0.0
		 * @param array  $args array of arguments.
		 * @param [type] $type - post, term.
		 * @param int    $id ID of post.
		 * @return string
		 */
		public function get_content( $args = array(), $type = 'post', $id = 0 ) {
			$object = call_user_func( array( $this, 'get_' . $type . '_object' ), $id );

			if ( 'post' === $type && empty( $object->ID ) || 'term' === $type && empty( $object->term_id ) ) {
				return '';
			}

			$default_args = array(
				'visible'      => true,
				'content_type' => 'post_content',
				'length'       => -1,
				'trimmed_type' => 'word',
				'ending'       => '&hellip;',
				'html'         => '<p %1$s>%2$s</p>',
				'class'        => '',
				'echo'         => false,
			);
			$args = wp_parse_args( $args, $default_args );
			$html = '' ;
			$content_type = $args['content_type'];

			if ( filter_var( $args['visible'], FILTER_VALIDATE_BOOLEAN ) ) {
				if ( 'term' === $type ) {
					$text = $object->description;
				} elseif ( 'post_content' === $content_type || 'post_excerpt' === $content_type && empty( $object->$content_type ) ) {
					$text = get_the_content();
				} else {
					$text = get_the_excerpt();
				}

				$text = $this->cut_text( $text, $args['length'], $args['trimmed_type'], $args['ending'], true );

				if ( $text ) {
					$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;

					$html = sprintf( $args['html'], $html_class, $text );
				}
			}

			$html = apply_filters( 'the_content', $html );

			return $this->output_method( $html, $args['echo'] );
		}

		/**
		 * Get post more button
		 *
		 * @since  1.0.0
		 * @param array  $args array of arguments.
		 * @param [type] $type - post, term.
		 * @param int    $id ID of post.
		 * @return string
		 */
		public function get_button( $args = array(), $type = 'post', $id = 0 ) {
			$object = call_user_func( array( $this, 'get_' . $type . '_object' ), $id );

			if ( 'post' === $type && empty( $object->ID ) || 'term' === $type && empty( $object->term_id ) ) {
				return false;
			}

			$default_args = array(
				'visible' => true,
				'text'    => '',
				'icon'    => '',
				'html'    => '<a href="%1$s" %2$s %3$s><span class="btn__text">%4$s</span>%5$s</a>',
				'class'   => 'btn',
				'title'   => '',
				'echo'    => false,
			);
			$args = wp_parse_args( $args, $default_args );
			$html = '' ;

			if ( filter_var( $args['visible'], FILTER_VALIDATE_BOOLEAN ) ) {

				if ( $args['text'] || $args['icon'] ) {

					$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;
					$text = esc_html( $args['text'] );

					if ( 'term' === $type ) {

						$title = $object->name;
						$link = $this->get_term_permalink( $object->term_id );
					} else {
						$title = $object->post_title;
						$link = $this->get_post_permalink();
					}

					$title = ( $args['title'] ) ? 'title="' . $args['title'] . '"' : 'title="' . $title . '"' ;
					$html = sprintf( $args['html'], $link, $title, $html_class, wp_kses( $text, wp_kses_allowed_html( 'post' ) ), $args['icon'] );
				}
			}

			return $this->output_method( $html, $args['echo'] );
		}
	}
}
