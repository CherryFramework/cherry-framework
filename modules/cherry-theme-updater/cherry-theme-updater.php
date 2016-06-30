<?php
/**
 *
 * Module Name: Theme Updater
 * Description: Provides functionality for updating themes
 * Version: 1.1.0
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package    Cherry_Framework
 * @subpackage Modules
 * @version    1.1.0
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Theme_Updater' ) ) {
	require_once( '/inc/cherry-base-updater.php' );

	/**
	 * Define theme updater class.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Theme_Updater extends Cherry_Base_Updater {
		/**
		 * Updater settings.
		 *
		 * @var array
		 */
		protected $settings = array();

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Init class parameters.
		 *
		 * @since  1.0.0
		 * @param  object $core core.
		 * @param  array  $args Input attributes array.
		 * @return void
		 */
		public function __construct( $core, $args = array() ) {
			/**
			 * Set default settings
			*/
			$theme_headers = wp_get_theme();

			$this->default_settings['slug'] = $theme_headers->get( 'Name' );
			$this->default_settings['repository_name'] = $theme_headers->get( 'Name' );
			$this->default_settings['version'] = $theme_headers->get( 'Version' );

			$this->base_init( $args );

			/**
			 * Need for test update - set_site_transient( 'pre_set_site_transient_', null );
			*/
			add_action( 'pre_set_site_transient_update_themes', array( $this, 'update' ), 1, 1 );
			add_filter( 'upgrader_source_selection', array( $this, 'rename_github_folder' ), 11, 3 );
			add_filter( 'wp_prepare_themes_for_js', array( $this, 'change_details_url' ) );
		}

		/**
		 * Process update.
		 *
		 * @since  1.0.0
		 * @param  object $data Update data.
		 * @return object
		 */
		public function update( $data ) {
			$new_update = $this->check_update();

			if ( $new_update['version'] ) {

				$update = array(
					'theme'       => $this->settings['slug'],
					'new_version' => $new_update['version'],
					'url'         => $this->settings['details_url'],
					'package'     => $new_update['package'],
				);

				$data->response[ $this->settings['slug'] ] = $update;
			}
			return $data;
		}

		/**
		 * Change theme detail URL.
		 *
		 * @since  1.0.0
		 * @param  array $prepared_themes array with update parametr.
		 *
		 * @return array
		 */
		public function change_details_url( $prepared_themes ) {

			if ( ! empty( $prepared_themes ) ) {

				foreach ( $prepared_themes as $theme_key => $theme_value ) {

					if ( 'cherryframework4' === $theme_key || 'Cherry Framework' === $theme_value['parent'] ) {

						if ( $theme_value['hasUpdate'] ) {

							$prepared_themes[ $theme_key ]['update'] = str_replace( 'class="thickbox"', 'target ="_blank"', $theme_value['update'] );
						}

						remove_filter( 'wp_prepare_themes_for_js', array( $this, 'change_details_url' ) );
					}
				}
			}

			return $prepared_themes;
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $core, $args ) {
			return new self( $core, $args );
		}
	}
}
