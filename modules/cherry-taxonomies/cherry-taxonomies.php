<?php
/**
 * Module Name: Taxanomies
 * Description: Provides functionality for creating custom taxanomies
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

if ( ! class_exists( 'Cherry_Taxonomies' ) ) {

	/**
	 * Cherry Taxonomies.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Removed `module_directory` property.
	 */
	class Cherry_Taxonomies {
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
		public $module_slug = 'cherry-taxonomies';

		/**
		 * Default post type arguments
		 *
		 * @var null
		 */
		private $defaults = null;

		/**
		 * Cherry_Post_Type class constructor
		 */
		public function __construct( $core, $args = array() ) {
			$this->defaults = $args;

			if ( ! class_exists( 'Cherry_Taxonomy' ) ) {
				require_once( __DIR__ . '/inc/cherry-taxonomy.php' );
			}
		}

		/**
		 * Create new Post Type.
		 *
		 * @param  [type] $single         name.
		 * @param  [type] $post_type_slug post types slug.
		 * @param  [type] $plural         name.
		 * @return Cherry_Post_Type
		 */
		public function create( $single, $post_type_slug = 'post', $plural = '' ) {
			$tax = new Cherry_Taxonomy( $single, $post_type_slug, $plural );

			$this->defaults = array_merge(
				$this->defaults,
				$this->get_default_arguments(
					$tax->get_single(),
					$tax->get_plural(),
					$tax->get_post_type_slug()
				)
			);
			$tax->set_arguments( $this->defaults );

			return $tax;
		}

		/**
		 * Get the taxonomy default arguments.
		 *
		 * @param [type] $plural The post type plural display name.
		 * @param [type] $singular The post type singular display name.
		 * @return array
		 */
		public function get_default_arguments( $plural, $singular, $post_type_slug ) {
			$labels = array(
				'name'              => $plural,
				'singular_name'     => $singular,
				'search_items'      => 'Search ' . $plural,
				'all_items'         => 'All ' . $plural,
				'parent_item'       => 'Parent ' . $singular,
				'parent_item_colon' => 'Parent ' . $singular . ' :',
				'edit_item'         => 'Edit ' . $singular,
				'update_item'       => 'Update ' . $singular,
				'add_new_item'      => 'Add New ' . $singular,
				'new_item_name'     => 'New ' . $singular . ' Name',
				'menu_name'         => $plural,
			);

			return array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => $post_type_slug ),
			);
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
