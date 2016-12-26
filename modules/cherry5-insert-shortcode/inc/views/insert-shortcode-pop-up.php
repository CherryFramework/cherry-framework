<?php
/**
 * Popup view.
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
<div class="cherry5-is cherry5-is__modal-window cherry-ui-kit">
	<div class="cherry5-is__popup">
		<div class="cherry5-is__popup-header">
			<div class="cherry5-is__popup-header-inner">
				<h3 class="cherry5-is__popup-title"><?php echo $popup_title ?></h3>
				<div class="cherry5-is__close-button">
					<span class="dashicons dashicons-no"></span>
				</div>
			</div>
		</div>
		<div class="cherry5-is__popup-body">
			<div class="cherry5-is__popup-sidebar">
				<div class="cherry5-is__sidebar-list cherry-scroll">
					<?php echo $sidebar_list ?>
				</div>
				<div class="cherry5-is__sidebar-button">
					<span class="dashicons dashicons-arrow-left-alt2 close"></span>
					<span class="dashicons dashicons-arrow-right-alt2 open"></span>
				</div>
			</div>
			<div class="cherry5-is__popup-section">
				<div class="cherry5-is__shortcodes-options cherry-scroll">
					<span class="cherry-loader-wrapper"><span class="cherry-loader"></span></span>
				</div>
				<div class="cherry5-is__popup-footer">
					<?php echo $insert_button ?>
				</div>
			</div>
		</div>
	</div>
	<div class="cherry5-is__background"></div>
</div>
