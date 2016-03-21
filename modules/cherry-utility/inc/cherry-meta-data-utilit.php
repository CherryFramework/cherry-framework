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
				'visible'	=> 'true',
				'type'		=> 'category',
				'icon'		=> '',
				'prefix'	=> '',
				'delimiter'	=> ' ',
				'before'	=> '<div class="post-terms">',
				'after'		=> '</div>',
				'class'		=> 'post-term',
				'html'		=> '<a href="%1$s" %2$s %3$s rel="category tag">%4$s</a>%5$s',
				'echo'		=> false,
			);
			$args = array_merge( $default_args, $args );
			$html = $before = $after = '';

			if ( 'true' === $args['visible'] ) {
				$html = $args['prefix'] . $args['icon'] ;
				$before = $args['before'];
				$after = $args['after'];

				$terms = get_the_terms( $object, $args['type'] );

				if ( ! $terms || is_wp_error( $terms ) ) {
					return '';
				}

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

			$html = $before . $html . $after;

			if ( ! $args['echo'] ) {
				return $html;
			} else {
				echo $html;
			}
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
				'title'		=> '',
				'class'		=> 'post-author',
				'html'		=> '%1$s<a href="%2$s" %3$s %4$s rel="author">%5$s%6$s</a>',
				'echo'		=> false,
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

			if ( ! $args['echo'] ) {
				return $html;
			} else {
				echo $html;
			}
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
				'icon'		=> '',
				'prefix'	=> '',
				'sufix'		=> array( 'single' => '%s', 'plural' => '%s' ),
				'title'		=> '',
				'class'		=> 'post-comments-count',
				'html'		=> '%1$s<a href="%2$s" %3$s %4$s>%5$s%6$s</a>',
				'echo'		=> false,
			);
			$args = array_merge( $default_args, $args );
			$html = $count = '' ;

			if ( 'true' === $args['visible'] ) {
				$post_type = get_post_type( $object->ID );
				if ( post_type_supports( $post_type, 'comments' ) ) {
					$singular = $args['sufix']['single'];
					$plural = $args['sufix']['plural'];

					$count = sprintf( _n( $singular, $plural, $object->comment_count ), $object->comment_count );
				}

				$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '';
				$title = ( $args['title'] ) ? 'title="' . $args['title'] . '"' : '';
				$link = get_comments_link();

				$html = sprintf( $args['html'], $args['prefix'], $link, $title, $html_class, $args['icon'], $count );
			}

			if ( ! $args['echo'] ) {
				return $html;
			} else {
				echo $html;
			}
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
				'icon'		=> '',
				'prefix'	=> '',
				'title'		=> '',
				'class'		=> 'post-date',
				'html'		=> '%1$s<a href="%2$s" %3$s %4$s ><time datetime="%5$s">%6$s%7$s</time></a>',
				'echo'		=> false,
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
				$link = get_day_link( (int) $date_array[0][0], (int) $date_array[0][1], (int) $date_array[0][2] );

				$html = sprintf( $args['html'], $args['prefix'], $link, $title, $html_class, $time, $args['icon'], $date );
			}

			if ( ! $args['echo'] ) {
				return $html;
			} else {
				echo $html;
			}
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
				'echo'		=> false,
			);
			$args = array_merge( $default_args, $args );
			$html = '' ;

			if ( 'true' === $args['visible'] ) {
				$html_class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;
				$count = sprintf( $args['sufix'], $object->count );

				$html = sprintf( $args['html'], $args['prefix'], $html_class, $count );
			}

			if ( ! $args['echo'] ) {
				return $html;
			} else {
				echo $html;
			}
		}
	}
}
