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

if ( ! class_exists( 'Cherry_UI_Elements' ) ) {

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
			'ui_elements'	=> array( 'text', 'textarea', 'select', 'checkbox', 'radio', 'colorpicker', 'media', 'stepper', 'switcher', 'slider' ),
		);

		/**
		* Cherry_Test_Builder constructor
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
		}

		/**
		 * Get ui element instance.
		 *
		 * @param  string ui_element slug.
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

			if ( ! empty( $this->args['ui_elements'] ) ) {
				foreach ( $this->args['ui_elements'] as $ui_element ) {
					require_once( $this->module_directory . '/inc/ui-elements/ui-' . $ui_element . '/ui-' . $ui_element . '.php' );
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
					$ui_class_name::enqueue_assets();
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

?>
