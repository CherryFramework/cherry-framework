<?php
/**
 * Class Cherry Array Utilit
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Array_Utilit' ) ) {

	/**
	 * Class Cherry Media Utilit
	 */
	class Cherry_Array_Utilit {

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
		 * Try get value by key from array
		 *
		 * @param  array $array values list.
		 * @param  type  $key value key.
		 * @param  type  $default default value.
		 * @return mixed value by key
		 */
		public static function get( $array, $key, $default = '' ) {
			$array = (array) $array;
			if ( is_null( $key ) ) {
				return $array;
			}
			if ( array_key_exists( $key, $array ) ) {
				return $array[ $key ];
			}
			return $default;
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
