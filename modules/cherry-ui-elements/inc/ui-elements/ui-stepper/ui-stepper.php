<?php
/**
 * Class for the building ui stepper elements.
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.en.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'UI_Stepper' ) ) {

	/**
	 * Class for the building UI_Stepper elements.
	 */
	class UI_Stepper extends UI_Element implements I_UI {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $defaults_settings = array(
			'id'          => 'cherry-ui-stepper-id',
			'name'        => 'cherry-ui-stepper-name',
			'value'       => '0',
			'max_value'   => '100',
			'min_value'   => '0',
			'step_value'  => '1',
			'label'       => '',
			'class'       => '',
			'master'      => '',
			'placeholder' => '',
		);

		/**
		 * Constructor method for the UI_Stepper class.
		 *
		 * @since 1.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-stepper-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}


		/**
		 * Render html UI_Stepper.
		 *
		 * @since 1.0.0
		 */
		public function render() {
			$html = '';
			$class = $this->settings['class'];
			$class .= ' ' . $this->settings['master'];

			$html .= '<div class="cherry-ui-container ' . esc_attr( $class ) . '">';

				if ( '' !== $this->settings['label'] ) {
					$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . esc_html( $this->settings['label'] ) . '</label> ';
				}
				$html .= '<div class="cherry-ui-stepper">';
					$html .= '<input type="number" id="' . esc_attr( $this->settings['id'] ) . '" class="cherry-ui-stepper-input" pattern="[0-5]+([\.,][0-5]+)?" name="' . esc_attr( $this->settings['name'] ) . '" value="' . esc_html( $this->settings['value'] ) . '" min="' . esc_html( $this->settings['min_value'] ) . '" max="' . esc_html( $this->settings['max_value'] ) . '" step="' . esc_html( $this->settings['step_value'] ) . '" placeholder="' . esc_attr( $this->settings['placeholder'] ) . '">';
				$html .= '</div>';
			$html .= '</div>';

			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Stepper.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_style(
				'ui-stepper-min',
				esc_url( Cherry_Core::base_url( 'assets/min/ui-stepper.min.css', __FILE__ ) ),
				array(),
				'1.3.2',
				'all'
			);
		}
	}
}
