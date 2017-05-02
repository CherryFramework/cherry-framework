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
		 * Constructor method for the UI_Switcher class.
		 *
		 * @since 1.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-swither-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

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

			$lock_html = '';
			$input_lock = '' ;
			if ( ! empty( $this->settings['lock'] ) ){
				$input_lock = 'disabled';
				$default_attr = array(
					'label' => esc_html__( 'Unlocked in PRO', 'cherry-framework' ),
					'url'   => esc_url('#'),
					'icon'  => '<i class="fa fa-unlock-alt" aria-hidden="true"></i>',
				);
				$lock_attr = is_array( $this->settings['lock'] ) ? wp_parse_args( $this->settings['lock'], $default_attr ) : $default_attr ;

				$lock_html = sprintf( '<a class="cherry-lock__area" target="_blanl" href="%1$s" alt="%3$s"><span class="cherry-lock__label">%2$s %3$s</span></a>', esc_url( $lock_attr['url'] ), $lock_attr['icon'], esc_attr( $lock_attr['label'] ) );
			}

			$html = '';
			$class = implode( ' ',
				array(
					$this->settings['class'],
					$this->settings['master'],
				)
			);

			$html .= '<div class="cherry-ui-container ' . esc_attr( $class ) . '">';
				if ( '' !== $this->settings['label'] ) {
					$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . esc_html( $this->settings['label'] ) . '</label> ';
				}

				$value = filter_var( $this->settings['value'], FILTER_VALIDATE_BOOLEAN );

				$html .= '<div class="cherry-switcher-wrap size-' . esc_attr( $this->settings['style'] ) . '" ' . $master_true . '>';
					$html .= '<input type="radio" id="' . esc_attr( $this->settings['id'] ) . '-true" class="cherry-input-switcher cherry-input-switcher-true" name="' . esc_attr( $this->settings['name'] ) . '" ' . checked( true, $value, false ) . ' value="true" ' . $data_slave_true . ' ' . $input_lock . '>';
					$html .= '<input type="radio" id="' . esc_attr( $this->settings['id'] ) . '-false" class="cherry-input-switcher cherry-input-switcher-false" name="' . esc_attr( $this->settings['name'] ) . '" ' . checked( false, $value, false ) . ' value="false" ' . $data_slave_false . ' ' . $input_lock . '>';
					$html .= '<span class="cherry-lable-content">';
					$html .= '<label class="sw-enable"><span>' . esc_html( $this->settings['toggle']['true_toggle'] ) . '</span></label>';
					$html .= '<label class="sw-disable"><span>' . esc_html( $this->settings['toggle']['false_toggle'] ) . '</span></label>';
					$html .= '<span class="state-marker"></span>';
					$html .= '</span>';
					$html .= $lock_html;
				$html .= '</div>';
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
				'ui-switcher-min',
				esc_url( Cherry_Core::base_url( 'assets/min/ui-switcher.min.js', __FILE__ ) ),
				array( 'jquery' ),
				'1.3.2',
				true
			);

			wp_enqueue_style(
				'ui-switcher-min',
				esc_url( Cherry_Core::base_url( 'assets/min/ui-switcher.min.css', __FILE__ ) ),
				array(),
				'1.3.2',
				'all'
			);
		}
	}
}
