<?php
/**
 * Class for the building ui-iconpicker elements.
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

if ( ! class_exists( 'UI_Iconpicker' ) ) {

	/**
	 * Class for the building ui-iconpicker elements.
	 */
	class UI_Iconpicker extends UI_Element implements I_UI {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $defaults_settings = array(
			'type'        => 'iconpicker',
			'id'          => 'cherry-ui-input-id',
			'name'        => 'cherry-ui-input-name',
			'value'       => '',
			'placeholder' => '',
			'icon_data'   => array(),
			'auto_parse'  => false,
			'label'       => '',
			'class'       => '',
			'master'      => '',
			'width'       => 'fixed', // full, fixed
			'required'    => false,
			'lock'        => false,
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
		 * Default icon data settings.
		 *
		 * @var array
		 */
		private $default_icon_data = array(
			'icon_set'    => '',
			'icon_css'    => '',
			'icon_base'   => 'icon',
			'icon_prefix' => '',
			'icons'       => '',
		);

		/**
		 * Icons sets
		 *
		 * @var array
		 */
		public static $sets = array();

		/**
		 * Check if sets already printed
		 *
		 * @var boolean
		 */
		public static $printed = false;

		/**
		 * Array of already printed sets to check it before printing current
		 *
		 * @var array
		 */
		public static $printed_sets = array();

		public $temp_icons = null;

		/**
		 * Constructor method for the UI_Iconpicker class.
		 *
		 * @since 1.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-input-icon-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );
			$this->lock_element = new Cherry5_Lock_Element( $this->settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
			add_action( 'admin_footer', array( $this, 'print_icon_set' ), 1 );
			add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_icon_set' ), 9999 );
			add_filter( 'cherry_handler_response_data', array( $this, 'send_icon_set' ), 10, 1 );
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
		 * Render html UI_Iconpicker.
		 *
		 * @since 1.0.0
		 */
		public function render() {
			$html       = '';
			$lock_lable = ! empty( $this->settings['lock']['label'] )? sprintf('<div class="cherry-lock-label">%1$s</div>', $this->settings['lock']['label'] ) : '' ;
			$class      = implode( ' ',
				array(
					$this->settings['class'],
					$this->settings['master'],
					$this->settings['width'],
					$this->lock_element->get_class( 'inline-block' ),
				)
			);

			$html .= '<div class="cherry-ui-container ' . esc_attr( $class ) . '">';
				if ( '' !== $this->settings['label'] ) {
					$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . esc_html( $this->settings['label'] ) . '</label> ';
				}

				$this->settings['icon_data'] = wp_parse_args(
					$this->settings['icon_data'],
					$this->default_icon_data
				);

				$html .= '<div class="cherry-ui-iconpicker-group">';

				if ( $this->validate_icon_data( $this->settings['icon_data'] ) ) {
					$html .= $this->render_picker();
				} else {
					$html .= 'Incorrect Icon Data Settings';
				}

				$html .= '</div>';
				$html .= $this->lock_element->get_html();
			$html .= '</div>';

			/**
			 * Maybe add js repeater template to response
			 *
			 * @var bool
			 */
			$add_js_to_response = apply_filters( 'cherry_ui_add_data_to_element', false );

			if ( $add_js_to_response ) {

				ob_start();
				$this->print_icon_set();
				$icons = ob_get_clean();

				$in_repeater = apply_filters( 'cherry_ui_is_repeater', false );

				if ( $in_repeater ) {
					$this->temp_icons = $icons;
					add_filter( 'cherry_ui_add_repater_data', array( $this, 'store_icons' ) );
				} else {
					$html .= $icons;
				}

			}

			return $html;
		}

		public function store_icons( $data = array() ) {

			if ( ! is_array( $data ) ) {
				$data = array();
			}

			$data[] = $this->temp_icons;

			return $data;
		}

		/**
		 * Returns iconpicker html markup
		 *
		 * @return string
		 */
		private function render_picker() {

			$format = '<span class="input-group-addon"></span><input type="text" name="%1$s" id="%2$s" value="%3$s" class="widefat cherry-ui-text cherry-ui-iconpicker %4$s" data-set="%5$s">';

			$this->prepare_icon_set();

			return sprintf(
				$format,
				$this->settings['name'],
				$this->settings['id'],
				$this->settings['value'],
				$this->settings['class'],
				$this->settings['icon_data']['icon_set']
			);

		}

		/**
		 * Return JS markup for icon set variable.
		 *
		 * @return void
		 */
		public function prepare_icon_set() {

			if ( empty( $this->settings['icon_data']['icons'] ) ) {
				$this->maybe_parse_set_from_css();
			}

			if ( ! array_key_exists( $this->settings['icon_data']['icon_set'], self::$sets ) ) {
				self::$sets[ $this->settings['icon_data']['icon_set'] ] = array(
					'iconCSS'    => $this->settings['icon_data']['icon_css'],
					'iconBase'   => $this->settings['icon_data']['icon_base'],
					'iconPrefix' => $this->settings['icon_data']['icon_prefix'],
					'icons'      => $this->settings['icon_data']['icons'],
				);
			}
		}

		/**
		 * Check if 'parse_set' is true and try to get icons set from CSS file
		 *
		 * @return void
		 */
		private function maybe_parse_set_from_css() {

			if ( true !== $this->settings['auto_parse'] || empty( $this->settings['icon_data']['icon_css'] ) ) {
				return;
			}

			ob_start();

			$path = str_replace( content_url(), WP_CONTENT_DIR, $this->settings['icon_data']['icon_css'] );
			if ( file_exists( $path ) ) {
				include $path;
			}

			$result = ob_get_clean();

			preg_match_all( '/\.([-_a-zA-Z0-9]+):before[, {]/', $result, $matches );

			if ( ! is_array( $matches ) || empty( $matches[1] ) ) {
				return;
			}

			if ( is_array( $this->settings['icon_data']['icons'] ) ) {
				$this->settings['icon_data']['icons'] = array_merge(
					$this->settings['icon_data']['icons'],
					$matches[1]
				);
			} else {
				$this->settings['icon_data']['icons'] = $matches[1];
			}

		}

		/**
		 * Checks if all required icon data fields are passed
		 *
		 * @param  array $data Icon data.
		 * @return bool
		 */
		private function validate_icon_data( $data ) {

			$validate = array_diff( $this->default_icon_data, array( 'icon_base', 'icon_prefix' ) );

			foreach ( $validate as $key => $field ) {

				if ( empty( $data[ $key ] ) ) {
					return false;
				}

				return true;
			}

		}

		/**
		 * Function sends the icons into ajax response.
		 *
		 * @param  array $data Icon data.
		 * @return array
		 */
		public function send_icon_set( $data ) {

			if ( empty( $data['cherryIconsSets'] ) ) {
				$data['cherry5IconSets'] = array();
			}

			foreach ( self::$sets as $key => $value ) {
				$data['cherry5IconSets'][ $key ] = $value;
			}

			return $data;
		}

		/**
		 * Print icon sets
		 *
		 * @return void
		 */
		public function print_icon_set() {

			if ( empty( self::$sets ) || true === self::$printed ) {
				return;
			}

			self::$printed = true;

			foreach ( self::$sets as $set => $data ) {

				if ( in_array( $set, self::$printed_sets ) ) {
					continue;
				}

				self::$printed_sets[] = $set;
				$json = json_encode( $data );

				printf( '<script> if ( ! window.cherry5IconSets ) { window.cherry5IconSets = {} } window.cherry5IconSets.%1$s = %2$s</script>', $set, $json );
			}

		}

		/**
		 * Enqueue javascript and stylesheet UI_Iconpicker
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_assets() {

			wp_enqueue_style(
				'ui-iconpicker',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-iconpicker/assets/min/ui-iconpicker.min.css', Cherry_UI_Elements::$module_path ) ),
				array(),
				Cherry_UI_Elements::$core_version,
				'all'
			);

			wp_enqueue_script(
				'jquery-iconpicker',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-iconpicker/assets/min/jquery-iconpicker.min.js', Cherry_UI_Elements::$module_path ) ),
				array( 'jquery' ),
				Cherry_UI_Elements::$core_version,
				true
			);

			wp_enqueue_script(
				'ui-iconpicker',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-iconpicker/assets/min/ui-iconpicker.min.js', Cherry_UI_Elements::$module_path ) ),
				array( 'jquery' ),
				Cherry_UI_Elements::$core_version,
				true
			);
		}
	}
}
