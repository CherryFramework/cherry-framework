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
<div class="cherry-ui-kit cherry-control <?php echo $args['class']; ?>">
	<?php if ( ! empty( $args['title'] ) || ! empty( $args['description'] ) ) { ?>
		<div class="cherry-control__info">
			<?php if ( ! empty( $args['title'] ) ) { ?>
				<h4 class="cherry-ui-kit__title cherry-control__title" role="banner" ><?php echo $args['title']; ?></h4>
			<?php } ?>
			<?php if ( ! empty( $args['description'] ) ) { ?>
				<div class="cherry-ui-kit__description cherry-control__description" role="note" ><?php echo $args['description']; ?></div>
			<?php } ?>
		</div>
	<?php } ?>
	<?php if ( ! empty( $args['children'] ) ) { ?>
		<div class="cherry-ui-kit__content cherry-control__content" role="group" >
			<?php echo $args['children']; ?>
		</div>
	<?php } ?>
</div>
