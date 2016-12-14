<?php
/**
 * Shortcode options view.
 *
 * @package    cherry5_insert_shortcode
 * @subpackage Views
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div class="cherry5-is__content-area">
	<header class="cherry5-is__content-area-header">
		<h3 class="cherry5-is__content-area-title" role="banner">%3$s</h3>
	</header>
	<textarea id="%1$s-%2$s-content" name="cherry5-is__shortcode-content" class="cherry5-is__shortcode-content cherry-ui-textarea" rows="10" cols="20" placeholder="%4$s">%5$s</textarea>
</div>
