<?php
/**
 * Module Name: Term Meta
 * Description: Manage term metadata
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

if ( ! class_exists( 'Cherry_Term_Meta' ) ) {

	/**
	 * Term meta management module
	 */
	class Cherry_Term_Meta {

		/**
		 * Module slug
		 *
		 * @var string
		 */
		public $module_slug = 'cherry-term-meta';

		/**
		 * Module arguments
		 *
		 * @var array
		 */
		public $args = array();

		/**
		 * Existing field types
		 *
		 * @var array
		 */
		public $field_types = array();

		/**
		 * UI builder instance
		 *
		 * @var object
		 */
		public $ui_builder = null;

		/**
		 * Already registered field.
		 *
		 * @since  1.0.2
		 * @var array
		 */
		static public $register_fields = array();

		/**
		 * Core instance
		 *
		 * @var object
		 */
		public $core = null;

		/**
		 * Constructor for the module
		 */
		function __construct( $core, $args ) {

			$this->core = $core;
			$this->args = wp_parse_args( $args, array(
				'tax'      => 'category',
				'priority' => 10,
				'fields'   => array(),
			) );

			if ( empty( $this->args['fields'] ) ) {
				return;
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'init_ui' ), 1 );

			$priority = intval( $this->args['priority'] );
			$tax      = esc_attr( $this->args['tax'] );

			add_action( "{$tax}_add_form_fields", array( $this, 'render_add_fields' ), $priority );
			add_action( "{$tax}_edit_form_fields", array( $this, 'render_edit_fields' ), $priority, 2 );

			add_action( "created_{$tax}", array( $this, 'save_meta' ) );
			add_action( "edited_{$tax}", array( $this, 'save_meta' ) );
		}

		/**
		 * Init UI builder.
		 *
		 * @since  1.0.0
		 * @return bool
		 */
		public function init_ui() {

			global $current_screen;

			if ( empty( $current_screen ) || ! in_array( $current_screen->base, array( 'edit-tags', 'term' ) ) ) {
				return false;
			}

			array_walk( $this->args['fields'], array( $this, 'set_field_types' ) );

			if ( in_array( 'slider', $this->field_types ) ) {
				$this->field_types[] = 'stepper';
			}

			$this->ui_builder = $this->core->init_module( 'cherry-ui-elements', $this->field_types );

			return true;
		}

		/**
		 * Render add term form fields
		 *
		 * @since  1.0.0
		 * @param  [type] $taxonomy taxonomy name.
		 * @return void
		 */
		public function render_add_fields( $taxonomy ) {

			$format = '<div style="padding:10px 0;">%s</div>';
			echo $this->get_fields( false, $taxonomy, $format );
		}

		/**
		 * Render edit term form fields
		 *
		 * @since  1.0.0
		 * @param  object $term     current term object.
		 * @param  [type] $taxonomy taxonomy name.
		 * @return void
		 */
		public function render_edit_fields( $term, $taxonomy ) {

			$format = '<tr class="form-field cherry-term-meta-wrap"><th>&nbsp;</th><td>%s</td></tr>';
			echo $this->get_fields( $term, $taxonomy, $format );
		}

		/**
		 * Get registered control fields
		 *
		 * @since  1.0.0
		 * @param  mixed  $term     current term object.
		 * @param  [type] $taxonomy current taxonomy name.
		 * @return string
		 */
		public function get_fields( $term, $taxonomy, $format = '%s' ) {

			$result = '';

			foreach ( $this->args['fields'] as $key => $field ) {

				if ( in_array( $key, Cherry_Term_Meta::$register_fields ) ) {
					continue;
				} else {
					Cherry_Term_Meta::$register_fields[] = $key;
				}

				if ( false !== $term ) {
					$value = get_term_meta( $term->term_id, $key, true );
				} else {
					$value = '';
				}

				$value = ! empty( $value ) ? $value : Cherry_Toolkit::get_arg( $field, 'value', '' );

				if ( isset( $field['options_callback'] ) ) {
					$options = call_user_func( $field['options_callback'] );
				} else {
					$options = Cherry_Toolkit::get_arg( $field, 'options', array() );
				}

				$args = array(
					'type'               => Cherry_Toolkit::get_arg( $field, 'type', 'text' ),
					'id'                 => $key,
					'name'               => $key,
					'value'              => $value,
					'label'              => Cherry_Toolkit::get_arg( $field, 'label', '' ),
					'options'            => $options,
					'multiple'           => Cherry_Toolkit::get_arg( $field, 'multiple', false ),
					'filter'             => Cherry_Toolkit::get_arg( $field, 'filter', false ),
					'size'               => Cherry_Toolkit::get_arg( $field, 'size', 1 ),
					'null_option'        => Cherry_Toolkit::get_arg( $field, 'null_option', 'None' ),
					'multi_upload'       => Cherry_Toolkit::get_arg( $field, 'multi_upload', true ),
					'library_type'       => Cherry_Toolkit::get_arg( $field, 'library_type', 'image' ),
					'upload_button_text' => Cherry_Toolkit::get_arg( $field, 'upload_button_text', 'Choose' ),
					'max_value'          => Cherry_Toolkit::get_arg( $field, 'max_value', '100' ),
					'min_value'          => Cherry_Toolkit::get_arg( $field, 'min_value', '0' ),
					'max'                => Cherry_Toolkit::get_arg( $field, 'max', '100' ),
					'min'                => Cherry_Toolkit::get_arg( $field, 'min', '0' ),
					'step_value'         => Cherry_Toolkit::get_arg( $field, 'step_value', '1' ),
					'style'              => Cherry_Toolkit::get_arg( $field, 'style', 'normal' ),
					'display_input'      => Cherry_Toolkit::get_arg( $field, 'display_input', true ),
					'controls'           => Cherry_Toolkit::get_arg( $field, 'controls', array() ),
					'fields'             => Cherry_Toolkit::get_arg( $field, 'fields', array() ),
					'auto_parse'         => Cherry_Toolkit::get_arg( $field, 'auto_parse', false ),
					'icon_data'          => Cherry_Toolkit::get_arg( $field, 'icon_data', array() ),
					'toggle'             => Cherry_Toolkit::get_arg( $field, 'toggle', array(
						'true_toggle'  => 'On',
						'false_toggle' => 'Off',
						'true_slave'   => '',
						'false_slave'  => '',
					) ),
					'class'       => Cherry_Toolkit::get_arg( $field, 'class' ),
					'required'    => Cherry_Toolkit::get_arg( $field, 'required', false ),
					'placeholder' => Cherry_Toolkit::get_arg( $field, 'placeholder' ),
					'master'      => Cherry_Toolkit::get_arg( $field, 'master' ),
					'title_field' => Cherry_Toolkit::get_arg( $field, 'title_field' ),
					'ui_kit'      => Cherry_Toolkit::get_arg( $field, 'ui_kit', true ),
				);

				$current_element = $this->ui_builder->get_ui_element_instance( $args['type'], $args );

				$result .= sprintf( $format, $current_element->render() );

			}

			return $result;

		}

		/**
		 * Store field types used in this widget into class property
		 *
		 * @since  1.0.0
		 * @param  array  $field field data.
		 * @param  [type] $id    field key.
		 * @return bool
		 */
		public function set_field_types( $field, $id ) {

			if ( is_array( $field ) || ! isset( $field['type'] ) ) {
				return false;
			}

			if ( ! in_array( $field['type'], $this->field_types ) ) {
				$this->field_types[] = $field['type'];
			}

			return true;

		}

		/**
		 * Save additional taxonomy meta on edit or create tax
		 *
		 * @since  1.0.0
		 * @param  int $term_id Term ID.
		 * @return bool
		 */
		public function save_meta( $term_id ) {

			if ( ! current_user_can( 'edit_posts' ) ) {
				return false;
			}

			foreach ( $this->args['fields'] as $key => $field ) {

				if ( ! isset( $_POST[ $key ] ) ) {
					continue;
				}

				if ( is_array( $_POST[ $key ] ) ) {
					$new_val = array_filter( $_POST[ $key ] );
				} else {
					$new_val = esc_attr( $_POST[ $key ] );
				}

				update_term_meta( $term_id, $key, $new_val );

			}

			return true;

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
