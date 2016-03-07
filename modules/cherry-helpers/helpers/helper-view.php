<?php
/**
 * View helper
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Helper View class
 */
class Helper_View {

	/**
	 * Render
	 *
	 * @param type  $__path storage view path.
	 * @param  array $__data include data.
	 * @return rendered html
	 */
	public static function render( $__path, $__data ) {
		ob_start();

		if ( file_exists( $__path ) ) {
			// Compile the view.
			try {
				// Include the view.
				include( $__path );
			} catch ( Exception $e ) {
				echo $e;
				die();
			}
		}
		// Return the compiled view and terminate the output buffer.
		return ltrim( ob_get_clean() );
	}
}
