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
			return Cherry_Toolkit::render_view(
				dirname( __FILE__ ) . '/views/number.php',
				array(
					'master'   => 'cherry-ui-container ' . Cherry_Toolkit::get_get( $this->settings, 'master', '' ),
					'label'    => Cherry_Toolkit::get_get( $this->settings, 'label', '' ),
					'atts'     => $this->get_input_attributes(),
					'atts_str' => Cherry_Toolkit::join( $this->get_input_attributes() ),
				)
			);
		}

		/**
		 * Get attributes to input control
		 *
		 * @return [array] attributes.
		 */
		public function get_input_attributes() {
			$settings = Cherry_Toolkit::leave_right_keys(
				array(
					'id',
					'class',
					'name',
					'value',
					'placeholder',
					'max',
					'min',
				),
				$this->settings
			);
			$settings['type']  = 'number';
			$settings['class'] = sprintf( 'widefat cherry-ui-text %s', Cherry_Toolkit::get_get( $settings, 'class' ) );
			return Cherry_Toolkit::remove_empty( $settings );
		}
	}
}
