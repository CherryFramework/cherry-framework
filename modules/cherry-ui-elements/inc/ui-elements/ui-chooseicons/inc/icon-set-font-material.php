<?php
/**
 * Сlass font material icon set.
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Material font icon set class
 */
class Icon_Set_Font_Material extends Icon_Set {
	/**
	 * Get icon set name
	 * I wrote this because we need stand by php 5.2
	 * and i this version we don't have "Late Static Bindings"
	 * http://php.net/manual/en/language.oop5.late-static-bindings.php
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return sanitize_title( __CLASS__ );
	}

	/**
	 * Render data
	 *
	 * @return [string] rendered HTML.
	 */
	public function render() {
		$result = array();
		if ( count( $this->converted_data ) ) {
			foreach ( $this->converted_data as &$icon ) {
				$icon['html'] = Cherry_Toolkit::render_view(
					dirname( dirname( __FILE__ ) ) . '/views/material-icons.php',
					$icon
				);
			}
		}
		return $this;
	}

	/**
	 * Convert data to needed format
	 * Something like that: { "class" : "fa-face", "code" : "09123", "text" : "class" }
	 *
	 * @param  [string] $data from file.
	 * @return [array] converted data.
	 */
	public function convert_data( $data ) {
		// Get from cache
		$cache_key = md5( $this->css_file . '_converted_data' );
		$cached    = self::get_cache( $cache_key );
		if ( false !== $cached ) {
			return $cached;
		}

		$pattern = '/(\w+)\s(\w+)/';
		preg_match_all( $pattern, $data, $matches, PREG_SET_ORDER );
		$icons = array();

		foreach ( $matches as $match ) {
			array_push(
				$icons,
				array(
					'class' => $match[1],
					'text'  => $match[1],
					'code'  => stripcslashes( $match[2] ),
				)
			);
		}

		return $icons;
	}
}
