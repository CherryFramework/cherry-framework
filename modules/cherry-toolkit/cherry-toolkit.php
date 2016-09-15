<?php
/**
 * Module Name: Framework Toolkit
 * Description: Framework Toolkit contains various PHP utilities
 * Version: 1.2.0
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @version    1.2.0
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Toolkit' ) ) {

	/**
	 * Various PHP utilities
	 */
	 class Cherry_Toolkit {

		/**
		 * Module slug
		 *
		 * @var string Module slug
		 */
		public $module_slug = 'cherry-toolkit';

		/**
		 * Constructor for the module
		 *
		 * @param Cherry_Core $core Core instance.
		 * @param array       $args Module arguments.
		 */
		public function __construct( $core, $args ) {}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $core, $args ) {
			return new self( $core, $args );
		}

		/**
		 * Safely get attribute from field settings array.
		 *
		 * @since  1.0.0
		 * @param  array            $field   arguments array.
		 * @param  string|int|float $arg     argument key.
		 * @param  mixed            $default default argument value.
		 * @return mixed
		 */
		public static function get_arg( $field, $arg, $default = '' ) {

			if ( is_array( $field ) && isset( $field[ $arg ] ) ) {
				return $field[ $arg ];
			}

			return $default;
		}

		/**
		 * Get class instance
		 *
		 * @param  string      $class_name Class name.
		 * @param  Cherry_Core $core Core instance.
		 * @param  array       $args Additional arguments.
		 * @return object New class instance.
		 * @throws InvalidArgumentException If class does not exists.
		 */
		public static function get_class_instance( $class_name = '', $core, $args ) {
			if ( ! class_exists( $class_name ) ) {
				throw new InvalidArgumentException( 'Class "' . $class_name . '" doesn\'t exists' );
			}

			return new $class_name( $core, $args );
		}

		/**
		 * Render view
		 *
		 * @param  string $path View path.
		 * @param  array  $data Include data.
		 * @return string       Rendered html.
		 */
		public static function render_view( $path, array $data = array() ) {

			// Add parameters to temporary query variable.
			if ( array_key_exists( 'wp_query', $GLOBALS ) ) {
				if ( is_array( $GLOBALS['wp_query']->query_vars ) ) {
					$GLOBALS['wp_query']->query_vars['__data'] = $data;
				}
			}

			ob_start();
			load_template( $path, false );
			$result = ltrim( ob_get_clean() );

			/**
			 * Remove temporary wp query variable
			 * Yeah. I'm paranoic.
			 */
			if ( array_key_exists( 'wp_query', $GLOBALS ) ) {
				if ( is_array( $GLOBALS['wp_query']->query_vars ) ) {
					unset( $GLOBALS['wp_query']->query_vars['__data'] );
				}
			}

			// Return the compiled view and terminate the output buffer.
			return $result;
		}

		/**
		 * Remove empty elements
		 *
		 * @param  array $arr --- array with empty elements.
		 * @return array --- array without empty elements
		 */
		public static function remove_empty( $arr ) {
			return array_filter( $arr, array( __CLASS__, 'remove_empty_check' ) );
		}

		/**
		 * Check if empty.
		 * It's need for PHP 5.2.4 version
		 *
		 * @param  [type] $var variable.
		 * @return boolean
		 */
		public static function remove_empty_check( $var ) {
			return '' != $var;
		}

		/**
		 * Join array to string
		 *
		 * @param  array $arr --- array like 'key' => 'value'.
		 * @return string --- joined string
		 */
		public static function join( $arr = array() ) {
			$arr    = self::remove_empty( $arr );
			$result = array();
			foreach ( $arr as $key => $value ) {
				$result[] = sprintf( '%s="%s"', $key, $value );
			}
			return implode( ' ', $result );
		}

		/**
		 * Lave just right keys in array
		 *
		 * @param  array $right_keys right keys to leave.
		 * @param  array $array list.
		 * @return array
		 */
		public static function leave_right_keys( $right_keys, $array ) {
			$right_keys = (array) $right_keys;
			$array      = (array) $array;
			if ( count( $array ) ) {
				foreach ( $array as $key => $value ) {
					if ( ! in_array( $key, $right_keys ) ) {
						unset( $array[ $key ] );
					}
				}
			}
			return $array;
		}

		/**
		 * Safely get file content by path
		 *
		 * @param  string $filepath Path to file.
		 * @return mixed
		 */
		public static function get_file( $filepath ) {

			if ( ! file_exists( $filepath ) ) {
				return false;
			}

			ob_start();
			include $filepath;
			return ob_get_clean();
		}

		/**
		 * Remove some keys form array
		 *
		 * @param  [type] $right_keys keys to remove.
		 * @param  [type] $array      where we want remove this keys.
		 * @return array without keys
		 */
		public static function remove_right_keys( $right_keys, $array ) {
			$right_keys = (array) $right_keys;
			$array      = (array) $array;
			if ( count( $right_keys ) ) {
				foreach ( $right_keys as $key ) {
					if ( array_key_exists( $key, $array ) ) {
						unset( $array[ $key ] );
					}
				}
			}
			return $array;
		}
	}
}
