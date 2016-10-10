<?php
/**
 * Dynamic CSS collector class.
 *
 * @package    Cherry_Framework
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
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
		 * Holder for grabbed CSS
		 * @var array
		 */
		public static $grabbed_css = array();

		/**
		 * Array with sorted css
		 * @var array
		 */
		public static $sorted_css = array();

		/**
		 * Add new style to collector
		 *
		 * @param  string $selector CSS selector to add styles for.
		 * @param  array  $style    Styles array to add.
		 * @param  array  $media    Media breakpoints.
		 * @return void
		 */
		public function add_style( $selector, $style = array(), $media = array() ) {

			self::$grabbed_css[ $selector ] = array(
				'style' => $style,
				'media' => $media,
			);

		}

		/**
		 * Print grabbed CSS
		 *
		 * @return void
		 */
		public function print_style() {

			$format = apply_filters(
				'cherry_dynamic_css_collector_print_format',
				'<style title="cherry-collected-dynamic-style" type="text/css">%s</style>'
			);

			self::$grabbed_css = apply_filters(
				'cherry_dynamic_css_collected_styles',
				self::$grabbed_css
			);

			if ( empty( self::$grabbed_css ) || ! is_array( self::$grabbed_css ) ) {
				return;
			}

			ob_start();

			do_action( 'cherry_dynamic_css_before_print_collected' );

			foreach ( self::$grabbed_css as $selector => $rule ) {
				$this->prepare_rule( $selector, $rule );
			}

			if ( ! empty( self::$sorted_css ) ) {
				array_walk( self::$sorted_css, array( $this, 'print_breakpoint' ) );
			}

			do_action( 'cherry_dynamic_css_after_print_collected' );

			$styles = ob_get_clean();

			printf( $format, $styles );

		}

		/**
		 * Print single breakpoint
		 *
		 * @param  array $rules Rules array.
		 * @return void
		 */
		public function print_breakpoint( $rules, $breakpoint ) {

			if ( empty( $rules ) ) {
				return;
			}

			if ( 'all' !== $breakpoint ) {
				echo '@' . $breakpoint . ' {';
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
		 * @param  array  $rule     Single rule
		 * @param  string $selector Selector name.
		 * @return void
		 */
		public function print_rules( $rule, $selector ) {

			echo $selector . ' {';

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
		 * @return void
		 */
		public function prepare_rule( $selector, $rule ) {

			$rule = array_merge( array(
				'style' => array(),
				'media' => array(),
			), $rule );

			if ( empty( $rule['style'] ) ) {
				return;
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
		 * @return void
		 */
		public function breakpoint_name( $media ) {

			$has_media = false;
			$min       = '';
			$max       = '';

			if ( ! empty( $media['min'] ) ) {
				$has_media = true;
				$min       = sprintf( '(min-width: %1$s)', esc_attr( $media['min'] ) );
			}

			if ( ! empty( $media['max'] ) ) {
				$has_media = true;
				$sep       = true === $has_media ? ' & ' : '';
				$max       = sprintf( '(max-width: %1$s)', esc_attr( $media['min'] ) );
			}

			if ( ! $has_media ) {
				return 'all';
			}

			return sprintf( 'media %1$s%2$s', $min, $max );
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.2.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}

}
