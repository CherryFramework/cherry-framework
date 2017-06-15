<?php
/**
 * Class for the building ui-media elements.
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

if ( ! class_exists( 'UI_Media' ) ) {

	/**
	 * Class for the building UI_Media elements.
	 */
	class UI_Media extends UI_Element implements I_UI {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $defaults_settings = array(
			'id'                 => 'cherry-ui-media-id',
			'name'               => 'cherry-ui-media-name',
			'value'              => '',
			'multi_upload'       => true,
			'library_type'       => '', // image, video, sound
			'upload_button_text' => 'Choose Media',
			'label'              => '',
			'class'              => '',
			'master'             => '',
			'lock'               => false,
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
		 * Constructor method for the UI_Media class.
		 *
		 * @since 1.0.0
		 */
		public function __construct( $args = array() ) {

			$this->defaults_settings['id'] = 'cherry-ui-media-' . uniqid();
			$this->settings                = wp_parse_args( $args, $this->defaults_settings );
			$this->lock_element            = new Cherry5_Lock_Element( $this->settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Render html UI_Media.
		 *
		 * @since 1.0.0
		 */
		public function render() {
			$html = '';

			if ( current_user_can( 'upload_files' ) ) {

				$class = implode( ' ',
					array(
						$this->settings['class'],
						$this->settings['master'],
						$this->lock_element->get_class( 'inline-block' ),
					)
				);

				$html .= '<div class="cherry-ui-container ' . esc_attr( $class ) . '">';
					if ( '' != $this->settings['value'] ) {
						$this->settings['value'] = str_replace( ' ', '', $this->settings['value'] );
						$medias                  = explode( ',', $this->settings['value'] );
					} else {
						$this->settings['value'] = '';
						$medias                  = array();
					}

					$img_style = ! $this->settings['value'] ? 'style="display:none;"' : '' ;

						if ( '' !== $this->settings['label'] ) {
							$html .= '<label class="cherry-label" for="' . esc_attr( $this->settings['id'] ) . '">' . esc_html( $this->settings['label'] ) . '</label> ';
						}
						$html .= '<div class="cherry-ui-media-wrap">';
							$html .= '<div  class="cherry-upload-preview" >';
							$html .= '<div class="cherry-all-images-wrap">';

								if ( is_array( $medias ) && ! empty( $medias ) ) {

									foreach ( $medias as $medias_key => $medias_value ) {

										$media_title = get_the_title( $medias_value );
										$mime_type   = get_post_mime_type( $medias_value );
										$tmp         = wp_get_attachment_metadata( $medias_value );
										$img_src     = '';
										$thumb       = '';

										switch ( $mime_type ) {
											case 'image/jpeg':
											case 'image/png':
											case 'image/gif':
												$img_src = wp_get_attachment_image_src( $medias_value, 'thumbnail' );
												$img_src = $img_src[0];
												$thumb   = '<img  src="' . esc_html( $img_src ) . '" alt="">';
											break;

											case 'image/x-icon':
												$thumb = '<span class="dashicons dashicons-format-image"></span>';
											break;

											case 'video/mpeg':
											case 'video/mp4':
											case 'video/quicktime':
											case 'video/webm':
											case 'video/ogg':
												$thumb = '<span class="dashicons dashicons-format-video"></span>';
											break;

											case 'audio/mpeg':
											case 'audio/wav':
											case 'audio/ogg':
												$thumb = '<span class="dashicons dashicons-format-audio"></span>';
											break;
										}
										$html .= '<div class="cherry-image-wrap">';
											$html .= '<div class="inner">';
												$html .= '<div class="preview-holder" data-id-attr="' . esc_attr( $medias_value ) . '">';
													$html .= '<div class="centered">';
														$html .= $thumb;
													$html .= '</div>';
												$html .= '</div>';
												$html .= '<span class="title">' . $media_title . '</span>';
												$html .= '<a class="cherry-remove-image" href="#" title=""><i class="dashicons dashicons-no"></i></a>';
											$html .= '</div>';
										$html .= '</div>';
									}
								}
							$html .= '</div>';
						$html .= '</div>';
						$html .= '<div class="cherry-element-wrap">';
							$html .= '<input type="hidden" id="' . esc_attr( $this->settings['id'] ) . '" class="cherry-upload-input" name="' . esc_attr( $this->settings['name'] ) . '" value="' . esc_html( $this->settings['value'] ) . '"' . $this->lock_element->get_disabled_attr() . '>';
							$html .= '<button type="button" class="upload-button cherry-upload-button button-default_" value="' . esc_attr( $this->settings['upload_button_text'] ) . '" data-title="' . esc_attr( $this->settings['upload_button_text'] ) . '" data-multi-upload="' . esc_attr( $this->settings['multi_upload'] ) . '" data-library-type="' . esc_attr( $this->settings['library_type'] ) . '"' . $this->lock_element->get_disabled_attr() . '>' . esc_attr( $this->settings['upload_button_text'] ) . '</button>';
							$html .= '<div class="clear"></div>';
						$html .= '</div>';
					$html .= '</div>';
					$html .= $this->lock_element->get_html();
				$html .= '</div>';
			}

			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Media.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_assets() {
			if ( current_user_can( 'upload_files' ) ) {
				wp_enqueue_media();

				wp_enqueue_script(
					'ui-media',
					esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-media/assets/min/ui-media.min.js', Cherry_UI_Elements::$module_path ) ),
					array( 'jquery', 'jquery-ui-sortable' ),
					Cherry_UI_Elements::$core_version,
					true
				);

				wp_enqueue_style(
					'ui-media',
					esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-media/assets/min/ui-media.min.css', Cherry_UI_Elements::$module_path ) ),
					array(),
					Cherry_UI_Elements::$core_version,
					'all'
				);
			}
		}
	}
}
