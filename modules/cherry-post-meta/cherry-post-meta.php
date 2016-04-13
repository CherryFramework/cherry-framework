<?php
/**
 * Post meta management module
 * Module Name: Post Meta
 * Description: Manage post meta
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

if ( ! class_exists( 'Cherry_Post_Meta' ) ) {

	/**
	 * Post meta management module
	 */
	class Cherry_Post_Meta {

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
		 * Module directory
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private $module_directory = '';

		/**
		 * Constructor for the module
		 */
		function __construct( $core, $args ) {
			$this->module_directory = $core->settings['base_dir'] . '/modules/' . $this->module_slug;
			$this->core = $core;
			$this->args = wp_parse_args(
				$args,
				array(
					'id'            => 'cherry-post-metabox',
					'title'         => '',
					'page'          => array( 'post' ),
					'context'       => 'normal',
					'priority'      => 'high',
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

			add_filter( 'cherry_core_js_ui_init_settings', array( $this, 'init_ui_js' ), 10 );

			array_walk( $this->args['fields'], array( $this, 'set_field_types' ) );

			$this->ui_builder = $this->core->init_module( 'cherry-ui-elements', $this->field_types );

			return true;
		}

		/**
		 * Init UI elements JS
		 *
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function init_ui_js() {

			$settings['auto_init'] = true;
			$settings['targets']   = array( 'body' );

			return $settings;

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

			wp_nonce_field( $this->nonce, $this->nonce );
			echo $this->get_fields( $post, '<div style="padding:10px 0">%s</div>' );

		}

		/**
		 * Get registered control fields
		 *
		 * @since  1.0.0
		 * @param  mixed  $post     current post object.
		 * @param  [type] $format current format name.
		 * @return string
		 */
		public function get_fields( $post, $format = '%s' ) {

			$elements = array();

			foreach ( $this->args['fields'] as $key => $field ) {

				if ( is_object( $post ) ) {
					$value = get_post_meta( $post->ID, $key, true );
				} else {
					$value = '';
				}

				$value = ( false !== $value ) ? $value : Cherry_Toolkit::get_arg( $field, 'value', '' );

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
					'toggle'             => Cherry_Toolkit::get_arg( $field, 'toggle', array(
						'true_toggle'  => 'On',
						'false_toggle' => 'Off',
						'true_slave'   => '',
						'false_slave'  => '',
					) ),
					'required'           => Cherry_Toolkit::get_arg( $field, 'required', false ),
				);

				$current_element = $this->ui_builder->get_ui_element_instance( $args['type'], $args );

				$elements[] = array(
					'html'  => $current_element->render(),
					'field' => $field,
				);

			}
			return Cherry_Core::render_view(
				$this->module_directory . '/views/meta.php',
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

			if ( ! current_user_can( 'edit_posts' ) ) {
				return;
			}

			if ( ! is_object( $post ) ) {
				$post = get_post();
			}

			foreach ( $this->args['fields'] as $key => $field ) {

				if ( ! isset( $_POST[ $key ] ) ) {
					continue;
				}

				update_post_meta( $post_id, $key, $_POST[ $key ] );

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
