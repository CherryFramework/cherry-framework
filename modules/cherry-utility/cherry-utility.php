<?php
/**
 * Module Name: Utility
 * Description: Multiple utility functions
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
				'term_thumb' => 'cherry_terms_thumbnails',
			),
		);

		/**
		 * Module directory path.
		 *
		 * @since 1.5.0
		 * @access protected
		 * @var srting.
		 */
		protected $module_path;

		/**
		 * Utilit class.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $utility = null;

		/**
		 * Cherry_Utility constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct( $core, $args = array() ) {
			$this->args        = array_merge( $this->args, $args );
			$this->module_path = $args['module_path'];

			$this->utility_require( $core );
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
				array_unshift( $utility, 'satellite' );

				foreach ( $utility as $utilit ) {

					require_once( $this->module_path . '/inc/cherry-' . $utilit . '-utilit.php' );

					$utilit     = str_replace( '-', ' ', $utilit );
					$class_name = ucwords( $utilit );
					$class_name = str_replace( ' ', '_', $class_name );
					$utilit     = str_replace( ' ', '_', $utilit );
					$class_name = 'Cherry_' . $class_name . '_Utilit';

					$instance = new $class_name( $this );

					$this->utility->{$utilit} = $instance;
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
