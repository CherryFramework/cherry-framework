<?php
/**
 * Class Cherry Core
 * Version: 1.0.1
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Core' ) ) {

	/**
	 * Class Cherry Core
	 */
	class Cherry_Core {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

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
			$base_dir = trailingslashit( __DIR__ );
			$base_url = trailingslashit( $this->base_url( '', __FILE__ ) );

			$defaults = array(
				'framework_path' => 'cherry-framework',
				'modules'        => array(),
				'base_dir'       => $base_dir,
				'base_url'       => $base_url,
				'extra_base_dir' => '',
			);

			$this->settings = array_merge( $defaults, $settings );

			$this->settings['extra_base_dir'] = trailingslashit( $this->settings['base_dir'] );
			$this->settings['base_dir']       = $base_dir;
			$this->settings['base_url']       = $base_url;

			// Cherry_Toolkit module should be loaded by default
			if ( ! isset( $this->settings['modules']['cherry-toolkit'] ) ) {
				$this->settings['modules']['cherry-toolkit'] = array(
					'autoload' => true,
				);
			}

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

				// Get module priority
				$priority = $this->get_module_priority( $module );

				// Attach all modules to apropriate hooks.
				add_filter( $hook, array( $this, 'pre_load' ), $priority, 3 );

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
		 * Init single module
		 *
		 * @param  [type] $module module slug.
		 * @param  array  $args   Module arguments array.
		 *
		 * @since  1.0.0
		 * @return mixed
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
		 * @param Cherry_Core $core_instance            Current core object.
		 * @return object|bool
		 */
		public function pre_load( $module_instance, $args = array(), $core_instance ) {

			if ( $this !== $core_instance ) {
				return $module_instance;
			}

			$hook   = current_filter();
			$module = str_replace( '-module', '', $hook );

			$this->load_module( $module );

			return $this->get_module_instance( $module, $args );
		}

		/**
		 * Check module autoload.
		 *
		 * @param  [type] $module module slug.
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
		 * @param  [type] $module module slug.
		 *
		 * @since  1.0.0
		 * @return bool
		 */
		public function load_module( $module ) {
			$class_name = $this->get_class_name( $module );

			if ( class_exists( $class_name ) ) {
				return true;
			}

			$path = $this->get_module_path( $module );

			if ( ! $path ) {
				return false;
			}

			require_once( $path );

			return true;
		}

		/**
		 * Get module instance.
		 *
		 * @param  [type] $module module slug.
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
		 * @param  [type] $slug Module slug.
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
		 * @param  [type] $module module slug.
		 * @return string
		 */
		public function get_module_path( $module ) {
			$abs_path = false;
			$rel_path = 'modules/' . $module . '/' . $module . '.php';

			if ( file_exists( $this->settings['base_dir'] . $rel_path ) ) {
				$abs_path = $this->settings['base_dir'] . $rel_path;
			} else if ( file_exists( $this->settings['extra_base_dir'] . $rel_path ) ) {
				$abs_path = $this->settings['extra_base_dir'] . $rel_path;
			}

			return $abs_path ? $abs_path : false;
		}

		/**
		 * Get module priority from it's version.
		 * Version information should be provided as a value stored in the header notation.
		 *
		 * @link   https://developer.wordpress.org/reference/functions/get_file_data/
		 *
		 * @since  1.0.0
		 * @param  [string]  $module   module slug or path.
		 * @param  [boolean] $is_path  set this as true, if `$module` contains a path.
		 * @return [integer]
		 */
		public function get_module_priority( $module, $is_path = false ) {

			// Default phpDoc headers
			$default_headers = array(
				'version' => 'Version',
			);

			// Maximum version number (major, minor, patch)
			$max_version = array(
				99,
				99,
				999,
			);

			// If `$module` is a slug, get module path
			if ( ! $is_path ) {
				$module = $this->get_module_path( $module );
			}

			$version = '1.0.0';

			/* @TODO: Add smart check */
			if ( ! $module ) {
				return $version;
			}

			$data = get_file_data( $module , $default_headers );

			// Check if version string has a valid value
			if ( isset( $data['version'] ) &&
					 false !== strpos( $data['version'], '.' ) ) {
				// Clean the version string
				preg_match( '/[\d\.]+/', $data['version'], $version );
				$version = $version[0];
			}

			// Convert version into integer
			$parts = explode( '.', $version );

			// Calculate priority
			foreach ( $parts as $index => $part ) {
				$parts[ $index ] = $max_version[ $index ] - (int) $part;
			}

			return (int) join( '', $parts );
		}

		/**
		 * Retrieves the absolute URL to the current file.
		 * Like a WordPress function `plugins_url`.
		 *
		 * @link   https://codex.wordpress.org/Function_Reference/plugins_url
		 * @since  1.0.1
		 * @param  string $file_path   Optional. Extra path appended to the end of the URL.
		 * @param  string $module_path A full path to the core or module file.
		 * @return string
		 */
		public static function base_url( $file_path = '', $module_path ) {
			$module_path = wp_normalize_path( $module_path );
			$module_dir  = dirname( $module_path );

			$plugin_dir  = wp_normalize_path( WP_PLUGIN_DIR );
			$stylesheet  = get_stylesheet();
			$theme_root  = get_raw_theme_root( $stylesheet );
			$theme_dir   = "$theme_root/$stylesheet";

			if ( 0 === strpos( $module_path, $plugin_dir ) ) {
				$url = plugin_dir_url( $module_path );
			} else if ( false !== strpos( $module_path, $theme_dir ) ) {
				$explode = explode( $theme_dir, $module_dir, 2 );
				$url     = get_stylesheet_directory_uri() . $explode[1];
			} else {
				$site_url = site_url();
				$abs_path = wp_normalize_path( ABSPATH );
				$url      = str_replace( untrailingslashit( $abs_path ), $site_url, $module_dir );
			}

			if ( $file_path && is_string( $file_path ) ) {
				$url = trailingslashit( $url );
				$url .= ltrim( $file_path, '/' );
			}

			return apply_filters( 'cherry_core_base_url', $url, $file_path, $module_path );
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
