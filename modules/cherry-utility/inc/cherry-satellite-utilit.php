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

if ( ! class_exists( 'Cherry_Satellite_Utilit' ) ) {

	class Cherry_Satellite_Utilit {

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
		 * Get post
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public function get_post_object( $ID ) {
			return get_post( $ID );
		}

		/**
		 * Get term
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public function get_term_object( $ID ) {
			return get_term( $ID);
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
		public function get_term_permalink( $ID = 0 ) {
			return esc_url( get_category_link( $ID ) );
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
		 * get array image size
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_thumbnail_size_array( $size ) {
			global $_wp_additional_image_sizes;
			$size_array = array();

			if( array_key_exists ( $size, $_wp_additional_image_sizes ) ){
				$size_array = $_wp_additional_image_sizes[ $size ];
			}else {
				$size_array = $_wp_additional_image_sizes[ 'post-thumbnail' ];
			}

			return $size_array;
		}

		/**
		 * Return post terms.
		 *
		 * @since  1.0.0
		 * @param string $tax - category, post_tag, post_format
		 * @param string $key - slug, term_id
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