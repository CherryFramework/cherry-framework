<?php
/**
 * Module Name: Widget Factory
 * Description: Base widget class that simplifies creating of your own widgets.
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Cherry_Framework
 * @subpackage Modules
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2017, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Widget_Factory' ) ) {

	/**
	 * Widget factory module main class
	 */
	class Cherry_Widget_Factory {

		/**
		 * Module slug
		 *
		 * @var string
		 */
		public $module_slug = 'cherry-widget-factory';

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
		 * Module directory path.
		 *
		 * @since 1.5.0
		 * @access protected
		 * @var srting.
		 */
		public static $module_path;

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor for the module
		 */
		function __construct( $core, $args ) {
			$this->core        = $core;
			$this->args        = $args;
			self::$module_path = $args['module_path'];

			$this->include_abstract_widget();
			add_filter( 'cherry_widget_factory_core', array( $this, 'pass_core_to_widgets' ), 10, 2 );
		}

		/**
		 * Pass current core instance into widget
		 *
		 * @param  mixed  $core current core object.
		 * @param  [type] $path abstract widget file path.
		 * @return mixed
		 */
		public function pass_core_to_widgets( $core, $path ) {

			$path         = str_replace( '\\', '/', $path );
			$current_core = str_replace( '\\', '/', $this->core->settings['base_dir'] );

			if ( false !== strpos( $path, $current_core ) ) {
				return $this->core;
			}

			return $core;
		}

		/**
		 * Include abstract widget class
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function include_abstract_widget() {
			require_once( self::$module_path . '/inc/class-cherry-abstract-widget.php' );
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $core, $args ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $core, $args );
			}

			return self::$instance;
		}
	}
}
