<?php
/**
 * Class Cherry Media Utilit
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

			if ( is_callable( array( $this, 'get_' . $type . '_object' ) ) ) {
				$object = call_user_func( array( $this, 'get_' . $type . '_object' ), $id );

				if ( 'post' === $type && empty( $object->ID ) || 'term' === $type && empty( $object->term_id ) ) {
					return '';
				}
			}

			$default_args = array(
				'visible'                => true,
				'size'                   => apply_filters( 'cherry_normal_image_size', 'post-thumbnail' ),
				'mobile_size'            => apply_filters( 'cherry_mobile_image_size', 'post-thumbnail' ),
				'html'                   => '<a href="%1$s" %2$s ><img src="%3$s" alt="%4$s" %5$s ></a>',
				'class'                  => 'wp-image',
				'placeholder'            => true,
				'placeholder_background' => '000',
				'placeholder_foreground' => 'fff',
				'placeholder_title'      => '',
				'html_tag_suze'          => true,
				'echo'                   => false,
			);
			$args = wp_parse_args( $args, $default_args );
			$html = '';

			if ( filter_var( $args['visible'], FILTER_VALIDATE_BOOLEAN ) ) {

				$intermediate_image_sizes   = get_intermediate_image_sizes();
				$intermediate_image_sizes[] = 'full';

				$size = wp_is_mobile() ? $args['mobile_size'] : $args['size'];
				$size = in_array( $size, $intermediate_image_sizes ) ? $size : 'post-thumbnail';

				// Placeholder defaults attr.
				$size_array = $this->get_thumbnail_size_array( $size );

				switch ( $type ) {
					case 'post':
						$id           = $object->ID;
						$thumbnail_id = get_post_thumbnail_id( $id );
						$alt          = esc_attr( $object->post_title );
						$link         = $this->get_post_permalink();
					break;

					case 'term':
						$id           = $object->term_id;
						$thumbnail_id = get_term_meta( $id, $this->args['meta_key']['term_thumb'] , true );
						$alt          = esc_attr( $object->name );
						$link         = $this->get_term_permalink( $id );
					break;

					case 'attachment':
						$thumbnail_id = $id;
						$alt          = get_the_title( $thumbnail_id );
						$link         = wp_get_attachment_image_url( $thumbnail_id, $size );
					break;
				}

				if ( $thumbnail_id ) {
					$image_data = wp_get_attachment_image_src( $thumbnail_id, $size );
					$src        = $image_data[0];

					$size_array['width']  = $image_data[1];
					$size_array['height'] = $image_data[2];

				} elseif ( filter_var( $args['placeholder'], FILTER_VALIDATE_BOOLEAN ) ) {
					$title = ( $args['placeholder_title'] ) ? $args['placeholder_title'] : $size_array['width'] . 'x' . $size_array['height'];
					$attr = array(
						'width'      => $size_array['width'],
						'height'     => $size_array['height'],
						'background' => $args['placeholder_background'],
						'foreground' => $args['placeholder_foreground'],
						'title'      => $title,
					);

					$attr = array_map( 'esc_attr', $attr );

					$width  = ( 4000 < intval( $attr['width'] ) )  ? 4000 : intval( $attr['width'] );
					$height = ( 4000 < intval( $attr['height'] ) ) ? 4000 : intval( $attr['height'] );

					$src = $this->get_placeholder_url( array(
						'width'      => $width,
						'height'     => $height,
						'background' => $attr['background'],
						'foreground' => $attr['foreground'],
						'title'      => $attr['title'],
					) );
				}

				$class         = ( $args['class'] ) ? 'class="' . esc_attr( $args['class'] ) . '"' : '';
				$html_tag_suze = ( filter_var( $args['html_tag_suze'], FILTER_VALIDATE_BOOLEAN ) ) ? 'width="' . $size_array['width'] . '" height="' . $size_array['height'] . '"' : '';

				if ( isset( $src ) ) {
					$html = sprintf( $args['html'], esc_url( $link ), $class, esc_url( $src ), esc_attr( $alt ), $html_tag_suze );
				}
			}

			return $this->output_method( $html, $args['echo'] );
		}

		/**
		 * Get placeholder image URL
		 *
		 * @param array $args Image argumnets.
		 * @return string
		 */
		public function get_placeholder_url( $args = array() ) {

			$args = wp_parse_args( $args, array(
				'width'      => 300,
				'height'     => 300,
				'background' => '000',
				'foreground' => 'fff',
				'title'      => '',
			) );

			$args      = array_map( 'urlencode', $args );
			$base_url  = 'http://fakeimg.pl';
			$format    = '%1$s/%2$sx%3$s/%4$s/%5$s/?text=%6$s';
			$image_url = sprintf(
				$format,
				$base_url, $args['width'], $args['height'], $args['background'], $args['foreground'], $args['title']
			);

			/**
			 * Filter image placeholder URL
			 *
			 * @param string $image_url Default URL.
			 * @param string $args      Image arguments.
			 */
			return apply_filters( 'cherry_utility_placeholder_image_url', esc_url( $image_url ), $args );
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

			if ( ! filter_var( $args['visible'], FILTER_VALIDATE_BOOLEAN ) ) {
				return '';
			}

			$size = wp_is_mobile() ? $args['mobile_size'] : $args['size'];
			$size_array = $this->get_thumbnail_size_array( $size );
			$url_array = wp_extract_urls( $object->post_content );

			if ( empty( $url_array ) || ! $url_array ) {
				return '';
			}

			$html = wp_oembed_get( $url_array[0], array(
				'width' => $size_array['width'],
			) );

			if ( ! $html ) {
				$url_array = $this->sorted_array( $url_array );

				if ( empty( $url_array['video'] ) ) {
					return '';
				}

				if ( empty( $url_array['poster'] ) ) {
					$post_thumbnail_id = get_post_thumbnail_id( $object->ID );
					$url_array['poster'] = wp_get_attachment_image_url( $post_thumbnail_id, $size );
				}

				$shortcode_attr = array(
					'width' => '100%',
					'height' => '100%',
					'poster' => $url_array['poster'],
				);

				$shortcode_attr = wp_parse_args( $url_array['video'], $shortcode_attr );

				$html = wp_video_shortcode( $shortcode_attr );
			}

			return $this->output_method( $html, $args['echo'] );
		}

		/**
		 * Sorted video and poster url
		 *
		 * @since  1.0.0
		 * @return array
		 */
		private function sorted_array( $array ) {
			$output_array = array(
				'video'		=> array(),
				'poster'	=> '',
			);

			$default_types = wp_get_video_extensions();
			$pattern = '/.(' . implode( '|', $default_types ) . ')/im';

			foreach ( $array as $url ) {
				foreach ( $default_types as $type ) {
					if ( strpos( $url, $type ) ) {
						$output_array['video'][ $type ] = $url;
					}
				}
				if ( ! preg_match( $pattern, $url ) ) {
					$output_array['poster'] = $url;
				}
			}

			return $output_array;
		}
	}
}
