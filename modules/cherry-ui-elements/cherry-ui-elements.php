<?php
/**
 * Class for the building ui elements
 * Module Name: UI Elements
 * Description:
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

if ( ! class_exists( 'Cherry_UI_Elements' ) ) {

	/**
	 * Class for the building ui elements
	 */
	class Cherry_UI_Elements {

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
			'ui_elements'	=> array(
				'text',
				'number',
				'textarea',
				'select',
				'checkbox',
				'radio',
				'colorpicker',
				'media',
				'stepper',
				'switcher',
				'slider',
				'collection',
				'chooseicons',
			),
		);

		/**
		 * Cherry_Test_Builder constructor
		 *
		 * @param object $core core.
		 * @param array  $args arguments.
		 *
		 * @since 1.0.0
		 */
		function __construct( $core, $args ) {

			$this->module_directory = $core->settings['base_dir'] . '/modules/cherry-ui-elements';
			$this->module_directory_uri = $core->settings['base_url'] . 'modules/cherry-ui-elements/';

			$this->args = array_merge( $this->args, $args );

			$this->ui_elements_require();

			// Load admin assets.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ), 9 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_admin_assets' ), 9 );
		}

		/**
		 * Get ui element instance.
		 *
		 * @param [type] $ui_slug ui element.
		 * @param array  $args arguments.
		 *
		 * @since  1.0.0
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
		 * Require ui elements
		 *
		 * @return void
		 */
		public function ui_elements_require() {
			// Add I_UI interface.
			if ( ! interface_exists( 'I_UI' ) ) {
				require_once( __DIR__ . '/i-ui.php' );
			}

			if ( ! class_exists( 'UI_Element' ) ) {
				require_once( __DIR__ . '/ui-element.php' );
			}

			if ( ! empty( $this->args['ui_elements'] ) ) {
				foreach ( $this->args['ui_elements'] as $ui_element ) {
					require_once( __DIR__ . '/inc/ui-elements/ui-' . $ui_element . '/ui-' . $ui_element . '.php' );
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
					if ( in_array( 'I_UI', class_implements( $ui_class_name ) ) ) {
						$ui_class_name::enqueue_assets();
					}
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

