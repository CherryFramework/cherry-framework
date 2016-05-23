<?php
/**
 * Control class for customize.
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Control for custumize
	 */
	class WP_Chooseicons extends WP_Customize_Control {

		/**
		 * Render control
		 */
		public function render_content() {
			$ui_chooseicons = new UI_Chooseicons(
				array(
					'value' => $this->value,
					'link'  => $this->get_link(),
				)
			);
			echo Cherry_Toolkit::render_view(
				dirname( dirname( __FILE__ ) ) . '/views/customize-row.php',
				array(
					'label' => $this->label,
					'value' => $this->value,
					'link'  => $this->get_link(),
					'html'  => $ui_chooseicons->render(
						array(
							new Icon_Set_Font_Awesome( UI_Chooseicons::get_path() . '/assets/css/font-awesome.css' ),
							new Icon_Set_Font_Material( UI_Chooseicons::get_path() . '/assets/material/material_icons_codepoints.list' ),
						)
					),
				)
			);
		}
	}
}
