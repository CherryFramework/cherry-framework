<?php
/**
 * Abstract widget class.
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.en.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Abstract_Widget' ) ) {

	/**
	 * Define Cherry_Abstract_Widget class
	 */
	abstract class Cherry_Abstract_Widget extends WP_Widget {

		/**
		 * CSS class
		 *
		 * @var string
		 */
		public $widget_cssclass;

		/**
		 * Widget description
		 *
		 * @var string
		 */
		public $widget_description;

		/**
		 * Widget ID
		 *
		 * @var string
		 */
		public $widget_id;

		/**
		 * Widget name
		 *
		 * @var string
		 */
		public $widget_name;

		/**
		 * Settings
		 *
		 * @var array
		 */
		public $settings;

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
		public $ui_builder;

		/**
		 * Temporary arguments holder
		 *
		 * @var array
		 */
		public $args;

		/**
		 * Temporary instance holder
		 *
		 * @var array
		 */
		public $instance;

		/**
		 * Core instance
		 *
		 * @var null
		 */
		public $core = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			$widget_ops = array(
				'classname'   => $this->widget_cssclass,
				'description' => $this->widget_description,
			);

			parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

			add_action( 'save_post', array( $this, 'flush_cache' ) );
			add_action( 'deleted_post', array( $this, 'flush_cache' ) );
			add_action( 'switch_theme', array( $this, 'flush_cache' ) );

			if ( $this->is_ajax() ) {
				add_action( 'admin_init', array( $this, 'admin_init' ) );
			} else {
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_init' ), 1 );
			}

			add_action( 'widgets.php', array( $this, 'ajax_init' ), 1 );

			add_filter( 'widget_display_callback', array( $this, 'prepare_instance' ), 10, 2 );

		}

		/**
		 * Get default widget instance from settings
		 *
		 * @since  1.0.0
		 * @param  array     $instance The current widget instance's settings.
		 * @param  WP_Widget $widget   The current widget instance.
		 * @return array
		 */
		public function prepare_instance( $instance, $widget ) {

			if ( ! empty( $instance ) ) {
				return $instance;
			}

			$instance = array();

			if ( empty( $widget->settings ) ) {
				return $instance;
			}

			foreach ( $widget->settings as $key => $data ) {

				if ( ! isset( $data['value'] ) ) {
					$instance[ $key ] = '';
				} else {
					$instance[ $key ] = $data['value'];
				}
			}

			return $instance;
		}

		/**
		 * Check if is AJAX-request processing.
		 *
		 * @return boolean
		 */
		public function is_ajax() {
			return ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX );
		}

		/**
		 * Initalize UI elements in admin area widgets page
		 *
		 * @since  1.0.0
		 * @return bool
		 */
		public function admin_init() {

			$current_screen  = get_current_screen();
			$is_allowed_page = ( $current_screen && 'widgets' == $current_screen->id );

			/**
			 * Filter - is current admin page are allowed to apply UI elements for
			 *
			 * @var bool
			 */
			$is_allowed_page = apply_filters( 'cherry_widget_factory_allowed_ui_page', $is_allowed_page );

			if ( ! $this->is_ajax() && ! $is_allowed_page ) {
				return false;
			}

			$this->init_ui();
		}

		/**
		 * Initalize UI elements on AJAX callbacks
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function ajax_init() {
			$this->init_ui();
		}

		/**
		 * Init UI builder.
		 *
		 * @since  1.0.0
		 * @return bool
		 */
		public function init_ui() {

			if ( empty( $this->settings ) ) {
				return false;
			}

			array_walk( $this->settings, array( $this, 'set_field_types' ) );

			if ( in_array( 'slider', $this->field_types ) ) {
				$this->field_types[] = 'stepper';
			}

			$core = $this->get_core();

			if ( ! $core ) {
				return false;
			}

			$this->ui_builder = $core->init_module( 'cherry-ui-elements', array( 'ui_elements' => $this->field_types ) );

			return true;
		}

		/**
		 * Get widget cache ID
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_cache_id() {
			return apply_filters( 'cherry_cached_widget_id', $this->widget_id );
		}

		/**
		 * Get cached widget from WordPress object cache
		 *
		 * @since  1.0.0
		 * @param  array $args widget arguments array.
		 * @return bool
		 */
		public function get_cached_widget( $args ) {

			$cache = wp_cache_get( $this->get_cache_id(), 'widget' );

			if ( ! is_array( $cache ) ) {
				$cache = array();
			}

			if ( isset( $cache[ $args['widget_id'] ] ) ) {
				echo $cache[ $args['widget_id'] ];
				return true;
			}

			return false;
		}

		/**
		 * Get core instance inside of widget class
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public function get_core() {

			if ( null == $this->core ) {
				$this->core = apply_filters( 'cherry_widget_factory_core', false, dirname( __FILE__ ) );
			}

			return $this->core;

		}

		/**
		 * Save widget into WordPress object cache
		 *
		 * @since  1.0.0
		 * @param  array  $args    widget arguments.
		 * @param  [type] $content widget content.
		 * @return string the content that was cached
		 */
		public function cache_widget( $args, $content ) {
			wp_cache_set( $this->get_cache_id(), array( $args['widget_id'] => $content ), 'widget' );

			return $content;
		}

		/**
		 * Flush the cache
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function flush_cache() {
			wp_cache_delete( $this->get_cache_id(), 'widget' );
		}

		/**
		 * Output the html at the start of a widget
		 *
		 * @since  1.0.0
		 * @param  array $args     widget arguments.
		 * @param  array $instance widget instance.
		 * @return void
		 */
		public function widget_start( $args, $instance ) {

			echo $args['before_widget'];

			$title = apply_filters(
				'widget_title',
				empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base
			);

			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
		}

		/**
		 * Output the html at the end of a widget
		 *
		 * @since  1.0.0
		 * @param  array $args widget arguments.
		 * @return void
		 */
		public function widget_end( $args ) {
			echo $args['after_widget'];
		}

		/**
		 * Update function.
		 *
		 * @since  1.0.0
		 * @see    WP_Widget->update
		 * @param  array $new_instance new widget instance, passed from widget form.
		 * @param  array $old_instance old instance, saved in database.
		 * @return array
		 */
		public function update( $new_instance, $old_instance ) {

			$instance = $old_instance;

			if ( empty( $this->settings ) ) {
				return $instance;
			}

			foreach ( $this->settings as $key => $setting ) {

				if ( isset( $new_instance[ $key ] ) ) {

					$instance[ $key ] = ! empty( $setting['sanitize_callback'] ) && is_callable( $setting['sanitize_callback'] )
					? call_user_func( $setting['sanitize_callback'],$new_instance[ $key ] )
					: $this->sanitize_instance_item( $new_instance[ $key ] );

				} elseif ( 'checkbox' === $setting['type'] ) {
					$instance[ $key ] = array();
				} elseif ( isset( $old_instance[ $key ] ) && is_array( $old_instance[ $key ] ) ) {
					$instance[ $key ] = array();
				} elseif ( isset( $old_instance[ $key ] ) ) {
					$instance[ $key ] = '';
				}

				// WPML - register strings for translation.
				if ( in_array( $setting['type'], array( 'text', 'textarea' ) ) && 'title' !== $key ) {
					do_action( 'wpml_register_single_string', 'Widgets', "{$this->widget_name} - {$key}", $instance[ $key ] );
				}
			}

			$this->flush_cache();

			/**
			 * Fires after current widget update is proceed, before returning result.
			 *
			 * @since 1.0.0
			 * @param array $instance current widget instance.
			 */
			do_action( 'cherry_widget_after_update', $instance );

			return $instance;
		}

		/**
		 * Sanitize widget instance item
		 *
		 * @since  1.0.0
		 * @param  mixed $input instance item to sanitize.
		 * @return mixed
		 */
		public function sanitize_instance_item( $input ) {
			if ( is_array( $input ) ) {
				return array_filter( $input );
			} else {
				return sanitize_text_field( $input );
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
		 * Render single from control.
		 *
		 * @param  array $args control arguments.
		 * @return void|null
		 */
		public function render_control( $args ) {

			$allowed_controls = array(
				'text',
				'textarea',
				'checkbox',
				'colorpicker',
				'media',
				'radio',
				'select',
				'slider',
				'stepper',
				'switcher',
				'slider',
				'collection',
				'chooseicons',
				'repeater',
				'iconpicker',
			);

			if ( ! in_array( $args['type'], $allowed_controls ) ) {
				do_action( 'cherry_widget_factory_control', $args );
				return;
			}

			if ( ! is_object( $this->ui_builder ) ) {
				return;
			}

			$current_element = $this->ui_builder->get_ui_element_instance( $args['type'], $args );

			?>
			<div>
				<?php echo $current_element->render(); ?>
			</div>
			<?php

		}

		/**
		 * Show widget form
		 *
		 * @since  1.0.0
		 * @see    WP_Widget->form
		 * @param  array $instance current widget instance.
		 * @return void
		 */
		public function form( $instance ) {

			if ( empty( $this->settings ) ) {
				return;
			}

			foreach ( $this->settings as $key => $setting ) {

				$value = isset( $instance[ $key ] ) ? $instance[ $key ] : Cherry_Toolkit::get_arg( $setting, 'value', '' );

				if ( isset( $setting['options_callback'] ) ) {

					$callback = $this->get_callback_data( $setting['options_callback'] );
					$options  = call_user_func_array( $callback['callback'], $callback['args'] );

				} else {
					$options = Cherry_Toolkit::get_arg( $setting, 'options', array() );
				}

				$args = array(
					'type'               => Cherry_Toolkit::get_arg( $setting, 'type', 'text' ),
					'id'                 => $this->get_field_id( $key ),
					'name'               => $this->get_field_name( $key ),
					'value'              => $value,
					'label'              => Cherry_Toolkit::get_arg( $setting, 'label', '' ),
					'options'            => $options,
					'multiple'           => Cherry_Toolkit::get_arg( $setting, 'multiple', false ),
					'filter'             => Cherry_Toolkit::get_arg( $setting, 'filter', false ),
					'size'               => Cherry_Toolkit::get_arg( $setting, 'size', 1 ),
					'null_option'        => Cherry_Toolkit::get_arg( $setting, 'null_option', 'None' ),
					'multi_upload'       => Cherry_Toolkit::get_arg( $setting, 'multi_upload', true ),
					'library_type'       => Cherry_Toolkit::get_arg( $setting, 'library_type', 'image' ),
					'upload_button_text' => Cherry_Toolkit::get_arg( $setting, 'upload_button_text', 'Choose' ),
					'max_value'          => Cherry_Toolkit::get_arg( $setting, 'max_value', '100' ),
					'min_value'          => Cherry_Toolkit::get_arg( $setting, 'min_value', '0' ),
					'step_value'         => Cherry_Toolkit::get_arg( $setting, 'step_value', '1' ),
					'style'              => Cherry_Toolkit::get_arg( $setting, 'style', 'normal' ),
					'placeholder'        => Cherry_Toolkit::get_arg( $setting, 'placeholder', '' ),
					'toggle'             => Cherry_Toolkit::get_arg( $setting, 'toggle', array(
						'true_toggle'  => 'On',
						'false_toggle' => 'Off',
						'true_slave'   => '',
						'false_slave'  => '',
					) ),
					'master'             => Cherry_Toolkit::get_arg( $setting, 'master', '' ),
					'icon_data'          => Cherry_Toolkit::get_arg( $setting, 'icon_data', array() ),
					'title_field'        => Cherry_Toolkit::get_arg( $setting, 'title_field' ),
					'add_label'          => Cherry_Toolkit::get_arg( $setting, 'add_label', '' ),
					'fields'             => Cherry_Toolkit::get_arg( $setting, 'fields', array() ),
					'ui_kit'             => Cherry_Toolkit::get_arg( $setting, 'ui_kit', true ),
				);

				$this->render_control( $args );
			}
		}

		/**
		 * Parse callback data.
		 *
		 * @since  1.0.0
		 * @param  array $options_callback Callback data.
		 * @return array
		 */
		public function get_callback_data( $options_callback ) {

			if ( 2 === count( $options_callback ) ) {

				$callback = array(
					'callback' => $options_callback,
					'args'     => array(),
				);

				return $callback;
			}

			$callback = array(
				'callback' => array_slice( $options_callback, 0, 2 ),
				'args'     => $options_callback[2],
			);

			return $callback;
		}

		/**
		 * Save current widget data to property object properties
		 *
		 * @since  1.0.0
		 * @param  array $args     widget arguments.
		 * @param  array $instance current widget instance.
		 */
		public function setup_widget_data( $args, $instance ) {
			$this->args     = $args;
			$this->instance = $instance;
		}

		/**
		 * Clear current widget data.
		 *
		 * @since  1.0.0
		 */
		public function reset_widget_data() {

			$this->args     = null;
			$this->instance = null;

			/**
			 * Fires on widgget data reseting
			 */
			do_action( 'cherry_widget_reset_data' );
		}

		/**
		 * Add widget_id-related CSS selector
		 *
		 * @since  1.2.0
		 * @param  string $selector Selector inside widget.
		 * @param  array  $args     widget arguments (optional, pass it only setup_widget_data not called before).
		 * @return string|bool
		 */
		public function add_selector( $selector = null, $args = array() ) {

			if ( null == $this->args && empty( $args ) ) {
				return false;
			}

			$args = null !== $this->args ? $this->args : $args;

			return sprintf( '#%1$s %2$s', $args['widget_id'], $selector );
		}

		/**
		 * Retrieve a string translation via WPML.
		 *
		 * @since  1.0.1
		 * @param  [type] $id Widget setting ID.
		 */
		public function use_wpml_translate( $id ) {
			return ! empty( $this->instance[ $id ] ) ? apply_filters( 'wpml_translate_single_string', $this->instance[ $id ], 'Widgets', "{$this->widget_name} - {$id}" ) : '';
		}
	}
}
