<?php
/**
 * UI element interface file.
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
}
