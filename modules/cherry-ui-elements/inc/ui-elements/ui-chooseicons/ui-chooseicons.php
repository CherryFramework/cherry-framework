<?php
/**
 * Class for the building ui-choose-icons elements.
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

if ( ! class_exists( 'UI_Choose_Icons' ) ) {

	/**
	 * Class for the building ui-choose-icons elements.
	 */
	class UI_Chooseicons extends UI_Element implements I_UI {

		/**
		 * Classes dictionary
		 *
		 * @var array
		 */
		protected $classes = array();

		/**
		 * Constructor method for the UI_Choose_Icons class.
		 *
		 * @since  4.0.0
		 */
		function __construct( $args = array() ) {
			$this->classes = self::get_all_classes_paths( array( self::get_path() . '/inc' ) );
			spl_autoload_register( array( &$this, 'autoload' ) );

			$this->settings = wp_parse_args(
				$args,
				array(
					'id'			=> 'cherry-ui-choose-icons-id',
					'name'			=> 'cherry-ui-choose-icons-name',
					'value'			=> '',
					'placeholder'	=> 'Type to filter',
					'label'			=> 'Choose your icon',
					'end_text'		=> 'The end',
					'class'			=> '',
					'required'		=> false,
				)
			);
		}

		/**
		 * Autoload my classes
		 *
		 * @return void
		 */
		public function autoload( $class ) {
			if ( array_key_exists( $class, $this->classes ) ) {
				require_once $this->classes[ $class ];
			}
		}

		/**
		 * Get all classes by paths
		 *
		 * @return array
		 */
		public static function get_all_classes_paths( $folders ) {
			$result    = array();
			$paths     = self::get_all_paths( $folders );
			$paths     = array_unique( $paths );

			if ( count( $paths ) ) {
				foreach ( $paths as $file ) {
					$class = self::filename_to_class( basename( $file ) );
					$result[ $class ] = $file;
				}
			}
			return $result;
		}

		/**
		 * Get all paths to classes
		 *
		 * @param  [type] $folders folders list.
		 * @return array
		 */
		public static function get_all_paths( $folders ) {
			$paths  = array();
			if ( is_array( $folders ) && count( $folders ) ) {
				foreach ( $folders as $folder ) {
					$pattern = self::get_folder_pattern( $folder );
					$paths   = array_merge( $paths, (array) glob( $pattern ) );
				}
			}
			return $paths;
		}

		/**
		 * Filename to Classname
		 *
		 * @param  [type] $filename file name.
		 * @return class name.
		 */
		public static function filename_to_class( $filename ) {
			$filename = str_replace( '.php', '', $filename );
			$pieces   = explode( '-', $filename );
			foreach ( $pieces as &$piece ) {
				$piece = ucwords( $piece );
			}
			return implode( '_', $pieces );
		}

		/**
		 * Get folder glob pattern
		 *
		 * @param  [string] $folder folder name.
		 * @return folder glob pattern
		 */
		public static function get_folder_pattern( $folder = '' ) {
			if ( '' !== $folder ) {
				$folder = trailingslashit( $folder );
			}
			return $folder.'*.php';
		}

		/**
		 * Render html UI_Choose_Icons.
		 *
		 * @param [array] $icon_sets object instanceof Icon_Set.
		 * @since  4.0.0
		 */
		public function render( array $icon_sets = array() ) {
			$settings = $this->settings;
			$settings['required'] = $this->get_required();
			$settings['icons']    = array();

			// Convert and render our icon sets
			if ( count( $icon_sets ) ) {
				foreach ( $icon_sets as $icon_set ) {
					if ( $icon_set instanceof Icon_Set ) {
						$settings['icons'] = $settings['icons'] + $icon_set->render()->get_converted_data();
					}
				}
			}

			return Cherry_Toolkit::render_view(
				dirname( __FILE__ ) . '/views/choose-icons.php',
				$settings
			);
		}

		/**
		 * Get required attribute
		 *
		 * @return [string] attr.
		 */
		private function get_required() {
			if ( true === $this->settings['required'] ) {
				return 'required="required"';
			}
			return '';
		}

		/**
		 * Enqueue javascript and stylesheet UI_Choose_Icons
		 *
		 * @since  4.0.0
		 */
		public static function enqueue_assets() {
			// Styles
			wp_enqueue_style( 'ui-chooseicons', self::get_current_file_url( __FILE__ ) . '/assets/css/ui-chooseicons.css' );
			wp_enqueue_style( 'material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons' );

			// Scripts
			wp_enqueue_script( 'ui-chooseicons', self::get_current_file_url( __FILE__ ) . '/assets/js/ui-chooseicons.min.js', array( 'cherry-js-core' ) );
		}

		/**
		 * Get current file path
		 *
		 * @return [string] current file path;
		 */
		public static function get_path() {
			return dirname( __FILE__ );
		}
	}
}
