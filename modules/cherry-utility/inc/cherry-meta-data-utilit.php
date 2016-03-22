<?php
/**
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Meta_Data_Utilit' ) ) {

	class Cherry_Meta_Data_Utilit extends Cherry_Satellite_Utilit{

		/**
		 * Get post terms
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_terms( $args = array() , $ID = 0 ) {
			$object =  $this->get_post_object( $ID );

			if ( empty( $object->ID ) ) {
				return false;
			}

			$default_args = array(
				'visible'	=> true,
				'type'		=> 'category',
				'icon'		=> '',//apply_filters( 'cherry_terms_icon', '<i class="material-icons">bookmark</i>' )
				'prefix'	=> '',
				'delimiter'	=> ' ',
				'before'	=> '<div class="post-terms">',
				'after'		=> '</div>',
				'html'		=> '<a href="%1$s" %2$s %3$s rel="tag">%4$s</a>%5$s',
				'title'		=> '',
				'class'		=> 'post-term',
				'echo'		=> false,
			);
			$args = array_merge( $default_args, $args );
			$html = $before = $after = '';

			if ( $args['visible'] ) {
				$html = $args['prefix'] . $args['icon'] ;
				$before = $args['before'];
				$after = $args['after'];

				//$terms = get_the_term_list ( $object, $args['type'] );
				$terms = get_the_terms( $object, $args['type'] );

				if ( ! $terms || is_wp_error( $terms ) ) {
					return '';
				}

				$terms_count = count( $terms ) - 1 ;

				foreach ( $terms as $key => $term ) {
					$html_class= 'class="' . $args['class'] . ' ' . $term->slug . ' "';
					$name = $term->name ;
					$title =  ( $args['title'] ) ? 'title="' . $args['title'] . '"' : 'title="' . $name . '"' ;
					$link = get_term_link( $term->term_id , $args['type'] );
					$delimiter = ( $terms_count !== $key ) ? $args['delimiter'] : '' ;

					$html .= sprintf( $args['html'], $link, $title, $html_class, $name, $delimiter );
				}
			}

			$html = $before . $html . $after;

			if ( ! $args[ 'echo' ] ) {
				return $html;
			}else{
				echo $html;
			}
		}

		/**
		 * Get post author
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_author( $args = array() , $ID = 0 ) {
			$object =  $this->get_post_object( $ID );

			if ( empty( $object->ID ) ) {
				return false;
			}

			$default_args = array(
				'visible'	=> true,
				'icon'		=> '',//apply_filters( 'cherry_author_icon', '<i class="material-icons">person</i>' )
				'prefix'	=> '',
				'html'		=> '%1$s<a href="%2$s" %3$s %4$s rel="author">%5$s%6$s</a>',
				'title'		=> '',
				'class'		=> 'post-author',
				'echo'		=> false,
			);
			$args = array_merge( $default_args, $args );
			$html = '' ;

			if ( $args['visible'] ) {
				$html_class=  ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;
				$title=  ( $args['title'] ) ? 'title="' . $args['title'] . '"' : '' ;
				$author = get_the_author();
				$link = get_author_posts_url( $object->post_author );

				$html = sprintf( $args['html'], $args['prefix'], $link, $title, $html_class, $args['icon'], $author );
			}

			if ( ! $args[ 'echo' ] ) {
				return $html;
			}else{
				echo $html;
			}
		}

		/**
		 * Get comment count
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_comment_count( $args = array() , $ID = 0 ) {
			$object =  $this->get_post_object( $ID );

			if ( empty( $object->ID ) ) {
				return false;
			}

			$default_args = array(
				'visible'		=> true,
				'icon'			=> '',//apply_filters( 'cherry_comment_icon', '<i class="material-icons">chat_bubble_outline</i>' )
				'prefix'		=> '',
				'sufix'			=> '%s', //_n_noop( '%s comment', '%s comments')
				'html'			=> '%1$s<a href="%2$s" %3$s %4$s>%5$s%6$s</a>',
				'title'			=> '',
				'class'			=> 'post-comments-count',
				'echo'			=> false,
			);
			$args = array_merge( $default_args, $args );
			$html = $count = '' ;

			if ( $args['visible'] ) {
				$post_type = get_post_type( $object->ID );
				if ( post_type_supports( $post_type, 'comments' ) ) {
					$sufix = is_string( $args['sufix'] ) ? $args['sufix'] : translate_nooped_plural( $args['sufix'], $object->comment_count, $args['sufix']['domain'] ) ;
					$count = sprintf( $sufix, $object->comment_count );
				}

				$html_class=  ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;
				$title=  ( $args['title'] ) ? 'title="' . $args['title'] . '"' : '' ;
				$link = get_comments_link();

				$html = sprintf( $args['html'], $args['prefix'], $link, $title, $html_class, $args['icon'], $count );
			}

			if ( ! $args[ 'echo' ] ) {
				return $html;
			}else{
				echo $html;
			}
		}


		/**
		 * Get post date.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_date( $args = array() , $ID = 0 ) {
			$object =  $this->get_post_object( $ID );

			if ( empty( $object->ID ) ) {
				return false;
			}

			$default_args = array(
				'visible'	=> true,
				'icon'		=> '',//apply_filters( 'cherry_date_icon', '<i class="material-icons">schedule</i>' )
				'prefix'	=> '',
				'html'		=> '%1$s<a href="%2$s" %3$s %4$s ><time pubdate datetime="%5$s">%6$s%7$s</time></a>',
				'title'		=> '',
				'class'		=> 'post-date',
				'echo'		=> false,
			);
			$args = array_merge( $default_args, $args );
			$html = '' ;

			if ( $args['visible'] ) {
				$html_class=  ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;
				$title=  ( $args['title'] ) ? 'title="' . $args['title'] . '"' : '' ;
				$post_format = get_option( 'date_format' );
				$time = esc_attr( get_the_time( 'Y-m-d\TH:i:sP' ) );
				$date = get_the_time( $post_format );

				preg_match_all('/(\d+)/mi', $time, $date_array );
				$link = get_day_link( ( int ) $date_array[0][0], ( int ) $date_array[0][1], ( int ) $date_array[0][2] );

				$html = sprintf( $args['html'], $args['prefix'], $link, $title, $html_class, $time, $args['icon'], $date );
			}

			if ( ! $args[ 'echo' ] ) {
				return $html;
			}else{
				echo $html;
			}
		}

		/**
		 * Get post count in  term
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_post_count_in_term( $args = array() , $ID = 0 ) {
			$object = $this->get_term_object( $ID );

			if ( empty( $object->term_id ) ){
				return false;
			}

			$default_args = array(
				'visible'		=> true,
				'icon'			=> '',//apply_filters( 'cherry_date_icon', '<i class="material-icons">schedule</i>' )
				'prefix'		=> '',
				'sufix'			=> '%s', //_n_noop( '%s comment', '%s comments')
				'html'			=> '%1$s<a href="%2$s" %3$s %4$s rel="bookmark">%5$s%6$s</a>',
				'title'			=> '',
				'class'			=> 'post-count',
				'echo'			=> false,
			);
			$args = array_merge( $default_args, $args );
			$html = '' ;

			if ( $args['visible'] ) {
				$html_class=  ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;
				$name = $object->name ;
				$title = ( $args['title'] ) ? 'title="' . $args['title'] . '"' : 'title="' . $name . '"' ;
				$link = get_term_link( $object->term_id , $object->taxonomy );

				$sufix = is_string( $args['sufix'] ) ? $args['sufix'] : translate_nooped_plural( $args['sufix'], $object->count, $args['sufix']['domain'] ) ;
				$count = sprintf( $sufix, $object->count );

				$html = sprintf( $args['html'], $args['prefix'], $link, $title, $html_class, $args['icon'], $count );
			}

			if ( ! $args[ 'echo' ] ) {
				return $html;
			}else{
				echo $html;
			}
		}
	}
}