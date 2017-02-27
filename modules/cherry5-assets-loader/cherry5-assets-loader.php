<?php
/**
 * Module Name: Assets Loader
 * Description: The module allows you deferred loading scripts and styles.
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
		 * CSS hanles list for deferred loading.
		 *
		 * @var array
		 */
		public static $css = array();

		/**
		 * Prepared CSS tags list
		 *
		 * @var array
		 */
		public static $css_tags = array();

		/**
		 * JS hanles list for deferred loading.
		 *
		 * @var array
		 */
		public static $js = array();

		/**
		 * Prepared JS tags list
		 *
		 * @var array
		 */
		public static $js_tags = array();

		/**
		 * Is module hooks initalized or not.
		 *
		 * @var boolean
		 */
		public static $initlized = false;

		/**
		 * Class constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( $core = null, $args = array() ) {

			$this->args = $args;

			if ( ! empty( $this->args['css'] ) && is_array( $this->args['css'] ) ) {
				self::$css = array_merge( self::$css, $this->args['css'] );
			}

			if ( ! empty( $this->args['js'] ) && is_array( $this->args['js'] ) ) {
				self::$js = array_merge( self::$css, $this->args['css'] );
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'init' ), 0 );
		}

		/**
		 * Initalize module hooks
		 *
		 * @return bool|void
		 */
		public function init() {

			if ( ! self::$initlized ) {
				return null;
			}

			require 'inc/cherry5-assets-loader-handle.php';
			require 'inc/cherry5-assets-loader-handle-css.php';
			require 'inc/cherry5-assets-loader-handle-js.php';

			if ( ! empty( self::$css ) ) {
				new Cherry5_Assets_Loader_Handle_CSS( self::$css );
			}

			if ( ! empty( self::$js ) ) {
				new Cherry5_Assets_Loader_Handle_CSS( self::$js );
			}

		}

	}
}
