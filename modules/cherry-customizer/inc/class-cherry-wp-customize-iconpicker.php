<?php
/**
 * Iconpicker customizer control
 *
 * @package    Cherry_Framework
 * @subpackage Modules/Customizer
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Iconpicker control for customizer
	 */
	class Cherry_WP_Customize_Iconpicker extends WP_Customize_Control {

		/**
		 * Cherry Core instance
		 *
		 * @var array
		 */
		public $icon_data = array();

		/**
		 * UI instance
		 *
		 * @var object
		 */
		private $iconpicker = null;

		/**
		 * Render the control's content.
		 */
		public function render_content() {
			?>
			<label>
				<span class="customize-control-title">
					<?php echo esc_html( $this->label ); ?>
				</span>
				<?php if ( isset( $this->description ) ) : ?>
				<span class="description customize-control-description">
					<?php echo wp_kses_post( $this->description ); ?>
				</span>
				<?php endif; ?>
			</label>
			<?php
			echo str_replace(
				'id="' . $this->id . '"',
				'id="' . $this->id . '" ' . $this->get_link(),
				$this->iconpicker->render()
			);
		}

		/**
		 * Enqueue assets
		 */
		public function enqueue() {

			$core       = apply_filters( 'cherry_customizer_get_core', false );
			$ui_builder = $core->init_module(
				'cherry-ui-elements',
				array( 'ui_elements' => array( 'iconpicker' ) )
			);

			$args = array(
				'type'      => 'iconpicker',
				'id'        => $this->id,
				'name'      => $this->id,
				'value'     => $this->value(),
				'icon_data' => $this->icon_data,
			);

			add_action( 'customize_controls_print_styles', array( $this, 'print_sets' ) );

			$this->iconpicker = $ui_builder->get_ui_element_instance( 'iconpicker', $args );
			$this->iconpicker->enqueue_assets();
		}

		/**
		 * Print JS var with sets data
		 *
		 * @return void
		 */
		public function print_sets() {
			$this->iconpicker->prepare_icon_set();
			UI_Iconpicker::$printed = false;
			$this->iconpicker->print_icon_set();
		}
	}

}
