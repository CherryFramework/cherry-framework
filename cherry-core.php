<?php
/**
 * Class Cherry Core
 * Version: 1.4.1
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.en.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Core' ) ) {

	/**
	 * Class Cherry Core.
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
		 * @since 1.0.0
		 * @var array
		 */
		public $settings = array();

		/**
		 * Holder for all registered modules for current core instance.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $modules = array();

		/**
		 * Holder for all modules.
		 *
		 * @since 1.1.0
		 * @var array
		 */
		public static $all_modules = array();

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @since 1.1.1 Using dirname( __FILE__ ) instead of __DIR__.
		 */
		public function __construct( $settings = array() ) {
			$base_dir = trailingslashit( dirname( __FILE__ ) );
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

			$this->run_collector();

			/**
			 * In this hooks priority parameter are very important.
			 */
			add_action( 'after_setup_theme', array( 'Cherry_Core', 'load_all_modules' ), 2 );
			add_action( 'after_setup_theme', array( $this, 'init_required_modules' ),    2 );

			// Load the framework textdomain.
			add_action( 'after_setup_theme', array( $this, 'load_textdomain' ),         10 );

			// Init modules with autoload seted up into true.
			add_action( 'after_setup_theme', array( $this, 'init_autoload_modules' ), 9999 );

			// Backward compatibility for `cherry-widget-factory` module.
			remove_all_filters( 'cherry_widget_factory_core', 10 );
			add_filter( 'cherry_widget_factory_core', array( $this, 'pass_core_to_widgets' ), 11, 2 );
		}

		/**
		 * Fire collector for modules.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		private function run_collector() {

			if ( ! is_array( $this->settings['modules'] ) || empty( $this->settings['modules'] ) ) {
				return false;
			}

			// Cherry_Toolkit module should be loaded by default.
			if ( ! isset( $this->settings['modules']['cherry-toolkit'] ) ) {
				$this->settings['modules']['cherry-toolkit'] = array(
					'autoload' => true,
				);
			}

			foreach ( $this->settings['modules'] as $module => $settings ) {
				$priority = $this->get_module_priority( $module );
				$path     = $this->get_module_path( $module );

				if ( ! array_key_exists( $module, self::$all_modules ) ) {
					self::$all_modules[ $module ] = array( $priority => $path );
				} else {

					$old_priority = array_keys( self::$all_modules[ $module ] );

					if ( ! is_array( $old_priority ) || ! isset( $old_priority[0] ) ) {
						continue;
					}

					$compare = version_compare( $old_priority[0], $priority, '<' );

					if ( $compare ) {
						continue;
					}

					self::$all_modules[ $module ] = array( $priority => $path );
				}
			}

			/**
			 * Filter a holder for all modules.
			 *
			 * @since 1.1.0
			 * @var array
			 */
			self::$all_modules = apply_filters( 'cherry_core_all_modules', self::$all_modules, $this );
		}

		/**
		 * Loaded all modules.
		 *
		 * @since 1.1.0
		 */
		public static function load_all_modules() {

			foreach ( self::$all_modules as $module => $data ) {

				$path   = current( $data );
				$loaded = self::load_module( $module, $path );

				if ( ! $loaded ) {
					continue;
				}
			}
		}

		/**
		 * Load the framework textdomain.
		 *
		 * @since 1.4.0
		 */
		public function load_textdomain() {
			$mo_file_path = dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo';

			load_textdomain( 'cherry-framework', $mo_file_path );
		}

		/**
		 * Init a required modules.
		 *
		 * @since 1.1.0
		 */
		public function init_required_modules() {
			$required_modules = apply_filters( 'cherry_core_required_modules', array(
				'cherry-toolkit',
				'cherry-widget-factory',
			), $this );

			foreach ( $required_modules as $module ) {

				if ( ! array_key_exists( $module, $this->settings['modules'] ) ) {
					continue;
				}

				$settings = $this->settings['modules'][ $module ];
				$args     = ! empty( $settings['args'] ) ? $settings['args'] : array();

				$this->init_module( $module, $args );
			}
		}

		/**
		 * Init autoload modules.
		 *
		 * @since 1.1.0
		 */
		public function init_autoload_modules() {

			if ( empty( $this->modules ) ) {
				return;
			}

			foreach ( $this->settings['modules'] as $module => $settings ) {

				if ( ! $this->is_module_autoload( $module ) ) {
					continue;
				}

				if ( ! empty( $this->modules[ $module ] ) ) {
					continue;
				}

				$args = ! empty( $settings['args'] ) ? $settings['args'] : array();
				$this->init_module( $module, $args );
			}
		}

		/**
		 * Init single module.
		 *
		 * @since  1.0.0
		 * @param  string $module Module slug.
		 * @param  array  $args   Module arguments array.
		 * @return mixed
		 */
		public function init_module( $module, $args = array() ) {
			$this->modules[ $module ] = $this->get_module_instance( $module, $args );

			/**
			 * Filter a single module after initialization.
			 *
			 * @since 1.1.0
			 */
			return apply_filters( 'cherry_core_init_module', $this->modules[ $module ], $module, $args, $this );
		}

		/**
		 * Check module autoload.
		 *
		 * @since  1.0.0
		 * @param  string $module Module slug.
		 * @return bool
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
		 * @since  1.0.0
		 * @param  string $module Module slug.
		 * @param  string $path   Module path.
		 * @return bool
		 */
		public static function load_module( $module, $path ) {
			$class_name = self::get_class_name( $module );

			if ( class_exists( $class_name ) ) {
				return true;
			}

			if ( ! $path ) {
				return false;
			}

			require_once( $path );

			return true;
		}

		/**
		 * Get module instance.
		 *
		 * @since  1.0.0
		 * @param  string $module Module slug.
		 * @param  array  $args   Module arguments.
		 * @return object
		 */
		public function get_module_instance( $module, $args = array() ) {
			$class_name = self::get_class_name( $module );

			if ( ! class_exists( $class_name ) ) {
				echo '<p>Class <b>' . esc_html( $class_name ) . '</b> not exist!</p>';
				return false;
			}

			return $this->modules[ $module ] = call_user_func( array( $class_name, 'get_instance' ), $this, $args );
		}

		/**
		 * Get class name by module slug.
		 *
		 * @since  1.0.0
		 * @param  string $slug Module slug.
		 * @return string
		 */
		public static function get_class_name( $slug = '' ) {
			$slug  = str_replace( '-', ' ', $slug );
			$class = str_replace( ' ', '_', ucwords( $slug ) );

			return $class;
		}

		/**
		 * Get path to main file for passed module.
		 *
		 * @since  1.0.1
		 * @param  string $module Module slug.
		 * @return string
		 */
		public function get_module_path( $module ) {
			$abs_path = false;
			$rel_path = 'modules/' . $module . '/' . $module . '.php';

			if ( file_exists( $this->settings['extra_base_dir'] . $rel_path ) ) {
				$abs_path = $this->settings['extra_base_dir'] . $rel_path;
			} else if ( file_exists( $this->settings['base_dir'] . $rel_path ) ) {
				$abs_path = $this->settings['base_dir'] . $rel_path;
			}

			return $abs_path;
		}

		/**
		 * Get module priority from it's version.
		 * Version information should be provided as a value stored in the header notation.
		 *
		 * @link   https://developer.wordpress.org/reference/functions/get_file_data/
		 * @since  1.0.0
		 * @param  string $module   Module slug or path.
		 * @param  bool   $is_path  Set this as true, if `$module` contains a path.
		 * @return int
		 */
		public function get_module_priority( $module, $is_path = false ) {

			// Default phpDoc headers.
			$default_headers = array(
				'version' => 'Version',
			);

			// Maximum version number (major, minor, patch).
			$max_version = array(
				99,
				99,
				999,
			);

			// If `$module` is a slug, get module path.
			if ( ! $is_path ) {
				$module = $this->get_module_path( $module );
			}

			$version = '1.0.0';

			/* @TODO: Add smart check */
			if ( ! $module ) {
				return $version;
			}

			$data = get_file_data( $module , $default_headers );

			// Check if version string has a valid value.
			if ( isset( $data['version'] ) && false !== strpos( $data['version'], '.' ) ) {

				// Clean the version string.
				preg_match( '/[\d\.]+/', $data['version'], $version );
				$version = $version[0];
			}

			// Convert version into integer.
			$parts = explode( '.', $version );

			// Calculate priority.
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
				$explode = explode( $theme_dir, $module_dir );
				$url     = get_stylesheet_directory_uri() . end( $explode );
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
		 * Pass core instance into widget.
		 *
		 * @since  1.1.0
		 * @param  mixed  $core Current core object.
		 * @param  string $path Abstract widget file path.
		 * @return mixed
		 */
		public function pass_core_to_widgets( $core, $path ) {
			$path         = str_replace( '\\', '/', $path );
			$current_core = str_replace( '\\', '/', $this->settings['extra_base_dir'] );

			if ( false !== strpos( $path, $current_core ) ) {
				return self::get_instance();
			}

			return $core;
		}

		/**
		 * Get path to the core directory.
		 *
		 * @since 1.0.0
		 * @deprecated 1.1.0 Use constant `dirname( __FILE__ )`
		 * @return string
		 */
		public function get_core_dir() {
			return trailingslashit( $this->settings['base_dir'] );
		}

		/**
		 * Get URI to the core directory.
		 *
		 * @since 1.0.0
		 * @deprecated 1.1.0 Use `base_url()` method
		 * @return string
		 */
		public function get_core_url() {
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
