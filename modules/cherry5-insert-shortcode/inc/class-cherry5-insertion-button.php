<?php
/**
 * Sets up the admin functionality for the plugin.
 *
 * @package    Cherry_Framework
 * @subpackage Modules
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
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
				'style'      => 'primary',
				'content'    => '<span class="cherry5-is__icon dashicons dashicons-plus"></span>' . esc_html__( 'Cherry shortcodes', 'cherry-framework' ),
				'class'      => 'cherry5-is__open-button',
			) );

			echo $this->parent_self->ui_elements->get_ui_element_instance( 'button', $args )->render();
		}
	}
}

