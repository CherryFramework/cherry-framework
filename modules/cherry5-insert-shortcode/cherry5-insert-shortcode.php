<?php
/**
 * Module Name: Insert Shortcode
 * Description: The module allows you to add shortcodes from editor tinyMCE.
 * Version: 1.0.1
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Cherry_Framework
 * @subpackage Modules
 * @version    1.0.1
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// If class `Cherry5_Insert_Shortcode` doesn't exists yet.
if ( ! class_exists( 'Cherry5_Insert_Shortcode' ) ) {

	/**
	 * Cherry5_Insert_Shortcode class.
	 */
	class Cherry5_Insert_Shortcode {

		/**
		 * Module version.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private $module_version = '1.0.1';

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var object
		 * @access private
		 */
		private static $instance = null;

		/**
		 * Module arguments
		 *
		 * @since 1.0.0
		 * @var array
		 * @access private
		 */
		private $args = array();

		/**
		 * Core instance
		 *
		 * @since 1.0.0
		 * @var object
		 * @access private
		 */
		private $core = null;

		/**
		 * UI element instance.
		 *
		 * @since  1.0.0
		 * @var    object
		 * @access public
		 */
		public $ui_elements = null;

		/**
		 * Cherry Interface Builder instance.
		 *
		 * @since  1.0.0
		 * @var    object
		 * @access public
		 */
		public $cherry_interface_builder = null;

		/**
		 * A reference to an instance of this class Cherry_Insert_Admin_Button.
		 *
		 * @since 1.0.0
		 * @var object
		 * @access private
		 */
		private $shortcodes_button = null;

		/**
		 * A reference to an instance of this class Cherry5_Insertion_Popup.
		 *
		 * @since 1.0.0
		 * @var object
		 * @access private
		 */
		private $shortcodes_popup = null;

		/**
		 * Shortcode list.
		 *
		 * @since  1.0.0
		 * @var    object
		 * @access private
		 */
		private $added_shortcodes = array();

		/**
		 * Class constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( $core = null, $args = array(), $init = true ) {
			if ( $init ) {
				$this->core = $core;
				$this->args = array_merge_recursive(
					$args,
					array(
						'module_dir' => trailingslashit( dirname( __FILE__ ) ),
						'in_screen'  => array( 'post' ),
					)
				);

				$this->ui_elements = $this->core->init_module( 'cherry-ui-elements' );
				$this->cherry_interface_builder = $this->core->init_module( 'cherry-interface-builder' );

				// Include libraries from the `inc/`.
				$this->includes();

				// Initializing child classes.
				$this->shortcodes_button = new Cherry5_Insertion_Button( $this->core, $this->args, $this );
				$this->shortcodes_popup = new Cherry5_Insertion_Popup( $this->core, $this->args, $this );

				// Register admin assets.
				add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ), 0 );

				// Load admin assets.
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ), 11 );
			}
		}

		/**
		 * Include libraries from the `inc/`.
		 *
		 * @since 1.0.0
		 * @access private
		 * @return void
		 */
		private function includes() {
			require_once( dirname( __FILE__ ) . '/inc/class-cherry5-insertion-button.php' );
			require_once( dirname( __FILE__ ) . '/inc/class-cherry5-insertion-popup.php' );
		}

		/**
		 * Register assets.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function register_assets() {
			// Register stylesheets.
			wp_register_style( 'cherry5-insert-shortcode', esc_url( Cherry_Core::base_url( 'assets/min/cherry-insert-shortcode.min.css', __FILE__ ) ), array(), $this->module_version, 'all' );

			// Register JavaScripts.
			wp_register_script( 'cherry5-insert-shortcode-js', esc_url( Cherry_Core::base_url( 'assets/min/cherry-insert-shortcode.min.js', __FILE__ ) ), array( 'cherry-js-core' ), $this->module_version, true );
		}

		/**
		 * Enqueue admin stylesheets.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function enqueue_assets() {
			$screen = get_current_screen();

			if ( in_array( $screen->base, $this->args['in_screen'] ) ) {
				wp_enqueue_style( 'cherry5-insert-shortcode' );
				wp_enqueue_script( 'cherry5-insert-shortcode-js' );

				$dev_mode = ( constant( 'WP_DEBUG' ) ) ? 'true' : 'false' ;
				wp_localize_script( 'cherry-js-core', 'cherry5InsertShortcode', array( 'devMode' => $dev_mode ) );
			}
		}

		/**
		 * The function is called a filter to add shortcode.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function register_shortcode( $args = array() ) {
			$this->added_shortcodes = $args;
			add_filter( 'cherry5-is__shortcode_list', array( $this, 'add_new_shortcode' ), 10, 1 );
		}

		/**
		 * Function add new shortcode.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return array
		 */
		public function add_new_shortcode( $shortcodes = array() ) {
			$plugin_slug = $this->added_shortcodes['slug'];
			$new_shortcodes = $this->added_shortcodes['shortcodes'];
			$this->added_shortcodes['shortcodes'] = array();

			if ( ! array_key_exists( $plugin_slug, $shortcodes ) ) {
				$shortcodes[ $plugin_slug ] = $this->added_shortcodes;
			}

			foreach ( $new_shortcodes as $value ) {
				$shortcode_slug = $value['slug'];

				if ( array_key_exists( $shortcode_slug, $shortcodes[ $plugin_slug ]['shortcodes'] ) ) {
					continue;
				}

				$shortcodes[ $plugin_slug ]['shortcodes'][ $shortcode_slug ] = $value;
			}

			return $shortcodes;
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public static function get_instance( $core = null, $args = array() ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $core, $args );
			}

			return self::$instance;
		}
	}
	// If class `Cherry5_Insert_Shortcode` doesn't exists yet.
	if ( ! function_exists( 'cherry5_register_shortcode' ) ) {

		/**
		 * The function registers a new shortcode.
		 *
		 * @since  1.0.0
		 */
		function cherry5_register_shortcode( $args = array() ) {
			$cherry5_insert_shortcode = new Cherry5_Insert_Shortcode( null, array(), false );
			$cherry5_insert_shortcode->register_shortcode( $args );
		}
	}
}
