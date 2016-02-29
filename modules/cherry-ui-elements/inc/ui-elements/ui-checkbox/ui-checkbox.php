<?php
/**
 * Class for the building ui-checkbox elements.
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

if ( ! class_exists( 'UI_Checkbox' ) ) {
	class UI_Checkbox {

		private $settings = array();
		private $defaults_settings = array(
			'id'			=> 'cherry-ui-checkbox-id',
			'name'			=> 'cherry-ui-checkbox-name',
			'value'			=> array(
				'checkbox-1' => 'true',
				'checkbox-2' => 'true',
				'checkbox-3' => 'true',
			),
			'options'		=> array(
				'checkbox-1'	=> 'checkbox 1',
				'checkbox-2'	=> 'checkbox 2',
				'checkbox-3'	=> 'checkbox 3'
			),
			'label'			=> '',
			'class'			=> '',
			'master'		=> '',
		);

		/**
		 * Constructor method for the UI_Checkbox class.
		 *
		 * @since  4.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-checkbox-'.uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			//$arr = array( self::get_current_file_url() . '/assets/min/ui-checkbox.min.js', self::get_current_file_url() . '/assets/ui-checkbox.css' );
			//$this->type_of_assets();
			/*if ( defined( 'DOING_AJAX' ) && DOING_AJAX ){
				?>
					<script>
					(function(){

						CherryJsCore.utilites.namespace('ui_elements.tmp_assets');
						CherryJsCore.ui_elements.tmp_assets = (typeof CherryJsCore.ui_elements.tmp_assets === 'object') ? [] : CherryJsCore.ui_elements.tmp_assets ;

						if( $.inArray( 'ui-checkbox.min.js', CherryJsCore.variable.loaded_assets.script ) == -1 ){
							CherryJsCore.ui_elements.tmp_assets.push("<?php echo self::get_current_file_url() . '/assets/min/ui-checkbox.min.js'; ?>");
						}else{
							CherryJsCore.ui_elements.checkbox.init( $('body') );
						}

						if( $.inArray( 'ui-checkbox.css', CherryJsCore.variable.loaded_assets.style ) == -1 ){
							CherryJsCore.ui_elements.tmp_assets.push("<?php echo self::get_current_file_url() . '/assets/ui-checkbox.css' ?>");
						}
					}())
					</script>
				<?php
			}else{}*/

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Render html UI_Checkbox.
		 *
		 * @since  4.0.0
		 */
		public function render() {
			$html = '';
			$master_class = ! empty( $this->settings['master'] ) && isset( $this->settings['master'] ) ? esc_html( $this->settings['master'] ) : '';

			$html .= '<div class="cherry-ui-container ' . $master_class . '">';

			$counter = 0;
				if( $this->settings['options'] && !empty( $this->settings['options'] ) && is_array( $this->settings['options'] ) ) {
					if ( !is_array( $this->settings['value'] ) ) {
						$this->settings['value'] = array( $this->settings['value'] );
					}
					if( '' !== $this->settings['label'] ){
						$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . esc_html( $this->settings['label'] ) . '</label> ';
					}

					foreach ( $this->settings['options'] as $option => $option_value ) {

						if ( ! empty( $this->settings['value'] ) ) {
							$option_checked = array_key_exists( $option, $this->settings['value'] ) ? $option : '';
							$item_value     = ! empty( $option_checked ) ? $this->settings['value'][ $option ] : 'false';
						} else {
							$option_checked = '';
							$item_value     = 'false';
						}

						$checked = ( ! empty( $option_checked ) && 'true' === $item_value ) ? 'checked' : '';

						$option_label = isset( $option_value ) && is_array( $option_value ) ? $option_value['label'] : $option_value;
						$data_slave = isset( $option_value['slave'] ) && !empty( $option_value['slave'] ) ? ' data-slave="' . $option_value['slave'] . '"' : '';

						$html .= '<div class="cherry-checkbox-item-wrap ' . esc_attr( $this->settings['class'] ) . '">';
							$html .= '<div class="cherry-checkbox-item ' . $checked . '"><span class="marker dashicons dashicons-yes"></span></div>';
							$html .= '<input type="hidden" id="' . esc_attr( $this->settings['id'] ) . '-' . $counter . '" class="cherry-checkbox-input" name="' . esc_attr( $this->settings['name'] ) . '['. $option .']" value="' . esc_html( $item_value ) . '"' . $data_slave . '>';
							$html .= '<label class="cherry-checkbox-label" for="' . esc_attr( $this->settings['id'] ) . '-' . $counter . '">' . esc_html( $option_label ) . '</label> ';
						$html .= '</div>';

						$counter++;
					}
				}
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
		 * Enqueue javascript and stylesheet UI_Checkbox
		 *
		 * @since  4.0.0
		 */
		public static function enqueue_assets(){
			wp_enqueue_script(
				'ui-checkbox-min',
				self::get_current_file_url() . '/assets/min/ui-checkbox.min.js',
				array( 'jquery' ),
				'1.0.0',
				true
			);

			wp_enqueue_style(
				'ui-checkbox-min',
				self::get_current_file_url() . '/assets/min/ui-checkbox.min.css',
				array(),
				'1.0.0',
				'all'
			);
		}

	}
}