<?php
/**
 * I'am UI interface
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * UI element interface
 */

interface I_UI {

	/**
	 * Enqueue javascript and stylesheet to UI element.
	 */
	public static function enqueue_assets();

	/**
	 * Render UI element.
	 *
	 * @return string.
	 */
	public function render();

	/**
	 * Get control name
	 *
	 * @return string control name.
	 */
	public function get_name();

	/**
	 * Set control name
	 *
	 * @param [type] $name new control name.
	 */
	public function set_name( $name );

}
