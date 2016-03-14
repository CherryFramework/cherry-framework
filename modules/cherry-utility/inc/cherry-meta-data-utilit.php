<?php
/**
 * Class Cherry Meta Data Utilit
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

if ( ! class_exists( 'Cherry_Meta_Data_Utilit' ) ) {

	/**
	 * Class Cherry Meta Data Utilit
	 */
	class Cherry_Meta_Data_Utilit extends Cherry_Satellite_Utilit{

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
				'visible'	=> 'true',
				'type'		=> 'category',
				'icon'		=> apply_filters( 'cherry_terms_icon', '<i class="material-icons">bookmark</i>' ),
				'prefix'	=> '',
				'delimiter'	=> ' ',
				'before'	=> '<div class="post-terms">',
				'after'		=> '</div>',
				'class'		=> 'post-term',
				'html'		=> '<a href="%1$s" %2$s %3$s>%4$s</a>%5$s',
			);
			$args = array_merge( $default_args, $args );
			$html = $before = $after = '';

			if ( 'true' === $args['visible'] ) {
				$html = $args['prefix'] . $args['icon'] ;
				$before = $args['before'];
				$after = $args['after'];

				$terms = get_the_terms( $object, $args['type'] );
				$terms_count = count( $terms ) - 1 ;

				foreach ( $terms as $key => $term ) {
					$html_class = 'class="' . $args['class'] . ' ' . $term->slug . ' "';
					$name = $term->name ;
					$title = 'title="' . $name . '"' ;
					$link = get_term_link( $term->term_id , $args['type'] );
					$delimiter = ( $terms_count !== $key ) ? $args['delimiter'] : '' ;

					$html .= sprintf( $args['html'], $link, $title, $html_class, $name, $delimiter );
				}
			}

			return $before . $html . $after;
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
				'icon'		=> apply_filters( 'cherry_author_icon', '<i class="material-icons">person</i>' ),
				'prefix'	=> '',
				'title'		=> '',
				'class'		=> 'post-author',
				'html'		=> '%1$s<a href="%2$s" %3$s %4$s>%5$s%6$s</a>',
			);
			$args = array_merge( $default_args, $args );
			$html = '' ;

			if ( 'true' === $args['visible'] ) {
				$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;
				$title = ( $args['title'] ) ? 'title="' . $args['title'] . '"' : '' ;
				$author = get_the_author();
				$link = get_author_posts_url( $object->post_author );

				$html = sprintf( $args['html'], $args['prefix'], $link, $title, $html_class, $args['icon'],  $author );
			}

			return $html;
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
				'visible'	=> 'true',
				'icon'		=> apply_filters( 'cherry_comment_icon', '<i class="material-icons">chat_bubble_outline</i>' ),
				'prefix'	=> '',
				'sufix'		=> '%s', // _n( '%s comment', '%s comments', $post->comment_count )
				'title'		=> '',
				'class'		=> 'post-comments-count',
				'html'		=> '%1$s<a href="%2$s" %3$s %4$s>%5$s%6$s</a>',
			);
			$args = array_merge( $default_args, $args );
			$html = '' ;

			if ( 'true' === $args['visible'] ) {
				$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;
				$title = ( $args['title'] ) ? 'title="' . $args['title'] . '"' : '' ;
				$link = get_comments_link();
				$count = sprintf( $args['sufix'], $object->comment_count );

				$html = sprintf( $args['html'], $args['prefix'], $link, $title, $html_class, $args['icon'], $count );
			}

			return $html;
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
				'visible'	=> 'true',
				'icon'		=> apply_filters( 'cherry_date_icon', '<i class="material-icons">schedule</i>' ),
				'prefix'	=> '',
				'title'		=> '',
				'class'		=> 'post-date',
				'html'		=> '%1$s<a href="%2$s" %3$s %4$s><time datetime="%5$s">%6$s%7$s</time></a>',
			);
			$args = array_merge( $default_args, $args );
			$html = '' ;

			if ( 'true' === $args['visible'] ) {
				$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;
				$title = ( $args['title'] ) ? 'title="' . $args['title'] . '"' : '' ;
				$post_format = get_option( 'date_format' );
				$time = esc_attr( get_the_time( 'Y-m-d\TH:i:sP' ) );
				$date = get_the_time( $post_format );

				preg_match_all( '/(\d+)/mi', $time, $date_array );
				$link = get_day_link( ( int ) $date_array[0][0], ( int ) $date_array[0][1], ( int ) $date_array[0][2] );

				$html = sprintf( $args['html'], $args['prefix'], $link, $title, $html_class, $time, $args['icon'], $date );
			}

			return $html;
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
				'visible'	=> 'true',
				'class'		=> 'post-count',
				'prefix'	=> '',
				'sufix'		=> '%s', // _n( '%s post', '%s posts', $object->count)
				'html'		=> '%1$s<span %2$s>%3$s</span>',
			);
			$args = array_merge( $default_args, $args );
			$html = '' ;

			if ( 'true' === $args['visible'] ) {
				$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;
				$count = sprintf( $args['sufix'], $object->count );

				$html = sprintf( $args['html'], $args['prefix'], $html_class, $count );
			}

			return $html;
		}
	}
}
