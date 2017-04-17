<?php
/**
 * Facebook embed
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2017, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.en.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Facebook_Embed' ) ) {

	/**
	 * Define Cherry_Facebook_Embed class
	 */
	class Cherry_Facebook_Embed {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor for the class
		 */
		function __construct() {
			add_filter( 'init', array( $this, 'add_facebook' ) );
		}

		/**
		 * Register Facebook provider
		 *
		 * @since  1.0.0
		 * @param  array $providers Existing providers.
		 */
		public function add_facebook( $providers ) {

			$endpoints = array(
				'#https?://www\.facebook\.com/video.php.*#i'      => 'https://www.facebook.com/plugins/video/oembed.json/',
				'#https?://www\.facebook\.com/.*/videos/.*#i'     => 'https://www.facebook.com/plugins/video/oembed.json/',
				'#https?://www\.facebook\.com/.*/posts/.*#i'      => 'https://www.facebook.com/plugins/post/oembed.json/',
				'#https?://www\.facebook\.com/.*/activity/.*#i'   => 'https://www.facebook.com/plugins/post/oembed.json/',
				'#https?://www\.facebook\.com/photo(s/|.php).*#i' => 'https://www.facebook.com/plugins/post/oembed.json/',
				'#https?://www\.facebook\.com/permalink.php.*#i'  => 'https://www.facebook.com/plugins/post/oembed.json/',
				'#https?://www\.facebook\.com/media/.*#i'         => 'https://www.facebook.com/plugins/post/oembed.json/',
				'#https?://www\.facebook\.com/questions/.*#i'     => 'https://www.facebook.com/plugins/post/oembed.json/',
				'#https?://www\.facebook\.com/notes/.*#i'         => 'https://www.facebook.com/plugins/post/oembed.json/',
			);

			foreach ( $endpoints as $pattern => $endpoint ) {
				wp_oembed_add_provider( $pattern, $endpoint, true );
			}
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}

}
