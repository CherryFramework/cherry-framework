<?php
/**
 * Module Name: Customizer
 * Description: Customizer functionality.
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Cherry_Framework
 * @subpackage Modules
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Customizer' ) ) {

	/**
	 * Contains methods for customizing the theme customization screen.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Removed `module_dir` and `module_uri` properties.
	 */
	class Cherry_Customizer {

		/**
		 * Unique prefix.
		 * This is a theme or plugin slug.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $prefix;

		/**
		 * Capability.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $capability;

		/**
		 * Setting type.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $type;

		/**
		 * Options.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $options;

		/**
		 * Core instance.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var object
		 */
		protected $core;

		/**
		 * WP_Customize_Manager instance.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var object.
		 */
		protected $customize;

		/**
		 * Module directory URI.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array.
		 */
		protected $fonts;

		/**
		 * Module directory path.
		 *
		 * @since 1.5.0
		 * @access protected
		 * @var srting.
		 */
		protected $module_path;

		/**
		 * Module initialization.
		 *
		 * @since 1.0.0
		 * @param object $core Cherry_Core instance.
		 * @param array  $args Aguments.
		 */

		/*
		 * $args = array(
		 *      'just_fonts' => false, // set to TRUE if you want use customizer only as fonts manager.
		 *      'prefix'     => 'unique_prefix', // theme or plugin slug (*).
		 *      'capability' => 'edit_theme_options', // (default: `edit_theme_options`).
		 *      'type'       => 'theme_mod', // `theme_mod` - for themes; `option` - for plugins (default: `theme_mod`)
		 *      'options'    => array(
		 *          'unique_panel_ID' => array(
		 *              'title'           => esc_html__( 'Panel Title', 'text-domain' ),
		 *              'description'     => esc_html__( 'Panel Description', 'text-domain' ),
		 *              'priority'        => 140,
		 *              'capability'      => '', (optional)
		 *              'theme_supports'  => '', (optional)
		 *              'active_callback' => '', // (optional: is_front_page, is_single)
		 *              'type'            => 'panel', // panel, section or control (*).
		 *          ),
		 *          'unique_section_ID' => array(
		 *              'title'       => esc_html__( 'Section Title', 'text-domain' ),
		 *              'description' => esc_html__( 'Section Description', 'text-domain' ),
		 *              'priority'    => 10, (10, 20, 30, ...)
		 *              'panel'       => 'unique_panel_ID', (*)
		 *              'type'        => 'section', (*)
		 *          ),
		 *          'unique_control_ID' => array(
		 *              'title'       => esc_html__( 'Control Title', 'text-domain' ),
		 *              'description' => esc_html__( 'Control Description', 'text-domain' ),
		 *              'section'     => 'unique_section_ID', (*)
		 *              'default'     => '',
		 *              'field'       => 'text',  // text, textarea, checkbox, radio, select,
		 *                                        // iconpicker, fonts, hex_color, image, file.
		 *              'choices'     => array(), // for `select` and `radio` field.
		 *              'type'        => 'control', (*)
		 *              'active_callback'      => '', (optional: is_front_page, is_single)
		 *              'transport'            => 'refresh', // refresh or postMessage (default: refresh)
		 *              'sanitize_callback'    => '', (optional) Maybe need to use a custom function or sanitization.
		 *              'sanitize_js_callback' => '', (optional)
		 *          ),
		 *      )
		 * );
		 */

		/**
		 * Cherry customizer class construct.
		 */
		public function __construct( $core, $args ) {

			/**
			 * Cherry Customizer only works in WordPress 4.0 or later.
			 */
			if ( version_compare( $GLOBALS['wp_version'], '4.0', '<' ) ) {
				return;
			}

			$this->core        = $core;
			$this->fonts       = array();
			$this->module_path = $args['module_path'];

			// Prepare fonts data.
			add_action( 'after_switch_theme', array( $this, 'init_fonts' ),  10 );
			add_action( 'after_switch_theme', array( $this, 'add_options' ), 11 );

			// Clear fonts data.
			add_action( 'switch_theme', array( $this, 'clear_fonts' ) );
			add_action( 'upgrader_process_complete', array( $this, 'fire_clear_fonts' ), 10, 2 );

			/**
			 * Fonts are loaded, abort if $args['just_fonts'] set to TRUE
			 */
			if ( isset( $args['just_fonts'] ) && true === $args['just_fonts'] ) {
				return;
			}

			$this->type = ! empty( $args['type'] ) && $this->sanitize_type( $args['type'] ) ? $args['type'] : 'theme_mod';

			if ( empty( $args['options'] ) || ( ( 'option' === $this->type ) && empty( $args['prefix'] ) ) ) {
				return;
			}

			$this->prefix     = $this->prepare_prefix( $args['prefix'] );
			$this->capability = ! empty( $args['capability'] ) ? $args['capability'] : 'edit_theme_options';
			$this->type       = ! empty( $args['type'] ) && $this->sanitize_type( $args['type'] ) ? $args['type'] : 'theme_mod';
			$this->options    = $args['options'];


			add_action( 'customize_register', array( $this, 'register' ) );

			add_filter( 'cherry_customizer_get_core', array( $this, 'pass_core_into_control' ) );

			$this->include_custom_controls();

		}

		/**
		 * Pass current core instance into custom controls
		 *
		 * @param  mixed $core Default core instance (false) or core instance if its not first callback.
		 * @return Cherry_Core
		 */
		public function pass_core_into_control( $core = false ) {
			return $this->core;
		}

		/**
		 * Include advanced customizer controls classes
		 *
		 * @since 1.1.0
		 */
		private function include_custom_controls() {

			if ( ! class_exists( 'Cherry_WP_Customize_Iconpicker' ) ) {
				require_once( $this->module_path . 'inc/class-cherry-wp-customize-iconpicker.php' );
			}

		}

		/**
		 * Registeration for a new panel, sections, settings and controls.
		 *
		 * @since 1.0.0
		 * @param object $wp_customize WP_Customize_Manager instance.
		 */
		public function register( $wp_customize ) {

			// Failsafe is safe.
			if ( ! isset( $wp_customize ) ) {
				return;
			}

			$this->set_customize( $wp_customize );

			foreach ( (array) $this->options as $id => $option ) {

				if ( empty( $option['type'] ) ) {
					continue;
				}

				if ( 'panel' === $option['type'] ) {
					$this->add_panel( $id, $option );
				}

				if ( 'section' === $option['type'] ) {
					$this->add_section( $id, $option );
				}

				if ( 'control' === $option['type'] ) {
					$this->add_control( $id, $option );
				}
			}
		}

		/**
		 * Add a customize panel.
		 *
		 * @since 1.0.0
		 * @param number $id Settings ID.
		 * @param array  $args Panel arguments.
		 */
		public function add_panel( $id, $args ) {
			$prefix          = $this->prefix . '_';
			$priority        = isset( $args['priority'] )        ? $args['priority'] : 160;
			$theme_supports  = isset( $args['theme_supports'] )  ? $args['theme_supports'] : '';
			$title           = isset( $args['title'] )           ? esc_attr( $args['title'] ) : esc_html__( 'Untitled Panel', 'cherry-framework' );
			$description     = isset( $args['description'] )     ? esc_attr( $args['description'] ) : '';
			$active_callback = isset( $args['active_callback'] ) ? $this->active_callback( $args['active_callback'] ) : '';

			$this->customize->add_panel( $prefix . esc_attr( $id ), array(
				'priority'        => $priority,
				'capability'      => $this->capability,
				'theme_supports'  => $theme_supports,
				'title'           => $title,
				'description'     => $description,
				'active_callback' => $active_callback,
			) );
		}

		/**
		 * Add a customize section.
		 *
		 * @since 1.0.0
		 * @param array $id   Settings ID.
		 * @param array $args Section arguments.
		 */

		/**
		 * The priorities of the core sections are below:
		 *
		 * Title                ID                Priority (Order)
		 * Site Title & Tagline title_tagline     20
		 * Colors               colors            40
		 * Header Image         header_image      60
		 * Background Image     background_image  80
		 * Navigation           nav               100
		 * Widgets (Panel)      widgets           110
		 * Static Front Page    static_front_page 120
		 */
		public function add_section( $id, $args ) {
			$prefix          = $this->prefix . '_';
			$title           = isset( $args['title'] )           ? esc_attr( $args['title'] ) : esc_html__( 'Untitled Section', 'cherry-framework' );
			$description     = isset( $args['description'] )     ? esc_attr( $args['description'] ) : '';
			$panel           = isset( $args['panel'] )           ? $prefix . esc_attr( $args['panel'] ) : '';
			$priority        = isset( $args['priority'] )        ? $args['priority'] : 160;
			$theme_supports  = isset( $args['theme_supports'] )  ? $args['theme_supports'] : '';
			$active_callback = isset( $args['active_callback'] ) ? $this->active_callback( $args['active_callback'] ) : '';

			$this->customize->add_section( $prefix . esc_attr( $id ), array(
				'title'           => $title,
				'description'     => $description,
				'panel'           => $panel,
				'priority'        => $priority,
				'capability'      => $this->capability,
				'theme_supports'  => $theme_supports,
				'active_callback' => $active_callback,
			) );
		}

		/**
		 * Add a customize control.
		 *
		 * @since 1.0.0
		 * @since 1.1.8 Added a `dropdown-pages` support.
		 * @param numder $id Settings ID.
		 * @param array  $args Control arguments.
		 */
		public function add_control( $id, $args ) {
			static $control_priority = 0;

			$prefix      = $this->prefix . '_';
			$section     = $this->get_control_section( $args );
			$id          = ( 'option' === $this->type )  ? sprintf( '%1$s_options[%2$s]', $this->prefix, esc_attr( $id ) ) : esc_attr( $id );
			$priority    = isset( $args['priority'] )    ? $args['priority'] : ++$control_priority;
			$default     = isset( $args['default'] )     ? $args['default'] : '';
			$title       = isset( $args['title'] )       ? esc_attr( $args['title'] ) : esc_html__( 'Untitled Control', 'cherry-framework' );
			$description = isset( $args['description'] ) ? esc_attr( $args['description'] ) : '';
			$transport   = isset( $args['transport'] )   ? esc_attr( $args['transport'] ) : 'refresh';
			$field_type  = isset( $args['field'] )       ? esc_attr( $args['field'] ) : 'text';

			$sanitize_callback    = isset( $args['sanitize_callback'] )    ? esc_attr( $args['sanitize_callback'] ) : array( $this, 'sanitize_' . $field_type );
			$sanitize_callback    = is_callable( $sanitize_callback ) ? $sanitize_callback : 'sanitize_text_field';
			$sanitize_js_callback = isset( $args['sanitize_js_callback'] ) ? esc_attr( $args['sanitize_js_callback'] ) : '';
			$active_callback      = isset( $args['active_callback'] )      ? $this->active_callback( $args['active_callback'] ) : '';

			// Add a customize setting.
			$this->customize->add_setting( $id, array(
				'type'                 => $this->type,
				'capability'           => $this->capability,
				'default'              => $default,
				'transport'            => $transport,
				'sanitize_callback'    => $sanitize_callback,
				'sanitize_js_callback' => $sanitize_js_callback,
			) );

			// Prepare arguments for a customize control.
			$control_args = array(
				'priority'        => $priority,
				'section'         => $section,
				'label'           => $title,
				'description'     => $description,
				'active_callback' => $active_callback,
				'choices'         => '', // select, radio
			);
			$control_class = '';

			switch ( $field_type ) {

				case 'text':
				case 'textarea':
				case 'email':
				case 'url':
				case 'password':
				case 'checkbox':
				case 'dropdown-pages':
						$control_args = wp_parse_args( array(
							'type' => $field_type,
						), $control_args );
					break;

				case 'range':
				case 'number':
						$input_attrs  = ( isset( $args['input_attrs'] ) ) ? $args['input_attrs'] : array();
						$control_args = wp_parse_args( array(
							'type'        => $field_type,
							'input_attrs' => $input_attrs,
						), $control_args );
					break;

				case 'select':
						$choices      = ( isset( $args['choices'] ) ) ? $args['choices'] : array();
						$control_args = wp_parse_args( array(
							'type'    => 'select',
							'choices' => $choices,
						), $control_args );
					break;

				case 'fonts':
						$choices      = ( isset( $args['choices'] ) ) ? $args['choices'] : $this->get_fonts();
						$control_args = wp_parse_args( array(
							'type'    => 'select',
							'choices' => $choices,
						), $control_args );
					break;

				case 'radio':
						$choices      = ( isset( $args['choices'] ) ) ? $args['choices'] : array();
						$control_args = wp_parse_args( array(
							'type'    => 'radio',
							'choices' => $choices,
						), $control_args );
					break;

				case 'hex_color':
						$control_class = 'WP_Customize_Color_Control';
					break;

				case 'image':
						$control_class = 'WP_Customize_Image_Control';
					break;

				case 'file':
						$control_class = 'WP_Customize_Upload_Control';
					break;

				case 'iconpicker':
						$control_class = 'Cherry_WP_Customize_Iconpicker';
						$icon_data     = ( isset( $args['icon_data'] ) ) ? $args['icon_data'] : array();
						$auto_parse    = ( isset( $args['auto_parse'] ) ) ? $args['auto_parse'] : array();
						$control_args  = wp_parse_args(
							array(
								'icon_data'  => $icon_data,
								'auto_parse' => $auto_parse,
							),
							$control_args
						);
					break;

				default:
						/**
						 * Filter arguments for a `$field_type` customize control.
						 *
						 * @since 1.0.0
						 * @param array  $control_args Control's arguments.
						 * @param string $id           Control's ID.
						 * @param object $this         Cherry_Customizer instance.
						 */
						$control_args = apply_filters( 'cherry_customizer_control_args_for_{$field_type}', $control_args, $id, $this );
					break;
			}

			/**
			 * Filter arguments for a customize control.
			 *
			 * @since 1.0.0
			 * @param array  $control_args Control's arguments.
			 * @param string $id           Control's ID.
			 * @param object $this         Cherry_Customizer instance.
			 */
			$control_args = apply_filters( 'cherry_customizer_control_args', $control_args, $id, $this );

			/**
			 * Filter PHP-class name for a customize control (maybe custom).
			 *
			 * @since 1.0.0
			 * @param array  $control_args Control's PHP-class name.
			 * @param string $id           Control's ID.
			 * @param object $this         Cherry_Customizer instance.
			 */
			$control_class = apply_filters( 'cherry_customizer_control_class', $control_class, $id, $this );

			if ( class_exists( $control_class ) ) {
				$this->customize->add_control( new $control_class( $this->customize, $id, $control_args ) );
			} else {
				$this->customize->add_control( $id, $control_args );
			}
		}

		/**
		 * Get section name from arguments - prefixed, if is custom section, unprefixed - if is core section.
		 *
		 * @since  1.0.0
		 * @param  array $args Control arguments.
		 * @return string
		 */
		public function get_control_section( $args ) {

			if ( ! isset( $args['section'] ) ) {
				return '';
			}

			$default_sections = apply_filters( 'cherry_customizer_core_sections', array(
				'title_tagline',
				'colors',
				'header_image',
				'background_image',
				'nav',
				'widgets',
				'static_front_page',
			) );

			if ( in_array( esc_attr( $args['section'] ), $default_sections ) ) {
				return esc_attr( $args['section'] );
			}

			return $this->prefix . '_' . esc_attr( $args['section'] );
		}

		/**
		 * Retrieve a prefix.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function prepare_prefix( $prefix ) {
			$prefix = preg_replace( '/\W/', '-', strtolower( $prefix ) );
			$prefix = sanitize_key( $prefix );

			return $prefix;
		}

		/**
		 * Save WP_Customize_Manager instance to prorerty.
		 *
		 * @since 1.0.0
		 * @param object $customize WP_Customize_Manager instance.
		 */
		public function set_customize( $customize ) {
			$this->customize = $customize;
		}

		/**
		 * Retrieve a option value by ID.
		 *
		 * @since  1.0.0
		 * @param  mixed $id Settings ID.
		 * @return bool|mixed
		 */
		public function get_value( $id, $default = null ) {

			if ( null === $default ) {
				$default = $this->get_default( $id );
			}

			if ( 'theme_mod' === $this->type ) {
				return get_theme_mod( $id, $default );
			}

			if ( 'option' === $this->type ) {
				$options = get_option( $this->prefix . '_options', array() );

				return isset( $options[ $id ] ) ? $options[ $id ] : $default;
			}

			return $default;
		}

		/**
		 * Retrieve a default option value.
		 *
		 * @since  1.0.0
		 * @param  [string] $id Settings ID.
		 * @return mixed
		 */
		public function get_default( $id ) {
			return isset( $this->options[ $id ]['default'] ) ? $this->options[ $id ]['default'] : null;
		}

		/**
		 * Whitelist for setting type.
		 *
		 * @since  1.0.0
		 * @param  [string] $type Settings type.
		 * @return bool
		 */
		public function sanitize_type( $type ) {
			return in_array( $type, array( 'theme_mod', 'option' ) );
		}

		/**
		 * Text sanitization callback.
		 *
		 * - Sanitization: html
		 * - Control: text, textarea
		 *
		 * Sanitization callback for 'html' type text inputs. This callback sanitizes `$html`
		 * for HTML allowable in posts.
		 *
		 * NOTE: wp_filter_post_kses() can be passed directly as `$wp_customize->add_setting()`
		 * 'sanitize_callback'. It is wrapped in a callback here merely for example purposes.
		 *
		 * @author WPTRT <https://github.com/WPTRT>
		 * @author Cherry Team <cherryframework@gmail.com>
		 * @see    wp_filter_post_kses() https://developer.wordpress.org/reference/functions/wp_filter_post_kses/
		 * @since  1.0.0
		 * @param  [string] $html HTML to sanitize.
		 * @return string       Sanitized HTML.
		 */
		public function sanitize_text( $html ) {
			return wp_filter_post_kses( $html );
		}

		/**
		 * Email sanitization callback.
		 *
		 * - Sanitization: email
		 * - Control: text
		 *
		 * Sanitization callback for 'email' type text controls. This callback sanitizes `$email`
		 * as a valid email address.
		 *
		 * @author WPTRT <https://github.com/WPTRT>
		 * @author Cherry Team <cherryframework@gmail.com>
		 * @see    sanitize_email() https://developer.wordpress.org/reference/functions/sanitize_key/
		 * @link   sanitize_email() https://codex.wordpress.org/Function_Reference/sanitize_email
		 * @since  1.0.0
		 * @param  [string]             $email   Email address to sanitize.
		 * @param  WP_Customize_Setting $setting Setting instance.
		 * @return string                        The sanitized email if not null; otherwise, the setting default.
		 */
		public function sanitize_email( $email, $setting ) {
			// Sanitize $input as a hex value without the hash prefix.
			$email = sanitize_email( $email );

			// If $email is a valid email, return it; otherwise, return the default.
			return ( '' === $email ) ? $setting->default : $email;
		}

		/**
		 * Textarea sanitization callback.
		 *
		 * @since  1.0.0
		 * @param  [string] $html HTML to sanitize.
		 * @return string       Sanitized HTML.
		 */
		public function sanitize_textarea( $html ) {
			return $this->sanitize_text( $html );
		}

		/**
		 * Select sanitization callback.
		 *
		 * - Sanitization: select
		 * - Control: select, radio
		 *
		 * Sanitization callback for 'select' and 'radio' type controls. This callback sanitizes `$input`
		 * as a slug, and then validates `$input` against the choices defined for the control.
		 *
		 * @author WPTRT <https://github.com/WPTRT>
		 * @author Cherry Team <cherryframework@gmail.com>
		 * @see    sanitize_key()               https://developer.wordpress.org/reference/functions/sanitize_key/
		 * @see    $wp_customize->get_control() https://developer.wordpress.org/reference/classes/wp_customize_manager/get_control/
		 * @since  1.0.0
		 * @param  [string]             $input   Slug to sanitize.
		 * @param  WP_Customize_Setting $setting Setting instance.
		 * @return string                        Sanitized slug if it is a valid choice; otherwise, the setting default.
		 */
		public function sanitize_select( $input, $setting ) {

			// Ensure input is a slug.
			$input = sanitize_key( $input );

			// Get list of choices from the control associated with the setting.
			$choices = $setting->manager->get_control( $setting->id )->choices;

			// If the input is a valid key, return it; otherwise, return the default.
			return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
		}

		/**
		 * Function sanitize_radio
		 */
		public function sanitize_radio( $input, $setting ) {
			return $this->sanitize_select( $input, $setting );
		}

		/**
		 * Checkbox sanitization callback.
		 *
		 * Sanitization callback for 'checkbox' type controls. This callback sanitizes `$checked`
		 * as a boolean value, either TRUE or FALSE.
		 *
		 * @author WPTRT <https://github.com/WPTRT>
		 * @author Cherry Team <cherryframework@gmail.com>
		 * @since  1.0.0
		 * @param  bool $checked Whether the checkbox is checked.
		 * @return bool          Whether the checkbox is checked.
		 */
		public function sanitize_checkbox( $checked ) {
			return ( ( isset( $checked ) && true == $checked ) ? true : false );
		}

		/**
		 * HEX Color sanitization callback example.
		 *
		 * - Sanitization: hex_color
		 * - Control: text, WP_Customize_Color_Control
		 *
		 * Note: sanitize_hex_color_no_hash() can also be used here, depending on whether
		 * or not the hash prefix should be stored/retrieved with the hex color value.
		 *
		 * @author WPTRT <https://github.com/WPTRT>
		 * @author Cherry Team <cherryframework@gmail.com>
		 * @see    sanitize_hex_color() https://developer.wordpress.org/reference/functions/sanitize_hex_color/
		 * @link   sanitize_hex_color_no_hash() https://developer.wordpress.org/reference/functions/sanitize_hex_color_no_hash/
		 * @since  1.0.0
		 * @param  [string]             $hex_color HEX color to sanitize.
		 * @param  WP_Customize_Setting $setting   Setting instance.
		 * @return string                          The sanitized hex color if not null; otherwise, the setting default.
		 */
		public function sanitize_hex_color( $hex_color, $setting ) {
			// Sanitize $input as a hex value without the hash prefix.
			$hex_color = sanitize_hex_color( $hex_color );

			// If $input is a valid hex value, return it; otherwise, return the default.
			return ( '' === $hex_color ) ? $setting->default : $hex_color;
		}

		/**
		 * Image sanitization callback.
		 *
		 * Checks the image's file extension and mime type against a whitelist. If they're allowed,
		 * send back the filename, otherwise, return the setting default.
		 *
		 * - Sanitization: image file extension
		 * - Control: text, WP_Customize_Image_Control
		 *
		 * @author WPTRT <https://github.com/WPTRT>
		 * @author Cherry Team <cherryframework@gmail.com>
		 * @see    wp_check_filetype() https://developer.wordpress.org/reference/functions/wp_check_filetype/
		 * @since  1.0.0
		 * @param  [string]             $image   Image filename.
		 * @param  WP_Customize_Setting $setting Setting instance.
		 * @return string                        The image filename if the extension is allowed; otherwise, the setting default.
		 */
		public function sanitize_image( $image, $setting ) {

			// Allow to correctly remove selected image
			if ( empty( $image ) ) {
				return $image;
			}

			$mimes = $this->get_image_types();

			// Return an array with file extension and mime_type.
			$file = wp_check_filetype( $image, $mimes );

			// If $image has a valid mime_type, return it; otherwise, return the default.
			return ( $file['ext'] ? $image : $setting->default );
		}

		/**
		 * URL sanitization callback.
		 *
		 * - Sanitization: url
		 * - Control: text, url
		 *
		 * Sanitization callback for 'url' type text inputs. This callback sanitizes `$url` as a valid URL.
		 *
		 * NOTE: esc_url_raw() can be passed directly as `$wp_customize->add_setting()` 'sanitize_callback'.
		 * It is wrapped in a callback here merely for example purposes.
		 *
		 * @author WPTRT <https://github.com/WPTRT>
		 * @author Cherry Team <cherryframework@gmail.com>
		 * @see    esc_url_raw() https://developer.wordpress.org/reference/functions/esc_url_raw/
		 * @since  1.0.0
		 * @param  [string] $url URL to sanitize.
		 * @return string Sanitized URL.
		 */
		public function sanitize_url( $url ) {
			return esc_url_raw( $url );
		}

		/**
		 * File URL sanitization callback.
		 *
		 * @since  1.0.0
		 * @param  [string] $url File URL to sanitize.
		 * @return string      Sanitized URL.
		 */
		public function sanitize_file( $url ) {
			return $this->sanitize_url( $url );
		}

		/**
		 * Range sanitization callback.
		 *
		 * - Sanitization: number_range
		 * - Control: number, tel
		 *
		 * Sanitization callback for 'number' or 'tel' type text inputs. This callback sanitizes
		 * `$number` as an absolute integer within a defined min-max range.
		 *
		 * @author WPTRT <https://github.com/WPTRT>
		 * @author Cherry Team <cherryframework@gmail.com>
		 * @see    absint() https://developer.wordpress.org/reference/functions/absint/
		 * @since  1.0.0
		 * @param  int                  $number  Number to check within the numeric range defined by the setting.
		 * @param  WP_Customize_Setting $setting Setting instance.
		 * @return int|string                    The number, if it is zero or greater and falls within the defined range;
		 *                                       otherwise, the setting default.
		 */
		public function sanitize_range( $number, $setting ) {
			// Get the input attributes associated with the setting.
			$atts = $setting->manager->get_control( $setting->id )->input_attrs;

			// Get step.
			$step = ( isset( $atts['step'] ) ? $atts['step'] : 1 );

			$number = ( ! isset( $atts['min'] ) && 0 > $number ) ? $setting->default : $number ;

			if ( is_float( $step ) ) {

				// Ensure input is a float value.
				$number  = floatval( $number );
				$checker = is_float( $number / $step );
			} else {

				// Ensure input is an absolute integer.
				$number  = ( isset( $atts['min'] ) && 0 > $atts['min'] && 0 > $number ) ? intval( $number ) : absint( $number );
				$checker = is_int( $number / $step );
			}

			// Get minimum number in the range.
			$min = ( isset( $atts['min'] ) ? $atts['min'] : $number );

			// Get maximum number in the range.
			$max = ( isset( $atts['max'] ) ? $atts['max'] : $number );

			// If the number is within the valid range, return it; otherwise, return the default
			return ( $min <= $number && $number <= $max && $checker ? $number : $setting->default );
		}

		/**
		 * Number sanitization callback.
		 *
		 * @since  1.0.0
		 * @param  int                  $number  Number to check within the numeric range defined by the setting.
		 * @param  WP_Customize_Setting $setting Setting instance.
		 * @return int|string                    The number, if it is zero or greater and falls within the defined range;
		 *                                       otherwise, the setting default.
		 */
		public function sanitize_number( $number, $setting ) {
			return $this->sanitize_range( $number, $setting );
		}

		/**
		 * Retrieve array of image file types.
		 *
		 * @author WPTRT <https://github.com/WPTRT>
		 * @author Cherry Team <cherryframework@gmail.com>
		 * @since  1.0.0
		 * @return array
		 */
		public function get_image_types() {
			/**
			 * Filter array of valid image file types.
			 *
			 * The array includes image mime types that are included in wp_get_mime_types()
			 *
			 * @since 1.0.0
			 * @param array  $mimes Image mime types.
			 * @param object $this  Cherry_Customiser instance.
			 */
			return apply_filters( 'cherry_customizer_get_image_types', array(
				'jpg|jpeg|jpe' => 'image/jpeg',
				'gif'          => 'image/gif',
				'png'          => 'image/png',
				'bmp'          => 'image/bmp',
				'tif|tiff'     => 'image/tiff',
				'ico'          => 'image/x-icon',
			), $this );
		}

		/**
		 * Fonts initialization.
		 * Once add records to the `wp_options` table.
		 *
		 * @since 1.0.0
		 */
		public function init_fonts() {

			$inited = get_option( 'cherry_customiser_fonts_inited' );

			if ( $inited ) {
				return;
			}

			$fonts_data = $this->get_fonts_data();
			$fonts_data = (array) $fonts_data;

			foreach ( $fonts_data as $type => $file ) {
				$data = $this->read_font_file( $file );
				add_option( 'cherry_customiser_fonts_' . $type, $data );
			}

			add_option( 'cherry_customiser_fonts_inited', true );
		}

		/**
		 * Prepare fonts.
		 *
		 * @since 1.0.0
		 */
		public function prepare_fonts() {
			$fonts_data = $this->get_fonts_data();
			$fonts_data = (array) $fonts_data;

			foreach ( $fonts_data as $type => $file ) {

				$fonts = get_option( 'cherry_customiser_fonts_' . $type, false );

				if ( false === $fonts ) {
					$fonts       = $this->read_font_file( $file );
					update_option( 'cherry_customiser_fonts_' . $type, $fonts );
				}

				if ( is_array( $fonts ) ) {
					$this->fonts = array_merge( $this->fonts, $this->satizite_font_family( $fonts ) );
				}
			}
		}

		/**
		 * Retrieve array with fonts file path.
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_fonts_data() {
			/**
			 * Filter array of fonts data.
			 *
			 * @since 1.0.0
			 * @param array  $data Set of fonts data.
			 * @param object $this Cherry_Customiser instance.
			 */
			return apply_filters( 'cherry_customizer_get_fonts_data', array(
				'standard' => $this->module_path . 'assets/fonts/standard.json',
				'google'   => $this->module_path . 'assets/fonts/google.json',
			), $this );
		}

		/**
		 * Retrieve array with font-family (for select element).
		 *
		 * @since  1.0.0
		 * @param  string $type Font type.
		 * @return array
		 */
		public function get_fonts( $type = '' ) {

			if ( ! empty( $this->fonts[ $type ] ) ) {
				return $this->fonts[ $type ];
			}

			if ( ! empty( $this->fonts ) ) {
				return $this->fonts;
			}

			$this->prepare_fonts( $type );

			return ! empty( $type ) && isset( $this->fonts[ $type ] ) ? $this->fonts[ $type ] : $this->fonts;
		}

		/**
		 * Retrieve a data from font's file.
		 *
		 * @since  1.0.0
		 * @param  string $file          File path.
		 * @return array        Fonts data.
		 */
		public function read_font_file( $file ) {

			if ( ! $this->file_exists( $file ) ) {
				return false;
			}

			// Read the file.
			$json = $this->get_file( $file );

			if ( ! $json ) {
				return new WP_Error( 'reading_error', 'Error when reading file' );
			}

			$content = json_decode( $json, true );

			return $content['items'];
		}

		/**
		 * Safely checks exists file or not.
		 *
		 * @since  1.1.4
		 * @global object $wp_filesystem
		 * @param  string $file File path.
		 * @return bool
		 */
		public function file_exists( $file ) {
			return file_exists( $file );
		}

		/**
		 * Safely get file content.
		 *
		 * @since  1.1.4
		 * @global object $wp_filesystem
		 * @param  string $file File path.
		 * @return bool
		 */
		public function get_file( $file ) {
			$result = Cherry_Toolkit::get_file( $file );
			return $result;
		}

		/**
		 * Retrieve a set with `font-family` ( 'foo' => 'foo' ).
		 *
		 * @since  1.0.0
		 * @param  array $data All fonts data.
		 * @return array
		 */
		public function satizite_font_family( $data ) {
			$keys   = array_map( array( $this, '_build_keys' ), $data );
			$values = array_map( array( $this, '_build_values' ), $data );

			array_filter( $keys );
			array_filter( $values );

			return array_combine( $keys, $values );
		}

		/**
		 * Function _build_keys.
		 *
		 * @since 1.0.0
		 */
		public function _build_keys( $item ) {

			if ( empty( $item['family'] ) ) {
				return false;
			}

			return sprintf( '%1$s, %2$s', $item['family'], $item['category'] );
		}

		/**
		 * Function _build_values.
		 *
		 * @since 1.0.0
		 */
		public function _build_values( $item ) {

			if ( empty( $item['family'] ) ) {
				return false;
			}

			return $item['family'];
		}

		/**
		 * Function add_options
		 *
		 * @since 1.0.0
		 */
		public function add_options() {

			if ( empty( $this->options ) ) {
				return;
			}

			$mods = get_theme_mods();

			foreach ( $this->options as $id => $option ) {

				if ( 'control' != $option['type'] ) {
					continue;
				}

				if ( isset( $mods[ $id ] ) ) {
					continue;
				}

				$mods[ $id ] = $this->get_default( $id );
			}

			$theme = get_option( 'stylesheet' );
			update_option( "theme_mods_$theme", $mods );
		}

		/**
		 * Callback-function for `upgrader_process_complete` hook (for clear fonts data).
		 *
		 * @since  1.0.0
		 * @param  WP_Upgrader $updater Upgrader instance.
		 * @param  array       $data    Array of bulk item update data.
		 */
		public function fire_clear_fonts( $updater, $data ) {
			$this->clear_fonts();
		}

		/**
		 * Clear customizer fonts.
		 *
		 * @since 1.0.0
		 */
		public function clear_fonts() {
			$fonts_data = $this->get_fonts_data();
			$fonts_data = (array) $fonts_data;

			foreach ( $fonts_data as $type => $file ) {
				delete_option( 'cherry_customiser_fonts_' . $type );
			}

			delete_option( 'cherry_customiser_fonts_inited' );

			$this->fonts = array();
		}

		/**
		 * Handler for custom `active_callback` feature.
		 *
		 * @since  1.0.0
		 * @param  string $callback Callback-function.
		 * @return mixed
		 */
		public function active_callback( $callback ) {
			$callback = esc_attr( $callback );

			if ( is_callable( array( $this, $callback ) ) ) {
				return array( $this, $callback );
			}

			return $callback;
		}

		/**
		 * Is the customizer preview a single post?
		 *
		 * @since  1.0.0
		 * @return bool
		 */
		public function callback_single() {
			return is_single();
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
