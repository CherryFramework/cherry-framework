<?php
/**
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Utility' ) ) {

	class Cherry_Utility {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Module version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private $module_version = '1.0.0';

		/**
		 * Module directory
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private $module_directory = '';

		/**
		 * Module directory URL
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private $module_directory_uri = '';

		/**
		 * Default args
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $args = array(
			'utility'	=> array(
				'media',
				'attributes',
				'meta-data',
			),
			'meta_key'	=> array(
				'term_thumb'	=> 'cherry_thumb'
			)
		);

		/**
		 * Satellite utilit class
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private static $satellite_utilit_class = 'satellite';

		/**
		 * Default static args
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private static $static_args = array();

		/**
		* Cherry_Utility constructor
		*
		* @since 1.0.0
		*/
		function __construct( $core, $args = array() ) {

			$this->module_directory = $core->settings['base_dir'] . '/modules/cherry-utility';
			$this->module_directory_uri = $core->settings['base_url'] . 'modules/cherry-utility/';

			$this->args = array_merge( $this->args, $args );
			array_unshift( $this->args['utility'], self::$satellite_utilit_class );
			self::$static_args = $this->args;

			$this->utility_require();
		}

		/**
		 * Require utility
		 *
		 * @return void
		 */
		public function utility_require() {

			if ( ! empty( $this->args['utility'] ) ) {

				$utility = $this->args['utility'];
				foreach ( $utility as $utilit ) {
					require_once( $this->module_directory . '/inc/cherry-' . $utilit . '-utilit.php' );
				}
			}
		}

		/**
		 * Require utility
		 *
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

					$self->utility->$sud_module = new $class_name( self::$static_args );
				}
			}
		}
		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $core, $args = array() ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $core, $args );
			}

			return self::$instance;
		}
	}
}