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
		 * Constructor method for the UI_Radio class.
		 *
		 * @since 1.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-radio-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

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
				)
			);

			$html .= '<div class="cherry-ui-container ' . esc_attr( $class ) . '" >';
				if ( $this->settings['options'] && ! empty( $this->settings['options'] ) && is_array( $this->settings['options'] ) ) {
					if ( '' !== $this->settings['label'] ) {
						$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . $this->settings['label'] . '</label> ';
					}
					$html .= '<div class="cherry-radio-group">';
						foreach ( $this->settings['options'] as $option => $option_value ) {
							$lock_html = '';
							$input_lock = '' ;
							if ( ! empty( $option_value['lock'] ) ){
								$input_lock = 'disabled';
								$default_attr = array(
									'label' => esc_html__( 'Unlocked in PRO', 'cherry-framework' ),
									'url'   => esc_url('#'),
									'icon'  => '<i class="fa fa-unlock-alt" aria-hidden="true"></i>',
								);
								$lock_attr = is_array( $option_value['lock'] ) ? wp_parse_args( $option_value['lock'], $default_attr ) : $default_attr ;

								$lock_html = sprintf( '<a class="cherry-lock__area" target="_blanl" href="%1$s" alt="%3$s"><span class="cherry-lock__label">%2$s %3$s</span></a>', esc_url( $lock_attr['url'] ), $lock_attr['icon'], esc_attr( $lock_attr['label'] ) );
							}

							$checked    = $option == $this->settings['value'] ? ' checked' : '';
							$radio_id   = $this->settings['id'] . '-' . $option;
							$img        = isset( $option_value['img_src'] ) && ! empty( $option_value['img_src'] ) ? '<img src="' . esc_url( $option_value['img_src'] ) . '" alt="' . esc_html( $option_value['label'] ) . '">' : '<span class="cherry-radio-item"><i></i></span>';
							$data_slave = isset( $option_value['slave'] ) && ! empty( $option_value['slave'] ) ? ' data-slave="' . $option_value['slave'] . '"' : '';
							$class_box  = isset( $option_value['img_src'] ) && ! empty( $option_value['img_src'] ) ? ' cherry-radio-img' : ' cherry-radio-item' ;
							$class_box  .= ( ! empty( $input_lock ) ) ? ' cherry-lock' : '' ;

							$html .= '<div class="' . $class_box . '">';
							$html .= '<input type="radio" id="' . esc_attr( $radio_id ) . '" class="cherry-radio-input" name="' . esc_attr( $this->settings['name'] ) . '" ' . checked( $option, $this->settings['value'], false ) . ' value="' . esc_attr( $option ) . '"' . $data_slave . ' ' . $input_lock . '/>';
							$label_content = $img . $option_value['label'];
							$html .= '<label for="' . esc_attr( $radio_id ) . '"><span class="cherry-lable-content">' . $label_content . '</span>' . $lock_html . '</label> ';
							$html .= '</div>';
						}
						$html .= '<div class="clear"></div>';
					$html .= '</div>';
				}
			$html .= '</div>';

			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Radio.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_style(
				'ui-radio-min',
				esc_url( Cherry_Core::base_url( 'assets/min/ui-radio.min.css', __FILE__ ) ),
				array(),
				'1.3.2',
				'all'
			);

			wp_enqueue_script(
				'ui-radio-min',
				esc_url( Cherry_Core::base_url( 'assets/min/ui-radio.min.js', __FILE__ ) ),
				array( 'jquery' ),
				'1.3.2',
				true
			);
		}
	}
}
