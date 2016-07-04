<?php
/**
 *
 * Module Name: Plugin Updater
 * Description: Provides functionality for updating plugins
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

if ( ! class_exists( 'Cherry_Plugin_Updater' ) ) {
	require_once( '/inc/cherry-base-updater.php' );

	/**
	 * Define plugin updater class.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Plugin_Updater extends Cherry_Base_Updater {
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
		 * @param object $core Core of framework.
		 * @param array  $args Argument of base init.
		 * @return void
		 */
		public function __construct( $core, $args = array() ) {
			$this->base_init( $args );

			/**
			 * Need for test update - set_site_transient( 'update_plugins', null );
			 */
			add_action( 'pre_set_site_transient_update_plugins', array( $this, 'update' ) );
			add_filter( 'upgrader_source_selection', array( $this, 'rename_github_folder' ), 11, 3 );
			add_action( 'admin_footer', array( $this, 'change_details_url' ) );
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
				$this->settings['plugin'] = $this->settings['slug'] . '/' . $this->settings['slug'] . '.php';

				$update = new stdClass();

				$update->slug = $this->settings['slug'];
				$update->plugin = $this->settings['plugin'];
				$update->new_version = $new_update['version'];
				$update->url = $this->settings['details_url'];
				$update->package = $new_update['package'];

				$data->response[ $this->settings['plugin'] ] = $update;

			}

			return $data;
		}

		/**
		 * Change plugin detail URL.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function change_details_url() {
			global $change_details_plugin_url_script, $pagenow;

			$plugins = get_plugin_updates();

			if ( ! $change_details_plugin_url_script && in_array( $pagenow, array( 'update-core.php', 'plugins.php' ) ) && ! empty( $plugins ) ) {

				$plugins_string = '';

				foreach ( $plugins as $plugin_key => $plugin_value ) {
					$plugin_key = strtolower( $plugin_key );
					if ( strpos( $plugin_key, 'cherry' ) !== false ) {
						$plugins_string .= '"' . $plugin_value ->update ->slug . '" : "' . $plugin_value ->update ->url .'", ';
					}
				}

				?>
				<script>
					( function( $ ){
						var plugin_updates = {<?php echo $plugins_string; ?>};
						for ( var plugin in plugin_updates ) {
							$('[href*="' + plugin + '"].thickbox').removeClass('thickbox').attr( {'href': plugin_updates[plugin], 'target' : "_blank" } );
						};
					}( jQuery ) )
				</script>
				<?php
			}

			$change_details_plugin_url_script = true;
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
