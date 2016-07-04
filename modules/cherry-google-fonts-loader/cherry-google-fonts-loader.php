<?php
/**
 * Module Name: Google Fonts Loader
 * Description: Enqueue Google fonts
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

if ( ! class_exists( 'Cherry_Google_Fonts_Loader' ) ) {

	/**
	 * Google fonts loader main class
	 */
	class Cherry_Google_Fonts_Loader {

		/**
		 * Module version
		 *
		 * @var string
		 */
		public $module_version = '1.1.0';

		/**
		 * Module version
		 *
		 * @var string
		 */
		public $module_slug = 'cherry-google-fonts-loader';

		/**
		 * Module arguments
		 *
		 * @var array
		 */
		public $args = array();

		/**
		 * Core instance
		 *
		 * @var object
		 */
		public $core = null;

		/**
		 * Define fonts server URL
		 *
		 * @var string
		 */
		public $fonts_host = '//fonts.googleapis.com/css';

		/**
		 * Google fonts set
		 *
		 * @var array
		 */
		public $google_fonts = null;

		/**
		 * Array of stored google fonts data
		 *
		 * @var array
		 */
		public $fonts_data = array();

		/**
		 * Constructor for the class
		 */
		function __construct( $core, $args ) {

			$this->core = $core;
			$this->args = wp_parse_args( $args, array( 'options' => array() ) );

			$this->fonts_host = apply_filters( 'cherry_google_fonts_cdn', $this->fonts_host );

			add_action( 'customize_preview_init', array( $this, 'reset_fonts_cache' ) );
			add_action( 'customize_save_after', array( $this, 'reset_fonts_cache' ) );
			add_action( 'switch_theme', array( $this, 'reset_fonts_cache' ) );

			if ( is_admin() ) {
				return;
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'prepare_fonts' ) );

		}

		/**
		 * Get fonts data and enqueue URL
		 *
		 * @since 1.0.0
		 */
		public function prepare_fonts() {

			$font_url = $this->get_fonts_url();
			wp_enqueue_style( 'cherry-google-fonts', $font_url );
		}

		/**
		 * Return theme Google fonts URL to enqueue it
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_fonts_url() {

			$font_url = get_transient( 'cherry_google_fonts_url' );

			if ( ! $font_url ) {

				// Get typography options list
				$options_set = $this->get_options_set();

				// build Google fonts data array
				foreach ( $options_set as $option ) {
					$this->add_font( $option );
				}

				$font_url = $this->build_fonts_url();

				if ( false == $font_url ) {
					return;
				}

				global $wp_customize;
				if ( ! isset( $wp_customize ) ) {
					set_transient( 'cherry_google_fonts_url', $font_url, WEEK_IN_SECONDS );
				}
			}

			return $font_url;

		}

		/**
		 * Get options set from module arguments
		 *
		 * @return array
		 */
		public function get_options_set() {
			return $this->args['options'];
		}

		/**
		 * Get current setting by name
		 *
		 * @since  1.0.0
		 * @return mixed
		 */
		public function get_setting( $name ) {

			$type = $this->args['type'];

			if ( 'theme_mod' == $type ) {
				$setting = get_theme_mod( $name );
				return $setting;
			}

			if ( true != $this->args['single'] ) {
				$setting = get_option( $name );
				return $setting;
			}

			$settings = get_option( $this->args['prefix'] );

			if ( ! empty( $settings ) && isset( $settings[ $name ] ) ) {
				return $settings[ $name ];
			}

			return false;

		}

		/**
		 * Build Google fonts stylesheet URL from stored data
		 *
		 * @since  1.0.0
		 */
		public function build_fonts_url() {

			$font_families = array();
			$subsets       = array();

			if ( empty( $this->fonts_data ) ) {
				return false;
			}

			foreach ( $this->fonts_data as $family => $data ) {
				$styles = implode( ',', array_unique( array_filter( $data['style'] ) ) );
				$font_families[] = $family . ':' . $styles;
				$subsets = array_merge( $subsets, $data['character'] );
			}

			$subsets = array_unique( array_filter( $subsets ) );

			$query_args = array(
				'family' => urlencode( implode( '|', $font_families ) ),
				'subset' => urlencode( implode( ',', $subsets ) ),
			);

			$fonts_url = add_query_arg( $query_args, $this->fonts_host );

			return $fonts_url;
		}

		/**
		 * Get single typography option value from database and store it in object property
		 *
		 * @since  1.0.0
		 * @param  [type] $font option name to get from database.
		 */
		public function add_font( $font ) {

			$font = wp_parse_args( $font, array(
				'family'  => '',
				'style'   => 'normal',
				'weight'  => '400',
				'charset' => 'latin',
			) );

			$family = $this->get_setting( $font['family'] );
			$family = explode( ',', $family );
			$family = trim( $family[0], "'" );

			if ( ! $this->is_google_font( $family ) ) {
				return;
			}

			$load_style = $this->get_setting( $font['weight'] );
			$font_style = $this->get_setting( $font['style'] );

			if ( 'italic' === $font_style ) {
				$load_style .= $font_style;
			}

			if ( ! isset( $this->fonts_data[ $family ] ) ) {

				$this->fonts_data[ $family ] = array(
					'style'     => array( $load_style ),
					'character' => array( $this->get_setting( $font['charset'] ) ),
				);

			} else {

				$this->fonts_data[ $family ] = array(
					'style' => $this->add_font_prop(
						$load_style,
						$this->fonts_data[ $family ]['style']
					),
					'character' => $this->add_font_prop(
						$this->get_setting( $font['charset'] ),
						$this->fonts_data[ $family ]['character']
					),
				);

			}

		}

		/**
		 * Add new font property to existaing properties array
		 *
		 * @since 1.0.0
		 * @param [type] $new      property to add.
		 * @param array  $existing existing properties.
		 */
		public function add_font_prop( $new, $existing ) {

			if ( ! is_array( $existing ) ) {
				return array( $new );
			}

			if ( ! in_array( $new, $existing ) ) {
				$existing[] = $new;
			}

			return $existing;

		}

		/**
		 * Check if selected font is google font
		 *
		 * @since  1.0.0
		 * @param  array $font_family font family to check.
		 * @return boolean
		 */
		public function is_google_font( $font_family ) {

			$google_fonts = $this->get_google_fonts();

			if ( empty( $google_fonts ) ) {

				$customizer = isset( $this->core->modules['cherry-customizer'] ) ? $this->core->modules['cherry-customizer'] : false;

				if ( ! $customizer ) {
					return false;
				}

				$customizer->init_fonts();

				$google_fonts = $this->get_google_fonts();

				if ( empty( $google_fonts ) ) {
					return false;
				}
			}

			$font_family = explode( ',', $font_family );
			$font_family = trim( $font_family[0], "'" );

			foreach ( $google_fonts as $font ) {
				if ( $font_family === $font['family'] ) {
					return true;
				}
			}

			return false;

		}

		/**
		 * Get google fonts array
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_google_fonts() {

			if ( null === $this->google_fonts ) {
				$this->google_fonts = get_option( 'cherry_customiser_fonts_google', null );
			}

			return $this->google_fonts;
		}

		/**
		 * Reset fonts cache
		 *
		 * @since 1.0.0
		 */
		public function reset_fonts_cache() {
			delete_transient( 'cherry_google_fonts_url' );
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
