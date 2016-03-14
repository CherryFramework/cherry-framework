<?php
/**
 * Class for the building ui-text elements.
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

if ( ! class_exists( 'UI_Text' ) ) {

	/**
	 * Class for the building ui-text elements.
	 */
	class UI_Text extends UI_Element implements I_UI {

		/**
		 * Default settings
		 *
		 * @var array
		 */
		private $defaults_settings = array(
			'type'			=> 'text',
			'id'			=> 'cherry-ui-input-id',
			'name'			=> 'cherry-ui-input-name',
			'value'			=> '',
			'placeholder'	=> '',
			'label'			=> '',
			'class'			=> '',
			'master'		=> '',
			'required'      => false,
		);

		/**
		 * Constructor method for the UI_Text class.
		 *
		 * @since  4.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-input-text-'.uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Get required attribute
		 *
		 * @return string required attribute
		 */
		public function get_required() {
			if ( $this->settings['required'] ) {
				return 'required="required"';
			}
			return '';
		}

		/**
		 * Render html UI_Text.
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
				$html .= '<input type="' . esc_attr( $this->settings['type'] ) . '" id="' . esc_attr( $this->settings['id'] ) . '" class="widefat cherry-ui-text ' . esc_attr( $this->settings['class'] ) . '"  name="' . esc_attr( $this->settings['name'] ) . '"  value="' . esc_html( $this->settings['value'] ) . '" placeholder="' . esc_attr( $this->settings['placeholder'] ) . '" '.$this->get_required().'>';
			$html .= '</div>';
			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Text
		 *
		 * @since  4.0.0
		 */
		public static function enqueue_assets() {

			wp_enqueue_style(
				'ui-text',
				self::get_current_file_url( __FILE__ ) . '/assets/min/ui-text.min.css',
				array(),
				'1.0.0',
				'all'
			);
		}
	}
}
