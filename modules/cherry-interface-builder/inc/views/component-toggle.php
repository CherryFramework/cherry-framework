<?php
/**
 * Toggle template.
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
<div class="cherry-ui-kit cherry-component cherry-toggle <?php echo $id; ?> <?php echo $class; ?>" data-compotent-id='#<?php echo $id ?>'>
	<?php if ( ! empty( $title ) ) { ?>
		<h2 class="cherry-ui-kit__title cherry-component__title" role="banner" ><?php echo $title ?></h2>
	<?php } ?>
	<?php if ( ! empty( $description ) ) { ?>
		<div class="cherry-ui-kit__description cherry-component__description" role="note" ><?php echo $description ?></div>
	<?php } ?>
	<?php if ( ! empty( $children ) ) { ?>
		<div class="cherry-ui-kit__content cherry-component__content cherry-toggle__content" role="group" >
			<?php echo $children ?>
		</div>
	<?php } ?>
</div>
