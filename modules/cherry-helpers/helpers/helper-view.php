<?php
/**
 * Helper View class file
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

		// Extract view datas.
		extract( $__data );
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