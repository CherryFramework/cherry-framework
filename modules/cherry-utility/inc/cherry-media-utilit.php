<?php
/**
 * Class Cherry Media Utilit
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

if ( ! class_exists( 'Cherry_Media_Utilit' ) ) {

	/**
	 * Class Cherry Media Utilit
	 */
	class Cherry_Media_Utilit extends Cherry_Satellite_Utilit {
		/**
		 * Get post image.
		 *
		 * @return string
		 */
		public function get_image( $args = array(), $type = 'post', $id = 0 ) {
			$object = call_user_func( array( $this, 'get_' . $type . '_object' ), $id );

			if ( 'post' === $type && empty( $object->ID ) || 'term' === $type && empty( $object->term_id ) ) {
				return '';
			}

			$default_args = array(
				'visible'					=> true,
				'size'						=> apply_filters( 'cherry_normal_image_size', 'post-thumbnail' ),
				'mobile_size'				=> apply_filters( 'cherry_mobile_image_size', 'post-thumbnail' ),
				'html'						=> '<a href="%1$s" %2$s ><img src="%3$s" alt="%4$s" %5$s ></a>',
				'class'						=> 'wp-image',
				'placeholder'				=> true,
				'placeholder_background'	=> '000',
				'placeholder_foreground'	=> 'fff',
				'placeholder_title'			=> '',
				'html_tag_suze'				=> true,
				'echo'						=> false,
			);
			$args = wp_parse_args( $args, $default_args );
			$html = '';

			if ( filter_var( $args['visible'], FILTER_VALIDATE_BOOLEAN ) ) {
				if ( 'post' === $type ) {
					$id = $object->ID;
					$thumbnail_id = get_post_thumbnail_id( $id );
					$alt = esc_attr( $object->post_title );
					$link = $this->get_post_permalink();
				} else {
					$id = $object->term_id;
					$thumbnail_id = get_term_meta( $id, $this->args['meta_key']['term_thumb'] , true );
					$alt = esc_attr( $object->name );
					$link = $this->get_term_permalink( $id );
				}

				$size		= wp_is_mobile() ? $args['mobile_size'] : $args['size'];
				$size_array	= $this->get_thumbnail_size_array( $size );

				if ( $thumbnail_id ) {
					$src = wp_get_attachment_image_url( $thumbnail_id, $size );
				} elseif ( filter_var( $args['placeholder'], FILTER_VALIDATE_BOOLEAN ) ) {
					// Place holder defaults attr
					$title = ( $args['placeholder_title'] ) ? $args['placeholder_title'] : $size_array['width'] . 'x' . $size_array['height'] ;
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

				$class			= ( $args['class'] ) ? 'class="' . $args['class'] . '"' : '' ;
				$html_tag_suze	= ( filter_var( $args['html_tag_suze'], FILTER_VALIDATE_BOOLEAN ) ) ? 'width="' . $size_array['width']  . '" height="' . $size_array['height']  . '"' : '' ;

				if ( isset( $src ) ) {
					$html = sprintf( $args['html'], $link, $class, $src, $alt, $html_tag_suze );
				}
			}

			return $this->output_method( $html, $args['echo'] );
		}


		/**
		 * Get post embed
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_video( $args = array(), $id = 0 ) {
			$object = $this->get_post_object( $id );

			if ( empty( $object->ID ) ) {
				return '';
			}

			$default_args = array(
				'visible'		=> true,
				'size'			=> apply_filters( 'cherry_normal_video_size', 'post-thumbnail' ),
				'mobile_size'	=> apply_filters( 'cherry_mobile_video_size', 'post-thumbnail' ),
				'class'			=> 'wp-video',
				'echo'			=> false,
			);
			$args = wp_parse_args( $args, $default_args );
			$html = '';

			if ( filter_var( $args['visible'], FILTER_VALIDATE_BOOLEAN ) ) {
				$size = wp_is_mobile() ? $args['mobile_size'] : $args['size'];
				$size_array = $this->get_thumbnail_size_array( $size );
				$video_url = wp_extract_urls( $object->post_content );

				if ( empty( $video_url ) || ! $video_url ) {
					return;
				}

				$html = wp_oembed_get( $video_url[0], array( 'width' => $size_array['width'] ) );

				if ( ! $html ) {
					$post_thumbnail_id = get_post_thumbnail_id( $object->ID );
					$poster = wp_get_attachment_image_url( $post_thumbnail_id, $size );

					$html = wp_video_shortcode( array( 'src' => $video_url[0], 'width' => '100%', 'height' => '100%', 'poster' => $poster ) );
				}
			}

			return $this->output_method( $html, $args['echo'] );
		}
	}
}
