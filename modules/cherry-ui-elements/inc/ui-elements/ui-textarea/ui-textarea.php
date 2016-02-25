<?php
/**
 * Class for the building ui-textarea elements.
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

if ( ! class_exists( 'UI_Textarea' ) ) {
	class UI_Textarea {

		private $settings = array();
		private $defaults_settings = array(
			'id'			=> 'cherry-ui-textarea-id',
			'name'			=> 'cherry-ui-textarea-name',
			'value'			=> '',
			'placeholder'	=> '',
			'rows'			=> '10',
			'cols'			=> '20',
			'label'			=> '',
			'class'			=> '',
			'master'		=> '',
		);

		/**
		 * Constructor method for the UI_Textarea class.
		 *
		 * @since  4.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-textarea-'.uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Render html UI_Textarea.
		 *
		 * @since  4.0.0
		 */
		public function render() {
			$html = '';

			$master_class = ! empty( $this->settings['master'] ) && isset( $this->settings['master'] ) ? esc_html( $this->settings['master'] ) : '';

			$html .= '<div class="cherry-ui-container ' . $master_class . '">';
				if( '' !== $this->settings['label'] ){
					$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . $this->settings['label'] . '</label> ';
				}
				$html .= '<textarea id="' . esc_attr( $this->settings['id'] ) . '" class="cherry-ui-textarea ' . esc_attr( $this->settings['class'] ) . '" name="' . esc_attr( $this->settings['name'] ) . '" rows="' . esc_attr( $this->settings['rows'] ) . '" cols="' . esc_attr( $this->settings['cols'] ) . '" placeholder="' . esc_attr( $this->settings['placeholder'] ) . '">' . esc_html( $this->settings['value'] ) . '</textarea>';
			$html .= '</div>';

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
		 * Enqueue javascript and stylesheet UI_Textarea
		 *
		 * @since  4.0.0
		 */
		public static function enqueue_assets(){
			wp_enqueue_style(
				'ui-textarea',
				self::get_current_file_url() . '/assets/min/ui-textarea.min.css',
				array(),
				'1.0.0',
				'all'
			);
		}

	}
}