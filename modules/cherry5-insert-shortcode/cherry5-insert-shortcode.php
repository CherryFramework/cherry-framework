<?php
/**
 * Module Name: Insert Shortcode
 * Description: The module allows you to add shortcodes editor tinyMCE.
 * Version: 1.0.0
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Cherry_Framework
 * @subpackage Modules
 * @version    1.0.0
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
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var object
		 * @access private
		 */
		private static $instance = null;

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
		 * @access public
		 */
		public $shortcodes_popup = null;

		/**
		 * Shortcode list.
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    object
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
				// Include libraries from the `includes/admin`.
				$this->includes();

				// Initializing child classes.
				$this->shortcodes_button = new Cherry5_Insertion_Button( $core, $args );
				$this->shortcodes_popup = new Cherry5_Insertion_Popup( $core, $args );

				// Register admin assets.
				add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ), 0 );

				// Load admin assets.
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			}
		}

		/**
		 * Include libraries from the `includes/admin`.
		 *
		 * @since 1.0.0
		 * @access public
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
		 */
		public function register_assets() {

			// Register stylesheets.
			wp_register_style( 'cherry5-insert-shortcode', esc_url( Cherry_Core::base_url( 'assets/min/cherry-insert-shortcode.min.css', __FILE__ ) ), array(), '1.0.0', 'all' );

			// Register JavaScripts.
			wp_register_script( 'cherry5-insert-shortcode-js', esc_url( Cherry_Core::base_url( 'assets/min/cherry-insert-shortcode.min.js', __FILE__ ) ), array( 'cherry-js-core' ), '1.0.0' , true );
		}

		/**
		 * Enqueue admin stylesheets.
		 *
		 * @since  1.0.0
		 * @access public
		 * @param  string $hook The current admin page.
		 * @return void
		 */
		public function enqueue_assets( $hook ) {
			$screen = get_current_screen();

			if ( 'post' === $screen->base ) {
				wp_enqueue_style( 'cherry5-insert-shortcode' );
				wp_enqueue_script( 'cherry5-insert-shortcode-js' );
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
			add_filter( 'cherry5_shortcode_list', array( $this, 'add_shortcode' ), 10, 1 );
		}

		/**
		 * Function add new shortcode.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function add_shortcode( $shotrcodes = array() ) {
			$plugin_slug = $this->added_shortcodes['slug'];
			$new_shortcodes = $this->added_shortcodes['shortcodes'];
			$this->added_shortcodes['shortcodes'] = [];

			if ( ! array_key_exists( $plugin_slug, $shotrcodes ) ) {
				$shotrcodes[ $plugin_slug ] = $this->added_shortcodes;
			}

			foreach ( $new_shortcodes as $value ) {
				$shortcode_slug = $value['slug'];

				if ( array_key_exists( $shortcode_slug, $shotrcodes[ $plugin_slug ]['shortcodes'] ) ) {
					continue;
				}

				$shotrcodes[ $plugin_slug ]['shortcodes'][ $shortcode_slug ] = $value;
			}

			return $shotrcodes;
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
		function cherry5_register_shortcode( $args =array() ) {
			$cherry5_insert_shortcode = new Cherry5_Insert_Shortcode( null, array(), false );
			$cherry5_insert_shortcode->register_shortcode( $args );
		}
	}
}
