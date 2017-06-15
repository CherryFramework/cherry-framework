<?php
/**
 * Class for the building ui-checkbox elements.
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

if ( ! class_exists( 'UI_Checkbox' ) ) {

	/**
	 * Class for the building UI_Checkbox elements.
	 */
	class UI_Checkbox extends UI_Element implements I_UI {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $defaults_settings = array(
			'id'    => 'cherry-ui-checkbox-id',
			'name'  => 'cherry-ui-checkbox-name',
			'value' => array(
				'checkbox-1' => 'true',
				'checkbox-2' => 'true',
				'checkbox-3' => 'true',
			),
			'options' => array(
				'checkbox-1' => 'checkbox 1',
				'checkbox-2' => 'checkbox 2',
				'checkbox-3' => 'checkbox 3',
			),
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
		 * Constructor method for the UI_Checkbox class.
		 *
		 * @since 1.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-checkbox-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );
			$this->lock_element = new Cherry5_Lock_Element( $this->settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Render html UI_Checkbox.
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

			$html .= '<div class="cherry-ui-container ' . esc_attr( $class ) . '">';

			$counter = 0;
				if ( $this->settings['options'] && ! empty( $this->settings['options'] ) && is_array( $this->settings['options'] ) ) {
					if ( ! is_array( $this->settings['value'] ) ) {
						$this->settings['value'] = array( $this->settings['value'] );
					}
					if ( '' !== $this->settings['label'] ) {
						$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . esc_html( $this->settings['label'] ) . '</label> ';
					}

					foreach ( $this->settings['options'] as $option => $option_value ) {
						$lock_option = new Cherry5_Lock_Element( $option_value );

						if ( ! empty( $this->settings['value'] ) ) {
							$option_checked = array_key_exists( $option, $this->settings['value'] ) ? $option : '';
							$item_value     = ! empty( $option_checked ) ? $this->settings['value'][ $option ] : 'false';
						} else {
							$option_checked = '';
							$item_value     = 'false';
						}

						$checked      = ( ! empty( $option_checked ) && 'true' === $item_value ) ? 'checked' : '';
						$option_label = isset( $option_value ) && is_array( $option_value ) ? $option_value['label'] : $option_value;
						$data_slave   = isset( $option_value['slave'] ) && ! empty( $option_value['slave'] ) ? ' data-slave="' . $option_value['slave'] . '"' : '';

						$html .= '<div class="cherry-checkbox-item-wrap">';
							$html .= '<span class="' . $lock_option->get_class( 'inline-block' ) . '"">';
									$html .= '<span class="cherry-lable-content">';
									$html .= '<input type="hidden" id="' . esc_attr( $this->settings['id'] ) . '-' . $counter . '" class="cherry-checkbox-input" name="' . esc_attr( $this->settings['name'] ) . '[' . $option . ']" ' . $checked . ' value="' . esc_html( $item_value ) . '"' . $data_slave . $lock_option->get_disabled_attr() . '>';
									$html .= '<div class="cherry-checkbox-item"><span class="marker dashicons dashicons-yes"></span></div>';
									$html .= '<label class="cherry-checkbox-label" for="' . esc_attr( $this->settings['id'] ) . '-' . $counter . '"><span class="cherry-lable-content">' . esc_html( $option_label ) . '</span></label> ';
									$html .= '</span>';
								$html .= $lock_option->get_html();
							$html .= '</span>';
						$html .= '</div>';

						$counter++;
					}
				}
			$html .= $this->lock_element->get_html() . '</div>';

			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Checkbox.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_script(
				'ui-checkbox',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-checkbox/assets/min/ui-checkbox.min.js', Cherry_UI_Elements::$module_path ) ),
				array( 'jquery' ),
				Cherry_UI_Elements::$core_version,
				true
			);

			wp_enqueue_style(
				'ui-checkbox',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-checkbox/assets/min/ui-checkbox.min.css', Cherry_UI_Elements::$module_path ) ),
				array(),
				Cherry_UI_Elements::$core_version,
				'all'
			);
		}
	}
}
