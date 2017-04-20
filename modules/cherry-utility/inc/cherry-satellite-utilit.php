<?php
/**
 * Class Cherry Satellite Utilit
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

if ( ! class_exists( 'Cherry_Satellite_Utilit' ) ) {

	/**
	 * Class Cherry Satellite Utilit
	 */
	class Cherry_Satellite_Utilit {

		/**
		 * Parent module args
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $args = null;

		/**
		 * Cherry_Satellite_Utilit constructor
		 *
		 * @since 1.0.0
		 */
		function __construct( $module = null ) {
			if ( null !== $module ) {
				$this->args = $module->args;
			}
		}

		/**
		 * Get post
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public function get_post_object( $id = 0 ) {
			return get_post( $id );
		}

		/**
		 * Get term
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public function get_term_object( $id = 0 ) {
			return get_term( $id );
		}

		/**
		 * Get post permalink
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_post_permalink() {
			return esc_url( get_the_permalink() );
		}

		/**
		 * Get post permalink.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_term_permalink( $id = 0 ) {
			return esc_url( get_category_link( $id ) );
		}

		/**
		 * Cut text
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function cut_text( $text = '', $length = -1, $trimmed_type = 'word', $after, $content = false ) {

			if ( -1 !== $length ) {

				if ( $content ) {
					$text = strip_shortcodes( $text );
					$text = apply_filters( 'the_content', $text );
					$text = str_replace( ']]>', ']]&gt;', $text );
				}

				if ( 'word' === $trimmed_type ) {
					$text = wp_trim_words( $text, $length, $after );
				} else {
					$text = wp_html_excerpt( $text, $length, $after );
				}
			}

			return $text;
		}

		/**
		 * Get array image size
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_thumbnail_size_array( $size ) {
			$sizes = $this->get_image_sizes();

			if ( isset( $sizes[ $size ] ) ) {
				$size_array = $sizes[ $size ];

			} else if ( isset( $sizes['post-thumbnail'] ) ) {
				$size_array = $sizes['post-thumbnail'];

			} else {
				$size_array = $sizes['thumbnail'];
			}

			return $size_array;
		}

		/**
		 * Get size information for all currently-registered image sizes.
		 *
		 * @global $_wp_additional_image_sizes
		 * @uses   get_intermediate_image_sizes()
		 * @link   https://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
		 * @since  1.1.6
		 * @return array $sizes Data for all currently-registered image sizes.
		 */
		function get_image_sizes() {
			global $_wp_additional_image_sizes;

			$sizes = array();

			foreach ( get_intermediate_image_sizes() as $_size ) {
				if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
					$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
					$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
					$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );

				} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
					$sizes[ $_size ] = array(
						'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
						'height' => $_wp_additional_image_sizes[ $_size ]['height'],
						'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
					);
				}
			}

			return $sizes;
		}

		/**
		 * Output content method.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function output_method( $content = '', $echo = false ) {
			if ( ! filter_var( $echo, FILTER_VALIDATE_BOOLEAN ) ) {
				return $content;
			} else {
				echo $content;
			}
		}

		/**
		 * Return post terms.
		 *
		 * @since  1.0.0
		 * @param [type] $tax - category, post_tag, post_format.
		 * @param [type] $return_key - slug, term_id.
		 * @return array
		 */
		public function get_terms_array( $tax = array( 'category' ), $return_key = 'slug' ) {
			$terms = array();
			$tax = is_array( $tax ) ? $tax : array( $tax ) ;

			foreach ( $tax as $key => $value ) {
				if ( ! taxonomy_exists( $value ) ) {
					unset( $tax[ $key ] );
				}
			}

			$all_terms = (array) get_terms( $tax, array(
				'hide_empty'   => 0,
				'hierarchical' => 0,
			) );

			if ( empty( $all_terms ) || is_wp_error( $all_terms ) ) {
				return '';
			}

			foreach ( $all_terms as $term ) {
				$terms[ $term->$return_key ] = $term->name;
			}

			return $terms;
		}
	}
}
