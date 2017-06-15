<?php
/**
 * Module Name: Template Manager
 * Description: Module load and parse tmpl files.
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Template_Manager
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

if ( ! class_exists( 'Cherry_Template_Manager' ) ) {

	/**
	 * Class Cherry Template Manager.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Template_Manager {

		/**
		 * Module directory path.
		 *
		 * @since 1.5.0
		 * @access protected
		 * @var srting.
		 */
		protected $module_path;

		/**
		 * Module arguments.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    array
		 */
		private $args = array();

		/**
		 * Core instance
		 *
		 * @var object
		 */
		public $core = null;

		/**
		 * It contains a class Cherry_Template_Parser.
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    object
		 */
		public $parser = null;

		/**
		 * It contains a class Cherry_Template_Loader.
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    object
		 */
		public $loader = null;

		/**
		 * Cherry_Template_Manager constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( $core = null, $args = array() ) {
			$this->core = $core;

			$this->include_class();

			$this->args = wp_parse_args(
				$args,
				$this->args
			);

			$this->module_path  = $args['module_path'];

			$this->set_class();
		}

		/**
		 * Include abstract widget class
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function include_class() {
			require_once( $this->module_path . 'inc/cherry-template-loader.php' );
			require_once( $this->module_path . 'inc/cherry-template-parser.php' );
		}

		/**
		 * Function set the child classes.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function set_class() {
			$this->loader = new Cherry_Template_Loader( $this->args, $this );
			$this->parser = new Cherry_Template_Parser( $this->args, $this );
		}
	}
}
