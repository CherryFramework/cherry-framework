<?php
/**
 * Module Name: Post Meta
 * Description: Manage post meta
 * Version: 1.1.6
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Cherry_Framework
 * @subpackage Modules
 * @version    1.1.6
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Post_Meta' ) ) {

	/**
	 * Post meta management module.
	 *
	 * @since 1.0.0
	 * @since 1.0.2 Removed `module_directory` property.
	 */
	class Cherry_Post_Meta {

		/**
		 * Module slug
		 *
		 * @var string
		 */
		public $module_slug = 'cherry-post-meta';

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
		 * Current nonce name to check
		 *
		 * @var null
		 */
		public $nonce = 'cherry-meta-nonce';

		/**
		 * Storage of meta values.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		public $meta_values = array();

		/**
		 * Constructor for the module.
		 *
		 * @since 1.0.0
		 */
		public function __construct( $core, $args ) {
			$this->core = $core;
			$this->args = wp_parse_args(
				$args,
				array(
					'id'            => 'cherry-post-metabox',
					'title'         => '',
					'page'          => array( 'post' ),
					'context'       => 'normal',
					'priority'      => 'high',
					'single'        => false,
					'callback_args' => false,
					'fields'        => array(),
				)
			);

			if ( empty( $this->args['fields'] ) ) {
				return;
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'init_ui' ), 1 );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
			add_action( 'save_post', array( $this, 'save_meta' ), 10, 2 );

			$this->init_columns_actions();
		}

		/**
		 * Initalize admin columns
		 *
		 * @return void
		 */
		public function init_columns_actions() {

			if ( empty( $this->args['admin_columns'] ) ) {
				return;
			}

			if ( ! is_array( $this->args['page'] ) ) {
				$pages = array( $this->args['page'] );
			} else {
				$pages = $this->args['page'];
			}

			foreach ( $pages as $page ) {
				add_filter( 'manage_edit-' . $page . '_columns', array( $this, 'edit_columns' ) );
				add_action( 'manage_' . $page . '_posts_custom_column', array( $this, 'manage_columns' ), 10, 2 );
			}

		}

		/**
		 * Edit admin columns
		 *
		 * @since  1.1.3
		 * @param  array $columns current post table columns.
		 * @return array
		 */
		public function edit_columns( $columns ) {

			foreach ( $this->args['admin_columns'] as $column_key => $column_data ) {

				if ( empty( $column_data['label'] ) ) {
					continue;
				}

				if ( ! empty( $column_data['position'] ) && 0 !== (int) $column_data['position'] ) {

					$length = count( $columns );

					if ( (int) $column_data['position'] > $length ) {
						$columns[ $column_key ] = $column_data['label'];
					}

					$columns_before = array_slice( $columns, 0, (int) $column_data['position'] );
					$columns_after  = array_slice( $columns, (int) $column_data['position'], $length - (int) $column_data['position'] );

					$columns = array_merge(
						$columns_before,
						array( $column_key => $column_data['label'] ),
						$columns_after
					);
				} else {
					$columns[ $column_key ] = $column_data['label'];
				}
			}

			return $columns;

		}

		/**
		 * Add output for custom columns.
		 *
		 * @since  1.1.3
		 * @param  string $column  current post list categories.
		 * @param  int    $post_id current post ID.
		 * @return void
		 */
		public function manage_columns( $column, $post_id ) {

			if ( empty( $this->args['admin_columns'][ $column ] ) ) {
				return;
			}

			if ( ! empty( $this->args['admin_columns'][ $column ]['callback'] ) && is_callable( $this->args['admin_columns'][ $column ]['callback'] ) ) {
				call_user_func( $this->args['admin_columns'][ $column ]['callback'], $column, $post_id );
			} else {
				echo get_post_meta( $post_id, $column, true );
			}

		}

		/**
		 * Init UI builder.
		 *
		 * @since  1.0.0
		 * @return bool
		 */
		public function init_ui() {

			if ( ! $this->is_allowed_page() ) {
				return;
			}

			array_walk( $this->args['fields'], array( $this, 'set_field_types' ) );

			if ( in_array( 'slider', $this->field_types ) ) {
				$this->field_types[] = 'stepper';
			}

			$this->ui_builder = $this->core->init_module( 'cherry-ui-elements', array( 'ui_elements' => $this->field_types ) );

			return true;
		}

		/**
		 * Check if defined metabox is allowed on current page
		 *
		 * @since  1.0.0
		 * @return boolean
		 */
		public function is_allowed_page() {

			global $current_screen;

			if ( empty( $current_screen ) ) {
				return false;
			}

			if ( is_array( $this->args['page'] ) && ! in_array( $current_screen->id, $this->args['page'] ) ) {
				return false;
			}

			if ( is_string( $this->args['page'] ) && $current_screen->id !== $this->args['page'] ) {
				return false;
			}

			return true;
		}

		/**
		 * Add meta box handler
		 *
		 * @since  1.0.0
		 * @param  [type] $post_type The post type of the current post being edited.
		 * @param  object $post      The current post object.
		 * @return void
		 */
		public function add_meta_boxes( $post_type, $post ) {

			if ( ! $this->is_allowed_page() ) {
				return;
			}

			add_meta_box(
				$this->args['id'],
				$this->args['title'],
				array( $this, 'render_metabox' ),
				$this->args['page'],
				$this->args['context'],
				$this->args['priority'],
				$this->args['callback_args']
			);
		}

		/**
		 * Render metabox funciton
		 *
		 * @since  1.0.0
		 * @param  object $post    The post object currently being edited.
		 * @param  array  $metabox Specific information about the meta box being loaded.
		 * @return void
		 */
		public function render_metabox( $post, $metabox ) {

			/**
			 * Filter custom metabox output. Prevent from showing main box, if user output passed
			 *
			 * @var string
			 */
			$custom_box = apply_filters( 'cherry_post_meta_custom_box', false, $post, $metabox );

			if ( false !== $custom_box ) {
				echo $custom_box;
				return;
			}

			wp_nonce_field( $this->nonce, $this->nonce );

			/**
			 * Hook fires before metabox output started.
			 */
			do_action( 'cherry_post_meta_box_before' );

			echo $this->get_fields( $post, '<div style="padding:10px 0">%s</div>' );

			/**
			 * Hook fires after metabox output finished.
			 */
			do_action( 'cherry_post_meta_box_after' );

		}

		/**
		 * Get registered control fields
		 *
		 * @since  1.0.0
		 * @since  1.1.3 Using dirname( __FILE__ ) instead of __DIR__.
		 * @param  mixed  $post   Current post object.
		 * @param  [type] $format Current format name.
		 * @return string
		 */
		public function get_fields( $post, $format = '%s' ) {

			$elements = array();

			if ( is_array( $this->args['single'] ) && isset( $this->args['single']['key'] ) ) {
				$this->meta_values = get_post_meta( $post->ID, $this->args['single']['key'], true );
			}

			foreach ( $this->args['fields'] as $key => $field ) {
				$value = $this->get_meta( $post, $key );
				$value = ( false !== $value ) ? $value : Cherry_Toolkit::get_arg( $field, 'value', '' );

				if ( isset( $field['options_callback'] ) ) {
					$options = call_user_func( $field['options_callback'] );
				} else {
					$options = Cherry_Toolkit::get_arg( $field, 'options', array() );
				}

				$args = array(
					'type'               => Cherry_Toolkit::get_arg( $field, 'type', 'text' ),
					'id'                 => Cherry_Toolkit::get_arg( $field, 'id', $key ),
					'name'               => Cherry_Toolkit::get_arg( $field, 'name', $key ),
					'value'              => $value,
					'label'              => Cherry_Toolkit::get_arg( $field, 'label', '' ),
					'add_label'          => Cherry_Toolkit::get_arg( $field, 'add_label', '' ),
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

				$elements[] = array(
					'html'  => $current_element->render(),
					'field' => $field,
				);

			}
			return Cherry_Toolkit::render_view(
				dirname( __FILE__ ) . '/views/meta.php',
				array(
					'elements' => $elements,
				)
			);
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

			if ( ! is_array( $field ) || ! isset( $field['type'] ) ) {
				return false;
			}

			if ( ! in_array( $field['type'], $this->field_types ) ) {
				$this->field_types[] = $field['type'];
			}

			$this->maybe_add_repeater_fields( $field );

			return true;

		}

		/**
		 * Maybe add reapeater sub-fields to required elements list
		 *
		 * @since  1.0.1
		 * @param  array $field field data.
		 * @return bool
		 */
		public function maybe_add_repeater_fields( $field ) {

			if ( 'repeater' !== $field['type'] || empty( $field['fields'] ) ) {
				return false;
			}

			foreach ( $field['fields'] as $repeater_field ) {
				$this->set_field_types( $repeater_field, null );
			}

			return true;

		}

		/**
		 * Save additional taxonomy meta on edit or create tax
		 *
		 * @since  1.0.0
		 * @param  int    $post_id The ID of the current post being saved.
		 * @param  object $post    The post object currently being saved.
		 * @return void|int
		 */
		public function save_meta( $post_id, $post = '' ) {

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! isset( $_POST[ $this->nonce ] ) || ! wp_verify_nonce( $_POST[ $this->nonce ], $this->nonce ) ) {
				return;
			}

			$posts = ! empty( $this->args['page'] ) ? $this->args['page'] : array( 'post' );
			$posts = is_array( $posts ) ? $posts : array( $posts );

			$maybe_break = false;

			foreach ( $posts as $post_type ) {

				if ( get_post_type( $post_id ) !== $post_type ) {
					$maybe_break = true;
					continue;
				}

				$maybe_break = false;
				$obj         = get_post_type_object( $post_type );

				if ( ! isset( $obj->cap->edit_posts ) || ! current_user_can( $obj->cap->edit_posts ) ) {
					$maybe_break = true;
					continue;
				}

				break;
			}

			if ( true === $maybe_break ) {
				return;
			}

			if ( ! $this->is_allowed_page() ) {
				return;
			}

			if ( ! is_object( $post ) ) {
				$post = get_post();
			}

			/**
			 * Hook on current metabox saving
			 */
			do_action( 'cherry_save_meta_' . $this->args['id'] );

			if ( is_array( $this->args['single'] ) && isset( $this->args['single']['key'] ) ) {
				$this->save_meta_mod( $post_id );
			} else {
				$this->save_meta_option( $post_id );
			}

		}

		/**
		 * Save all meta values as a one array value in `wp_postmeta` table.
		 *
		 * @since 1.1.0
		 * @param int $post_id Post ID.
		 */
		public function save_meta_mod( $post_id ) {
			$meta_key = $this->args['single']['key'];

			// Array of new post meta value.
			$new_meta_value = array();

			foreach ( $_POST[ $meta_key ] as $key => $value ) {

				$new_meta_value[ $key ] = $this->sanitize_meta( $key, $value );
			}

			// Get current post meta data.
			$meta_value = get_post_meta( $post_id, $meta_key, true );

			if ( $new_meta_value && '' == $meta_value ) {
				add_post_meta( $post_id, $meta_key, $new_meta_value, true );
			} elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
				update_post_meta( $post_id, $meta_key, $new_meta_value );
			} elseif ( empty( $new_meta_value ) && $meta_value ) {
				delete_post_meta( $post_id, $meta_key, $meta_value );
			}
		}

		/**
		 * Save each meta value as a single value in `wp_postmeta` table.
		 *
		 * @since 1.1.0
		 * @param int $post_id Post ID.
		 */
		public function save_meta_option( $post_id ) {

			foreach ( $this->args['fields'] as $key => $field ) {

				if ( empty( $_POST[ $key ] ) ) {
					update_post_meta( $post_id, $key, false );
					continue;
				}

				$value = $this->sanitize_meta( $key, $_POST[ $key ] );
				update_post_meta( $post_id, $key, $value );
			}

		}

		/**
		 * Sanitize passed meta value
		 *
		 * @since  1.1.3
		 * @param  string $key   Meta key to sanitize.
		 * @param  mixed  $value Meta value.
		 * @return mixed
		 */
		public function sanitize_meta( $key, $value ) {

			if ( empty( $this->args['fields'][ $key ]['sanitize_callback'] ) ) {
				return $this->sanitize_deafult( $value );
			}

			if ( ! is_callable( $this->args['fields'][ $key ]['sanitize_callback'] ) ) {
				return $this->sanitize_deafult( $value );
			}

			return call_user_func(
				$this->args['fields'][ $key ]['sanitize_callback'],
				$value,
				$key,
				$this->args['fields'][ $key ]
			);

		}

		/**
		 * Cleare value with sanitize_text_field if not is array
		 *
		 * @since  1.1.3
		 * @param  mixed $value Passed value.
		 * @return mixed
		 */
		public function sanitize_deafult( $value ) {
			return is_array( $value ) ? $value : sanitize_text_field( $value );
		}

		/**
		 * Retrieve post meta field.
		 *
		 * @since  1.1.0
		 * @param  object $post Current post object.
		 * @param  string $key  The meta key to retrieve.
		 * @return string
		 */
		public function get_meta( $post, $key ) {

			if ( ! is_object( $post ) ) {
				return '';
			}

			if ( is_array( $this->args['single'] ) && isset( $this->args['single']['key'] ) ) {
				$meta = isset( $this->meta_values[ $key ] ) ? $this->meta_values[ $key ] : '';
			} else {
				$meta = get_post_meta( $post->ID, $key, true );
			}

			return $meta;
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $core, $args ) {

			if ( ! is_admin() ) {
				return;
			}

			return new self( $core, $args );
		}
	}
}
