<?php
/**
 * Class for the building ui-repeater elements.
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

if ( ! class_exists( 'UI_Repeater' ) ) {

	/**
	 * Class for the building ui-repeater elements.
	 */
	class UI_Repeater extends UI_Element implements I_UI {

		/**
		 * Default settings
		 *
		 * @var array
		 */
		private $defaults_settings = array(
			'type'      => 'repeater',
			'id'        => 'cherry-ui-repeater-id',
			'name'      => 'cherry-ui-repeater-name',
			'value'     => array(),
			'fields'    => array(),
			'label'     => '',
			'add_label' => 'Add Item',
			'class'     => '',
			'master'    => '',
			'ui_kit'    => true,
			'required'  => false,
		);

		/**
		 * Stored data to process it while renderinr row
		 *
		 * @var array
		 */
		public $data = array();

		/**
		 * Constructor method for the UI_Text class.
		 *
		 * @since  1.0.0
		 */
		function __construct( $args = array() ) {

			$this->defaults_settings['id'] = 'cherry-ui-input-text-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
			add_action( 'admin_footer', array( $this, 'print_js_template' ), 0 );
		}

		/**
		 * Get required attribute
		 *
		 * @return string required attribute
		 */
		public function get_required() {
			if ( $this->settings['required'] ) {
				return 'required="required"';
			}
			return '';
		}

		/**
		 * Render html UI_Repeater.
		 *
		 * @since  1.0.0
		 */
		public function render() {
			$html = '';

			$master_class = ! empty( $this->settings['master'] ) && isset( $this->settings['master'] ) ? esc_html( $this->settings['master'] ) : '';

			$ui_kit = ! empty( $this->settings['ui_kit'] ) ? 'cherry-ui-kit ' : '';

			$html .= '<div class="cherry-ui-repeater-container cherry-ui-container ' . $ui_kit . $master_class . '">';
				if ( '' !== $this->settings['label'] ) {
					$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . esc_html( $this->settings['label'] ) . '</label> ';
				}

				$html .= sprintf(
					'<div class="cherry-ui-repeater-list" data-name="%1$s" data-index="%2$s">',
					esc_attr( $this->settings['name'] ),
					( ! empty( $this->settings['value'] ) ) ? count( $this->settings['value'] ) : 0
				);

				if ( is_array( $this->settings['value'] ) ) {
					$index = 0;
					foreach ( $this->settings['value'] as $data ) {
						$html .= $this->render_row( $index, $data );
						$index++;
					}
				}
				$html .= '</div>';
				$html .= sprintf(
					'<a href="#" class="cherry-ui-repeater-add">%1$s</a>',
					esc_html( $this->settings['add_label'] )
				);
			$html .= '</div>';
			return $html;
		}

		/**
		 * Render single row for repeater
		 *
		 * @param string $index Current row index.
		 * @param array  $data  Values to paste.
		 * @since 1.0.0
		 */
		public function render_row( $index, $data ) {

			$this->data = $data;

			$html = '<div class="cherry-ui-repeater-item">';
			$html .= '<div class="cherry-ui-repeater-remove-box"><a hre="#" class="cherry-ui-repeater-remove"></a></div>';
			foreach ( $this->settings['fields'] as $field ) {
				$html .= $this->render_field( $index, $field );
			}
			$html .= '</div>';

			$this->data = array();

			return $html;
		}

		/**
		 * Render single repeater field
		 *
		 * @param  string $index Current row index.
		 * @param  array  $field Values to paste.
		 * @return string
		 */
		public function render_field( $index, $field ) {

			if ( empty( $field['type'] ) || empty( $field['name'] ) ) {
				return '"type" and "name" are required fields for UI_Repeater items';
			}

			$field = wp_parse_args( $field, array( 'value' => '' ) );

			$field['id']    = sprintf( '%s-%s', $field['id'], $index );
			$field['value'] = isset( $this->data[ $field['name'] ] ) ? $this->data[ $field['name'] ] : $field['value'];
			$field['name']  = sprintf( '%1$s[item-%2$s][%3$s]', $this->settings['name'], $index, $field['name'] );

			$ui_class_name = 'UI_' . ucwords( $field['type'] );

			if ( ! class_exists( $ui_class_name ) ) {
				return '<p>Class <b>' . $ui_class_name . '</b> not exist!</p>';
			}

			$ui_item = new $ui_class_name( $field );

			return $ui_item->render();
		}

		/**
		 * Enqueue javascript and stylesheet UI_Text
		 *
		 * @since  1.0.0
		 */
		public static function enqueue_assets() {

			wp_enqueue_style(
				'ui-repeater',
				self::get_current_file_url( __FILE__ ) . '/assets/min/ui-repeater.min.css',
				array(),
				'1.0.0',
				'all'
			);

			wp_enqueue_script(
				'ui-repeater',
				self::get_current_file_url( __FILE__ ) . '/assets/min/ui-repeater.min.js',
				array( 'wp-util', 'jquery-ui-sortable' ),
				'1.0.0',
				true
			);

		}

		/**
		 * Print JS template for current repeater instance
		 *
		 * @return void
		 */
		public function print_js_template() {

			printf(
				'<script type="text/html" id="tmpl-%1$s">%2$s</script>',
				esc_attr( $this->settings['name'] ),
				$this->render_row( '{{{data.index}}}', array() )
			);

		}
	}
}
