<?php
/**
 * Module Name: Post Types
 * Description: Provides functionality for creating custom post types
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

if ( ! class_exists( 'Cherry_Post_Types' ) ) {

	/**
	 * Cherry Post Types.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Removed `module_directory` property.
	 */
	class Cherry_Post_Types {

		/**
		 * Default post type arguments.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $defaults = null;

		/**
		 * Created post types list.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public static $created_post_types = array();

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct( $core, $args = array() ) {
			$this->defaults = $args;

			if ( ! class_exists( 'Cherry_Post_Type' ) ) {
				require_once( __DIR__ . '/inc/cherry-post-type.php' );
			}
		}

		/**
		 * Create new Post Type.
		 *
		 * @since  1.0.0
		 * @param  string $slug     The post type slug name.
		 * @param  string $plural   The post type plural name for display.
		 * @param  string $singular The post type singular name for display.
		 * @param  array  $args     The custom post type arguments.
		 * @return object
		 */
		public function create( $slug, $args = array() ) {

			// Register post type.
			self::$created_post_types[ $slug ] = new Cherry_Post_Type( $slug, $args );

			return self::$created_post_types[ $slug ];
		}

		/**
		 * Get the custom post type default arguments.
		 *
		 * @since  1.0.0
		 * @param  string $plural   The post type plural display name.
		 * @param  string $singular The post type singular display name.
		 * @return array
		 */
		private function get_default_arguments( $plural, $singular ) {
			$labels = array(
				'name'               => $plural,
				'archives'           => $plural,
				'menu_name'          => $plural,
				'all_items'          => 'All ' . $plural,
				'singular_name'      => $singular,
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New '. $singular,
				'edit_item'          => 'Edit '. $singular,
				'new_item'           => 'New ' . $singular,
				'view_item'          => 'View ' . $singular,
				'search_items'       => 'Search ' . $singular,
				'not_found'          => 'No '. $singular .' found',
				'not_found_in_trash' => 'No '. $singular .' found in Trash',
				'parent_item_colon'  => '',
			);

			$defaults = array(
				'label'         => $plural,
				'labels'        => $labels,
				'description'   => '',
				'public'        => true,
				'menu_position' => 20,
				'has_archive'   => true,
			);

			return $defaults;
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
