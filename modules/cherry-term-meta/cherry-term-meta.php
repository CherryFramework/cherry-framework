<?php
/**
 * Term meta management module
 * Module Name: Term Meta
 * Description: Manage term metadata
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

if ( ! class_exists( 'Cherry_Term_Meta' ) ) {

	/**
	 * Term meta management module
	 */
	class Cherry_Term_Meta {

		/**
		 * Module version
		 *
		 * @var string
		 */
		public $module_version = '1.0.0';

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

			if ( empty( $current_screen ) || 'edit-tags' !== $current_screen->base ) {
				return false;
			}

			add_filter( 'cherry_core_js_ui_init_settings', array( $this, 'init_ui_js' ), 10 );

			array_walk( $this->args['fields'], array( $this, 'set_field_types' ) );

			$this->ui_builder = $this->core->init_module( 'cherry-ui-elements', $this->field_types );

			return true;
		}

		/**
		 * Init UI elements JS
		 *
		 * @since  1.0.0
		 * @param  array $settings UI elements init.
		 * @return array
		 */
		public function init_ui_js( $settings ) {

			$settings['auto_init'] = true;
			$settings['targets']   = array( 'body' );

			return $settings;

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

				if ( false !== $term ) {
					$value = get_term_meta( $term->term_id, $key, true );
				} else {
					$value = '';
				}

				$value = ! empty( $value ) ? $value : $this->get_arg( $field, 'value', '' );

				if ( isset( $field['options_callback'] ) ) {
					$options = call_user_func( $field['options_callback'] );
				} else {
					$options = $this->get_arg( $field, 'options', array() );
				}

				$args = array(
					'type'               => $this->get_arg( $field, 'type', 'text' ),
					'id'                 => $key,
					'name'               => $key,
					'value'              => $value,
					'label'              => $this->get_arg( $field, 'label', '' ),
					'options'            => $options,
					'multiple'           => $this->get_arg( $field, 'multiple', false ),
					'filter'             => $this->get_arg( $field, 'filter', false ),
					'size'               => $this->get_arg( $field, 'size', 1 ),
					'null_option'        => $this->get_arg( $field, 'null_option', 'None' ),
					'multi_upload'       => $this->get_arg( $field, 'multi_upload', true ),
					'library_type'       => $this->get_arg( $field, 'library_type', 'image' ),
					'upload_button_text' => $this->get_arg( $field, 'upload_button_text', 'Choose' ),
					'max_value'          => $this->get_arg( $field, 'max_value', '100' ),
					'min_value'          => $this->get_arg( $field, 'min_value', '0' ),
					'step_value'         => $this->get_arg( $field, 'step_value', '1' ),
					'style'              => $this->get_arg( $field, 'style', 'normal' ),
					'toggle'             => $this->get_arg( $field, 'toggle', array(
						'true_toggle'  => 'On',
						'false_toggle' => 'Off',
						'true_slave'   => '',
						'false_slave'  => '',
					) ),
				);

				$current_element = $this->ui_builder->get_ui_element_instance( $args['type'], $args );

				$result .= sprintf( $format, $current_element->render() );

			}

			return $result;

		}

		/**
		 * Safely get attribute from field settings array.
		 *
		 * @since  1.0.0
		 * @param  array  $field   arguments array.
		 * @param  [type] $arg     argument key.
		 * @param  mixed  $default default argument value.
		 * @return mixed
		 */
		public function get_arg( $field, $arg, $default = '' ) {

			if ( isset( $field[ $arg ] ) ) {
				return $field[ $arg ];
			} else {
				return $default;
			}

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
