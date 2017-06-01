<?php
/**
 * Class lock the elements
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2017, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.en.html
 */

// If class `Cherry5_Lock_Element` doesn't exists yet.
if ( ! class_exists( 'Cherry5_Lock_Element' ) ) {

	/**
	 * Cherry5_Lock_Element class.
	 */
	class Cherry5_Lock_Element {

		/**
		 * Default settings.
		 *
		 * @since 1.4.3
		 * @access private
		 * @var array
		 */
		private $defaults_args = array();

		/**
		 * The attributes of the class.
		 *
		 * @since 1.4.3
		 * @access private
		 * @var array
		 */
		private $args = array();

		/**
		 * The status of locked element.
		 *
		 * @since 1.4.3
		 * @access private
		 * @var bool
		 */
		private $element_lock = false;

		/**
		 * Constructor method for the class.
		 *
		 * @since 1.4.3
		 * @access public
		 * @return void
		 */
		public function __construct( $args = array() ) {

			if ( ! is_array( $args ) || empty( $args ) || empty( $args['lock'] ) ) {
				return false;
			}

			$this->element_lock  = true;
			$this->defaults_args = apply_filters( 'cherry5_lock_element_defaults', array(
				'label' => esc_html__( 'Unlocked in PRO', 'cherry-framework' ),
				'url'   => '#',
				'html'  => '<a class="cherry-lock-element__area" target="_blank" href="%1$s" title="%3$s"><span class="cherry-lock-element__label">%2$s %3$s</span></a>',
				'icon'  => '<i class="fa fa-unlock-alt" aria-hidden="true"></i>',
				'class' => 'cherry-lock-element',
			), $args );

			$this->args = wp_parse_args( $args['lock'], $this->defaults_args );
		}

		/**
		 * Return lock element HTML-class.
		 *
		 * @since 1.4.3
		 * @access public
		 * @return string
		 */
		public function get_class( $sub_class = '' ) {

			if ( ! $this->element_lock ) {
				return '';
			}

			$classes = array(
				$this->args['class'],
				$sub_class,
			);

			$classes = array_filter( $classes );
			$classes = array_map( 'esc_attr', $classes );

			return ' ' . join( ' ', $classes );
		}

		/**
		 * Return disabled attribute.
		 *
		 * @since 1.4.3
		 * @access public
		 * @return string
		 */
		public function get_disabled_attr() {
			return $this->element_lock ? ' disabled' : '';
		}

		/**
		 * Return lock element HTML-markup.
		 *
		 * @since 1.4.3
		 * @access public
		 * @return string
		 */
		public function get_html() {

			if ( ! $this->element_lock ) {
				return '';
			}

			return sprintf( $this->args['html'],
				esc_url( $this->args['url'] ),
				$this->args['icon'],
				esc_attr( $this->args['label'] )
			);
		}
	}
}
