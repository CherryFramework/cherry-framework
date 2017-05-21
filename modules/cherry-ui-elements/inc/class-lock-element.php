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

// If class `Lock_Element` doesn't exists yet.
if ( ! class_exists( 'Lock_Element' ) ) {

	/**
	 * Lock_Element class.
	 */
	class Lock_Element {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var array
		 */
		private $defaults_args = array(
			'label' => '',
			'url'   => '',
			'html'  => '',
			'icon'  => '',
			'class' => 'cherry-lock-element',
		);

		/**
		 * The attributes of the class.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var array
		 */
		private $args = array();

		/**
		 * The status of locked element.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var   boolean
		 */
		private $element_lock = false;

		/**
		 * Constructor method for the class.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( $args = array() ) {

			if ( empty( $args ) || empty( $args['lock'] ) ) {
				return false;
			}

			$this->element_lock           = true;
			$this->defaults_args['label'] = esc_html__( 'Unlocked in PRO', 'cherry-framework' );
			$this->defaults_args['html']  = apply_filters( 'cherry_lock_ui_element_html', '<a class="cherry-lock-element__area" target="_blanl" href="%1$s" alt="%3$s"><span class="cherry-lock-element__label">%2$s %3$s</span></a>' );
			$this->defaults_args['icon']  = apply_filters( 'cherry_lock_ui_element_icon', '<i class="fa fa-unlock-alt" aria-hidden="true"></i>' );
			$this->args                   = wp_parse_args( $args['lock'], $this->defaults_args );
		}

		/**
		 * Return lock html class.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return string
		 */
		public function get_class( $sub_class = '' ) {
			return ( $this->element_lock ) ? ' ' . $this->args['class'] . ' ' . $sub_class : '' ;
		}

		/**
		 * Return disabled attribute.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return string
		 */
		public function get_disabled_attr() {
			return ( $this->element_lock ) ? ' disabled' : '' ;
		}

		/**
		 * Return lock element html.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return string
		 */
		public function get_html() {
			return ( $this->element_lock ) ? sprintf( $this->args['html'], esc_url( $this->args['url'] ), $this->args['icon'], esc_attr( $this->args['label'] ) ) : '' ;
		}
	}
}
