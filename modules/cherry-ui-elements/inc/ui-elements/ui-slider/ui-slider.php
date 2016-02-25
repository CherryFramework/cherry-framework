<?php
/**
 * Class for the building ui slider elements .
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

if ( ! class_exists( 'UI_Slider' ) ) {
	class UI_Slider {

		private $settings = array();
		private $defaults_settings = array(
			'id'			=> 'cherry-ui-slider-id',
			'name'			=> 'cherry-ui-slider-name',
			'max_value'		=> 100,
			'min_value'		=> 0,
			'value'			=> 50,
			'step_value'	=> 1,
			'label'			=> '',
			'class'			=> '',
			'master'		=> '',
		);

		/**
		 * Constructor method for the UI_Slider class.
		 *
		 * @since  4.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-slider-'.uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Render html UI_Slider.
		 *
		 * @since  4.0.0
		 */
		public function render() {
			$html = '';

			$master_class = ! empty( $this->settings['master'] ) && isset( $this->settings['master'] ) ? esc_html( $this->settings['master'] ) : '';

			$html .= '<div class="cherry-ui-container ' . $master_class . '">';

			$ui_stepper = new UI_Stepper(
				array(
					'id' => $this->settings['id'] . '-stepper',
					'name' => $this->settings['name'],
					'max_value' => $this->settings['max_value'],
					'min_value' => $this->settings['min_value'],
					'value' => $this->settings['value'],
					'step_value' => $this->settings['step_value'],
				)
			);
			$ui_stepper_html = $ui_stepper->render();

				if( '' !== $this->settings['label'] ){
					$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . esc_html( $this->settings['label'] ) . '</label> ';
				}
				$html .= '<div class="cherry-slider-wrap">';
					$html .= '<div class="cherry-slider-input">';
						$html .= $ui_stepper_html;
					$html .= '</div>';
					$html .= '<div class="cherry-slider-holder">';
						$html .= '<div class="cherry-slider-unit" data-left-limit="' . esc_attr( $this->settings['min_value'] ) . '" data-right-limit="' . esc_attr( $this->settings['max_value'] ) . '" data-value="' . esc_attr( $this->settings['value'] ) . '"></div>';
					$html .= '</div>';
					$html .= '<div class="clear"></div>';
				$html .=  '</div>';
			$html .=  '</div>';

			return $html;
		}

		/**
		 * Get current file URL
		 *
		 * @since  4.0.0
		 */
		public static function get_current_file_url() {
			$assets_url = dirname( __FILE__ );
			$site_url = site_url();
			$assets_url = str_replace( untrailingslashit( ABSPATH ), $site_url, $assets_url );
			$assets_url = str_replace( '\\', '/', $assets_url );

			return $assets_url;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Slider.
		 *
		 * @since  4.0.0
		 */
		public static function enqueue_assets(){

			wp_enqueue_script(
				'ui-slider-min',
				self::get_current_file_url() . '/assets/min/ui-slider.min.js',
				array( 'jquery', 'jquery-ui-slider' ),
				'1.0.0',
				true
			);

			wp_enqueue_style(
				'jquery-ui',
				self::get_current_file_url() . '/assets/jquery-ui.css',
				array(),
				'1.0.0',
				'all'
			);

			wp_enqueue_style(
				'ui-slider-min',
				self::get_current_file_url() . '/assets/min/ui-slider.min.css',
				array(),
				'1.0.0',
				'all'
			);

		}
	}
}