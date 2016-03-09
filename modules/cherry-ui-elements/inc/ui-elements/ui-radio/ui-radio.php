<?php
/**
 * Class for the building ui-radio elements.
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'UI_Radio' ) ) {
	class UI_Radio extends UI_Element implements I_UI {

		private $defaults_settings = array(
			'id'				=> 'cherry-ui-radio-id',
			'name'				=> 'cherry-ui-radio-name',
			'value'				=> 'radio-2',
			'options'			=> array(
				'radio-1' => array(
					'label' => 'Radio 1',
					'img_src'	=> '',
					'slave'		=> ''
				),
				'radio-2' => array(
					'label' => 'Radio 2',
					'img_src'	=> '',
					'slave'		=> ''
				),
				'radio-3' => array(
					'label' => 'Radio 3',
					'img_src'	=> '',
					'slave'		=> ''
				),
			),
			'slave'				=> array(),
			'label'				=> '',
			'class'				=> '',
			'master'			=> '',
		);

		/**
		 * Constructor method for the UI_Radio class.
		 *
		 * @since  4.0.0
		 */
		function __construct( $args = array() ) {

			$this->defaults_settings['id'] = 'cherry-ui-radio-'.uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

			self::enqueue_assets();
		}

		/**
		 * Render html UI_Radio.
		 *
		 * @since  4.0.0
		 */
		public function render() {
			$html = '';
			$master_class = ! empty( $this->settings['master'] ) && isset( $this->settings['master'] ) ? esc_html( $this->settings['master'] ) : '';

			$html .= '<div class="cherry-ui-container ' . $master_class . '">';
				if ( $this->settings['options'] && !empty( $this->settings['options'] ) && is_array( $this->settings['options']) ) {
					if( '' !== $this->settings['label'] ){
						$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . $this->settings['label'] . '</label> ';
					}
					$html .= '<div class="cherry-radio-group">';
						foreach ( $this->settings['options'] as $option => $option_value ) {
							$checked = $option == $this->settings['value'] ? ' checked' : '';
							$radio_id = $this->settings['id'] . '-' . $option;
							$img = isset( $option_value['img_src'] ) && !empty( $option_value['img_src'] ) ? '<img src="' . esc_url( $option_value['img_src'] ) . '" alt="' . esc_html( $option_value['label'] ) . '"><span class="check"><i class="dashicons dashicons-yes"></i></span>' : '<span class="cherry-radio-item"><i></i></span>';
							$data_slave = isset( $option_value['slave'] ) && !empty( $option_value['slave'] ) ? ' data-slave="' . $option_value['slave'] . '"' : '';
							$class_box = isset( $option_value['img_src'] ) && !empty( $option_value['img_src'] ) ? ' cherry-radio-img' . $checked : ' cherry-radio-item' . $checked;

							$html .= '<div class="' . $class_box . '">';
							$html .= '<input type="radio" id="' . esc_attr( $radio_id ) . '" class="cherry-radio-input ' . sanitize_html_class( $this->settings['class'] ) . '" name="' . esc_attr( $this->settings['name'] ) . '" ' . checked( $option, $this->settings['value'], false ) . ' value="' . esc_attr( $option ) . '"' . $data_slave . '>';

								$label_content = $img . $option_value['label'];
							$html .= '<label for="' . esc_attr( $radio_id ) . '">' . $label_content . '</label> ';
							$html .= '</div>';
						}
						$html .= '<div class="clear"></div>';
					$html .= '</div>';
				}
			$html .= '</div>';

			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Radio
		 *
		 * @since  4.0.0
		 */
		public static function enqueue_assets(){
			wp_enqueue_script(
				'ui-radio-min',
				self::get_current_file_url( __FILE__ ) . '/assets/min/ui-radio.min.js',
				array( 'jquery' ),
				'1.0.0',
				true
			);

			wp_enqueue_style(
				'ui-radio-min',
				self::get_current_file_url( __FILE__ ) . '/assets/min/ui-radio.min.css',
				array(),
				'1.0.0',
				'all'
			);
		}

	}
}