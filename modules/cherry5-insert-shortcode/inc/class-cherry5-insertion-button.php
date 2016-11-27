<?php
/**
 * Sets up the admin functionality for the plugin.
 *
 * @package    Blank_Plugin
 * @subpackage Admin
 * @author     Cherry Team
 * @license    GPL-3.0+
 * @copyright  2012-2016, Cherry Team
 */

// If class `Cherry5_Insertion_Button` doesn't exists yet.
if ( ! class_exists( 'Cherry5_Insertion_Button' ) ) {

	/**
	 * Cherry5_Insertion_Button class.
	 */
	class Cherry5_Insertion_Button {

		/**
		 * UI element instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    object
		 */
		public $ui_elements = null;

		/**
		 * Class constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( $core = null, $args = array() ) {
			$this->ui_elements = $core->init_module( 'cherry-ui-elements' );

			add_action( 'media_buttons', array( $this, 'add_button' ) );
		}

		/**
		 * Function add button into tinymce editor.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function add_button() {
			$args = array(
				'id'         => '',
				'name'       => '',
				'style'      => 'normal',
				'content'    => '<span class="cherry5-is-icon dashicons dashicons-plus"></span>' . esc_html__( 'Cherry shortcodes', 'shortcodes-ultimate' ),
				'class'      => 'cherry5-is__open-button',
			);

			echo $this->ui_elements->get_ui_element_instance( 'button', $args )->render();
		}
	}
}

