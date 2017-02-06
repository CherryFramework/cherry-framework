<?php
/**
 * Class for parse templates.
 *
 * @package    Template_Manager
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
		 * A reference to an instance of this Cherry_Template_Manager class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
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
			'macros_callback' => '/%%.+?%%/',
			'macros_variable' => '/\$\$.+?\$\$/',
		);

		/**
		 * Keeps the user callbacks class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $callbacks_class = null;

		/**
		 * Cherry_Template_Parser constructor.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( $args = array(), $main_class = null ) {
			$this->args = wp_parse_args(
				$args,
				$this->args
			);

			$this->cherry_template_manager_class = $main_class;
		}

		/**
		 * Function parsed template.
		 *
		 * @since  1.0.0
		 * @param  string          $template_name   Template Name.
		 * @param  string|stdClass $class           An instance or class name.
		 * @param  string          $macros_callback The regular expression for the callback.
		 * @param  string          $macros_variable The regular expression for the variable.
		 * @access public
		 * @return string|bool
		 */
		public function parsed_template( $template_name = false, $class = false, $macros_callback = false, $macros_variable = false ) {
			if ( $template_name && $class ) {

				if ( ! $macros_callback ) {
					$macros_callback = $this->args['macros_callback'];
				}

				if ( ! $macros_variable ) {
					$macros_variable = $this->args['macros_variable'];
				}

				$search_form_template = $this->cherry_template_manager_class->loader->get_template_by_name( $template_name );

				if ( ! $search_form_template ) {
					return false;
				}

				if ( 'string' === gettype( $class ) && class_exists( $class ) ) {
					$class = new $class();
				}

				if ( $class !== self::$callbacks_class ) {
					self::$callbacks_class = $class;
				}

				$ouput = preg_replace_callback( $macros_callback, array( $this, 'replace_callback' ), $search_form_template );
				$ouput = preg_replace_callback( $macros_variable, array( $this, 'replace_variable' ), $ouput );

				return $ouput;
			} else {
				return false;
			}

		}

		/**
		 * Callback to replace macros with data.
		 *
		 * @since  1.0.0
		 * @param  array $matches Founded macros.
		 * @access private
		 * @return string
		 */
		private function replace_callback( $matches, $slug = '' ) {
			if ( ! is_array( $matches ) || empty( $matches ) ) {
				return;
			}

			$slug     = $this->cherry_template_manager_class->loader->get_argument( 'slug' );
			$item     = trim( $matches[0], '%%' );
			$arr      = explode( ' ', $item, 2 );
			$macros   = strtolower( $arr[0] );
			$attr     = isset( $arr[1] ) ? shortcode_parse_atts( $arr[1] ) : array();
			$callback = apply_filters( $slug . '_set_callback_' . $macros, array( self::$callbacks_class, 'get_' . $macros ) );

			if ( ! is_callable( $callback ) ) {
				return;
			}

			if ( ! empty( $attr ) ) {
				// Call a WordPress function.
				return call_user_func( $callback, $attr );
			}

			return call_user_func( $callback );
		}

		/**
		 * Callback to replace macros with data.
		 *
		 * @since  1.0.0
		 * @param  array $matches Founded macros.
		 * @access private
		 * @return string
		 */
		private function replace_variable( $matches, $slug = '' ) {

			if ( ! is_array( $matches ) || empty( $matches ) ) {
				return;
			}

			$slug     = $this->cherry_template_manager_class->loader->get_argument( 'slug' );
			$item     = trim( $matches[0], '$$' );
			$arr      = explode( ' ', $item, 2 );
			$macros   = strtolower( $arr[0] );
			$variable = apply_filters( $slug . '_set_variable_' . $macros, null );

			if ( null === $variable ) {
				if ( isset( self::$callbacks_class->variable ) && array_key_exists( $macros, self::$callbacks_class->variable ) ) {
					$variable = self::$callbacks_class->variable[ $macros ];
				} else {
					return;
				}
			}

			return $variable;
		}

		/**
		 * Returns argument.
		 *
		 * @since  1.0.0
		 * @param  string $argument_name Argument name.
		 * @access public
		 * @return object
		 */
		public function get_argument( $argument_name ) {
			if ( isset( $this->args[ $argument_name ] ) ) {
				return $this->args[ $argument_name ];
			} else {
				return;
			}
		}
	}
}
