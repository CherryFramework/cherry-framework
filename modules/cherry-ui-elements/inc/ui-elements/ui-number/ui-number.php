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

if ( ! class_exists( 'UI_Number' ) ) {
	/**
	 * Class for the building ui-text elements.
	 */
	class UI_Number extends UI_Text implements I_UI {

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
				$html .= '<input type="number" id="' . esc_attr( $this->settings['id'] ) . '" class="widefat cherry-ui-text ' . esc_attr( $this->settings['class'] ) . '"  name="' . esc_attr( $this->settings['name'] ) . '"  value="' . esc_html( $this->settings['value'] ) . '" placeholder="' . esc_attr( $this->settings['placeholder'] ) . '">';
			$html .= '</div>';
			return $html;
		}
	}
}
