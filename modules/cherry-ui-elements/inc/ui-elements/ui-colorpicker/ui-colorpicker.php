<?php
/**
 * Class for the building ui-colorpicker elements.
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

if ( ! class_exists( 'UI_Colorpicker' ) ) {

	/**
	 * Class for the building UI_Colorpicker elements.
	 */
	class UI_Colorpicker extends UI_Element implements I_UI {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $defaults_settings = array(
			'id'     => 'cherry-ui-colorpicker-id',
			'name'   => 'cherry-ui-colorpicker-name',
			'value'  => '',
			'label'  => '',
			'class'  => '',
			'master' => '',
			'alpha'  => false,
			'lock'   => false,
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
		 * Constructor method for the UI_Colorpicker class.
		 *
		 * @since 1.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-colorpicker-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );
			$this->lock_element = new Cherry5_Lock_Element( $this->settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Render html UI_Colorpicker.
		 *
		 * @since 1.0.0
		 */
		public function render() {
			$html = '';
			$class = implode( ' ',
				array(
					$this->settings['class'],
					$this->settings['master'],
				)
			);

			$alpha = '';

			if ( true === $this->settings['alpha'] ) {
				$alpha = ' data-alpha=true ';
			}

			$html .= '<div class="cherry-ui-container ' . esc_attr( $class ) . '">';
				if ( '' !== $this->settings['label'] ) {
					$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . esc_html( $this->settings['label'] ) . '</label> ';
				}
				$html .= '<div class="cherry-ui-colorpicker-wrapper' . $this->lock_element->get_class() .'">';
					$html .= '<input type="text" id="' . esc_attr( $this->settings['id'] ) . '" class="cherry-ui-colorpicker" name="' . esc_attr( $this->settings['name'] ) . '"' . $alpha . 'value="' . esc_html( $this->settings['value'] ) . '"' . $this->lock_element->get_disabled_attr() . '/>';
				$html .= $this->lock_element->get_html();
				$html .= '</div>';
			$html .= '</div>';

			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Colorpicker.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_assets() {

			wp_enqueue_script(
				'wp-color-picker-alpha',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-colorpicker/assets/min/wp-color-picker-alpha.min.js', Cherry_UI_Elements::$module_path ) ),
				array( 'wp-color-picker' ),
				'1.2.2',
				true
			);

			wp_enqueue_script(
				'ui-colorpicker',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-colorpicker/assets/min/ui-colorpicker.min.js', Cherry_UI_Elements::$module_path ) ),
				array( 'jquery' ),
				Cherry_UI_Elements::$core_version,
				true
			);

			wp_enqueue_style(
				'ui-colorpicker',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-colorpicker/assets/min/ui-colorpicker.min.css', Cherry_UI_Elements::$module_path ) ),
				array( 'wp-color-picker' ),
				Cherry_UI_Elements::$core_version,
				'all'
			);
		}
	}
}
