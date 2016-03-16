<?php
/**
 * Module interface
 *
 * @package    Cherry_Framework
 * @subpackage Interface
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Module interface class
 */
interface I_Module {
	/**
	 * Returns the instance.
	 *
	 * @param [type] $core instance.
	 * @param [type] $args arguments.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance( $core, $args );
}
