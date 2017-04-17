<?php
/**
 * Shortcode options view.
 *
 * @package    cherry5_insert_shortcode
 * @subpackage Views
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2017, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<form id="%1$s-%2$s" name="%1$s-%2$s" class="cherry5-is__shortcode-form show" >
	<header class="cherry5-is__shortcode-form-header">
		<h2 class="cherry5-is__shortcode-title" role="banner">%3$s</h2>
		<div class="cherry5-is__shortcode-description" role="note">%4$s</div>
	</header>
	%5$s
	%6$s
<form>
