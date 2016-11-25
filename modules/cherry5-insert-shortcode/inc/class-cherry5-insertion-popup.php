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

// If class `Cherry5_Insertion_Popup` doesn't exists yet.
if ( ! class_exists( 'Cherry5_Insertion_Popup' ) ) {

	/**
	 * Cherry5_Insertion_Popup class.
	 */
	class Cherry5_Insertion_Popup {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
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
		 * Cherry Interface Builder instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    object
		 */
		public $cherry_interface_builder = null;

		/**
		 * Shortcode list.
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    object
		 */
		public $shortcode_list = array(
			'cherry-search' => array(
				'title'        => 'Cherry Search',
				'description' => '',
				'icon'        => '',
				'slug'        => 'cherry-search',
				'shortcodes'  => array(
					array(
						'title'       => '',
						'description' => '',
						'icon'        => '',
						'slug'        => 'cherry-search-form',
						'twin'        => false,
						'option'      => array(

						),
					)
				),
			),
		);

		/**
		 * Class constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( $core = null, $args = array() ) {
			$core->init_module(
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
			$this->cherry_interface_builder = $core->init_module( 'cherry-interface-builder' );

			if ( ! defined( 'DOING_AJAX' ) ){
				$this->ui_elements = $core->init_module( 'cherry-ui-elements' );
				add_action( 'admin_print_footer_scripts', array( $this, 'render_popup' ) );
			}
		}

		/**
		 * Add popup into page.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function render_popup( $hook ) {
			$screen = get_current_screen();

			if ( 'post' === $screen->base ) {
				$insert_button        = '';
				$this->shortcode_list = apply_filters( 'cherry5_shortcode_list', array() );
				$sidebar_list         = $this->get_sidebar_list( $this->shortcode_list );
				var_dump($this->shortcode_list);
				$args = array(
					'id'         => '',
					'name'       => '',
					'style'      => 'normal',
					'content'    => esc_html__( 'insert shortcodes', 'cherry' ),
					'class'      => 'cherry5-is__insert-button',
				);

				$insert_button  = $this->ui_elements->get_ui_element_instance( 'button', $args )->render();

				require_once( dirname( __FILE__ ) . '/views/insert-shortcode-pop-up.php' );
			}
		}

		/**
		 * .
		 *
		 * @since 1.0.0
		 * @access private
		 * @return string
		 */
		private function get_sidebar_list( $plugins = array() ) {
			$structure['shortcode-list'] = array(
				'type'  => 'component-toggle',
				'title' => esc_html__( 'Shortcode List', 'cherry' ),
			);

			foreach ( $plugins as $plugin_slug => $plugin_value ) {
				$icon        = ( isset( $plugin_value['icon'] ) ) ? $plugin_value['icon'] : '' ;
				$title       = ( isset( $plugin_value['title'] ) ) ? $plugin_value['title'] : '' ;
				$description = ( isset( $plugin_value['description'] ) ) ? $plugin_value['description'] : '' ;

				$structure[ $plugin_slug ] = array(
					'id'          => $plugin_slug,
					'type'        => 'settings',
					'parent'      => 'shortcode-list',
					'title'       => $icon . $title,
					'description' => $description,
				);

				$output_html = '<ul>';

				foreach ( $plugin_value['shortcodes'] as $shortcode_slug => $shortcode_value) {
					$shortcode_icon        = ( isset( $shortcode_value['icon'] ) ) ? $shortcode_value['icon'] : '' ;
					$shortcode_title       = ( isset( $shortcode_value['title'] ) ) ? $shortcode_value['title'] : '' ;
					$shortcode_description = ( isset( $shortcode_value['description'] ) ) ? $shortcode_value['description'] : '' ;

					$output_html .= sprintf( '<li><a href="#" class="cherry5-is__get-shotcode" data-plugin-slug="%1$s" data-shortcode-slug="%2$s" title="%3$s">%4$s%5$s</a></li>', $plugin_slug, $shortcode_slug, $shortcode_description, $shortcode_icon, $shortcode_title );
				}

				$output_html .= '</ul>';

				$structure[ $plugin_slug . '-shortcodes' ] = array(
					'id'          => $plugin_slug . '-shortcodes',
					'type'        => 'html',
					'parent'      => $plugin_slug,
					'html'        => $output_html
				 );
			}
			return $this->cherry_interface_builder->render( false, $structure );
		}

		/**
		 * .
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function get_shortcode_options() {
			$plugin_slug = ( ! empty( $_GET['data']['plugin_slug'] ) )? $_GET['data']['plugin_slug'] : '' ;
			$shortcode_slug = ( ! empty( $_GET['data']['shortcode_slug'] ) )? $_GET['data']['shortcode_slug'] : '' ;

			$shortcode_list = apply_filters( 'cherry5_shortcode_list', array() );

			if ( ! $plugin_slug || ! $shortcode_slug || ! isset( $shortcode_list[ $plugin_slug ]['shortcodes'][ $shortcode_slug ] ) ) {
				return array( 'error' => true, 'message' => 'Shortcone not find.' );
			} else {
				$shortcode_options = $shortcode_list[ $plugin_slug ]['shortcodes'][ $shortcode_slug ]['options'];
				$shortcode_options_html = $this->cherry_interface_builder->render( false, $shortcode_options );

				$output_html = sprintf( '<div id="%1$s-%2$s" class="cherry5-is__shortcode-section show">%3$s<div>', $plugin_slug, $shortcode_slug, $shortcode_options_html );

				return array( 'error' => false, 'html' => $output_html );
			}

		}
	}
}

