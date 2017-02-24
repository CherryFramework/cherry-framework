<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
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
		 * Store tag for deferred loading.
		 *
		 * @return string
		 */
		public function defer( $tag, $handle ) {

			if ( in_array( $handle, $this->handles ) ) {
				$this->prepared_handles[] = $tag;
				$tag = '';
			}

			return $tag;
		}

	}

}
