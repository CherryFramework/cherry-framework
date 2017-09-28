<?php
/**
 * Module Name: UI Elements
 * Description: Class for the building ui elements
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

if ( ! class_exists( 'Cherry_UI_Elements' ) ) {

	/**
	 * Class for the building ui elements.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Removed `module_directory` and `module_directory_uri` properties.
	 */
	class Cherry_UI_Elements {

		/**
		 * Default arguments.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $args = array(
			'ui_elements' => array(
				'text',
				'textarea',
				'select',
				'checkbox',
				'radio',
				'colorpicker',
				'media',
				'stepper',
				'switcher',
				'slider',
				'repeater',
				'iconpicker',
				'button',
				'dimensions',
			),
		);

		/**
		 * Core version.
		 *
		 * @since 1.5.0
		 * @access public
		 * @var string
		 */
		public static $core_version = '';

		/**
		 * Module directory path.
		 *
		 * @since 1.5.0
		 * @access protected
		 * @var srting.
		 */
		public static $module_path;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @param object $core Core.
		 * @param array  $args Arguments.
		 */
		public function __construct( $core, $args ) {
			$this->args         = array_merge( $this->args, $args );
			self::$core_version = $core->get_core_version();
			self::$module_path  = $args['module_path'];

			$this->ui_elements_require();

			// Load admin assets.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ), 9 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_admin_assets' ), 9 );
		}

		/**
		 * Get UI-element instance.
		 *
		 * @since  1.0.0
		 * @param [type] $ui_slug ui element.
		 * @param  array  $args arguments.
		 * @return object
		 */
		public function get_ui_element_instance( $ui_slug, $args ) {

			if ( empty( $ui_slug ) ) {
				echo '<p>Set an empty slug</p>';
				return false;
			}

			if ( ! in_array( $ui_slug, $this->args['ui_elements'] ) ) {
				echo '<p> Element <b>' . $ui_slug . '</b> has not been initialized in this instance!</p>';
				return false;
			}

			$ui_class_name = 'UI_' . ucwords( $ui_slug );

			if ( ! class_exists( $ui_class_name ) ) {
				echo '<p>Class <b>' . $ui_class_name . '</b> not exist!</p>';
				return false;
			}
			return new $ui_class_name( $args );
		}

		/**
		 * Require UI-elements.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function ui_elements_require() {

			// Add I_UI interface.
			if ( ! interface_exists( 'I_UI' ) ) {
				require_once( self::$module_path . 'i-ui.php' );
			}

			require_once( self::$module_path. 'ui-element.php' );
			require_once( self::$module_path . 'inc/class-cherry-lock-element.php' );

			if ( ! empty( $this->args['ui_elements'] ) ) {
				foreach ( $this->args['ui_elements'] as $ui_element ) {
					require_once( self::$module_path . 'inc/ui-elements/ui-' . $ui_element . '/ui-' . $ui_element . '.php' );
				}
			}
		}

		/**
		 * Load admin assets.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_admin_assets() {
			if ( ! empty( $this->args['ui_elements'] ) ) {
				foreach ( $this->args['ui_elements'] as $ui_element ) {
					$ui_class_name = 'UI_' . ucwords( $ui_element );
					call_user_func( array( $ui_class_name, 'enqueue_assets' ) );
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
			return new self( $core, $args );
		}
	}
}
