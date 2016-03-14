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
				'length'	=> 200,
				'class'		=> '',
				'ending'	=> '&hellip;',
				'html'		=> '<h3 %1$s><a href="%2$s" title="%3$s">%4$s</a></h3>',
			);
			$args = array_merge( $default_args, $args );
			$html = '' ;

			if ( '0' !== $args['length'] ) {
				$title = ( 'post' === $type ) ? $object->post_title : $object->name ;
				$title_cut = $this->cut_text( $title, $args['length'], $args['ending'] );

				if ( $title_cut ) {
					$link = ( 'post' === $type ) ? $this->get_post_permalink() : $this->get_term_permalink( $object->term_id );
					$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;

					$html = sprintf( $args['html'], $html_class, $link, $title, $title_cut );
				}
			}

			return $html;
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
				'length'		=> 1000000,
				'class'			=> '',
				'content_type'	=> 'post_content',// post_excerpt, post_content
				'ending'		=> '&hellip;',
				'html'			=> '<p %1$s>%2$s</p>',
			);
			$args = array_merge( $default_args, $args );
			$html = '' ;

			if ( '0' !== $args['length'] ) {
				if ( 'term' === $type ) {
					$args['content_type'] = 'description';
				}
				$text = $object->$args['content_type'];
				$text = $this->cut_text( $text, $args['length'], $args['ending'] );

				if ( $text ) {
					$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;

					$html = sprintf( $args['html'], $html_class, $text );
				}
			}

			return do_shortcode( $html );
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
				'icon'		=> apply_filters( 'cherry_author_icon', '<i class="material-icons">arrow_forward</i>' ),
				'class'		=> 'btn',
				'html'		=> '<a href="%1$s"title="%2$s" %3$s><span class="btn__text">%4$s</span>%5$s</a>',
			);
			$args = array_merge( $default_args, $args );
			$html = '' ;

			if ( 'true' === $args['visible'] ) {
				$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;

				if ( 'term' === $type ) {
					$title = $object->name;
					$link = $this->get_term_permalink( $object->term_id );
				} else {
					$title = $object->post_title;
					$link = $this->get_post_permalink();
				}

				$html = sprintf( $args['html'], $link, $title, $html_class, $args['text'], $args['icon'] );
			}

			return $html;
		}
	}
}
