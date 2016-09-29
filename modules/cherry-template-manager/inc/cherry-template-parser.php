<?php
/**
 * Module Name: Template Parser
 * Description: Module parsed tmpl files.
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

if ( ! class_exists( 'Cherry_Template_Parser' ) ) {

	/**
	 * Class Cherry Template Parser.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Template_Parser {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance = null;

		/**
		 * A reference to an instance of this Cherry_Template_Manager class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private $cherry_template_manager_class = null;

		/**
		 * Module arguments.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    array
		 */
		private $args = array(
			'macros_callback'       => '/%%.+?%%/',
			'macros_variable'       => '/\$\$.+?\$\$/',
		);

		/**
		 * Keeps the user callbacks class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $Callbacks_Class = null;

		/**
		 * Cherry_Template_Parser constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct( $args = array(), $main_class = null ) {
			$this->args = array_merge_recursive(
				$args,
				$this->args
			);

			$this->cherry_template_manager_class = $main_class;
		}

		/**
		 * Function parsed template.
		 *
		 * @since  1.0.0
		 * @param  string          $template_name  Template Name.
		 * @param  string|stdClass $class          An instance or class name.
		 * @param  string          $macros         The regular expression for the macro.
		 * @access public
		 * @return string|bool
		 */
		public function parsed_template( $template_name, $class = false, $macros = false ) {

			if ( $template_name && $class ) {
				if ( ! $macros ) {
					$macros = $this->args['macros'];
				}

				$search_form_template = $this->cherry_template_manager_class->loader->get_template_by_name( $template_name );

				if ( ! $search_form_template ) {
					return false;
				}

				if ( 'string' === gettype( $class ) && class_exists( $class ) ) {
					$class = new $class();
				}

				if ( $class !== self::$Callbacks_Class ) {
					self::$Callbacks_Class = $class;
				}

				return preg_replace_callback( $this->args['macros_callback'], array( $this, 'replace_callback' ), $search_form_template );
			} else {
				return false;
			}

		}

		/**
		 * Callback to replace macros with data.
		 *
		 * @since 1.0.0
		 * @param array $matches Founded macros.
		 * @access private
		 */
		private function replace_callback( $matches ) {

			if ( ! is_array( $matches ) ) {
				return false;
			}

			if ( empty( $matches ) ) {
				return false;
			}

			$item   = trim( $matches[0], '%%' );
			$arr    = explode( ' ', $item, 2 );
			$macros = strtolower( $arr[0] );
			$attr   = isset( $arr[1] ) ? shortcode_parse_atts( $arr[1] ) : array();

			$callback = array( self::$Callbacks_Class, 'get_' . $macros );

			if ( ! is_callable( $callback ) ) {
				return false;
			}

			if ( ! empty( $attr ) ) {
				// Call a WordPress function.
				return call_user_func( $callback, $attr );
			}

			return call_user_func( $callback );
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $args, $main_class ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $args, $main_class );
			}

			return self::$instance;
		}
	}
}
