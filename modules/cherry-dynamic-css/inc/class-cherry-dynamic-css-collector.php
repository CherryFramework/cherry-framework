<?php
/**
 * Dynamic CSS collector class.
 *
 * @package    Cherry_Framework
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2017, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Dynamic_Css_Collector' ) ) {

	/**
	 * Define Cherry_Dynamic_Css_Collector class
	 */
	class Cherry_Dynamic_Css_Collector {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.2.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Array with sorted css
		 *
		 * @var array
		 */
		public static $sorted_css = array();

		/**
		 * Apropriate JS handle name
		 *
		 * @var string
		 */
		public static $js_handle = 'cherry-js-core';

		/**
		 * Passed handler file content
		 *
		 * @var string
		 */
		public static $handler_file = null;

		/**
		 * Set handler file on construct
		 */
		function __construct( $handler_file = null ) {
			self::$handler_file = $handler_file;
		}

		/**
		 * Add new style to collector
		 *
		 * @param  string $selector CSS selector to add styles for.
		 * @param  array  $style    Styles array to add.
		 * @param  array  $media    Media breakpoints.
		 * @return void
		 */
		public function add_style( $selector, $style = array(), $media = array() ) {

			$this->prepare_rule(
				$selector,
				array(
					'style' => $style,
					'media' => $media,
				)
			);

		}

		/**
		 * Return JS handle name
		 *
		 * @return string
		 */
		public function get_handle() {
			return apply_filters( 'cherry_dynamic_css_collector_handle', self::$js_handle );
		}

		/**
		 * Add inline JS handler
		 *
		 * @return void
		 */
		public function add_js_handler() {

			if ( ! self::$handler_file ) {
				return;
			}

			wp_add_inline_script( $this->get_handle(), self::$handler_file );
		}

		/**
		 * Print grabbed CSS
		 *
		 * @return void
		 */
		public function print_style() {

			self::$sorted_css = apply_filters(
				'cherry_dynamic_css_collected_styles',
				self::$sorted_css
			);

			if ( empty( self::$sorted_css ) || ! is_array( self::$sorted_css ) ) {
				return;
			}

			ob_start();

			do_action( 'cherry_dynamic_css_before_print_collected' );

			array_walk( self::$sorted_css, array( $this, 'print_breakpoint' ) );

			do_action( 'cherry_dynamic_css_after_print_collected' );

			$styles = ob_get_clean();

			$localize_var = apply_filters( 'cherry_dynamic_css_collector_localize_object', array(
				'type'  => 'text/css',
				'title' => 'cherry-collected-dynamic-style',
				'css'   => $styles,
			) );

			wp_localize_script( $this->get_handle(), 'CherryCollectedCSS', $localize_var );

		}

		/**
		 * Print single breakpoint
		 *
		 * @param  array  $rules      Rules array.
		 * @param  string $breakpoint Breakpoint name.
		 * @return void
		 */
		public function print_breakpoint( $rules, $breakpoint ) {

			if ( empty( $rules ) ) {
				return;
			}

			if ( 'all' !== $breakpoint ) {
				echo '@' . esc_attr( $breakpoint ) . ' {';
			}

			do_action( 'cherry_dynamic_css_breakpoint_start', $breakpoint );

			array_walk( $rules, array( $this, 'print_rules' ) );

			do_action( 'cherry_dynamic_css_breakpoint_end', $breakpoint );

			if ( 'all' !== $breakpoint ) {
				echo '}';
			}

		}

		/**
		 * Print rules for selector.
		 *
		 * @param  array  $rule     Single rule.
		 * @param  string $selector Selector name.
		 * @return void
		 */
		public function print_rules( $rule, $selector ) {

			echo esc_attr( $selector ) . ' {';

			do_action( 'cherry_dynamic_css_rule_start', $selector );

			array_walk( $rule, array( $this, 'print_property' ) );

			do_action( 'cherry_dynamic_css_rule_end', $selector );

			echo '}';

		}

		/**
		 * Print single rule.
		 *
		 * @param  string $value Property value.
		 * @param  string $name  Property name.
		 * @return void
		 */
		public function print_property( $value, $name ) {
			printf( '%1$s:%2$s; ', $name, $value );
		}

		/**
		 * Print passed rule.
		 *
		 * @param  string $selector Selector name.
		 * @param  array  $rule     CSS rule data.
		 * @return void|bool
		 */
		public function prepare_rule( $selector, $rule ) {

			$rule = array_merge( array(
				'style' => array(),
				'media' => array(),
			), $rule );

			if ( empty( $rule['style'] ) ) {
				return false;
			}

			$breakpoint = $this->breakpoint_name( $rule['media'] );

			if ( ! isset( self::$sorted_css[ $breakpoint ] ) ) {
				self::$sorted_css[ $breakpoint ] = array();
			}

			if ( isset( self::$sorted_css[ $breakpoint ][ $selector ] ) ) {
				self::$sorted_css[ $breakpoint ][ $selector ] = array_merge(
					self::$sorted_css[ $breakpoint ][ $selector ],
					$rule['style']
				);
			} else {
				self::$sorted_css[ $breakpoint ][ $selector ] = $rule['style'];
			}

		}

		/**
		 * Generate media rule name
		 *
		 * @param  array $media Media breakpoints.
		 * @return string
		 */
		public function breakpoint_name( $media ) {

			$has_media = false;
			$min       = '';
			$max       = '';
			$sep       = '';

			if ( ! empty( $media['min'] ) ) {
				$has_media = true;
				$min       = sprintf( '(min-width: %1$s)', esc_attr( $media['min'] ) );
			}

			if ( ! empty( $media['max'] ) ) {
				$sep       = true === $has_media ? ' and ' : '';
				$has_media = true;
				$max       = sprintf( '(max-width: %1$s)', esc_attr( $media['max'] ) );
			}

			if ( ! $has_media ) {
				return 'all';
			}

			return sprintf( 'media %1$s%3$s%2$s', $min, $max, $sep );
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.2.0
		 * @return object
		 */
		public static function get_instance( $handler_file = null ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $handler_file );
			}
			return self::$instance;
		}
	}

}
