<?php
/**
 * Class for the building ui-button elements.
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'UI_Button' ) ) {

	/**
	 * Class for the building ui-text elements.
	 */
	class UI_Button extends UI_Element implements I_UI {

		/**
		 * Default settings
		 *
		 * @var array
		 */
		private $defaults_settings = array(
			'type'       => 'text',
			'id'         => 'cherry-ui-button-id',
			'name'       => 'cherry-ui-button-name',
			'value'      => 'button',
			'disabled'   => false,
			'form'       => '',
			'formaction' => '',
			'type'       => 'button',
			'style'      => 'normal',
			'content'    => 'Button',
			'class'      => '',
			'master'     => '',
		);

		/**
		 * Constructor method for the UI_Text class.
		 *
		 * @since  4.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-button-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Render html UI_Text.
		 *
		 * @since  4.0.0
		 */
		public function render() {
			$html = '';
			$class = $this->settings['class'];
			$class .= ' ' . $this->settings['master'];

			/*$html .= sprintf(
				'<button type="%1$s" id="%2$s" name="%3$s" class="ui-button %4$s %5$s" %6$s%7$s%8$s>%9$s</button>',
				esc_attr( $this->settings['type'] ),
				esc_attr( $this->settings['id'] ),
				esc_attr( $this->settings['name'] ),
				! empty( $this->settings['style'] ) ? esc_attr( 'ui-button-' . $this->settings['style'] . '-style' ) : '',
				esc_attr( $this->settings['class'] ),
				filter_var( $this->settings['disabled'], FILTER_VALIDATE_BOOLEAN ) ? ' disabled="true"' : '',
				! empty( $this->settings['form'] ) ? ' form="' . esc_attr( $this->settings['form'] ) . '"' : '',
				! empty( $this->settings['formaction'] ) ? ' formaction="' . esc_attr( $this->settings['formaction'] ) . '"' : '',
				esc_attr( $this->settings['value'] )
			);


			return $html;*/



			$html = Cherry_Toolkit::render_view(
				dirname( __FILE__ ) . '/view/button-view.php',
				$this->settings
			);

			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Text
		 *
		 * @since  4.0.0
		 */
		public static function enqueue_assets() {

			wp_enqueue_style(
				'ui-button',
				esc_url( Cherry_Core::base_url( 'assets/min/ui-button.min.css', __FILE__ ) ),
				array(),
				'1.0.0',
				'all'
			);
		}
	}
}
