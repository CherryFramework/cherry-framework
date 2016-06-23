<?php
/**
 * Module Name: Utility
 * Description: Multiple utility functions
 * Version: 1.0.3
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Cherry_Framework
 * @subpackage Modules
 * @version    1.0.3
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Utility' ) ) {

	/**
	 * Class Cherry Utility.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Removed `module_directory` and `module_directory_uri` properties.
	 */
	class Cherry_Utility {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance = null;

		/**
		 * Default arguments.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $args = array(
			'utility' => array(
				'media',
				'attributes',
				'meta-data',
			),
			'meta_key' => array(
				'term_thumb' => 'cherry_thumb',
			),
		);

		/**
		 * Utilit class.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $utility = null;

		/**
		 * Default static args.
		 *
		 * @since 1.0.0
		 * @deprecated 1.0.1 Don't use this property
		 * @var array
		 */
		private static $static_args = array();

		/**
		 * Cherry_Utility constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct( $core, $args = array() ) {
			$this->args = array_merge( $this->args, $args );
			$this->utility_require( $core );

			// Backward compatibility.
			array_unshift( $this->args['utility'], 'satellite' );
			self::$static_args = $this->args;
		}

		/**
		 * Require utility.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function utility_require() {

			if ( ! empty( $this->args['utility'] ) ) {

				$this->utility = new stdClass();
				$utility = $this->args['utility'];

				if ( ! in_array( 'satellite', $utility ) ) {
					array_unshift( $utility, 'satellite' );
				}

				foreach ( $utility as $utilit ) {

					require_once( __DIR__ . '/inc/cherry-' . $utilit . '-utilit.php' );

					$utilit     = str_replace( '-', ' ', $utilit );
					$class_name = ucwords( $utilit );
					$class_name = str_replace( ' ', '_', $class_name );
					$utilit     = str_replace( ' ', '_', $utilit );
					$class_name = 'Cherry_' . $class_name . '_Utilit';

					$this->utility->$utilit = new $class_name( $this );
				}
			}
		}

		/**
		 * Require utility.
		 *
		 * @since 1.0.0
		 * @deprecated 1.0.1 Don't use this method
		 * @return void
		 */
		public static function utility_composition( $self ) {
			$utility = self::$static_args['utility'];

			if ( ! empty( $utility ) ) {
				$self->{'utility'} = new stdClass();

				foreach ( $utility as $utilit ) {
					$sud_module = str_replace('-', '_', $utilit );
					$class_name = str_replace('-', ' ', $utilit );
					$class_name = str_replace(' ', '_', ucwords( $class_name ) );
					$class_name = 'Cherry_' . $class_name . '_Utilit';

					$self->utility->$sud_module = new $class_name();
				}
			}
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $core, $args ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $core, $args );
			}

			return self::$instance;
		}
	}
}
