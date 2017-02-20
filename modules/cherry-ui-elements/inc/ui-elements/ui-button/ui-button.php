<?php
/**
 * Class for the building ui-button elements.
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

if ( ! class_exists( 'UI_Button' ) ) {

	/**
	 * Class for the building ui-button elements.
	 */
	class UI_Button extends UI_Element implements I_UI {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $defaults_settings = array(
			'id'          => 'cherry-ui-button-id',
			'name'        => 'cherry-ui-button-name',
			'value'       => 'button',
			'disabled'    => false,
			'form'        => '',
			'formaction'  => '',
			'button_type' => 'button',
			'style'       => 'normal',
			'content'     => 'Button',
			'class'       => '',
			'master'      => '',
		);

		/**
		 * Constructor method for the UI_Button class.
		 *
		 * @since 1.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-button-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Render html UI_Button.
		 *
		 * @since 1.0.0
		 */
		public function render() {
			$html = Cherry_Toolkit::render_view(
				dirname( __FILE__ ) . '/view/button-view.php',
				$this->settings
			);

			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Button.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_style(
				'ui-button',
				esc_url( Cherry_Core::base_url( 'assets/min/ui-button.min.css', __FILE__ ) ),
				array(),
				'1.3.2',
				'all'
			);
		}
	}
}
