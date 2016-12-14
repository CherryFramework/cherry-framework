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
		 * Module arguments
		 *
		 * @var array
		 */
		public $args = array();

		/**
		 * Core instance
		 *
		 * @var object
		 */
		public $core = null;

		/**
		 * Class constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( $core = null, $args = array(), $parent_self = null ) {
			$this->core        = $core;
			$this->args        = $args;
			$this->parent_self = $parent_self;

			add_action( 'media_buttons', array( $this, 'add_button' ) );
			add_action( 'cherry5-insert-shortcode', array( $this, 'add_button' ) );
		}

		/**
		 * Function add button into tinymce editor.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function add_button() {
			$args = apply_filters( 'cherry5-is__open-button', array(
				'id'         => '',
				'name'       => '',
				'style'      => 'normal',
				'content'    => '<span class="cherry5-is__icon dashicons dashicons-plus"></span>' . esc_html__( 'Cherry shortcodes', 'cherry' ),
				'class'      => 'cherry5-is__open-button',
			) );

			echo $this->parent_self->ui_elements->get_ui_element_instance( 'button', $args )->render();
		}
	}
}

