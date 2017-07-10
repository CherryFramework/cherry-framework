<?php
/**
 * Class for the building ui swither elements .
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

if ( ! class_exists( 'UI_Switcher' ) ) {

	/**
	 * Class for the building UI_Switcher elements.
	 */
	class UI_Switcher extends UI_Element implements I_UI {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $defaults_settings = array(
			'id'     => 'cherry-ui-swither-id',
			'name'   => 'cherry-ui-swither-name',
			'value'  => 'true',
			'toggle' => array(
				'true_toggle'  => 'On',
				'false_toggle' => 'Off',
				'true_slave'   => '',
				'false_slave'  => '',
			),
			'style'  => 'normal',
			'label'  => '',
			'class'  => '',
			'master' => '',
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
		 * Constructor method for the UI_Switcher class.
		 *
		 * @since 1.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-swither-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );
			$this->lock_element = new Cherry5_Lock_Element( $this->settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Render html UI_Switcher.
		 *
		 * @since 1.0.0
		 */
		public function render() {
			$data_slave_true  = ( ! empty( $this->settings['toggle']['true_slave'] ) ) ? 'data-slave="' . $this->settings['toggle']['true_slave'] . '" ' : '';
			$data_slave_false = ( ! empty( $this->settings['toggle']['false_slave'] ) ) ? 'data-slave="' . $this->settings['toggle']['false_slave'] . '" ' : '';
			$master_true = $data_slave_true || $data_slave_false ? 'data-master="true"' : '' ;

			$html = '';
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

				$value = filter_var( $this->settings['value'], FILTER_VALIDATE_BOOLEAN );

				$html .= '<div class="cherry-switcher-wrap size-' . esc_attr( $this->settings['style'] ) . '" ' . $master_true . '>';
					$html .= '<input type="radio" id="' . esc_attr( $this->settings['id'] ) . '-true" class="cherry-input-switcher cherry-input-switcher-true" name="' . esc_attr( $this->settings['name'] ) . '" ' . checked( true, $value, false ) . ' value="true" ' . $data_slave_true . ' ' . $this->lock_element->get_disabled_attr() . '>';
					$html .= '<input type="radio" id="' . esc_attr( $this->settings['id'] ) . '-false" class="cherry-input-switcher cherry-input-switcher-false" name="' . esc_attr( $this->settings['name'] ) . '" ' . checked( false, $value, false ) . ' value="false" ' . $data_slave_false . ' ' . $this->lock_element->get_disabled_attr() . '>';
					//$html .= '<span class="cherry-lable-content">';
					$html .= '<label class="sw-enable"><span>' . esc_html( $this->settings['toggle']['true_toggle'] ) . '</span></label>';
					$html .= '<label class="sw-disable"><span>' . esc_html( $this->settings['toggle']['false_toggle'] ) . '</span></label>';
					$html .= '<span class="state-marker"></span>';
				//	$html .= '</span>';
				$html .= '</div>';
				$html .= $this->lock_element->get_html();
			$html .= '</div>';

			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Switcher.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_script(
				'ui-switcher',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-switcher/assets/min/ui-switcher.min.js', Cherry_UI_Elements::$module_path ) ),
				array( 'jquery' ),
				Cherry_UI_Elements::$core_version,
				true
			);

			wp_enqueue_style(
				'ui-switcher',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-switcher/assets/min/ui-switcher.min.css', Cherry_UI_Elements::$module_path ) ),
				array(),
				Cherry_UI_Elements::$core_version,
				'all'
			);
		}
	}
}
