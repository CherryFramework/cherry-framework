<?php
/**
 * Class Cherry Satellite Utilit
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-3.0.html
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
			global $_wp_additional_image_sizes;
			$size_array = array();
			if ( array_key_exists( $size, $_wp_additional_image_sizes ) ) {
				$size_array = $_wp_additional_image_sizes[ $size ];
			} else {
				$size_array = $_wp_additional_image_sizes['post-thumbnail'];
			}

			return $size_array;
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
		 * @param [type] $key - slug, term_id.
		 * @return array
		 */
		public function get_terms_array( $tax = 'category', $key = 'slug' ) {
			$terms = array();
			$all_terms = (array) get_terms( $tax, array( 'hide_empty' => 0, 'hierarchical' => 0 ) );

			foreach ( $all_terms as $term ) {
				$terms[ $term->$key ] = $term->name;
			}

			return $terms;
		}
	}
}
