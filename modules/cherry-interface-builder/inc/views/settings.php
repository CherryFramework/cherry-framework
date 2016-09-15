<?php
/**
 * Settings template.
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
<div class="cherry-ui-kit cherry-settings <?php echo $args['class']; ?>">
	<?php if ( ! empty( $args['title'] ) ) {
		echo $args['title'];
	} ?>
	<?php if ( ! empty( $args['children'] ) || ! empty( $args['description'] ) ) { ?>
		<div class="cherry-ui-kit__content cherry-settings__content" role="group" id="<?php echo $args['id']; ?>"  >
			<?php if ( ! empty( $args['description'] ) ) { ?>
				<div class="cherry-ui-kit__description cherry-settings__description" role="note" ><?php echo $args['description']; ?></div>
			<?php } ?>
			<?php if ( ! empty( $args['children'] ) ) { ?>
				<?php echo $args['children']; ?>
			<?php } ?>
		</div>
	<?php } ?>
</div>
