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
			'lock'        => false,
		);

		/**
		 * Instance of this Cherry5_Lock_Element class.
		 *
		 * @since 1.0.0
		 * @var object
		 * @access private
		 */
		private $lock_element = null;

		/**
		 * Constructor method for the UI_Stepper class.
		 *
		 * @since 1.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-stepper-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );
			$this->lock_element = new Cherry5_Lock_Element( $this->settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}


		/**
		 * Render html UI_Stepper.
		 *
		 * @since 1.0.0
		 */
		public function render() {
			$html = '';
			$input_lock = ( ! empty( $this->settings['lock'] ) ) ? 'disabled' : '' ;
			$lock_lable = ! empty( $this->settings['lock']['label'] )? sprintf('<div class="cherry-lock-label">%1$s</div>', $this->settings['lock']['label'] ) : '' ;
			$class = implode( ' ',
				array(
					$this->settings['class'],
					$this->settings['master'],
					$this->lock_element->get_class( 'inline-block' ),
				)
			);

			$html .= '<div class="cherry-ui-container ' . esc_attr( $class ) . '">';

				if ( '' !== $this->settings['label'] ) {
					$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . esc_html( $this->settings['label'] ) . '</label> ';
				}
				$html .= '<div class="cherry-ui-stepper">';
					$html .= '<input type="number" id="' . esc_attr( $this->settings['id'] ) . '" class="cherry-ui-stepper-input" pattern="[0-5]+([\.,][0-5]+)?" name="' . esc_attr( $this->settings['name'] ) . '" value="' . esc_html( $this->settings['value'] ) . '" min="' . esc_html( $this->settings['min_value'] ) . '" max="' . esc_html( $this->settings['max_value'] ) . '" step="' . esc_html( $this->settings['step_value'] ) . '" placeholder="' . esc_attr( $this->settings['placeholder'] ) . $this->lock_element->get_disabled_attr() . '">';
				$html .= '</div>';
				$html .= $this->lock_element->get_html();
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
				'ui-stepper',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-stepper/assets/min/ui-stepper.min.css', Cherry_UI_Elements::$module_path ) ),
				array(),
				Cherry_UI_Elements::$core_version,
				'all'
			);
		}
	}
}
