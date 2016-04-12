<?php
/**
 * Abstract class icon set.
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Abstract icon set class
 */
abstract class Icon_Set {

	/**
	 * CSS Styels file.
	 *
	 * @var string
	 */
	protected $css_file = '';

	/**
	 * Data from file.
	 *
	 * @var string
	 */
	protected $data = '';

	/**
	 * Converted datat into array.
	 *
	 * @var array
	 */
	protected $converted_data = array();

	/**
	 * Abstract class constructor
	 *
	 * @param [string] $css_file styles.
	 */
	public function __construct( $css_file = '' ) {
		add_action( 'wp_enqueue_scripts', array( &$this, 'add_scripts_and_styles' ) );

		$this->css_file       = $css_file;
		$this->data           = $this->get_contents_with_cache();
		$this->converted_data = $this->convert_data( $this->data );
	}

	/**
	 * Add scripts and styles
	 */
	public function add_scripts_and_styles() {
		wp_enqueue_style( $this->get_name(), $this_css_file );
	}

	/**
	 * Get converted data
	 *
	 * @return [array] converted.
	 */
	public function get_converted_data() {
		return (array) $this->converted_data;
	}

	/**
	 * Instead $GLOBALS['wp_filesystem']->get_contents( $file )
	 *
	 * @param type $url host url.
	 * @return string requres data
	 */
	public static function get_contents( $url ) {
		$wp_filesystem = self::get_wp_filesystem();
		return $wp_filesystem->get_contents( $url );
	}

	/**
	 * Get contents
	 *
	 * @return [content] file data.
	 */
	public function get_contents_with_cache() {
		$cache_key = md5( $this->css_file );
		$cached    = self::get_cache( $cache_key );
		if ( false !== $cached ) {
			return $cached;
		}

		return self::get_contents( $this->css_file );
	}

	/**
	 * Set Cache
	 *
	 * @param [string]  $key cache key.
	 * @param [string]  $val value to cahce.
	 * @param [integer] $time life time.
	 */
	public static function set_cache( $key, $val, $time = 3600 ) {
		set_transient( $key, $val, $time );
	}

	/**
	 * Get Cache
	 *
	 * @param  [string] $key cache key.
	 * @return mixed
	 */
	public static function get_cache( $key ) {
		$cached   = get_transient( $key );
		if ( false !== $cached ) {
			return $cached;
		}
		return false;
	}

	/**
	 * Get wp_filesystem
	 *
	 * @return Object
	 */
	public static function get_wp_filesystem() {
		global $wp_filesystem;

		if ( ! defined( 'FS_CHMOD_FILE' ) ) {
			define( 'FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
		}

		if ( empty( $wp_filesystem ) || ! class_exists( 'WP_Filesystem_Direct' ) ) {
			include_once( ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php' );
			include_once( ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php' );
		}

		return new WP_Filesystem_Direct( null );
	}

	/**
	 * Get icon set name
	 * I wrote this because we need stand by php 5.2
	 * and i this version we don't have "Late Static Bindings"
	 * http://php.net/manual/en/language.oop5.late-static-bindings.php
	 *
	 * @return [type] [description]
	 */
	abstract public function get_name();

	/**
	 * Render data
	 *
	 * @return [string] rendered HTML.
	 */
	abstract public function render();

	/**
	 * Convert data to needed format
	 * Something like that: { "class" : "fa-face", "code" : "09123", "text" : "class" }
	 *
	 * @param  [string] $data from file.
	 * @return [array] converted data.
	 */
	abstract public function convert_data( $data );
}
