<?php
/**
 * Module Name: Assets Loader
 * Description: The module allows you deferred loading scripts and styles.
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

// If class `Cherry5_Assets_Loader` doesn't exists yet.
if ( ! class_exists( 'Cherry5_Assets_Loader' ) ) {

	/**
	 * Cherry5_Assets_Loader class.
	 */
	class Cherry5_Assets_Loader {

		/**
		 * Module slug
		 *
		 * @var string
		 */
		public $module_slug = 'cherry5-assets-loader';

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
		 * CSS handle object for deferred loading.
		 *
		 * @var array
		 */
		public static $css_handle = array();

		/**
		 * JS handle object for deferred loading.
		 *
		 * @var array
		 */
		public static $js_handle = array();

		/**
		 * Is module hooks initialized or not.
		 *
		 * @var boolean
		 */
		public static $initialized = false;

		/**
		 * Module directory path.
		 *
		 * @since 1.5.0
		 * @var   srting.
		 */
		public static $module_path = null;

		/**
		 * Class constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( $core = null, $args = array() ) {

			self::$module_path = $args['module_path'];
			$this->args        = $args;
			$this->init();

			if ( ! empty( $this->args['css'] ) && is_array( $this->args['css'] ) ) {
				self::$css_handle->add_handles( $this->args['css'] );
			}

			if ( ! empty( $this->args['js'] ) && is_array( $this->args['js'] ) ) {
				self::$js_handle->add_handles( $this->args['js'] );
			}
		}

		/**
		 * Initialize module hooks
		 *
		 * @return bool|void
		 */
		public function init() {

			if ( true === self::$initialized ) {
				return null;
			}

			require_once 'inc/cherry5-assets-loader-handle.php';
			require_once 'inc/cherry5-assets-loader-handle-css.php';
			require_once 'inc/cherry5-assets-loader-handle-js.php';

			self::$css_handle = new Cherry5_Assets_Loader_Handle_CSS();
			self::$js_handle  = new Cherry5_Assets_Loader_Handle_JS();

			self::$initialized = true;

			ob_start();
			include 'assets/min/append.min.js';
			$append_handler = ob_get_clean();
			wp_add_inline_script( 'cherry-js-core', $append_handler );

		}

		/**
		 * Returns new module instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $core, $args ) {
			return new self( $core, $args );
		}

	}
}
