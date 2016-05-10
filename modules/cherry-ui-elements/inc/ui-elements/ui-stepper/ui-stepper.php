<?php
/**
 * Class for the building ui stepper elements.
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

if ( ! class_exists( 'UI_Stepper' ) ) {

	/**
	 * Class for the building UI_Stepper elements.
	 */
	class UI_Stepper extends UI_Element implements I_UI {

		/**
		 * Default settings
		 *
		 * @var array
		 */
		private $defaults_settings = array(
			'id'			=> 'cherry-ui-stepper-id',
			'name'			=> 'cherry-ui-stepper-name',
			'value'			=> '0',
			'max_value'		=> '100',
			'min_value'		=> '0',
			'step_value'	=> '1',
			'label'			=> '',
			'class'			=> '',
			'master'		=> '',
			'step_up'		=> 'Step Up',
			'step_down'		=> 'Step Down',
		);
		/**
		 * Constructor method for the UI_Stepper class.
		 *
		 * @since  4.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-stepper-'.uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}


		/**
		 * Render html UI_Stepper.
		 *
		 * @since  4.0.0
		 */
		public function render() {
			$html = '';

			$master_class = ! empty( $this->settings['master'] ) && isset( $this->settings['master'] ) ? esc_html( $this->settings['master'] ) : '';

			$html .= '<div class="cherry-ui-container ' . $master_class . '">';

				if ( '' !== $this->settings['label'] ) {
					$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . esc_html( $this->settings['label'] ) . '</label> ';
				}
				$html .= '<div class="cherry-ui-stepper ' . esc_attr( $this->settings['class'] ) . '">';
					$html .= '<input type="text" id="' . esc_attr( $this->settings['id'] ) . '" class="cherry-ui-stepper-input" name="' . esc_attr( $this->settings['name'] ) . '" value="' . esc_html( $this->settings['value'] ) . '" data-max-value="' . esc_html( $this->settings['max_value'] ) . '" placeholder="inherit" data-min-value="' . esc_html( $this->settings['min_value'] ) . '" data-step-value="' . esc_html( $this->settings['step_value'] ) . '">';
					$html .= '<span class="cherry-stepper-controls"><em class="step-up" title="' . $this->settings['step_up'] . '">+</em><em class="step-down" title="' . $this->settings['step_down'] . '">-</em></span>';
				$html .= '</div>';
			$html .= '</div>';

			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Stepper.
		 *
		 * @since  4.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_script(
				'ui-stepper-min',
				self::get_current_file_url( __FILE__ ) . '/assets/min/ui-stepper.min.js',
				array( 'jquery' ),
				'1.0.0',
				true
			);
			wp_enqueue_style(
				'ui-stepper-min',
				self::get_current_file_url( __FILE__ ) . '/assets/min/ui-stepper.min.css',
				array(),
				'1.0.0',
				'all'
			);
		}
	}
}
