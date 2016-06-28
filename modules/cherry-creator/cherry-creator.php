<?php
/**
 * Creator
 *
 * Module Name: Creator
 * Description: Creator
 * Version: 1.1.0
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Cherry_Framework
 * @subpackage Modules
 * @version    1.1.0
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Creator' ) ) {

	/**
	 * Cherry post types class
	 */
	class Cherry_Creator {

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
		public $module_slug = 'cherry-creator';

		/**
		 * Default post type arguments
		 *
		 * @var null
		 */
		private $defaults = null;

		/**
		 * Module directory
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private $module_directory = '';

		/**
		 * Cherry_Post_Type class constructor
		 */
		public function __construct( $core, $args = array() ) {
			$this->defaults = $args;
			$this->module_directory = $core->settings['base_dir'] . '/modules/' . $this->module_slug;

			// Load Creator Term
			if ( ! class_exists( 'Cherry_Creator_Term' ) ) {
				require_once( $this->module_directory . '/inc/cherry-creator-term.php' );
			}
		}

		/**
		 * Create Chery_Creator_Term object
		 *
		 * @param  [type]   $title term.
		 * @param  [string] $tax   taxonomy.
		 * @param  array    $args  arguments.
		 * @return Chery_Creator_Term
		 */
		public static function term( $title, $tax = 'category', $args = array() ) {
			// Load Creator Term
			if ( ! class_exists( 'Cherry_Creator_Term' ) ) {
				require_once( 'cherry-creator-term.php' );
			}
			return new Cherry_Creator_Term( $title, $tax, $args );
		}

		/**
		 * New / Update post
		 *
		 * @param  array $properties new or update post properties.
		 * @return post id or 0.
		 */
		public static function post( $properties = array(), $unique = false ) {
			if ( $unique && array_key_exists( 'post_title', $properties ) ) {
				$post_type = 'page';
				if ( array_key_exists( 'post_type', $properties ) ) {
					$post_type = $properties['post_type'];
				}
				$post = get_page_by_path( sanitize_title( $properties['post_title'] ), OBJECT, $post_type );
				if ( null !== $post ) {
					$properties['ID'] = $post->ID;
				}
			}
			return wp_insert_post( $properties );
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
