<?php
/**
 * Module Name: Interface Builder
 * Description: The module for the creation of interfaces in the WordPress admin panel
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

if ( ! class_exists( 'Cherry_Interface_Builder' ) ) {

	/**
	 * Class Cherry Interface Builder.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Interface_Builder {

		/**
		 * Core version.
		 *
		 * @since 1.5.0
		 * @access public
		 * @var string
		 */
		public $core_version = '';

		/**
		 * Module directory path.
		 *
		 * @since 1.5.0
		 * @access protected
		 * @var srting.
		 */
		protected $module_path;

		/**
		 * Module settings.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    array
		 */
		private $args = array(
			'views' => array(
				'section'                  => 'inc/views/section.php',
				'component-tab-vertical'   => 'inc/views/component-tab-vertical.php',
				'component-tab-horizontal' => 'inc/views/component-tab-horizontal.php',
				'component-toggle'         => 'inc/views/component-toggle.php',
				'component-accordion'      => 'inc/views/component-accordion.php',
				'component-repeater'       => 'inc/views/component-repeater.php',
				'settings'                 => 'inc/views/settings.php',
				'control'                  => 'inc/views/control.php',
				'settings-children-title'  => 'inc/views/settings-children-title.php',
				'tab-children-title'       => 'inc/views/tab-children-title.php',
				'toggle-children-title'    => 'inc/views/toggle-children-title.php',
				'form'                     => 'inc/views/form.php',
				'html'                     => 'inc/views/html.php',
			),
			'views_args' => array(
				'parent'        => '',
				'type'          => '',
				'view'          => '',
				'view_wrapping' => true,
				'html'          => '',
				'scroll'        => false,
				'master'        => false,
				'title'         => '',
				'description'   => '',
			),
		);

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var object
		 */
		private static $instance = null;

		/**
		 * UI element instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    object
		 */
		public $ui_elements = null;

		/**
		 * The structure of the interface elements.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    array
		 */
		private $structure = array();

		/**
		 * Cherry_Interface_Builder constructor.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( $core, array $args = array() ) {
			$this->args = array_merge_recursive(
				$args,
				$this->args
			);

			$this->core_version = $core->get_core_version();
			$this->module_path  = $args['module_path'];
			$this->ui_elements  = $core->init_module( 'cherry-ui-elements' );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		}

		/**
		 * Register element type section.
		 *
		 * @since  1.0.0
		 * @access public
		 * @param  array $args Options section.
		 * @return void
		 */
		public function register_section( array $args = array() ) {
			$this->add_new_element( $args, 'section' );
		}

		/**
		 * Register element type component.
		 *
		 * @since  1.0.0
		 * @access public
		 * @param  array $args Options component.
		 * @return void
		 */
		public function register_component( array $args = array() ) {
			$this->add_new_element( $args, 'component' );
		}

		/**
		 * Register element type settings.
		 *
		 * @since  1.0.0
		 * @access public
		 * @param  array $args Options settings.
		 * @return void
		 */
		public function register_settings( array $args = array() ) {
			$this->add_new_element( $args, 'settings' );
		}

		/**
		 * Register element type control.
		 *
		 * @since  1.0.0
		 * @access public
		 * @param  array $args Options control.
		 * @return void
		 */
		public function register_control( array $args = array() ) {
			$this->add_new_element( $args, 'control' );
		}

		/**
		 * Register element type form.
		 *
		 * @since  1.0.0
		 * @access public
		 * @param  array $args Options form.
		 * @return void
		 */
		public function register_form( array $args = array() ) {
			$this->add_new_element( $args, 'form' );
		}

		/**
		 * Register element type html.
		 *
		 * @since  1.0.0
		 * @access public
		 * @param  array $args Options control.
		 * @return void
		 */
		public function register_html( array $args = array() ) {
			$this->add_new_element( $args, 'html' );
		}

		/**
		 * This function adds a new element to the structure.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @param  array  $args Options new element.
		 * @param  string $type Type new element.
		 * @return void
		 */
		protected function add_new_element( array $args = array(), $type = 'section' ) {

			if ( ! isset( $args[0] ) && ! is_array( current( $args ) ) ) {

					if ( 'control' !== $type && 'component' !== $type ) {
						$args['type'] = $type;
					}

					$this->structure[ $args['id'] ] = $args;

			} else {
				foreach ( $args as $key => $value ) {

					if ( 'control' !== $type && 'component' !== $type ) {
						$value['type'] = $type;
					}

					$this->structure[ $key ] = $value;
				}
			}
		}

		/**
		 * Sorts the elements of the structure, adding child items to the parent.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @param  array  $structure  The original structure of the elements.
		 * @param  string $parent_key The key of the parent element.
		 * @return array
		 */
		protected function sort_structure( array $structure = array(), $parent_key = null ) {
			$new_array = array();

			foreach ( $structure as $key => $value ) {
				if (
					( null === $parent_key && ! isset( $value['parent'] ) )
					|| null === $parent_key && ! isset( $structure[ $value['parent'] ] )
					|| ( isset( $value['parent'] ) && $value['parent'] === $parent_key )
				) {

					if ( ! isset( $value['id'] ) ) {
						$value['id'] = $key;
					}
					if ( ! isset( $value['name'] ) ) {
						$value['name'] = $key;
					}
					$new_array[ $key ] = $value;

					$children = $this->sort_structure( $structure, $key );
					if ( ! empty( $children ) ) {
						$new_array[ $key ]['children'] = $children;
					}
				}
			}

			return $new_array;
		}

		/**
		 * Reset structure array.
		 * Call this method only after render.
		 *
		 * @since  1.0.1
		 * @return void
		 */
		public function reset_structure() {
			$this->structure = array();
		}

		/**
		 * Get view for interface elements.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @param  string $type View type.
		 * @param  array  $args Input data.
		 * @return string
		 */
		protected function get_view( $type = 'control', array $args = array() ) {

			if ( empty( $args['view'] ) ) {
				$path = ( array_key_exists( $type, $this->args['views'] ) ) ? $this->args['views'][ $type ] : $this->args['views']['control'];

				$path = is_array( $path ) ? $path[0] : $path;
				$path = file_exists( $path ) ? $path : $this->module_path . $path;

			} else {
				$path = $args['view'];
			}

			return Cherry_Toolkit::render_view( $path, $args );
		}

		/**
		 * Render HTML elements.
		 *
		 * @since  1.0.0
		 * @access public
		 * @param  bool  $echo Input data.
		 * @param  array $args The original structure of the elements.
		 * @return string
		 */
		public function render( $echo = true, array $args = array() ) {

			if ( empty( $args ) ) {
				$args = $this->structure;
			}

			if ( empty( $args ) ) {
				return false;
			}

			$sorted_structure = $this->sort_structure( $args );

			$output = $this->build( $sorted_structure );
			$output = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $output );

			$this->reset_structure();

			return $this->output_method( $output, $echo );
		}

		/**
		 * Render HTML elements.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @param  array $args Input data.
		 * @return string
		 */
		protected function build( array $args = array() ) {
			$output = '';
			$views  = $this->args['views'];

			foreach ( $args as $key => $value ) {
				$value = wp_parse_args(
					$value,
					$this->args['views_args']
				);

				$value['class'] = isset( $value['class'] ) ? $value['class'] . ' ' : '';
				$value['class'] .= $value['id'] . ' ';

				if ( $value['scroll'] ) {
					$value['class'] .= 'cherry-scroll ';
				}

				if ( $value['master'] ) {
					$value['class'] .= $value['master'] . ' ';
				}

				$type      = array_key_exists( $value['type'], $views ) ? $value['type'] : 'field';
				$has_child = isset( $value['children'] ) && is_array( $value['children'] ) && ! empty( $value['children'] );

				switch ( $type ) {
					case 'component-tab-vertical':
					case 'component-tab-horizontal':
						if ( $has_child ) {
							$value['tabs'] = '';

							foreach ( $value['children'] as $key_children => $value_children ) {
								$value['tabs'] .= $this->get_view( 'tab-children-title', $value_children );

								unset( $value['children'][ $key_children ]['title'] );
							}
						}
					break;

					case 'component-toggle':
					case 'component-accordion':
						if ( $has_child ) {
							foreach ( $value['children'] as $key_children => $value_children ) {
								$value['children'][ $key_children ]['title_in_view'] = $this->get_view( 'toggle-children-title', $value_children );
							}
						}
					break;

					case 'settings':
						if ( isset( $value['title'] ) && $value['title'] ) {
							$value['title'] = isset( $value['title_in_view'] ) ? $value['title_in_view'] : $this->get_view( 'settings-children-title', $value );
						}
					break;

					case 'html':
						$value['children'] = $value['html'];
					break;

					case 'form':
						$value['accept-charset'] = isset( $value['accept-charset'] ) ? $value['accept-charset'] : 'utf-8';
						$value['action']         = isset( $value['action'] ) ? $value['action'] : '' ;
						$value['autocomplete']   = isset( $value['autocomplete'] ) ? $value['autocomplete'] : 'on';
						$value['enctype']        = isset( $value['enctype'] ) ? $value['enctype'] : 'application/x-www-form-urlencoded';
						$value['method']         = isset( $value['method'] ) ? $value['method'] : 'post';
						$value['novalidate']     = ( isset( $value['novalidate'] ) && $value['novalidate'] ) ? 'novalidate' : '';
						$value['target']         = isset( $value['target'] ) ? $value['target'] : '';
					break;

					case 'field':
						$ui_args = $value;

						$ui_args['class'] = isset( $ui_args['child_class'] ) ? $ui_args['child_class'] : '' ;

						if ( isset( $ui_args['options_callback'] ) ) {
							$ui_args['options'] = call_user_func( $ui_args['options_callback'] );
						}

						unset( $ui_args['master'] );

						$value['children'] = $this->ui_elements->get_ui_element_instance( $ui_args['type'], $ui_args )->render();
					break;
				}

				if ( $has_child ) {
					$value['children'] = $this->build( $value['children'] );
				}

				$output .= ( $value['view_wrapping'] ) ? $this->get_view( $type, $value ) : $value['children'];
			}

			return $output;
		}

		/**
		 * Output HTML.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @param  string  $output Output HTML.
		 * @param  boolean $echo   Output type.
		 * @return string
		 */
		protected function output_method( $output = '', $echo = true ) {
			if ( ! filter_var( $echo, FILTER_VALIDATE_BOOLEAN ) ) {
				return $output;
			} else {
				echo $output;
			}
		}

		/**
		 * Enqueue javascript and stylesheet interface builder.
		 *
		 * @since  4.0.0
		 * @access public
		 * @return void
		 */
		public function enqueue_assets() {
			wp_enqueue_script(
				'cherry-interface-builder',
				esc_url( Cherry_Core::base_url( 'inc/assets/min/cherry-interface-builder.min.js', $this->module_path ) ),
				array( 'jquery' ),
				$this->core_version,
				true
			);
			wp_enqueue_style(
				'cherry-interface-builder',
				esc_url( Cherry_Core::base_url( 'inc/assets/min/cherry-interface-builder.min.css', $this->module_path ) ),
				array(),
				$this->core_version,
				'all'
			);
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public static function get_instance( $core, $args ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $core, $args );
			}

			return self::$instance;
		}
	}
}
