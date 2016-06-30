<?php
/**
 * Module Name: Page Builder
 * Description: Provides functionality for building custom options pages
 * Version: 1.1.0
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Cherry_Framework
 * @subpackage Modules
 * @version    1.1.0
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Page_Builder' ) ) {

	/**
	 * Create options page
	 */
	class Cherry_Page_Builder {

		/**
		 * Module version
		 *
		 * @var string
		 */
		public $module_version = '1.1.0';

		/**
		 * Module slug
		 *
		 * @var string
		 */
		public $module_slug = 'cherry-page-builder';

		/**
		 * Module arguments
		 *
		 * @var array
		 */
		public $args = array();

		/**
		 * Page data
		 *
		 * @var array
		 */
		public $data = array();

		/**
		 * Core instance
		 *
		 * @var object
		 */
		public $core = null;

		/**
		 * Current nonce name to check
		 *
		 * @var string
		 */
		public $nonce = 'cherry-admin-menu-nonce';

		/**
		 * The page properties.
		 *
		 * @var DataContainer
		 */
		public $views;

		/**
		 * The page sections.
		 *
		 * @var array
		 */
		protected $sections;

		/**
		 * The page settings.
		 *
		 * @var array
		 */
		protected $settings;

		/**
		 * Constructor for the module
		 */
		function __construct( $core, $args = array() ) {

			$this->core = $core;
			$this->args = wp_parse_args(
				$args,
				array(
					'capability'    => 'manage_options',
					'position'      => 20,
					'icon'          => 'dashicons-admin-site',
					'sections'      => array(),
					'settings'      => array(),
					'before'        => '',
					'after'         => '',
					'before_button' => '',
					'after_button'  => '',
				)
			);

			$this->views = __DIR__ . '/views/';
			add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		}

		/**
		 * Add admin menu page
		 */
		function add_admin_page() {
			$page = $this->make( $this->args['slug'], $this->args['title'], $this->args['parent'], $this->args['views'] )->set(
				array(
					'capability'    => $this->args['capability'],
					'icon'          => $this->args['icon'],
					'position'      => $this->args['position'],
					'tabs'          => $this->args['tabs'],
					'sections'      => $this->args['sections'],
					'settings'      => $this->args['settings'],
				)
			);
			$page->add_sections( $this->args['sections'] );
			$page->add_settings( $this->args['settings'] );
		}

		/**
		 * Set base data of page
		 *
		 * @param type string $slug        The page slug name.
		 * @param type string $title       The page display title.
		 * @param type string $parent       The parent's page slug if a subpage.
		 *
		 * @return object
		 */
		public function make( $slug, $title, $parent = null ) {
			$page = new Cherry_Page_Builder( $this->core, $this->args );

			// Set the page properties.
			$page->data['slug']   = $slug;
			$page->data['title']  = $title;
			$page->data['parent'] = $parent;
			$page->data['args']   = array(
				'capability' => 'manage_options',
				'icon'       => '',
				'position'   => null,
				'tabs'       => true,
				'menu'       => $title,
			);
			$page->data['rules'] = array();

			return $page;
		}

		/**
		 * Set the custom page. Allow user to override
		 * the default page properties and add its own
		 * properties.
		 *
		 * @param array $params      Base parameter.
		 * @return object
		 */
		public function set( array $params = array() ) {
			$this->args = $params;

			$this->add_sections( $params['sections'] );
			$this->add_settings( $params['settings'] );

			add_action( 'admin_menu', array( $this, 'build' ) );

			return $this;
		}

		/**
		 * Triggered by the 'admin_menu' action event.
		 * Register/display the custom page in the WordPress admin.
		 *
		 * @return void
		 */
		public function build() {
			if ( ! is_null( $this->data['parent'] ) ) {
				add_submenu_page( $this->data['parent'], $this->data['title'], $this->data['args']['menu'], $this->data['args']['capability'], $this->data['slug'], array( $this, 'render' ) );
			} else {
				add_menu_page( $this->data['title'], $this->data['args']['menu'], $this->data['args']['capability'], $this->data['slug'], array( $this, 'render' ), $this->data['args']['icon'], $this->args['position'] );
			}
		}

		/**
		 * Triggered by the 'add_menu_page' or 'add_submenu_page'.
		 *
		 * @return void
		 */
		public function render() {
			$title         = ! empty( $this->data['title'] ) ? $this->data['title'] : '';
			$page_slug     = ! empty( $this->data['slug'] ) ? $this->data['slug'] : '';
			$page_before   = ! empty( $this->args['before'] ) ? $this->args['before'] : '';
			$page_after    = ! empty( $this->args['after'] ) ? $this->args['after'] : '';
			$button_before = ! empty( $this->args['button_before'] ) ? $this->args['button_before'] : '';
			$button_after  = ! empty( $this->args['button_after'] ) ? $this->args['button_after'] : '';
			$sections      = ( ! empty( $this->sections ) && is_array( $this->sections ) ) ? $this->sections : array();

			$html = Cherry_Toolkit::render_view(
				$this->views . 'page.php',
				array(
					'title'         => $title,
					'page_slug'     => $page_slug,
					'page_before'   => $page_before,
					'page_after'    => $page_after,
					'button_before' => $button_before,
					'button_after'  => $button_after,
					'sections'      => $sections,
				)
			);

			echo $html;
		}

		/**
		 * Add custom sections for your settings.
		 *
		 * @param array $sections    List of sections.
		 * @return void
		 */
		public function add_sections( array $sections = array() ) {
			$this->sections = $sections;
		}

		/**
		 * Check if the page has sections.
		 *
		 * @return bool
		 */
		public function has_sections() {
			return count( $this->sections ) ? true : false;
		}

		/**
		 * Check if the page has settings.
		 *
		 * @return bool
		 */
		public function has_settings() {
			return count( $this->settings ) ? true : false;
		}

		/**
		 * Add settings to the page. Define settings per section
		 * by setting the 'key' name equal to a registered section and
		 * pass it an array of 'settings' fields.
		 *
		 * @param array $settings The page settings.
		 * @return object
		 */
		public function add_settings( array $settings = array() ) {
			$this->settings = $settings;

			add_action( 'admin_init', array( $this, 'install_settings' ) );

			return $this;
		}

		/**
		 * Triggered by the 'admin_init' action.
		 * Perform the WordPress settings API.
		 *
		 * @return void
		 */
		public function install_settings() {
			if ( $this->has_sections() ) {
				foreach ( $this->sections as $section ) {
					if ( false === get_option( $section['slug'] ) ) {
						add_option( $section['slug'] );
					}
					add_settings_section( $section['slug'], $section['name'], array( $this, 'display_sections' ), $section['slug'] );
				}
			}

			if ( $this->has_settings() ) {
				foreach ( $this->settings as $section => $settings ) {
					foreach ( $settings as &$setting ) {
						$setting['section'] = $section;
						add_settings_field( $setting['slug'], $setting['title'], array( $this, 'display_settings' ), $section, $section, $setting );
					}
					register_setting( $section, $section );
				}
			}
		}

		/**
		 * Clear sections
		 */
		public function clear_sections() {
			if ( $this->has_sections() ) {
				foreach ( $this->sections as $section ) {
					delete_option( $section['slug'] );
				}
			}
		}

		/**
		 * Handle section display of the Settings API.
		 *
		 * @param array $args     Page parameter.
		 * @return void
		 */
		public function display_sections( array $args ) {
			$description = '';
			if ( ! empty( $this->sections[ $args['id'] ] ) ) {
				if ( ! empty( $this->sections[ $args['id'] ]['description'] ) ) {
					$description = $this->sections[ $args['id'] ]['description'];
				}
			}

			$html = Cherry_Toolkit::render_view(
				$this->views . 'section.php',
				array(
					'description' => $description,
				)
			);
			echo $html;
		}

		/**
		 * Handle setting display of the Settings API.
		 *
		 * @param array $setting     Fields setting.
		 * @return void
		 */
		public function display_settings( $setting ) {

			// Check if a registered value exists.
			$value = get_option( $setting['section'] );

			if ( isset( $value[ $setting['slug'] ] ) ) {
				$setting['field']['value'] = $value[ $setting['slug'] ];
			} else {
				$setting['field']['value'] = '';
			}

			// Set the name attribute.
			$setting['field']['name'] = $setting['section'] . '[' . $setting['slug'] . ']';

			if ( isset( $setting['custom_callback'] ) && is_callable( $setting['custom_callback'] ) ) {
				echo call_user_func( $setting['custom_callback'], $setting['field'] );

			} else if ( class_exists( 'UI_' . ucfirst( $setting['type'] ) ) ) {
				$ui_class = 'UI_' . ucfirst( $setting['type'] );
				$ui_element = new $ui_class( $setting['field'] );

				// Display the field.
				echo $ui_element->render();
			}
		}

		/**
		 * Add styles and scripts
		 *
		 * @return void
		 */
		public function assets() {
			wp_enqueue_script( 'jquery-form' );

			wp_localize_script( 'cherry-settings-page', 'TMRealEstateMessage', array(
				'success' => 'Successfully',
				'failed' => 'Failed',
			) );

			wp_enqueue_script(
				'cherry-settings-page',
				Cherry_Core::base_url( 'assets/js/min/page-settings.min.js', __FILE__ ),
				array( 'jquery' ),
				'0.2.0',
				true
			);

			wp_enqueue_style(
				'cherry-settings-page',
				Cherry_Core::base_url( 'assets/css/min/page-settings.min.css', __FILE__ ),
				array(),
				'0.1.0',
				'all'
			);
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
