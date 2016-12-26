<?php
/**
 * API functions for post formats specific content
 * Module Name: Post Formats API
 * Description: API for post formats specific content
 * Version: 1.1.2
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Cherry_Framework
 * @subpackage Modules
 * @version    1.1.2
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Post_Formats_Api' ) ) {

	/**
	 * API functions for post formats specific content
	 */
	class Cherry_Post_Formats_Api {

		/**
		 * Module version
		 *
		 * @var string
		 */
		public $module_version = '1.1.0';

		/**
		 * Module slug
		 *
		 * @var string
		 */
		public $module_slug = 'cherry-post-formats-api';

		/**
		 * Module arguments
		 *
		 * @var array
		 */
		public $args = array();

		/**
		 * Core instance
		 *
		 * @var object
		 */
		public $core = null;

		/**
		 * Constructor for the module
		 */
		function __construct( $core, $args ) {

			$this->core = $core;
			$this->args = wp_parse_args( $args, array(
				'rewrite_default_gallery' => false,
				'gallery_args'            => array(),
				'image_args'              => array(),
				'link_args'               => array(),
				'video_args'              => array(),
			) );

			$this->args['gallery_args'] = wp_parse_args( $this->args['gallery_args'], array(
				'base_class'     => 'post-gallery',
				'container'      => '<div class="%2$s" %3$s>%1$s</div>',
				'slide'          => '<figure class="%2$s">%1$s</figure>',
				'slide_item'     => '<a href="%3$s" %4$s>%1$s</a>%2$s',
				'slide_item_alt' => '%1$s%2$s',
				'img_class'      => '',
				'size'           => 'post-thumbnail',
				'link'           => 'file',
				'slider'         => false,
				'slider_init'    => false,
				'slider_handle'  => false,
				'popup'          => false,
				'popup_init'     => false,
				'popup_handle'   => false,
			) );

			$this->args['image_args'] = wp_parse_args( $this->args['image_args'], array(
				'base_class'   => 'post-thumbnail',
				'container'    => '<figure class="%2$s">%1$s</figure>',
				'item'         => '<a href="%3$s" class="%2$s" %4$s>%1$s</a>',
				'size'         => 'post-thumbnail',
				'link'         => 'file',
				'popup'        => false,
				'popup_init'   => false,
				'popup_handle' => false,
			) );

			$this->args['link_args'] = wp_parse_args( $this->args['link_args'], array(
				'render' => false,
				'class'  => '',
			) );

			$this->args['video_args'] = wp_parse_args( $this->args['video_args'], array(
				'width'  => 600,
				'height' => 400,
			) );

			$formats = array(
				'image',
				'gallery',
				'video',
				'audio',
				'link',
				'quote',
				'status',
			);

			// Register default post formats
			foreach ( $formats as $format ) {
				add_action( 'cherry_post_format_' . $format, array( $this, 'post_format_' . $format ) );
			}

			// Register an embed post formats
			add_filter( 'cherry_get_embed_post_formats', array( $this, 'embed_post_formats' ), 10, 2 );

			if ( true === $this->args['rewrite_default_gallery'] ) {
				// Replace gallery shortcode
				add_filter( 'post_gallery', array( $this, 'gallery_shortcode' ), 10, 3 );
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );

			$this->includes();

		}

		/**
		 * Register extra post formats rendering.
		 * Currently supported - facebook, twitter, soundcloud.
		 *
		 * @param  bool $embed Default embed value - false.
		 * @return string|bool
		 */
		public function embed_post_formats( $embed, $args = array() ) {

			$args = wp_parse_args( $args, array(
				'fields' => array(),
				'width'  => 350,
				'height' => 350,
			) );

			if ( empty( $args['fields'] ) ) {
				return $embed;
			}

			$extra_formats = array(
				'twitter'    => 'https://twitter.com',
				'facebook'   => 'https://www.facebook.com',
				'soundcloud' => 'https://soundcloud.com',
			);

			global $post;

			if ( ! $post ) {
				return $embed;
			}

			$excerpt = substr( $post->post_content, 0, 200 );

			foreach ( $args['fields'] as $name ) {

				$trigger = isset( $extra_formats[ $name ] ) ? $extra_formats[ $name ] : '';

				if ( ! $trigger ) {
					return $embed;
				}

				if ( false === strpos( $excerpt, $trigger ) ) {
					continue;
				}

				$url = $this->get_content_url( $post->post_content );

				if ( ! empty( $url ) ) {
					return wp_oembed_get( $url, $args );
				}
			}

			return $embed;

		}

		/**
		 * Include required API files
		 *
		 * @since  1.0.0
		 * @since  1.1.1 Using dirname( __FILE__ ) instead of __DIR__.
		 * @return void
		 */
		public function includes() {
			require_once dirname( __FILE__ ) . '/inc/class-cherry-facebook-embed.php';

			// Register Facebook Embed.
			if ( class_exists( 'Cherry_Facebook_Embed' ) ) {
				Cherry_Facebook_Embed::get_instance();
			}
		}

		/**
		 * Enqueue required assets
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function assets() {
			wp_enqueue_script(
				'cherry-post-formats',
				Cherry_Core::base_url( 'assets/js/min/cherry-post-formats.min.js', __FILE__ ),
				array( 'jquery', 'cherry-js-core' ),
				$this->module_version,
				true
			);
		}

		/**
		 * If did not find a URL, check the post content for one. If nothing is found, return the post permalink.
		 *
		 * @author Justin Tadlock <justin@justintadlock.com>
		 * @author Cherry Team <support@cherryframework.com>
		 * @since  1.0.0
		 * @param  array  $args API arguments.
		 * @param  object $post Post object.
		 * @return string
		 */
		public function get_post_format_link( $args = array(), $post = null ) {

			$args = wp_parse_args( $args, $this->args['link_args'] );

			$post        = is_null( $post ) ? get_post() : $post;
			$content_url = $this->get_content_url( $post->post_content );
			$url         = ! empty( $content_url ) ? $content_url : get_permalink( $post->ID );

			if ( false === $args['render'] ) {
				return esc_url( $url );
			} else {
				return sprintf(
					'<a href="%1$s" class="post-format-link %2$s">%1$s</a>',
					esc_url( $url ), esc_attr( $args['class'] )
				);
			}

		}

		/**
		 * Callback for apropriate hook to show link post format related link.
		 *
		 * @since  1.0.0
		 * @param  array $args Set of arguments.
		 */
		public function post_format_link( $args = array() ) {

			echo $this->get_post_format_link( $args );

		}

		/**
		 * Returns first blockquote from content. If not - returns excerpt.
		 *
		 * @author Cherry Team <support@cherryframework.com>
		 * @since  1.0.0
		 * @param  object $post Post object.
		 * @return string       UblockquoteRL.
		 */
		public function get_post_format_quote( $post = null ) {

			$post  = is_null( $post ) ? get_post() : $post;
			$quote = $this->get_content_quote( $post->post_content );
			$quote = ! empty( $quote ) ? $quote : get_the_excerpt();

			return sprintf( '<blockquote class="post-format-quote">%s</blockquote>', $quote );

		}

		/**
		 * Callback for apropriate hook to show link post format related link.
		 *
		 * @since  1.0.0
		 * @param  array $args Set of arguments.
		 */
		public function post_format_quote( $args ) {

			echo $this->get_post_format_quote();

		}

		/**
		 * Retrieve a featured audio.
		 *
		 * Returns first finded audio tag in page content.
		 *
		 * @since 1.0.0
		 */
		public function get_post_format_audio() {

			/**
			 * Filter post format audio output to rewrite audio from child theme or plugins.
			 *
			 * @since 1.0.0
			 * @param bool|mixed $result Value to return instead of the featured audio.
			 *                           Default false to skip it.
			 */
			$result = apply_filters( 'cherry_pre_get_post_audio', false );

			if ( false !== $result ) {
				return $result;
			}

			$content = get_the_content();
			$embeds  = get_media_embedded_in_content( apply_filters( 'the_content', $content ), array( 'audio' ) );

			if ( empty( $embeds ) ) {
				return false;
			}

			if ( false == preg_match( '/<audio[^>]*>(.*?)<\/audio>/', $embeds[0], $matches ) ) {
				return false;
			}

			/**
			 * Filter a featured audio.
			 *
			 * @since 1.0.0
			 * @param string $output Featured audio.
			 */
			return apply_filters( 'cherry_get_the_post_audio', $matches[0] );

		}

		/**
		 * Callback for apropriate hook to show audio post format related audio.
		 *
		 * @since  1.0.0
		 * @param  array $args Set of arguments.
		 */
		public function post_format_audio( $args ) {

			echo $this->get_post_format_audio();

		}

		/**
		 * Retrieve a featured video.
		 *
		 * Returns first finded video, iframe, object or embed tag in content.
		 *
		 * @since 1.0.0
		 * @param array $args Set of arguments.
		 */
		public function get_post_format_video( $args ) {

			$args = wp_parse_args( $args, $this->args['video_args'] );

			/**
			 * Filter post format video output to rewrite video from child theme or plugins.
			 *
			 * @since 1.0.0
			 * @param bool|mixed $result Value to return instead of the featured video.
			 *                           Default false to skip it.
			 */
			$result = apply_filters( 'cherry_pre_get_post_video', false );

			if ( false !== $result ) {
				return $result;
			}

			$post_content = get_the_content();

			if ( has_shortcode( $post_content, 'video' ) ) {
				$result_format = '%s';
			} else {
				$result_format = '<div class="entry-video embed-responsive embed-responsive-16by9">%s</div>';
			}

			/** This filter is documented in wp-includes/post-template.php */
			$content = apply_filters( 'the_content', $post_content );
			$types   = array( 'video', 'object', 'embed', 'iframe' );
			$embeds  = get_media_embedded_in_content( $content, $types );

			if ( empty( $embeds ) ) {
				return;
			}

			foreach ( $types as $tag ) {
				if ( preg_match( "/<{$tag}[^>]*>(.*?)<\/{$tag}>/", $embeds[0], $matches ) ) {
					$result = $matches[0];
					break;
				}
			}

			if ( false === $result ) {
				return false;
			}

			$regex = array(
				'/width=[\'\"](\d+)[\'\"]/',
				'/height=[\'\"](\d+)[\'\"]/',
			);

			$replace = array(
				'width="' . $args['width'] . '"',
				'height="' . $args['height'] . '"',
			);

			$result = preg_replace( $regex, $replace, $result );
			$result = sprintf( $result_format, $result );

			/**
			 * Filter a featured video.
			 *
			 * @since 1.0.0
			 * @param string $result Featured video.
			 */
			return apply_filters( 'cherry_get_the_post_video', $result );

		}

		/**
		 * Callback for apropriate hook to show video post format related video.
		 *
		 * @since  1.0.0
		 * @param  array $args Set of arguments.
		 */
		public function post_format_video( $args = array() ) {

			echo $this->get_post_format_video( $args );

		}

		/**
		 * Retrieve a featured image.
		 *
		 * If has post thumbnail - will get post thumbnail, else - get first image from content.
		 *
		 * @since  1.0.0
		 * @param  array $args Set of arguments.
		 * @return string       Featured image.
		 */
		public function get_post_format_image( $args ) {

			/**
			 * Filter post format image output to rewrite image from child theme or plugins.
			 *
			 * @since 1.0.0
			 * @param bool|mixed $result Value to return instead of the featured image.
			 *                           Default false to skip it.
			 */
			$result = apply_filters( 'cherry_pre_get_post_image', false );

			if ( false !== $result ) {
				return $result;
			}

			$post_id   = get_the_ID();
			$post_type = get_post_type( $post_id );

			/**
			 * Filter the default arguments used to display a post image.
			 *
			 * @since 1.0.0
			 * @param array  $defaults  Array of arguments.
			 * @param int    $post_id   The post ID.
			 * @param string $post_type The post type of the current post.
			 */
			$defaults = apply_filters(
				'cherry_get_the_post_image_defaults',
				$this->args['image_args'],
				$post_id,
				$post_type
			);

			$args = wp_parse_args( $args, $defaults );

			/**
			 * Filter image CSS model
			 *
			 * @param array $css_model Default CSS model.
			 * @param array $args      Post formats module arguments.
			 */
			$css_model = apply_filters( 'cherry_post_formats_image_css_model', array(
				'container' => $args['base_class'],
				'link'      => $args['base_class'] . '__link',
				'image'     => $args['base_class'] . '__img',
			), $args );

			/**
			 * Filter image attributes array passed to post format image
			 *
			 * @since 1.0.0
			 * @param array existing attributes.
			 */
			$img_atts = apply_filters( 'cherry_post_image_attributes', array( 'class' => $css_model['image'] ) );

			if ( has_post_thumbnail( $post_id ) ) {

				$thumb = get_the_post_thumbnail( $post_id, $args['size'], $img_atts );
				$url   = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );

			} else {

				$img = $this->get_post_images();

				if ( ! $img || empty( $img ) || empty( $img[0] ) ) {
					return false;

				} elseif ( is_int( $img[0] ) ) {

					$thumb = wp_get_attachment_image( $img[0], $args['size'], 0, $img_atts );
					$url   = wp_get_attachment_url( $img[0] );

				} else {

					global $_wp_additional_image_sizes;

					if ( ! isset( $_wp_additional_image_sizes[ $args['size'] ] ) ) {
						return false;
					}

					if ( has_excerpt( $post_id ) ) {
						$alt = trim( strip_tags( get_the_excerpt() ) );
					} else {
						$alt = trim( strip_tags( get_the_title( $post_id ) ) );
					}

					$default_atts = array(
						'width' => $_wp_additional_image_sizes[ $args['size'] ]['width'],
						'alt'   => esc_attr( $alt ),
					);

					$img_atts = array_merge( $default_atts, $img_atts );
					$thumb    = sprintf( '<img src="%s" %s>', esc_url( $img[0] ), $this->prepare_atts( $img_atts ) );
					$url      = $img[0];

				}
			}

			if ( 'file' !== $args['link'] ) {
				$url = get_permalink( $post_id );
			}

			$data_atts = array( 'data-cherrypopup' => true );

			if ( false !== $args['popup_init'] ) {
				$init                   = json_encode( $args['popup_init'] );
				$data_atts['data-init'] = $init;
			}

			if ( false !== $args['popup'] ) {
				$data_atts['data-popup'] = $args['popup'];
			}

			$data_string = $this->prepare_atts( $data_atts );

			if ( ! empty( $args['popup_handle'] ) ) {
				wp_enqueue_script( $args['popup_handle'] );
			}

			$item   = sprintf( $args['item'], $thumb, $css_model['link'], $url, $data_string );
			$result = sprintf( $args['container'], $item, $css_model['container'] );

			/**
			 * Filter a featured image.
			 *
			 * @since 1.0.0
			 * @param string $result Featured image.
			 * @param array  $args   Array of arguments.
			 */
			return apply_filters( 'cherry_get_the_post_image', $result, $args );

		}

		/**
		 * Callback for apropriate hook to show image post format related thumbnail.
		 *
		 * @since  1.0.0
		 * @param  array $args Set of arguments.
		 */
		public function post_format_image( $args ) {

			echo $this->get_post_format_image( $args );

		}

		/**
		 * Retrieve a featured gallery.
		 *
		 * If has post thumbnail - will get post thumbnail, else - get first image from content.
		 *
		 * @since  1.0.0
		 * @return string $output Featured gallery.
		 */
		public function get_post_format_gallery( $args ) {

			if ( ! $args ) {
				$args = array();
			}

			$args = wp_parse_args( $args, $this->args['gallery_args'] );

			/**
			 * Filter post format gallery output to rewrite gallery from child theme or plugins.
			 *
			 * @since 1.0.0
			 * @param bool|mixed $result Value to return instead of the featured gallery.
			 *                           Default false to skip it.
			 */
			$result = apply_filters( 'cherry_pre_get_post_gallery', false );

			if ( false !== $result ) {
				return $result;
			}

			// First - try to get images from galleries in post.
			$is_html      = ( true === $this->args['rewrite_default_gallery'] ) ? true : false;

			// Temporary replace default global args with currently parsed
			$temp_args                  = $this->args;
			$this->args['gallery_args'] = $args;

			$post_gallery = $this->get_gallery_images( $is_html, $args );

			// Restore default arguments list
			$this->args = null;
			$this->args = $temp_args;

			// If stanadrd gallery shortcode replaced with cherry - return HTML.
			if ( is_string( $post_gallery ) && ! empty( $post_gallery ) ) {
				return $post_gallery;
			} else if ( empty( $post_gallery ) ) {
				return false;
			}

			$output = $this->get_gallery_html( $post_gallery, $args );

			/**
			 * Filter a post gallery.
			 *
			 * @since 1.0.0
			 * @param string $output Post gallery.
			 */
			return apply_filters( 'cherry_get_the_post_gallery', $output );
		}

		/**
		 * Get galeery images list or try to get gallery HTML
		 *
		 * @param  bool  $is_html is HTML returns or not.
		 * @param  array $args    argumnets array.
		 * @return mixed
		 */
		public function get_gallery_images( $is_html, $args = array() ) {

			$post_id = get_the_ID();

			$post_gallery = get_post_gallery( $post_id, $is_html );

			// If stanadrd gallery shortcode replaced with cherry - return HTML.
			if ( is_string( $post_gallery ) && ! empty( $post_gallery ) ) {
				return $post_gallery;
			}

			if ( ! empty( $post_gallery['ids'] ) ) {
				$post_gallery = explode( ',', $post_gallery['ids'] );
			} elseif ( ! empty( $post_gallery['src'] ) ) {
				$post_gallery = $post_gallery['src'];
			} else {
				$post_gallery = false;
			}

			// If can't try to catch images inserted into post.
			if ( ! $post_gallery ) {
				$post_gallery = $this->get_post_images( $post_id, 15 );
			}

			// And if not find any images - try to get images attached to post.
			if ( ! $post_gallery || empty( $post_gallery ) ) {

				$attachments = get_children( array(
					'post_parent'    => $post_id,
					'posts_per_page' => 3,
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
				) );

				if ( $attachments && is_array( $attachments ) ) {
					$post_gallery = array_keys( $attachments );
				}
			}

			return $post_gallery;
		}

		/**
		 * Callback for apropriate hook to show gallery post format related gallery.
		 *
		 * @since  1.0.0
		 * @param  array $args Set of arguments.
		 */
		public function post_format_gallery( $args ) {

			echo $this->get_post_format_gallery( $args );

		}

		/**
		 * Custom output for gallery shortcode.
		 *
		 * @since  1.0.0
		 * @param  array $result S Value to return instead of the gallery shortcode.
		 * @param  array $attr Shortcode attributes.
		 * @return string       Gallery HTML.
		 */
		public function gallery_shortcode( $result, $attr ) {

			/**
			 * Filter a gallery output.
			 *
			 * @since 1.0.0
			 * @param bool|mixed $result Value to return instead of the gallery shortcode. Default false to skip it.
			 * @param array      $attr   Shortcode attributes.
			 * @param object     $this   Current class instance.
			 */
			$result = apply_filters( 'cherry_pre_get_gallery_shortcode', false, $attr, $this );

			if ( false !== $result ) {
				return $result;
			}

			$post = get_post();

			$atts = shortcode_atts( array(
				'order'      => 'ASC',
				'orderby'    => 'menu_order ID',
				'id'         => $post ? $post->ID : 0,
				'include'    => '',
				'exclude'    => '',
				'link'       => '',
			), $attr, 'gallery' );

			$id = intval( $atts['id'] );

			if ( ! empty( $atts['include'] ) ) {

				$attachments = $this->esc_include_ids( $atts['include'] );

			} elseif ( ! empty( $atts['exclude'] ) ) {

				$attachments = get_children(
					array(
						'post_parent'    => $id,
						'exclude'        => $atts['exclude'],
						'post_status'    => 'inherit',
						'post_type'      => 'attachment',
						'post_mime_type' => 'image',
						'order'          => $atts['order'],
						'orderby'        => $atts['orderby'],
					)
				);
				$attachments = array_keys( $attachments );

			} else {

				$attachments = get_children(
					array(
						'post_parent'    => $id,
						'post_status'    => 'inherit',
						'post_type'      => 'attachment',
						'post_mime_type' => 'image',
						'order'          => $atts['order'],
						'orderby'        => $atts['orderby'],
					)
				);
				$attachments = array_keys( $attachments );

			}

			if ( empty( $attachments ) || ! is_array( $attachments ) ) {
				return;
			}

			$result = $this->get_gallery_html( $attachments, $atts );

			return $result;

		}

		/**
		 * Build default gallery HTML from images array.
		 *
		 * @since  1.0.0
		 * @param  array $images Images array can contain image IDs or URLs.
		 * @param  array $args   Shortcode/user attributes array.
		 * @return string         Gallery HTML markup.
		 */
		public function get_gallery_html( $images, $args = array() ) {

			$post_id   = get_the_ID();
			$post_type = get_post_type( $post_id );

			/**
			 * Filter the default arguments used to display a post gallery.
			 *
			 * @since 1.0.0
			 * @param array  $defaults  Array of arguments.
			 * @param int    $post_id   The post ID.
			 * @param string $post_type The post type of the current post.
			 */
			$defaults = apply_filters(
				'cherry_get_the_post_gallery_defaults',
				$this->args['gallery_args'],
				$post_id,
				$post_type
			);

			$args = wp_parse_args( $args, $defaults );

			/**
			 * Filter image CSS model
			 *
			 * @param array $css_model Default CSS model.
			 * @param array $args      Post formats module arguments.
			 */
			$css_model = apply_filters( 'cherry_post_formats_gallery_css_model', array(
				'container' => $args['base_class'],
				'slide'     => $args['base_class'] . '__slide',
				'link'      => $args['base_class'] . '__link',
				'image'     => $args['base_class'] . '__image',
				'caption'   => $args['base_class'] . '__caption',
			) );

			if ( ! empty( $args['img_class'] ) ) {
				$css_model['image'] .= ' ' . esc_attr( $args['img_class'] );
			}

			if ( ! empty( $args['slider_handle'] ) ) {
				wp_enqueue_script( $args['slider_handle'] );
			}

			if ( ! empty( $args['popup_handle'] ) ) {
				wp_enqueue_script( $args['popup_handle'] );
			}

			$slider_data = array( 'data-cherryslider' => true );
			$popup_data  = array( 'data-cherrypopup' => true );

			if ( false !== $args['slider'] ) {
				$slider_data['data-slider'] = $args['slider'];
			}

			if ( false !== $args['slider_init'] ) {
				$slider_data['data-init'] = json_encode( $args['slider_init'] );
			}

			if ( false !== $args['popup'] ) {
				$popup_data['data-popup'] = $args['popup'];
			}

			if ( false !== $args['popup_init'] ) {
				$popup_data['data-init'] = json_encode( $args['popup_init'] );
			}

			$items    = array();
			$is_first = true;

			foreach ( $images as $img ) {

				$caption = '';

				if ( true === $is_first ) {
					$nth_class = '';
					$is_first  = false;
				} else {
					$nth_class = ' nth-child';
				}

				/**
				 * Filter image attributes for gallery item image
				 *
				 * @since 1.0.0
				 * @param array existing attributes.
				 * @param int|string $img current image,
				 */
				$img_atts = apply_filters(
					'cherry_post_gallery_image_attributes',
					array( 'class' => $css_model['image'] ),
					$img
				);

				if ( 0 < intval( $img ) ) {

					$image      = wp_get_attachment_image( $img, $args['size'], '', $img_atts );
					$attachment = get_post( $img );

					if ( '' === $args['link'] ) {
						$url = get_permalink( $img );
					} else {
						$url = wp_get_attachment_url( $img );
					}

					if ( ! empty( $attachment->post_excerpt ) ) {
						$capt_txt = wptexturize( $attachment->post_excerpt );
						$caption  = '<figcaption class="' . $css_model['caption'] . '">' . $capt_txt . '</figcaption>';
					}
				} else {

					global $_wp_additional_image_sizes;

					if ( ! isset( $_wp_additional_image_sizes[ $args['size'] ] ) ) {
						$width = 'auto';
					} else {
						$width = $_wp_additional_image_sizes[ $args['size'] ]['width'];
					}

					$default_atts = array( 'width' => $width );
					$img_atts     = array_merge( $default_atts, $img_atts );
					$thumb        = sprintf( '<img src="%s" %s>', esc_url( $img ), $this->prepare_atts( $img_atts ) );
					$url          = $img;

				}

				if ( 'none' === $args['link'] ) {
					$format = $args['slide_item_alt'];
				} else {
					$format = $args['slide_item'];
				}

				$slide_atts = $this->prepare_atts(
					array_merge( array( 'class' => $css_model['link'] . $nth_class ), $popup_data )
				);

				$slide_content = sprintf( $format, $image, $caption, $url, $slide_atts );
				$items[]       = sprintf( $args['slide'], $slide_content, $css_model['slide'] . $nth_class );
			}

			$items = implode( "\r\n", $items );

			$slider_data_str = $this->prepare_atts( $slider_data );
			$slider_id       = 'gallery-' . rand( 100, 999 );

			$result = sprintf(
				$args['container'],
				$items, $css_model['container'], $slider_data_str, $slider_id
			);

			return $result;
		}

		/**
		 * Include IDs set (array or string).
		 *
		 * @since  1.1.2
		 * @param  mixed $ids ID's set.
		 * @return array
		 */
		public function esc_include_ids( $ids ) {

			if ( is_array( $ids ) ) {
				return $ids;
			} else {
				return explode( ',', str_replace( ' ', '', $ids ) );
			}

		}

		/**
		 * Prepare attributes string from array
		 *
		 * @since  1.0.0
		 * @param  array $atts attributes array to parse.
		 * @return string
		 */
		public function prepare_atts( $atts ) {

			$result = '';

			if ( empty( $atts ) ) {
				return '';
			}

			foreach ( $atts as $attr => $value ) {
				$result .= ' ' . $attr . '=\'' . esc_attr( $value ) . '\'';
			}

			return $result;

		}

		/**
		 * Retrieve images from post content.
		 *
		 * Returns image ID's if can find this image in database,
		 * returns image URL or boolean false in other case.
		 *
		 * @since  1.0.0
		 * @param  int $post_id Post ID to search image in.
		 * @param  int $limit   Max images count to search.
		 * @return mixed          Images.
		 */
		public function get_post_images( $post_id = null, $limit = 1 ) {

			$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
			$content = get_the_content();

			// Gets first image from content.
			preg_match_all( '/< *img[^>]*src *= *["\']?([^"\']*)/i', $content, $matches );

			if ( ! isset( $matches[1] ) ) {
				return false;
			}

			$result = array();

			global $wpdb;

			for ( $i = 0; $i < $limit; $i++ ) {

				if ( empty( $matches[1][ $i ] ) ) {
					continue;
				}

				$image_src = esc_url( $matches[1][ $i ] );
				$image_src = preg_replace( '/^(.+)(-\d+x\d+)(\..+)$/', '$1$3', $image_src );

				// Try to get current image ID.
				$id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid = %s", $image_src ) );

				if ( ! $id ) {
					$result[] = $image_src;
				} else {
					$result[] = (int) $id;
				}
			}

			return $result;
		}

		/**
		 * Gets the first URL from the content, even if it's not wrapped in an <a> tag.
		 *
		 * @author Justin Tadlock <justin@justintadlock.com>
		 * @author Cherry Team <support@cherryframework.com>
		 * @since  1.0.0
		 * @param  [type] $content Post content.
		 * @return string          URL.
		 */
		public function get_content_url( $content ) {

			// Catch links that are not wrapped in an '<a>' tag.
			preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', make_clickable( $content ), $matches );

			return ! empty( $matches[1] ) ? esc_url_raw( $matches[1] ) : '';
		}

		/**
		 * Gets the first blockquote from post content.
		 *
		 * @author Cherry Team <support@cherryframework.com>
		 * @since  1.0.0
		 * @param  [type] $content Post content.
		 * @return string          Quote.
		 */
		public function get_content_quote( $content ) {

			// Catch links that are not wrapped in an '<a>' tag.
			preg_match( '/<blockquote[^>]*>(.*?)<\/blockquote>/im', $content, $matches );

			return ! empty( $matches[1] ) ? wp_kses_post( $matches[1] ) : '';
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $core, $args ) {
			return new self( $core, $args );
		}
	}
}
