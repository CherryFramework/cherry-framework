<?php
/**
 * Framework Toolkit contains various PHP utilities
 * Module Name: Framework Toolkit
 * Description: Various PHP utilities
 * Version: 1.0.0
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Cherry_Framework
 * @subpackage Class
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
		 * Module version
		 *
		 * @var string Module version
		 */
		public $module_version = '1.0.0';

		/**
		 * Module slug
		 *
		 * @var string Module slug
		 */
		public $module_slug = 'cherry-toolkit';

		/**
		 * Constructor for the module
		 * @param Cherry_Core $core Core instance
		 * @param array       $args Module arguments
		 */
		function __construct( $core, $args ) {
		  // void
		}

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
		 * Get class instance
		 * @param  string       $class_name Class name.
		 * @param  Cherry_Core  $core       Core instance.
		 * @param  array $args
		 * @return object                   [description]
		 */
		public static function get_class_instance( $class_name, $core, $args ) {
		  if ( ! class_exists( $class_name ) ) {
		    throw new InvalidArgumentException( 'Class "' . $class_name . '" does not exists' );
		  }

		  return new $class_name( $core, $args );
		}
	}
}
