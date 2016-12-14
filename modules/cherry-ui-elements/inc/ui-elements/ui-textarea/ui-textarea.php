<?php
/**
 * Class for the building ui-textarea elements
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

if ( ! class_exists( 'UI_Textarea' ) ) {

	/**
	 * Class for the building UI_Textarea elements.
	 */
	class UI_Textarea extends UI_Element implements I_UI {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $defaults_settings = array(
			'id'          => 'cherry-ui-textarea-id',
			'name'        => 'cherry-ui-textarea-name',
			'value'       => '',
			'placeholder' => '',
			'rows'        => '10',
			'cols'        => '20',
			'label'       => '',
			'class'       => '',
			'master'      => '',
		);

		/**
		 * Constructor method for the UI_Textarea class.
		 *
		 * @since 1.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-textarea-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Render html UI_Textarea.
		 *
		 * @since 1.0.0
		 */
		public function render() {
			$html = '';
			$class = $this->settings['class'];
			$class .= ' ' . $this->settings['master'];

			$html .= '<div class="cherry-ui-container ' . esc_attr( $class ) . '">';
				if ( '' !== $this->settings['label'] ) {
					$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . $this->settings['label'] . '</label> ';
				}
				$html .= '<textarea id="' . esc_attr( $this->settings['id'] ) . '" class="cherry-ui-textarea" name="' . esc_attr( $this->settings['name'] ) . '" rows="' . esc_attr( $this->settings['rows'] ) . '" cols="' . esc_attr( $this->settings['cols'] ) . '" placeholder="' . esc_attr( $this->settings['placeholder'] ) . '">' . esc_html( $this->settings['value'] ) . '</textarea>';
			$html .= '</div>';

			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Textarea
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_style(
				'ui-textarea',
				esc_url( Cherry_Core::base_url( 'assets/min/ui-textarea.min.css', __FILE__ ) ),
				array(),
				'1.3.2',
				'all'
			);
		}
	}
}
