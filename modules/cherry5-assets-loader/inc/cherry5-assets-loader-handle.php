<?php
/**
 * Base lodaer handler class
 *
 * @package    Cherry_Framework
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2017, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry5_Assets_Loader_Handle' ) ) {

	/**
	 * Define Cherry5_Assets_Loader_Handle class
	 */
	class Cherry5_Assets_Loader_Handle {

		/**
		 * Handles list
		 *
		 * @var array
		 */
		public static $handles = array();

		/**
		 * Handles list
		 *
		 * @var array
		 */
		public static $prepared_handles = array();

		/**
		 * Handlex context (defined in child classes)
		 *
		 * @var string
		 */
		public $context = null;

		/**
		 * Initalize defer loading
		 *
		 * @return void
		 */
		public function init() {

			if ( null !== $this->context ) {
				self::$handles = array_unique( self::$handles );
				add_filter( $this->context . '_loader_tag', array( $this, 'defer' ), 10, 2 );
				add_action( 'wp_footer', array( $this, 'print_tags_var' ), 99 );
			}

		}

		/**
		 * Store tag for deferred loading.
		 *
		 * @return string
		 */
		public function defer( $tag, $handle ) {

			if ( in_array( $handle, self::$handles ) ) {
				self::$prepared_handles[] = $tag;
				$tag = '';
			}

			return $tag;
		}

		/**
		 * Print stored handles.
		 *
		 * @return void|null
		 */
		public function print_tags_var() {

			if ( empty( self::$prepared_handles ) || null === $this->context ) {
				return;
			}

			ob_start();
			include 'assets/js/var.js';
			$var_template = ob_get_clean();

			printf( $var_template, ucfirst( $this->context ), json_encode( self::$prepared_handles ) );
		}

	}

}
