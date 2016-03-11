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

if ( ! class_exists( 'Cherry_Media_Utilit' ) ) {

	class Cherry_Media_Utilit extends Cherry_Satellite_Utilit{

		/**
		 * Default args
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $args = array();

		/**
		* Cherry_Satellite_Utilit constructor
		*
		* @since 1.0.0
		*/
		function __construct( $args = array() ) {
			$this->args = array_merge( $this->args, $args );
		}

		/**
		 * Get post image.
		 *
 		 * @since  1.0.0
		 * @return string
		 */
		public function get_image( $args = array(), $type = 'post', $ID = 0 ) {
			$object = call_user_func( array( $this, 'get_' . $type . '_object' ), $ID );

			if ( 'post' === $type && empty($object->ID) || 'term' === $type && empty($object->term_id) ){
				return false;
			}

			$default_args = array(
				'size'						=> apply_filters( 'cherry_normal_image_size', 'post-thumbnail' ),
				'mobile_size'				=> apply_filters( 'cherry_mobile_image_size', 'post-thumbnail' ),
				'class'						=> 'wp-image',
				'html'						=> '<img src="%1$s" alt="%2$s" %3$s %4$s >',
				'placeholder_background'	=> '000',
				'placeholder_foreground'	=> 'fff',
				'placeholder_title'			=> '',
				'html_tag_suze'				=> true,
			);
			$args = array_merge( $default_args, $args );
			$size= wp_is_mobile() ? $args[ 'mobile_size' ] : $args[ 'size' ] ;
			$size_array = $this->get_thumbnail_size_array( $size );

			$class = ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;
			$html_tag_suze = ( $args['html_tag_suze'] ) ? 'width="' . $size_array['width']  . 'px" height="' . $size_array['height']  . 'px"' : '' ;

			if( 'post' === $type ){
				$ID = $object->ID;
				$thumbnail_id = get_post_thumbnail_id( $ID );
				$alt = esc_attr( $object->post_title );
			}else{
				$ID = $object->term_id;
				$thumbnail_id = get_term_meta( $ID, $this->args['meta_key']['term_thumb'] , true );
				$alt = esc_attr( $object->name );
			}

			if( $thumbnail_id ){
				$src = wp_get_attachment_image_url( $thumbnail_id, $size );
			}else{
				// Place holder defaults attr
				$title = ( $args[ 'placeholder_title' ] ) ? $args[ 'placeholder_title' ] : $size_array['width'] . 'x' . $size_array['height'] ;
				$attr = array(
					'width'			=> $size_array['width'],
					'height'		=> $size_array['height'],
					'background'	=> $args['placeholder_background'],
					'foreground'	=> $args['placeholder_foreground'],
					'title'			=> $title,
				);

				$attr = array_map( 'esc_attr', $attr );

				$src = 'http://fakeimg.pl/' . $attr['width'] . 'x' . $attr['height'] . '/'. $attr['background'] .'/'. $attr['foreground'] . '/?text=' . $attr['title'] . '';
			}

			$html = sprintf( $args['html'], $src, $alt , $class, $html_tag_suze );

			return $html;
		}


		/**
		 * Get post embed
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_video( $args = array(), $ID = 0 ) {
			$object = $this->get_post_object( $ID );

			if ( empty( $object->ID ) ){
				return false;
			}

			$default_args = array(
				'size'						=> apply_filters( 'cherry_normal_video_size', 'post-thumbnail' ),
				'mobile_size'				=> apply_filters( 'cherry_mobile_video_size', 'post-thumbnail' ),
				'class'						=> 'wp-video',
			);
			$args = array_merge( $default_args, $args );
			$size= wp_is_mobile() ? $args[ 'mobile_size' ] : $args[ 'size' ] ;
			$size_array = $this->get_thumbnail_size_array( $size );
			$video_url = wp_extract_urls( $object->post_content );

			if( empty( $video_url ) || !$video_url ){
				return;
			}

			$video = wp_oembed_get( $video_url[ 0 ], array( 'width' => $size_array['width'] ) );

			if( !$video ){
				$post_thumbnail_id = get_post_thumbnail_id( $object->ID );
				$poster = wp_get_attachment_image_url( $post_thumbnail_id, $size );

				$video = wp_video_shortcode( array( 'src' => $video_url[ 0 ], 'width' => '100%', 'height' => '100%', 'poster' => $poster ) );
			}

			return $video;
		}
	}
}