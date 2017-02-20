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

// If class `Cherry5_Insertion_Popup` doesn't exists yet.
if ( ! class_exists( 'Cherry5_Insertion_Popup' ) ) {

	/**
	 * Cherry5_Insertion_Popup class.
	 */
	class Cherry5_Insertion_Popup {

		/**
		 * A reference to an instance of parent class.
		 *
		 * @since 1.0.0
		 * @var object
		 * @access private
		 */
		private static $instance = null;

		/**
		 * Module arguments
		 *
		 * @since  1.0.0
		 * @var    array
		 * @access private
		 */
		private $args = array();

		/**
		 * Core instance
		 *
		 * @since  1.0.0
		 * @var    object
		 * @access private
		 */
		private $core = null;

		/**
		 * Shortcode list.
		 *
		 * @since  1.0.0
		 * @var    array
		 * @access private
		 */
		private $shortcode_list = array(
			array(
				'title'       => '',
				'description' => '',
				'icon'        => '',
				'slug'        => '',
				'shortcodes'  => array(
					array(
						'title'       => '',
						'description' => '',
						'icon'        => '',
						'slug'        => '',
						'enclosing '  => false,
						'options'     => array(),
					),
				),
			),
		);

		/**
		 * Class constructor.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( $core = null, $args = array(), $parent_self = null ) {
			$this->core        = $core;
			$this->args        = $args;
			$this->parent_self = $parent_self;

			$this->core->init_module(
				'cherry-handler',
				array(
					'id'           => 'cherry5_insert_shortcode',
					'action'       => 'cherry5_insert_shortcode',
					'type'        => 'GET',
					'capability'   => 'manage_options',
					'callback'     => array( $this , 'get_shortcode_options' ),
					'sys_messages' => array(
						'invalid_base_data' => '',
						'no_right'          => '',
						'invalid_nonce'     => '',
						'access_is_allowed' => '',
					),
				)
			);

			if ( ! defined( 'DOING_AJAX' ) ) {
				add_action( 'admin_print_footer_scripts', array( $this, 'render_popup' ), 99 );
			}
		}

		/**
		 * Add popup into page.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function render_popup( $hook ) {
			$screen = get_current_screen();

			if ( in_array( $screen->base, $this->args['in_screen'] ) ) {

				$this->shortcode_list = apply_filters( 'cherry5-is__shortcode_list', array() );
				$popup_title          = esc_html( 'Insert Cherry Shortcode', 'cherry' );
				$sidebar_list         = $this->get_sidebar_list( $this->shortcode_list );

				$args = apply_filters( 'cherry5-is__insert-button', array(
					'id'         => '',
					'name'       => '',
					'style'      => 'primary',
					'content'    => esc_html__( 'insert shortcode', 'cherry-framework' ),
					'class'      => 'cherry5-is__insert-button',
				) );

				$insert_button = $this->parent_self->ui_elements->get_ui_element_instance( 'button', $args )->render();

				require_once( apply_filters( 'cherry5-is__popup-template', $this->args['module_dir'] . 'inc/views/insert-shortcode-pop-up.php' ) );
			}
		}

		/**
		 * The function returns a HTML structure of registered shortcodes.
		 *
		 * @since 1.0.0
		 * @access private
		 * @return string
		 */
		private function get_sidebar_list( $plugins = array() ) {
			$structure['shortcode-list'] = array(
				'type'  => 'component-accordion',
				'title' => esc_html__( 'Shortcode List', 'cherry-framework' ),
			);
			$defaults = array(
				'icon'        => '',
				'title'       => '',
				'description' => '',
			);

			$list_items_template = apply_filters( 'cherry5-is__list-items-template', '<li><button id="button-%1$s-%2$s" class="cherry5-is__get-shotcode" data-plugin-slug="%1$s" data-shortcode-slug="%2$s" title="%3$s">%4$s%5$s</button></li>' );

			foreach ( $plugins as $plugin_slug => $plugin_value ) {
				$plugin_value = wp_parse_args( $plugin_value, $defaults );

				$structure[ $plugin_slug ] = array(
					'id'          => $plugin_slug,
					'type'        => 'settings',
					'parent'      => 'shortcode-list',
					'title'       => $plugin_value['icon'] . $plugin_value['title'],
					'description' => $plugin_value['description'],
				);

				$output_html = '<ul class="cherry5-is__shortcode-list">';

				foreach ( $plugin_value['shortcodes'] as $shortcode_slug => $shortcode_value ) {
					$shortcode_value = wp_parse_args( $shortcode_value, $defaults );

					$output_html .= sprintf( $list_items_template, $plugin_slug, $shortcode_slug, $shortcode_value['description'], $shortcode_value['icon'], $shortcode_value['title'] );
				}

				$output_html .= '</ul>';

				$structure[ $plugin_slug . '-shortcodes' ] = array(
					'id'          => $plugin_slug . '-shortcodes',
					'type'        => 'html',
					'parent'      => $plugin_slug,
					'html'        => $output_html,
				);
			}
			return $this->parent_self->cherry_interface_builder->render( false, $structure );
		}

		/**
		 * The function returns option was shortcode.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return array
		 */
		public function get_shortcode_options() {
			$shortcode_list = apply_filters( 'cherry5-is__shortcode_list', array() );

			$plugin_slug = ( ! empty( $_GET['data']['plugin_slug'] ) )? $_GET['data']['plugin_slug'] : '' ;
			$shortcode_slug = ( ! empty( $_GET['data']['shortcode_slug'] ) )? $_GET['data']['shortcode_slug'] : '' ;
			$shortcode_attr = isset( $shortcode_list[ $plugin_slug ]['shortcodes'][ $shortcode_slug ] ) ? $shortcode_list[ $plugin_slug ]['shortcodes'][ $shortcode_slug ] : false;

			if ( ! $shortcode_attr ) {
				return array(
					'error'   => true,
					'message' => esc_html__( 'Shortcode not found.', 'cherry-framework' ),
				);
			} else {
				$defaults = array(
					'content_area'   => '',
					'enclosing'      => false,
					'title'          => '',
					'description'    => '',
					'defaultContent' => '',
					'options'        => false,
				);

				$shortcode_attr = wp_parse_args( $shortcode_attr, $defaults );

				if ( $shortcode_attr['enclosing'] ) {
					$shortcode_attr['content_area'] = $this->get_shortcode_content_editor( $default_content, $plugin_slug, $shortcode_slug );
				}

				if ( ! empty( $shortcode_attr['options'] ) ) {

					foreach ( $shortcode_attr['options'] as $key => $settings ) {

						if ( ! array_key_exists( 'id', $settings ) ) {
							$shortcode_attr['options'][ $key ]['id'] = $shortcode_slug . '_' . $key;
						}
					}

					$shortcode_options_html = $this->parent_self->cherry_interface_builder->render( false, $shortcode_attr['options'] );
				} else {
					$shortcode_options_html = $this->get_empty_layer();
				}

				$shortcode_option_template = apply_filters( 'cherry5-is__options-template', Cherry_Toolkit::get_file( $this->args['module_dir'] . 'inc/views/shortcode-options.php' ) );
				$output_html               = sprintf( $shortcode_option_template, $plugin_slug, $shortcode_slug, $shortcode_attr['title'], $shortcode_attr['description'], $shortcode_attr['content_area'], $shortcode_options_html );

				return array(
					'error'          => false,
					'pluginSlug'     => $plugin_slug,
					'shortcodeSlug'  => $shortcode_slug,
					'enclosing'      => $shortcode_attr['enclosing'],
					'defaultContent' => $shortcode_attr['defaultContent'],
					'html'           => $output_html,
				);
			}
		}

		/**
		 * The function returns content area HTML.
		 *
		 * @since 1.0.0
		 * @access private
		 * @return string
		 */
		private function get_shortcode_content_editor( $content = '', $plugin_slug = '', $shortcode_slug = '' ) {
			$template    = apply_filters( 'cherry5-is__content-area-template', Cherry_Toolkit::get_file( $this->args['module_dir'] . 'inc/views/shortcode-content-area.php' ) );
			$title       = apply_filters( 'cherry5-is__content-title', esc_html__( 'Shortcode content.', 'cherry-framework' ) );
			$placeholder = apply_filters( 'cherry5-is__content-placeholder', esc_html__( 'Input shortcode content.', 'cherry-framework' ) );

			$output = sprintf( $template, $plugin_slug, $shortcode_slug, $title, $placeholder, $content );

			return $output;
		}

		/**
		 * The function returns empty layer HTML.
		 *
		 * @since 1.0.0
		 * @access private
		 * @return string
		 */
		private function get_empty_layer() {
			$text     = apply_filters( 'cherry5-is__empty_layer-text', esc_html__( 'Shortcode not a have options.', 'cherry-framework' ) );
			$template = apply_filters( 'cherry5-is__empty_layer-template', Cherry_Toolkit::get_file( $this->args['module_dir'] . 'inc/views/shortcode-has-not-option.php' ) );
			$output   = sprintf( $template, $text );

			return $output;
		}
	}
}

