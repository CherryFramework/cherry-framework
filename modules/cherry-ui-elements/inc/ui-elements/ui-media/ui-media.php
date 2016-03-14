<?php
/**
 * Class for the building ui-media elements.
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

if ( ! class_exists( 'UI_Media' ) ) {

	/**
	 * Class for the building UI_Media elements.
	 */
	class UI_Media extends UI_Element implements I_UI {
		/**
		 * Default settings
		 *
		 * @var array
		 */

		private $defaults_settings = array(
			'id'					=> 'cherry-ui-media-id',
			'name'					=> 'cherry-ui-media-name',
			'value'					=> '',
			'multi_upload'			=> true,
			'library_type'			=> '', // image, video
			'upload_button_text'	=> 'Choose Media',
			'label'					=> '',
			'class'					=> '',
			'master'				=> '',
		);

		/**
		 * Constructor method for the UI_Media class.
		 *
		 * @since  4.0.0
		 */
		function __construct( $args = array() ) {

			$this->defaults_settings['id'] = 'cherry-ui-media-'.uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		}

		/**
		 * Render html UI_Media.
		 *
		 * @since  4.0.0
		 */
		public function render() {
			$html = '';

			$master_class = ! empty( $this->settings['master'] ) && isset( $this->settings['master'] ) ? esc_html( $this->settings['master'] ) : '';

			$html .= '<div class="cherry-ui-container ' . $master_class . '">';
				if ( '' != $this->settings['value'] ) {
					$this->settings['value'] = str_replace( ' ', '', $this->settings['value'] );
					$medias = explode( ',', $this->settings['value'] );
				} else {
					$this->settings['value'] = '';
					$medias = array();
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
									$mime_type = get_post_mime_type( $medias_value );
									$tmp = wp_get_attachment_metadata( $medias_value );
									$img_src = '';
									$thumb = '';

									switch ( $mime_type ) {
										case 'image/jpeg':
										case 'image/png':
										case 'image/gif':
											$img_src = wp_get_attachment_image_src( $medias_value, 'thumbnail' );
											$img_src = $img_src[0];
											$thumb = '<img  src="' . esc_html( $img_src ) . '" alt="">';
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
						$html .= '<input type="hidden" id="' . esc_attr( $this->settings['id'] ) . '" class="cherry-upload-input" name="' . esc_attr( $this->settings['name'] ) . '" value="' . esc_html( $this->settings['value'] ) . '" >';
						$html .= '<input type="button" class="upload-button button-default_" value="' . esc_attr( $this->settings['upload_button_text'] ) . '" data-title="' . esc_attr( $this->settings['upload_button_text'] ) . '" data-multi-upload="' . esc_attr( $this->settings['multi_upload'] ) . '" data-library-type="' . esc_attr( $this->settings['library_type'] ) . '"/>';
						$html .= '<div class="clear"></div>';
					$html .= '</div>';
				$html .= '</div>';
			$html .= '</div>';

			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Media
		 *
		 * @since  4.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_media();

			wp_enqueue_script(
				'ui-media-min',
				self::get_current_file_url( __FILE__ ) . '/assets/min/ui-media.min.js',
				array( 'jquery', 'jquery-ui-sortable' ),
				'1.0.0',
				true
			);

			wp_enqueue_style(
				'ui-media-min',
				self::get_current_file_url( __FILE__ ) . '/assets/min/ui-media.min.css',
				array(),
				'1.0.0',
				'all'
			);
		}
	}
}
