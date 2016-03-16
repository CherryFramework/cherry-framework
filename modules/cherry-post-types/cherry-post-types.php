<?php
/**
 * Create custom post type
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Cherry post types class
 * Example usage:
 * $this->core->modules['cherry-post-types']->create(
 *			'property',
 *			'Property',
 *			'Properties'
 *		)->font_awesome_icon( 'f1ad' );
 */
class Cherry_Post_Types implements I_Module {
	/**
	 * Module version
	 *
	 * @var string
	 */
	public $module_version = '1.0.0';

	/**
	 * Module slug
	 *
	 * @var string
	 */
	public $module_slug = 'cherry-post-types';

	/**
	 * Default post type arguments
	 *
	 * @var null
	 */
	private $defaults = null;

	/**
	 * Created popst types list
	 *
	 * @var array
	 */
	public static $created_post_types = array();

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
		$this->module_directory = $core->settings['base_dir'] . '/modules/cherry-post-types';

		if ( ! class_exists( 'Cherry_Post_Type' ) ) {
			require_once( $this->module_directory . '/cherry-post-type.php' );
		}
	}

	/**
	 * Create new Post Type.
	 *
	 * @param [type] $slug The post type slug name.
	 * @param [type] $plural The post type plural name for display.
	 * @param [type] $singular The post type singular name for display.
	 * @param array  $args The custom post type arguments.
	 * @throws Exception Invalid custom post type parameter.
	 * @return Cherry_Post_Type
	 */
	public function create( $slug, $plural, $singular, $args = array() ) {
		$params = array(
			'slug'     => $slug,
			'plural'   => $plural,
			'singular' => $singular,
		);

		foreach ( $params as $name => $param ) {
			if ( ! is_string( $param ) ) {
				throw new Exception( 'Invalid custom post type parameter "'.$name.'". Accepts string only.' );
			}
		}

		// Set main properties.
		$this->defaults      = array_merge(
			$this->get_default_arguments( $plural, $singular ),
			$this->defaults
		);
		$args = array_merge( $this->defaults, $args );
		// Register post type
		self::$created_post_types[ $slug ] = new Cherry_Post_Type( $slug, $args );

		return self::$created_post_types[ $slug ];
	}

	/**
	 * Get the custom post type default arguments.
	 *
	 * @param [type] $plural The post type plural display name.
	 * @param [type] $singular The post type singular display name.
	 * @return array
	 */
	private function get_default_arguments( $plural, $singular ) {
		$labels = array(
			'name'               => __( $plural, 'cherry' ),
			'singular_name'      => __( $singular, 'cherry' ),
			'add_new'            => __( 'Add New', 'cherry' ),
			'add_new_item'       => __( 'Add New '. $singular, 'cherry' ),
			'edit_item'          => __( 'Edit '. $singular, 'cherry' ),
			'new_item'           => __( 'New ' . $singular, 'cherry' ),
			'all_items'          => __( 'All ' . $plural, 'cherry' ),
			'view_item'          => __( 'View ' . $singular, 'cherry' ),
			'search_items'       => __( 'Search ' . $singular, 'cherry' ),
			'not_found'          => __( 'No '. $singular .' found', 'cherry' ),
			'not_found_in_trash' => __( 'No '. $singular .' found in Trash', 'cherry' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( $plural, 'cherry' ),
		);

		$defaults = array(
			'label' 		=> __( $plural, 'cherry' ),
			'labels' 		=> $labels,
			'description'	=> '',
			'public'		=> true,
			'menu_position'	=> 20,
			'has_archive'	=> true,
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
