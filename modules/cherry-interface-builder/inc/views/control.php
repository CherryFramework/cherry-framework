<?php
/**
 * Control template.
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
<div class="cherry-ui-kit cherry-control <?php echo $id; ?> <?php echo $class; ?>">
	<?php if ( ! empty( $title ) || ! empty( $description ) ) { ?>
		<div class="cherry-control__info">
			<?php if ( ! empty( $title ) ) { ?>
				<h4 class="cherry-ui-kit__title cherry-control__title" role="banner" ><?php echo $title ?></h4>
			<?php } ?>
			<?php if ( ! empty( $description ) ) { ?>
				<div class="cherry-ui-kit__description cherry-control__description" role="note" ><?php echo $description ?></div>
			<?php } ?>
		</div>
	<?php } ?>
	<?php if ( ! empty( $children ) ) { ?>
		<div class="cherry-ui-kit__content cherry-control__content" role="group" >
			<?php echo $children ?>
		</div>
	<?php } ?>
</div>
