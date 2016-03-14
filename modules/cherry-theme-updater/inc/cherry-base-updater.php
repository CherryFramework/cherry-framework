<?php
/**
 * Class for the base update.
 *
 * @package    Cherry_Base_Updater
 * @subpackage Base_Update
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Base_Updater' ) ) {

	/**
	 * Test definitions to allow alpha and beta updates
	 *
	 * Disable updates - define('CHERRY_UPDATE', false);
	 * Enable auto updates - define('CHERRY_ALPHA_UPDATE', true);
	 * Enable beta updates - define('CHERRY_BETA_UPDATE', true);
	 */

	/**
	 * Base updater class.
	 *
	 * @since 4.0.0
	 */
	class Cherry_Base_Updater {

		/**
		 * Default Settings.
		 *
		 * @since 4.0.0
		 * @access protected
		 * @var array
		 */
		protected $default_settings = array(
			'version'         => '',
			'slug'            => '',
			'cloud_url'       => 'https://cloud.cherryframework.com/cherry-update/',
			'product_name'    => 'CherryFramework',
			'repository_name' => '',
		);

		/**
		 * Init class parameters.
		 *
		 * @since  4.0.0
		 * @param  array $settings Input attributes array.
		 * @return void
		 */
		protected function base_init( $settings = array() ) {
			$this->settings = array_merge( $this->default_settings, $settings );
		}

		/**
		 * Check if update are avaliable.
		 *
		 * @since  4.0.0
		 * @return array
		 */
		protected function check_update() {
			$args = array(
				'user-agent'        => 'WordPress',
				'github_repository' => $this->settings['product_name'] . '/' . $this->settings['repository_name'],
				'current_version'   => $this->settings['version'],
				'up_query_limit'    => false,
				'get_alpha'         => false,
				'get_beta'          => false,
			);

			if ( defined( 'CHERRY_ALPHA_UPDATE' ) ) {
				$args['get_alpha'] = true;
			}

			if ( defined( 'CHERRY_BETA_UPDATE' ) ) {
				$args['get_beta'] = true;
			}

			if ( defined( 'CHERRY_UP_QUERY_LIMIT' ) ) {
				$args['up_query_limit'] = true;
			}

			$response = $this -> remote_query( $args );

			if ( $response && 'not_update' !== $response ) {
				$this->settings['details_url'] = $response->details_url;
				return array( 'version' => $response->new_version, 'package' => $response->package );
			}

			return array( 'version' => false );
		}

		/**
		 * Remote request to updater API.
		 *
		 * @since  4.0.0
		 * @param  array $args Request paprams.
		 * @return array|bool
		 */
		protected function remote_query( $args ) {
			$query = add_query_arg( $args, $this->settings['cloud_url'] );

			$response = wp_remote_get( $query );

			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != '200' ) {
				return false;
			}

			$response = json_decode( $response['body'] );

			return $response;
		}

		/**
		 * Rename github folder on update.
		 *
		 * @since  4.0.0
		 * @param  [type] $upgrate_dir   Theme folder name.
		 * @param  [type] $remote_dir    Remote folder name.
		 * @param  object $skin_upgrader Upgrader object instance.
		 * @return string
		 */
		public function rename_github_folder( $upgrate_dir, $remote_dir, $skin_upgrader ) {

			$slug = $this->settings['slug'];
			$is_theme = isset( $skin_upgrader->skin->theme ) || isset( $skin_upgrader->skin->theme_info ) ? true : false ;
			$is_plugin = isset( $skin_upgrader->skin->plugin_info ) ? true : false ;
			$domain_plugin = $is_plugin ? $skin_upgrader->skin->plugin_info['TextDomain'] : '' ;
			$title_plugin = $is_plugin ? str_replace( ' ', '-', strtolower( $skin_upgrader->skin->plugin_info['Title'] ) ) : '' ;
			$name_plugin = $is_plugin ? str_replace( ' ', '-', strtolower( $skin_upgrader->skin->plugin_info['Name'] ) ) : '' ;

			if ( $is_theme && strpos( $upgrate_dir, $slug ) !== false
				|| $is_plugin && $domain_plugin === $slug
				|| $is_plugin && $title_plugin === $slug
				|| $is_plugin && $name_plugin === $slug
			) {
				$upgrate_dir_path = pathinfo( $upgrate_dir );
				$new_upgrate_dir = trailingslashit( $upgrate_dir_path['dirname'] ) . trailingslashit( $slug );

				rename( $upgrate_dir, $new_upgrate_dir );

				$upgrate_dir = $new_upgrate_dir;

				remove_filter( 'upgrader_source_selection', array( $this, 'rename_github_folder' ), 11, 3 );
			}

			return $upgrate_dir;
		}
	}
}
