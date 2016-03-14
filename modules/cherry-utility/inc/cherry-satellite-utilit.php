<?php
/**
 * Class Cherry Satellite Utilit
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

if ( ! class_exists( 'Cherry_Satellite_Utilit' ) ) {
	/**
	 * Class Cherry Satellite Utilit
	 */
	class Cherry_Satellite_Utilit {

		/**
		 * Get post
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public function get_post_object( $id ) {
			return get_post( $id );
		}

		/**
		 * Get term
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public function get_term_object( $id ) {
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
		public function cut_text( $text, $length, $after ) {
			return ( '0' !== $length ) ? wp_trim_words( $text, $length, $after ) : '' ;
		}

		/**
		 * Get array image size
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_thumbnail_size_array( $size ) {
			global $_wp_additional_image_sizes;

			return $_wp_additional_image_sizes[ $size ];
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
			$all_terms = ( array ) get_terms( $tax, array( 'hide_empty' => 0, 'hierarchical' => 0 ) );

			foreach ( $all_terms as $term ) {
				$terms[ $term->$key ] = $term->name;
			}

			return $terms;
		}
	}
}
