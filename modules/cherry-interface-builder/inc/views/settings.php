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
<div class="cherry-ui-kit cherry-settings <?php echo $id; ?> <?php echo $class; ?>">
	<?php if ( ! empty( $title ) ) {
		echo $title;
	} ?>
	<?php if ( ! empty( $children ) || ! empty( $description ) ) { ?>
		<div class="cherry-ui-kit__content cherry-settings__content" role="group" id="<?php echo $id ?>"  >
			<?php if ( ! empty( $description ) ) { ?>
				<div class="cherry-ui-kit__description cherry-settings__description" role="note" ><?php echo $description ?></div>
			<?php } ?>
			<?php if ( ! empty( $children ) ) { ?>
				<?php echo $children ?>
			<?php } ?>
		</div>
	<?php } ?>
</div>
