<?php
/**
 * I_Module interface class
 */

/**
 * Module interface
 */
interface I_Module{
	/**
	 * Returns the instance.
	 *
	 * @param  $core instance.
	 * @param  $args arguments.
	 * 
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance( $core, $args );
}