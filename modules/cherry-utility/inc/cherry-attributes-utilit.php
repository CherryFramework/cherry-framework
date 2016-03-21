<?php
/**
 * Class Cherry Attributes Utilit
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Attributes_Utilit' ) ) {

	/**
	 * Class Cherry Attributes Utilit
	 */
	class Cherry_Attributes_Utilit extends Cherry_Satellite_Utilit{

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
				return false;
			}

			$default_args = array(
				'visible'		=> 'true',
				'length'		=> 200,
				'trimmed_type'	=> 'word',
				'class'			=> '',
				'ending'		=> '&hellip;',
				'html'			=> '<h3 %1$s><a href="%2$s" title="%3$s" rel="bookmark">%4$s</a></h3>',
				'echo'			=> false,
			);
			$args = array_merge( $default_args, $args );
			$html = '' ;

			if ( '0' !== $args['length'] && 'true' === $args['visible'] ) {
				$title = ( 'post' === $type ) ? $object->post_title : $object->name ;
				$title_cut = $this->cut_text( $title, $args['length'], $args['trimmed_type'], $args['ending'] );

				if ( $title_cut ) {
					$link = ( 'post' === $type ) ? $this->get_post_permalink() : $this->get_term_permalink( $object->term_id );
					$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;

					$html = sprintf( $args['html'], $html_class, $link, $title, $title_cut );
				}
			}

			if ( ! $args['echo'] ) {
				return $html;
			} else {
				echo $html;
			}
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
				return false;
			}

			$default_args = array(
				'visible'		=> 'true',
				'length'		=> '0',
				'trimmed_type'	=> 'word',
				'class'			=> '',
				'content_type'	=> 'post_content',
				'ending'		=> '&hellip;',
				'html'			=> '<p %1$s>%2$s</p>',
				'echo'			=> false,
			);
			$args = array_merge( $default_args, $args );
			$html = '' ;

			if ( 'true' === $args['visible'] ) {
				if ( 'term' === $type ) {
					$text = $object->description;
				} elseif ( 'post_content' === $args['content_type'] || 'post_excerpt' === $args['content_type'] && ! $object->$args['content_type'] ) {
					$text = get_the_content();
				} else {
					$text = get_the_excerpt();
				}

				$length = (int) $args['length'];

				if ( $length ) {
					$text = $this->cut_text( $text, $length, $args['trimmed_type'], $args['ending'] );
				}

				if ( $text ) {
					$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;

					$html = sprintf( $args['html'], $html_class, $text );
				}
			}

			$html = apply_filters( 'the_content', $html );

			if ( ! $args['echo'] ) {
				return $html;
			} else {
				echo $html;
			}
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
				'visible'	=> 'true',
				'text'		=> '',
				'icon'		=> '',
				'class'		=> 'btn',
				'html'		=> '<a href="%1$s" title="%2$s" %3$s><span class="btn__text">%4$s</span>%5$s</a>',
				'echo'		=> false,
			);
			$args = array_merge( $default_args, $args );
			$html = '' ;

			if ( 'true' === $args['visible'] ) {

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

					$html = sprintf( $args['html'], $link, $title, $html_class, wp_kses( $text, wp_kses_allowed_html( 'post' ) ), $args['icon'] );
				}
			}

			if ( ! $args['echo'] ) {
				return $html;
			} else {
				echo $html;
			}
		}
	}
}
