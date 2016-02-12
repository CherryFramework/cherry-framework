<?php
/**
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

if ( ! class_exists( 'Cherry_Core' ) ) {

	class Cherry_Core {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Core version.
		 *
		 * @var string
		 */
		public $core_version = '0.9.4';

		/**
		 * Core settings.
		 *
		 * @var array
		 */
		public $settings = array();

		/**
		 * Holder for all registered modules for current core instance.
		 *
		 * @var array
		 */
		public $modules = array();

		/**
		* Cherry_Core constructor
		*
		* @since 1.0.0
		*/
		public function __construct( $settings = array() ) {

			$default_settings = array(
				'base_dir'	=> null,
				'base_url'	=> null,
				'modules'	=> array(),
			);

			$this->settings = array_merge( $default_settings, $settings );

			$this->autoload_modules();

		}

		/**
		 * Try automatically include and load modules with autoload seted up into true.
		 * For other - only attach apropriate load actions and wait for handle calling.
		 *
		 * @return bool
		 */
		private function autoload_modules() {

			if ( ! is_array( $this->settings['modules'] ) || empty( $this->settings['modules'] ) ) {
				return false;
			}

			foreach ( $this->settings['modules'] as $module => $settings ) {

				$hook = $module . '-module';

				// Attach all modules to apropriate hooks.
				add_filter( $hook, array( $this, 'pre_load' ), $settings['priority'], 3 );

				// And immediately try to call hooks for autoloaded modules.
				if ( $this->is_module_autoload( $module ) ) {

					$arg = ! empty( $settings['args'] ) ? $settings['args'] : array();

					/**
					 * Call autoloaded modules.
					 *
					 * @since 1.0.0
					 * @param bool|object $module_instance Module instnce to return, false at start.
					 * @param array       $args            Module rguments.
					 * @param Cherry_Core $this            Current core object.
					 */
					$this->modules[ $module ] = apply_filters( $hook, false, $arg, $this );
				}
			}

		}

		/**
		 * Init sinle module
		 *
		 * @param  string $module module slug.
		 * @param  array  $args   Module arguments array.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function init_module( $module, $args = array() ) {
			$hook = $module . '-module';
			return apply_filters( $hook, false, $args, $this );

		}

		/**
		 * Module preload
		 *
		 * @since  1.0.0
		 * @param bool|object $module_instance Module instnce to return, false at start.
		 * @param array       $args            Module rguments.
		 * @param Cherry_Core $this            Current core object.
		 * @return object|bool
		 */
		public function pre_load( $module_instance, $args = array(), $core_instance ) {

			if ( $this !== $core_instance ) {
				return $module_instance;
			}

			$hook	= current_filter();
			$module	= str_replace( '-module', '', $hook );

			$this->load_module( $module );

			return $this->get_module_instance( $module, $args );
		}

		/**
		 * Check module autoload.
		 *
		 * @param  string  $module module slug.
		 * @return boolean
		 */
		public function is_module_autoload( $module ) {

			if ( empty( $this->settings['modules'][ $module ]['autoload'] ) ) {
				return false;
			}

			return $this->settings['modules'][ $module ]['autoload'];
		}

		/**
		 * Include module.
		 *
		 * @param  string $module module slug.
		 *
		 * @since  1.0.0
		 * @return bool
		 */
		public function load_module( $module ) {

			$class_name = $this->get_class_name( $module );

			if ( class_exists( $class_name ) ) {
				return true;
			}

			if ( ! file_exists( $this->get_module_path( $module ) ) ) {
				return false;
			}

			require_once( $this->get_module_path( $module ) );

			return true;
		}

		/**
		 * Get module instance.
		 *
		 * @param  string $module module slug.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public function get_module_instance( $module, $args ) {

			$class_name = $this->get_class_name( $module );

			if ( ! class_exists( $class_name ) ) {
				echo '<p>Class <b>' . $class_name . '</b> not exist!</p>';
				return false;
			}

			return $this->modules[ $module ] = call_user_func( array( $class_name, 'get_instance' ), $this, $args );
		}

		/**
		 * Get class name by module slug.
		 *
		 * @param  string $slug Module slug.
		 *
		 * @since  1.0.0
		 * @return string       Class name
		 */
		public function get_class_name( $slug = '' ) {
			$slug  = str_replace( '-', ' ', $slug );
			$class = str_replace( ' ', '_', ucwords( $slug ) );

			return $class;
		}

		/**
		 * Get path to main file for passed module
		 *
		 * @since  1.0.0
		 * @param  string $module module slug.
		 * @return string
		 */
		public function get_module_path( $module ) {
			return $this->get_core_dir() . '/modules/' . $module . '/' . $module . '.php';
		}

		/**
		 * Get path to the core directory.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_core_dir(){
			return trailingslashit( $this->settings['base_dir'] );
		}

		/**
		 * Get path to the core URI.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_core_url(){
			return trailingslashit( $this->settings['base_url'] );
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

	}

}
