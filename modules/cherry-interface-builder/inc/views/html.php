<?php
/**
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
<div class="cherry-ui-kit <?php echo $id; ?> <?php echo $class; ?>">
	<?php if ( ! empty( $children ) ) { ?>
		<div class="cherry-ui-kit__content" role="group" >
			<?php echo $children ?>
		</div>
	<?php } ?>
</div>
