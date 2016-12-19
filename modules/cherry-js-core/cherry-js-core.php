<?php
/**
 * Module Name: JS Core
 * Description: Initializes global JS object which provides additional plugin functionality
 * Version: 1.1.2
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Cherry_Framework
 * @subpackage Modules
 * @version    1.1.2
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Js_Core' ) ) {

	/**
	 * JS-core class.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Removed `module_directory` and `module_directory_uri` properties.
	 */
	class Cherry_Js_Core {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance = null;

		/**
		 * Module version.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private $module_version = '1.1.2';

		/**
		 * Default options.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $options = array(
			'product_type' => 'framework',
			'src'          => false,
			'version'      => false,
		);

		/**
		 * Class constructor.
		 *
		 * @since 1.0.0
		 * @param object $core Core instance.
		 * @param array  $args Class args.
		 */
		public function __construct( $core, $args = array() ) {
			$this->options = array_merge( $this->options, $args );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_cherry_scripts' ), 0 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_cherry_scripts' ), 0 );
			add_action( 'wp_print_scripts', array( $this, 'localize_script' ) );
		}

		/**
		 * Register and enqueue JS-core.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_cherry_scripts() {

			if ( 'framework' === $this->options['product_type'] ) {
				$src     = esc_url( Cherry_Core::base_url( 'assets/js/min/cherry-js-core.min.js', __FILE__ ) );
				$version = $this->module_version;
			} else {
				$src     = ( ! empty( $this->options['src'] ) ? esc_url( $this->options['src'] ) : false );
				$version = ( ! empty( $this->options['version'] ) ? absint( $this->options['src'] ) : false );
			}

			wp_enqueue_script( 'cherry-js-core', $src, array( 'jquery' ), $version, true );
		}

		/**
		 * Retrieve a scripts list.
		 *
		 * @since 1.0.0
		 * @return $array
		 */
		private function get_include_script() {
			return $this->add_suffix( '.js', wp_scripts()->queue );
		}

		/**
		 * Retrieve a styles list.
		 *
		 * @since 1.0.0
		 * @return $array
		 */
		private function get_include_style() {
			return $this->add_suffix( '.css', wp_styles()->queue );
		}

		/**
		 * [get_ui_init_settings]
		 *
		 * @since 1.0.0
		 * @return $array
		 */
		private function get_ui_init_settings() {

			// Default auto ui init settings.
			$ui_init_settings = array(
				'auto_init' => false,
				'targets'   => array(),
			);

			/**
			 * Filter to determine the list of selectors and the value of the automatic initialization ui js scripts
			 *
			 * @var array
			 */
			return apply_filters( 'cherry_core_js_ui_init_settings', $ui_init_settings );
		}

		/**
		 * Add suffix to array.
		 *
		 * @since 1.0.0
		 */
		private function add_suffix( $suffix, $array ) {

			foreach ( $array as $key => $value ) {
				$array[ $key ] = $value . $suffix;
			}

			return $array;
		}

		/**
		 * Prepare data for API script.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function localize_script() {
			wp_localize_script( 'cherry-js-core', 'wp_load_style', $this->get_include_style() );
			wp_localize_script( 'cherry-js-core', 'wp_load_script', $this->get_include_script() );
			wp_localize_script( 'cherry-js-core', 'cherry_ajax', wp_create_nonce( 'cherry_ajax_nonce' ) );

			$ui_init_settings = $this->get_ui_init_settings();
			$ui_init_settings['auto_init'] = ( true == $ui_init_settings['auto_init'] ) ? 'true' : 'false';
			wp_localize_script( 'cherry-js-core', 'ui_init_object', $ui_init_settings );
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
