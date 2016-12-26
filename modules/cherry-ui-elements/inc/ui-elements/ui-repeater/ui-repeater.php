<?php
/**
 * Class for the building ui-repeater elements.
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
			'type'        => 'repeater',
			'id'          => 'cherry-ui-repeater-id',
			'name'        => 'cherry-ui-repeater-name',
			'value'       => array(),
			'fields'      => array(),
			'label'       => '',
			'add_label'   => 'Add Item',
			'class'       => '',
			'master'      => '',
			'ui_kit'      => true,
			'required'    => false,
			'title_field' => '',
		);

		/**
		 * Stored data to process it while renderinr row
		 *
		 * @var array
		 */
		public $data = array();

		/**
		 * Repeater instances counter
		 *
		 * @var integer
		 */
		public static $instance_id = 0;

		/**
		 * Current onstance TMPL name
		 *
		 * @var string
		 */
		public $tmpl_name = '';

		/**
		 * Holder for templates to print it in bottom of customizer page
		 *
		 * @var string
		 */
		public static $customizer_tmpl_to_print = null;

		/**
		 * Is tmpl scripts already printed in customizer
		 *
		 * @var boolean
		 */
		public static $customizer_tmpl_printed = false;

		/**
		 * Constructor method for the UI_Repeater class.
		 *
		 * @since 1.0.0
		 */
		function __construct( $args = array() ) {

			$this->defaults_settings['id'] = 'cherry-ui-input-text-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			$this->set_tmpl_data();

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
			add_action( 'admin_footer', array( $this, 'print_js_template' ), 0 );

			add_action( 'customize_controls_print_footer_scripts', array( $this, 'fix_customizer_tmpl' ), 9999 );

		}

		/**
		 * Get required attribute.
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
		 * @since 1.0.1
		 */
		public function render() {
			$html        = '';
			$class       = $this->settings['class'] . ' ' . $this->settings['master'];
			$ui_kit      = ! empty( $this->settings['ui_kit'] ) ? 'cherry-ui-kit' : '';
			$value       = ! empty( $this->settings['value'] ) ? count( $this->settings['value'] ) : 0 ;
			$title_field = ! empty( $this->settings['title_field'] ) ? 'data-title-field="' . $this->settings['title_field'] . '"' : '' ;

			$html .= sprintf( '<div class="cherry-ui-repeater-container cherry-ui-container %1$s %2$s">',
					$ui_kit,
					esc_attr( $class )
				);
				if ( '' !== $this->settings['label'] ) {
					$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . esc_html( $this->settings['label'] ) . '</label> ';
				}

				$html .= sprintf(
					'<div class="cherry-ui-repeater-list" data-name="%1$s" data-index="%2$s" data-widget-id="__i__" %3$s id="%4$s">',
					$this->get_tmpl_name(),
					$value,
					$title_field,
					esc_attr( $this->settings['id'] )
				);

				if ( is_array( $this->settings['value'] ) ) {
					$index = 0;
					foreach ( $this->settings['value'] as $data ) {
						$html .= $this->render_row( $index, false, $data );
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
		 * @param string $index        Current row index.
		 * @param number $widget_index It contains widget index.
		 * @param array  $data         Values to paste.
		 * @since 1.0.1
		 */
		public function render_row( $index, $widget_index, $data ) {
			$this->data = $data;

			$html = '<div class="cherry-ui-repeater-item" >';
			$html .= '<div class="cherry-ui-repeater-actions-box">';

			$html .= '<a href="#" class="cherry-ui-repeater-remove"></a>';
			$html .= '<span class="cherry-ui-repeater-title">' . $this->get_row_title() . '</span>';
			$html .= '<a href="#" class="cherry-ui-repeater-toggle"></a>';

			$html .= '</div>';
			$html .= '<div class="cheryr-ui-repeater-content-box">';
			foreach ( $this->settings['fields'] as $field ) {
				$html .= '<div class="' . $field['id'] . '-wrap">';
				$html .= $this->render_field( $index, $widget_index, $field );
				$html .= '</div>';
			}
			$html .= '</div>';
			$html .= '</div>';

			$this->data = array();

			return $html;
		}

		/**
		 * Get repeater item title
		 *
		 * @return string
		 * @since 1.0.1
		 */
		public function get_row_title() {

			if ( empty( $this->settings['title_field'] ) ) {
				return '';
			}

			if ( ! empty( $this->data[ $this->settings['title_field'] ] ) ) {
				return  $this->data[ $this->settings['title_field'] ];
			}

			return '';
		}

		/**
		 * Render single repeater field
		 *
		 * @param  string $index        Current row index.
		 * @param  number $widget_index It contains widget index.
		 * @param  array  $field        Values to paste.
		 * @return string
		 */
		public function render_field( $index, $widget_index, $field ) {

			if ( empty( $field['type'] ) || empty( $field['name'] ) ) {
				return '"type" and "name" are required fields for UI_Repeater items';
			}

			$field          = wp_parse_args( $field, array( 'value' => '' ) );
			$parent_name    = str_replace( '__i__', $widget_index, $this->settings['name'] );

			$field['id']    = sprintf( '%s-%s', $field['id'], $index );
			$field['value'] = isset( $this->data[ $field['name'] ] ) ? $this->data[ $field['name'] ] : $field['value'];
			$field['name']  = sprintf( '%1$s[item-%2$s][%3$s]', $parent_name, $index, $field['name'] );

			$ui_class_name  = 'UI_' . ucwords( $field['type'] );

			if ( ! class_exists( $ui_class_name ) ) {
				return '<p>Class <b>' . $ui_class_name . '</b> not exist!</p>';
			}

			$ui_item = new $ui_class_name( $field );

			return $ui_item->render();
		}

		/**
		 * Enqueue javascript and stylesheet UI_Repeater.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_style(
				'ui-repeater',
				esc_url( Cherry_Core::base_url( 'assets/min/ui-repeater.min.css', __FILE__ ) ),
				array(),
				'1.3.2',
				'all'
			);

			wp_enqueue_script(
				'ui-repeater',
				esc_url( Cherry_Core::base_url( 'assets/min/ui-repeater.min.js', __FILE__ ) ),
				array( 'wp-util', 'jquery-ui-sortable' ),
				'1.3.2',
				true
			);

		}

		/**
		 * Get TMPL name for current repeater instance.
		 *
		 * @return string
		 */
		public function get_tmpl_name() {
			return $this->tmpl_name;
		}

		/**
		 * Set current repeater instance ID
		 *
		 * @return void
		 */
		public function set_tmpl_data() {
			self::$instance_id++;
			$this->tmpl_name = sprintf( 'repeater-template-%s', self::$instance_id );

			global $wp_customize;
			if ( isset( $wp_customize ) ) {
				self::$customizer_tmpl_to_print .= $this->get_js_template();
			}

		}

		/**
		 * Print JS template for current repeater instance
		 *
		 * @return void
		 */
		public function print_js_template() {
			echo $this->get_js_template();
		}

		/**
		 * Get JS template to print
		 *
		 * @return string
		 */
		public function get_js_template() {

			return sprintf(
				'<script type="text/html" id="tmpl-%1$s">%2$s</script>',
				$this->get_tmpl_name(),
				$this->render_row( '{{{data.index}}}', '{{{data.widgetId}}}', array() )
			);

		}

		/**
		 * Outputs JS templates on customizer page
		 *
		 * @return void
		 */
		public function fix_customizer_tmpl() {
			if ( true === self::$customizer_tmpl_printed ) {
				return;
			}
			self::$customizer_tmpl_printed = true;
			echo self::$customizer_tmpl_to_print;
		}
	}
}
