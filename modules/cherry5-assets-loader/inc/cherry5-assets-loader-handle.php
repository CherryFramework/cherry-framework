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
		public $handles = array();

		/**
		 * Handles list
		 *
		 * @var array
		 */
		public $prepared_handles = array();

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
				add_filter( $this->context . '_loader_tag', array( $this, 'defer' ), 10, 3 );
				add_action( 'wp_footer', array( $this, 'print_tags_var' ), 99 );
			}

		}

		/**
		 * Store tag for deferred loading.
		 *
		 * @return string
		 */
		public function defer( $tag, $handle, $src ) {

			if ( in_array( $handle, $this->handles ) ) {
				$this->prepared_handles[] = $tag;
				$tag = '';
			}

			return $tag;
		}

		/**
		 * Add new handles into list before processing
		 */
		public function add_handles( $handles = array() ) {
			$this->handles = array_merge( $this->handles, $handles );
			$this->handles = array_unique( $this->handles );
		}

		/**
		 * Print stored handles.
		 *
		 * @return void|null
		 */
		public function print_tags_var() {

			if ( empty( $this->prepared_handles ) || null === $this->context ) {
				return;
			}

			$path = Cherry5_Assets_Loader::$module_path . 'assets/var.js';

			ob_start();
			include $path;
			$var_template = ob_get_clean();

			$js_context = ( 'style' === $this->context ) ? 'head' : 'body';

			$var = sprintf(
				$var_template,
				ucfirst( $this->context ),
				json_encode( $this->prepared_handles ),
				$js_context
			);

			echo '<script type="text/javascript">' . $var . '</script>';
		}

	}

}
