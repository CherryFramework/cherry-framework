<?php
/**
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Cherry_Helpers implements I_Module {
	/**
	 * Module version
	 *
	 * @var string
	 */
	public $module_version = '1.0.0';

	/**
	 * Module slug
	 *
	 * @var string
	 */
	public $module_slug = 'cherry-helpers';

	/**
	 * Module directory
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $module_directory = '';

	/**
	 * Cherry_Post_Type class constructor
	 */
	public function __construct( $core, $args = array() ) {
		$this->module_directory = $core->settings['base_dir'] . '/modules/' . $this->$module_slug;

		// Load helpers.
		$this->load();
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

	/**
	 * Load all helpers.
	 * 
	 * @return void.
	 */
	private function load() {
		$paths = (array) glob( $this->module_directory.'/*.php' );
		foreach ($paths as $helper) {
			require_once( $helper );
		}
	}
}