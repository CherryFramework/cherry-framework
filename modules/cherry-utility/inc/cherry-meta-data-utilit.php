<?php
/**
 * Class Cherry Meta Data Utilit
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

if ( ! class_exists( 'Cherry_Meta_Data_Utilit' ) ) {

	/**
	 * Class Cherry Meta Data Utilit
	 */
	class Cherry_Meta_Data_Utilit extends Cherry_Satellite_Utilit {

		/**
		 * Get post terms
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_terms( $args = array(), $id = 0 ) {
			$object = $this->get_post_object( $id );

			if ( empty( $object->ID ) ) {
				return false;
			}

			$default_args = array(
				'visible'	=> true,
				'type'		=> 'category',
				'icon'		=> '',
				'prefix'	=> '',
				'delimiter'	=> ' ',
				'before'	=> '<div class="post-terms">',
				'after'		=> '</div>',
				'echo'		=> false,
			);
			$args = wp_parse_args( $args, $default_args );
			$html = '';

			if ( filter_var( $args['visible'], FILTER_VALIDATE_BOOLEAN ) ) {
				$prefix = $args['prefix'] . $args['icon'] ;
				$terms = get_the_term_list( $object, $args['type'], $prefix, $args['delimiter'] );

				if ( ! $terms || is_wp_error( $terms ) ) {
					return '';
				}

				$html = $args['before'] . $terms . $args['after'];
			}

			return $this->output_method( $html, $args['echo'] );
		}

		/**
		 * Get post author
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_author( $args = array(), $id = 0 ) {
			$object = $this->get_post_object( $id );

			if ( empty( $object->ID ) ) {
				return false;
			}

			$default_args = array(
				'visible'	=> 'true',
				'icon'		=> '',
				'prefix'	=> '',
				'html'		=> '%1$s<a href="%2$s" %3$s %4$s rel="author">%5$s%6$s</a>',
				'title'		=> '',
				'class'		=> 'post-author',
				'echo'		=> false,
			);
			$args = wp_parse_args( $args, $default_args );
			$html = '' ;

			if ( filter_var( $args['visible'], FILTER_VALIDATE_BOOLEAN ) ) {
				$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '';
				$title      = ( $args['title'] ) ? 'title="' . $args['title'] . '"' : '';
				$author     = get_the_author();
				$link       = get_author_posts_url( $object->post_author );

				$html = sprintf( $args['html'], $args['prefix'], $link, $title, $html_class, $args['icon'], $author );
			}

			return $this->output_method( $html, $args['echo'] );
		}

		/**
		 * Get comment count
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_comment_count( $args = array(), $id = 0 ) {
			$object = $this->get_post_object( $id );

			if ( empty( $object->ID ) ) {
				return false;
			}

			$default_args = array(
				'visible'		=> true,
				'icon'			=> '',
				'prefix'		=> '',
				'suffix'		=> '%s',
				'html'			=> '%1$s<a href="%2$s" %3$s %4$s>%5$s%6$s</a>',
				'title'			=> '',
				'class'			=> 'post-comments-count',
				'echo'			=> false,
			);

			$args = wp_parse_args( $args, $default_args );

			$args['suffix'] = ( isset( $args['sufix'] ) ) ? $args['sufix'] : $args['suffix'];

			$html = $count = '' ;

			if ( filter_var( $args['visible'], FILTER_VALIDATE_BOOLEAN ) ) {
				$post_type = get_post_type( $object->ID );
				if ( post_type_supports( $post_type, 'comments' ) ) {
					$suffix = is_string( $args['suffix'] ) ? $args['suffix'] : translate_nooped_plural( $args['suffix'], $object->comment_count, $args['suffix']['domain'] );
					$count = sprintf( $suffix, $object->comment_count );
				}

				$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '';
				$title = ( $args['title'] ) ? 'title="' . $args['title'] . '"' : '';
				$link = get_comments_link();

				$html = sprintf( $args['html'], $args['prefix'], $link, $title, $html_class, $args['icon'], $count );
			}

			return $this->output_method( $html, $args['echo'] );
		}


		/**
		 * Get post date.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_date( $args = array(), $id = 0 ) {
			$object = $this->get_post_object( $id );

			if ( empty( $object->ID ) ) {
				return false;
			}

			$default_args = array(
				'visible'		=> true,
				'icon'			=> '',
				'prefix'		=> '',
				'html'			=> '%1$s<a href="%2$s" %3$s %4$s ><time datetime="%5$s" title="%5$s">%6$s%7$s</time></a>',
				'title'			=> '',
				'class'			=> 'post-date',
				'date_format'	=> '',
				'human_time'	=> false,
				'echo'			=> false,
			);
			$args = wp_parse_args( $args, $default_args );
			$html = '' ;

			if ( filter_var( $args['visible'], FILTER_VALIDATE_BOOLEAN ) ) {
				$html_class			= ( $args['class'] ) ? 'class="' . esc_attr( $args['class'] ) . '"' : '' ;
				$title				= ( $args['title'] ) ? 'title="' . esc_attr( $args['title'] ) . '"' : '' ;
				$date_post_format	= ( $args['date_format'] ) ? esc_attr( $args['date_format'] ) : get_option( 'date_format' );
				$date				= ( $args['human_time'] ) ? human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) : get_the_date( $date_post_format );
				$time				= get_the_time( 'Y-m-d\TH:i:sP' );

				preg_match_all( '/(\d+)/mi', $time, $date_array );
				$link = get_day_link( (int) $date_array[0][0], (int) $date_array[0][1], (int) $date_array[0][2] );

				$html = sprintf( $args['html'], $args['prefix'], $link, $title, $html_class, $time, $args['icon'], $date );
			}

			return $this->output_method( $html, $args['echo'] );
		}

		/**
		 * Get post count in  term
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_post_count_in_term( $args = array(), $id = 0 ) {
			$object = $this->get_term_object( $id );

			if ( empty( $object->term_id ) ) {
				return false;
			}

			$default_args = array(
				'visible'		=> true,
				'icon'			=> '',
				'prefix'		=> '',
				'suffix'		=> '%s',
				'html'			=> '%1$s<a href="%2$s" %3$s %4$s rel="bookmark">%5$s%6$s</a>',
				'title'			=> '',
				'class'			=> 'post-count',
				'echo'			=> false,
			);
			$args = wp_parse_args( $args, $default_args );
			$args['suffix'] = ( isset( $args['sufix'] ) ) ? $args['sufix'] : $args['suffix'];

			$html = '' ;

			if ( filter_var( $args['visible'], FILTER_VALIDATE_BOOLEAN ) ) {
				$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;
				$name = $object->name ;
				$title = ( $args['title'] ) ? 'title="' . $args['title'] . '"' : 'title="' . $name . '"' ;
				$link = get_term_link( $object->term_id , $object->taxonomy );

				$suffix = is_string( $args['suffix'] ) ? $args['suffix'] : translate_nooped_plural( $args['suffix'], $object->count, $args['suffix']['domain'] );
				$count = sprintf( $suffix, $object->count );

				$html = sprintf( $args['html'], $args['prefix'], $link, $title, $html_class, $args['icon'], $count );
			}

			return $this->output_method( $html, $args['echo'] );
		}
	}
}
