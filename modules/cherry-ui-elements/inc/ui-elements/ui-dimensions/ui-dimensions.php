<?php
/**
 * Class for the building ui-dimensions elements.
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

if ( ! class_exists( 'UI_Dimensions' ) ) {

	/**
	 * Class for the building ui-dimensions elements.
	 */
	class UI_Dimensions extends UI_Element implements I_UI {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $defaults_settings = array(
			'type'        => 'dimensions',
			'id'          => 'cherry-ui-dimensions-id',
			'name'        => 'cherry-ui-dimensions-name',
			'value'       => array(),
			'range'       => array(
				'px' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
			),
			'label'            => '',
			'dimension_labels' => array(
				'top'    => 'Top',
				'right'  => 'Right',
				'bottom' => 'Bottom',
				'left'   => 'Left',
			),
			'class'            => '',
			'master'           => '',
			'required'         => false,
			'lock'             => false,
		);

		protected $default_value = array(
			'units'     => 'px',
			'is_linked' => true,
			'top'       => '',
			'right'     => '',
			'bottom'    => '',
			'left'      => '',
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
		 * Constructor method for the UI_Text class.
		 *
		 * @since 1.0.0
		 */
		function __construct( $args = array() ) {

			$this->defaults_settings['id'] = 'cherry-ui-dimensions-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			$this->lock_element = new Cherry5_Lock_Element( $this->settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Get required attribute.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function get_required() {

			if ( $this->settings['required'] ) {
				return 'required="required"';
			}

			return '';
		}

		/**
		 * Render html UI_Text.
		 *
		 * @since 1.0.0
		 */
		public function render() {
			$html = '';
			$class = implode( ' ',
				array(
					$this->settings['class'],
					$this->settings['master'],
					$this->lock_element->get_class(),
				)
			);

			if ( empty( $this->settings['value'] ) ) {
				$this->settings['value'] = $this->default_value;
			} else {
				$this->settings['value'] = array_merge( $this->default_value, $this->settings['value'] );
			}

			$html .= '<div class="cherry-ui-container ' . esc_attr( $class ) . '">';

				if ( '' !== $this->settings['label'] ) {
					$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . esc_html( $this->settings['label'] ) . '</label> ';
				}

				$html .= $this->get_fields();
				$html .= $this->lock_element->get_html();
			$html .= '</div>';
			return $html;
		}

		/**
		 * Return UI fileds
		 * @return [type] [description]
		 */
		public function get_fields() {

			$hidden = '<input type="hidden" name="%1$s" id="%3$s" value="%2$s">';
			$number = '<div class="cherry-ui-dimensions__value-item"><input type="number" name="%1$s" id="%3$s" value="%2$s" min="%4$s" max="%5$s" step="%6$s" class="cherry-ui-dimensions__val%7$s"><span class="cherry-ui-dimensions__value-label">%8$s</span></div>';

			$value = $this->settings['value'];
			$value = array_merge( $this->default_value, $value );

			$result = sprintf(
				'<div class="cherry-ui-dimensions" data-range=\'%s\'>',
				json_encode( $this->settings['range'] )
			);

			foreach ( array( 'units', 'is_linked' ) as $field ) {
				$result .= sprintf(
					$hidden,
					$this->get_name_attr( $field ), $value[ $field ], $this->get_id_attr( $field )
				);
			}
			$result .= $this->get_units();
			$result .= '<div class="cherry-ui-dimensions__values">';

			$value['is_linked'] = filter_var( $value['is_linked'], FILTER_VALIDATE_BOOLEAN );

			foreach ( array( 'top', 'right', 'bottom', 'left' ) as $field ) {
				$result .= sprintf(
					$number,
					$this->get_name_attr( $field ),
					$value[ $field ],
					$this->get_id_attr( $field ),
					$this->settings['range'][ $value['units'] ]['min'],
					$this->settings['range'][ $value['units'] ]['max'],
					$this->settings['range'][ $value['units'] ]['step'],
					( true === $value['is_linked'] ? ' is-linked' : '' ),
					$this->settings['dimension_labels'][ $field ]
				);
			}
			$result .= sprintf(
				'<div class="cherry-ui-dimensions__is-linked%s"><span class="dashicons dashicons-admin-links link-icon"></span><span class="dashicons dashicons-editor-unlink unlink-icon"></span></div>',
				( true === $value['is_linked'] ? ' is-linked' : '' )
			);
			$result .= '</div>';
			$result .= '</div>';

			return $result;
		}

		/**
		 * Returns units selector
		 *
		 * @return string
		 */
		public function get_units() {

			$units    = array_keys( $this->settings['range'] );
			$switcher = 'can-switch';

			if ( 1 === count( $units ) ) {
				$switcher = '';
			}

			$item   = '<span class="cherry-ui-dimensions__unit%2$s" data-unit="%1$s">%1$s</span>';
			$result = '';

			foreach ( $units as $unit ) {
				$result .= sprintf(
					$item,
					$unit,
					( $this->settings['value']['units'] === $unit ? ' is-active' : '' )
				);
			}

			return sprintf( '<div class="cherry-ui-dimensions__units">%s</div>', $result );
		}

		/**
		 * Retrurn full name attibute by name
		 *
		 * @param  [type] $name [description]
		 * @return [type]       [description]
		 */
		public function get_name_attr( $name = '' ) {
			return sprintf( '%s[%s]', esc_attr( $this->settings['name'] ), esc_attr( $name ) );
		}

		/**
		 * Retrurn full ID attibute by name
		 *
		 * @param  [type] $name [description]
		 * @return [type]       [description]
		 */
		public function get_id_attr( $name = '' ) {
			return sprintf( '%s_%s', esc_attr( $this->settings['name'] ), esc_attr( $name ) );
		}

		/**
		 * Enqueue javascript and stylesheet UI_Text.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_style(
				'ui-dimensions',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-dimensions/assets/min/ui-dimensions.min.css', Cherry_UI_Elements::$module_path ) ),
				array(),
				Cherry_UI_Elements::$core_version,
				'all'
			);

			wp_enqueue_script(
				'ui-dimensions',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-dimensions/assets/ui-dimensions.js', Cherry_UI_Elements::$module_path ) ),
				array( 'jquery' ),
				Cherry_UI_Elements::$core_version,
				true
			);

		}
	}
}
