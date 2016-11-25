<?php
/**
 * Accordion template.
 *
 * @package    Cherry_Interface_Builder
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
<div class="cherry5-is__modal-window">
	<div class="cherry5-is__popup">
		<div class="cherry5-is__popup-header">
			<div class="cherry5-is__popup-header-inner">
				<h3 class="cherry5-is__popup-title"><?php esc_html_e( 'Insert Shortcode', 'cherry' ); ?></h3>
				<div class="cherry5-is__close-button">
					<span class="dashicons dashicons-no"></span>
				</div>
			</div>
		</div>
		<div class="cherry5-is__popup-body">
			<div class="cherry5-is__popup-sidebar cherry-scroll">
				<?php echo $sidebar_list ?>
			</div>
			<div class="cherry5-is__popup-section">
				<div class="cherry5-is__shortcodes-options cherry-scroll"></div>
				<div class="cherry5-is__popup-footer">
					<?php echo $insert_button ?>
				</div>
			</div>
		</div>
	</div>
	<div class="cherry5-is__background"></div>
</div>
