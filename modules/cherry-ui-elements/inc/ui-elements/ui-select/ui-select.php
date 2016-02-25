<?php
/**
 * Class for the building ui-select elements.
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

if ( ! class_exists( 'UI_Select' ) ) {
	class UI_Select {

		private $settings = array();
		private $defaults_settings = array(
			'id'			=> 'cherry-ui-select-id',
			'name'			=> 'cherry-ui-select-name',
			'multiple'		=> false,
			'filter'		=> false,
			'size'			=> 1,
			'value'			=> 'select-8',
			'options'		=> array(
				'select-1'	=> 'select 1',
				'select-2'	=> 'select 2',
				'select-3'	=> 'select 3',
				'select-4'	=> 'select 4',
				'select-5'	=> 'select 5',
				'optgroup-1'	=> array(
					'label' => 'Group 1',
					'group_options' => array(
						'select-6'	=> 'select 6',
						'select-7'	=> 'select 7',
						'select-8'	=> 'select 8',
					)
				),
				'optgroup-2'	=> array(
					'label' => 'Group 2',
					'group_options' => array(
						'select-9'	=> 'select 9',
						'select-10'	=> 'select 10',
						'select-11'	=> 'select 11',
					)
				)
			),
			'placeholder'	=> 'Select',
			'label'			=> '',
			'class'			=> '',
			'master'		=> '',
		);

		/**
		 * Constructor method for the UI_Select class.
		 *
		 * @since  4.0.0
		 */
		function __construct( $args = array() ) {

			$this->defaults_settings['id'] = 'cherry-ui-select-'.uniqid();

			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Render html UI_Select.
		 *
		 * @since  4.0.0
		 */
		public function render() {

			$html = '';

			$master_class = ! empty( $this->settings['master'] ) && isset( $this->settings['master'] ) ? esc_html( $this->settings['master'] ) : '';

			$html .= '<div class="cherry-ui-container ' . $master_class . '">';

				( $this->settings['filter'] ) ? $filter_state = 'data-filter="true"' : $filter_state = 'data-filter="false"' ;

				( $this->settings['multiple'] ) ? $multi_state = 'multiple="multiple"' : $multi_state = '' ;
				( $this->settings['multiple'] ) ? $name = $this->settings['name'] . '[]' : $name = $this->settings['name'] ;

				if( '' !== $this->settings['label'] ){
					$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . $this->settings['label'] . '</label> ';
				}

				$html .= '<select id="' . esc_attr( $this->settings['id'] ) . '" class="cherry-ui-select ' . esc_attr( $this->settings['class'] ) . '" name="' . esc_attr( $name ) . '" size="' . esc_attr( $this->settings['size'] ) . '" ' . $multi_state . ' ' . $filter_state . ' placeholder="' . $this->settings['placeholder'] . '" style="width: 100%">';
				if( $this->settings['options'] && !empty( $this->settings['options'] ) && is_array( $this->settings['options'] ) ){
					foreach ( $this->settings['options'] as $option => $option_value) {
						if ( !is_array( $this->settings['value'] ) ) {
							$this->settings['value'] = array( $this->settings['value'] );
						}
						if( false === strpos( $option, 'optgroup' ) ){
							$selected_state = '';
							if( $this->settings['value'] && !empty( $this->settings['value'] ) ){
								foreach ( $this->settings['value'] as $key => $value) {
									$selected_state = selected( $value, $option, false );
									if( $selected_state == " selected='selected'" ){
										break;
									}
								}
							}
							$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected_state . '>'. esc_html( $option_value ) .'</option>';
						}else{
							$html .= '<optgroup label="' . esc_attr( $option_value['label'] ) . '">';
								$selected_state = '';
								foreach ( $option_value['group_options'] as $group_item => $group_value) {
									foreach ( $this->settings['value'] as $key => $value) {
										$selected_state = selected( $value, $group_item, false );
										if( $selected_state == " selected='selected'" ){
											break;
										}
									}
									$html .= '<option value="' . esc_attr( $group_item ) . '" ' . $selected_state . '>'. esc_html( $group_value ) .'</option>';
								}
							$html .= '</optgroup>';
						}
					}
				}
				$html .= '</select>';
			$html .=  '</div>';

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
		 * Enqueue javascript and stylesheet UI_Select
		 *
		 * @since  4.0.0
		 */
		public static function enqueue_assets(){
			wp_enqueue_script(
				'ui-select-select2',
				self::get_current_file_url() . '/assets/select2.js',
				array( 'jquery' ),
				'0.2.9',
				true
			);
			wp_enqueue_script(
				'ui-select-min',
				self::get_current_file_url() . '/assets/min/ui-select.min.js',
				array( 'jquery' ),
				'1.0.0',
				true
			);
			wp_enqueue_style(
				'ui-select-select2',
				self::get_current_file_url() . '/assets/select2.css',
				array(),
				'0.2.9',
				'all'
			);
			wp_enqueue_style(
				'ui-select-min',
				self::get_current_file_url() . '/assets/min/ui-select.min.css',
				array(),
				'1.0.0',
				'all'
			);
		}
	}
}