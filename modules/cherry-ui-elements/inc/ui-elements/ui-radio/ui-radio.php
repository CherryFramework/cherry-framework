<?php
/**
 * Class for the building ui-radio elements.
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

if ( ! class_exists( 'UI_Radio' ) ) {

	/**
	 * Class for the building UI_Radio elements.
	 */
	class UI_Radio extends UI_Element implements I_UI {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $defaults_settings = array(
			'id'      => 'cherry-ui-radio-id',
			'name'    => 'cherry-ui-radio-name',
			'value'   => 'radio-2',
			'options' => array(
				'radio-1' => array(
					'label'   => 'Radio 1',
					'img_src' => '',
					'slave'   => '',
					'lock'    => array(
						'label' => '',
						'url'   => '',
						'icon'  => '',
					),
				),
				'radio-2' => array(
					'label'   => 'Radio 2',
					'img_src' => '',
					'slave'   => '',
				),
				'radio-3' => array(
					'label'   => 'Radio 3',
					'img_src' => '',
					'slave'   => '',
				),
			),
			'slave'  => array(),
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
		 * Constructor method for the UI_Radio class.
		 *
		 * @since 1.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-radio-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );
			$this->lock_element = new Cherry5_Lock_Element( $this->settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

			self::enqueue_assets();
		}

		/**
		 * Render html UI_Radio.
		 *
		 * @since 1.0.0
		 */
		public function render() {
			$html  = '';
			$class = implode( ' ',
				array(
					$this->settings['class'],
					$this->settings['master'],
					$this->lock_element->get_class( 'inline-block' ),
				)
			);

			$html .= '<div class="cherry-ui-container ' . esc_attr( $class ) . '" >';
				if ( $this->settings['options'] && ! empty( $this->settings['options'] ) && is_array( $this->settings['options'] ) ) {
					if ( '' !== $this->settings['label'] ) {
						$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . $this->settings['label'] . '</label> ';
					}
					$html .= '<div class="cherry-radio-group">';
						foreach ( $this->settings['options'] as $option => $option_value ) {
							$lock_option = new Cherry5_Lock_Element( $option_value );

							$checked    = $option == $this->settings['value'] ? ' checked' : '';
							$radio_id   = $this->settings['id'] . '-' . $option;
							$img        = isset( $option_value['img_src'] ) && ! empty( $option_value['img_src'] ) ? '<img src="' . esc_url( $option_value['img_src'] ) . '" alt="' . esc_html( $option_value['label'] ) . '">' : '<span class="cherry-radio-item"><i></i></span>';
							$data_slave = isset( $option_value['slave'] ) && ! empty( $option_value['slave'] ) ? ' data-slave="' . $option_value['slave'] . '"' : '';
							$class_box  = isset( $option_value['img_src'] ) && ! empty( $option_value['img_src'] ) ? ' cherry-radio-img' : ' cherry-radio-item' ;
							$class_box  .= $lock_option->get_class();

							$html .= '<div class="' . $class_box . '">';
							$html .= '<input type="radio" id="' . esc_attr( $radio_id ) . '" class="cherry-radio-input" name="' . esc_attr( $this->settings['name'] ) . '" ' . checked( $option, $this->settings['value'], false ) . ' value="' . esc_attr( $option ) . '"' . $data_slave . ' ' . $lock_option->get_disabled_attr() . '/>';
							$label_content = $img . $option_value['label'];
							$html .= '<label for="' . esc_attr( $radio_id ) . '"><span class="cherry-lable-content">' . $label_content . '</span>' . $lock_option->get_html() . '</label> ';
							$html .= '</div>';
						}
						$html .= '<div class="clear"></div>';
					$html .= '</div>';
				}
			$html .= $this->lock_element->get_html() . '</div>';

			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Radio.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_style(
				'ui-radio',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-radio/assets/min/ui-radio.min.css', Cherry_UI_Elements::$module_path ) ),
				array(),
				Cherry_UI_Elements::$core_version,
				'all'
			);

			wp_enqueue_script(
				'ui-radio-min',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-radio/assets/min/ui-radio.min.js', Cherry_UI_Elements::$module_path ) ),
				array( 'jquery' ),
				Cherry_UI_Elements::$core_version,
				true
			);
		}
	}
}
