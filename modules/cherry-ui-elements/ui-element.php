<?php
/**
 * UI_Element
 *
 * @package    Cherry_Framework
 * @subpackage Abstract Class
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * UI_Element abstract class
 */
if ( ! class_exists( 'UI_Element' ) ) {

	/**
	 * UI_Element Abstract Class
	 */
	abstract class UI_Element {

		/**
		 * Settings list
		 *
		 * @var array
		 */
		protected $settings = array();

		/**
		 * Get current file URL
		 *
		 * @since  4.0.0
		 */
		public static function get_current_file_url( $file ) {
			$assets_url = dirname( $file );
			$site_url = site_url();
			$assets_url = str_replace( untrailingslashit( ABSPATH ), $site_url, $assets_url );
			$assets_url = str_replace( '\\', '/', $assets_url );

			return $assets_url;
		}

		/**
		 * Get control value
		 *
		 * @return string control value.
		 */
		public function get_value() {
			return $this->settings['value'];
		}

		/**
		 * Set control value
		 *
		 * @param [type] $value new.
		 */
		public function set_value( $value ) {
			$this->settings['value'] = $value;
		}

		/**
		 * Get control name
		 *
		 * @return string control name.
		 */
		public function get_name() {
			return $this->settings['name'];
		}

		/**
		 * Set control name
		 *
		 * @param [type] $name new control name.
		 * @throws Exception Invalid control name.
		 */
		public function set_name( $name ) {
			$name = (string) $name;
			if ( '' !== $name ) {
				$this->settings['name'] = $name;
			} else {
				throw new Exception( "Invalid control name '".$name."'. Name can't be empty." );
			}
		}
	}
}
