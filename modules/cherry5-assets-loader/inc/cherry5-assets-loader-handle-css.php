<?php
/**
 * CSS lodaer handler class
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

if ( ! class_exists( 'Cherry5_Assets_Loader_Handle_CSS' ) ) {

	/**
	 * Define Cherry5_Assets_Loader_Handle_CSS class
	 */
	class Cherry5_Assets_Loader_Handle_CSS extends Cherry5_Assets_Loader_Handle {

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
		 * Define required properies
		 */
		public function __construct() {

			$this->context = 'style';
			$this->init();

		}

	}

}
