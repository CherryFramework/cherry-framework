<?php
/**
 * Settings template.
 *
 * @package    Cherry_Interface_Builder
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
<div class="cherry-ui-kit cherry-settings <?php echo esc_attr( $__data['class'] ); ?>">
	<?php if ( ! empty( $__data['title'] ) ) {
		echo $__data['title'];
	} ?>
	<?php if ( ! empty( $__data['children'] ) || ! empty( $__data['description'] ) ) { ?>
		<div class="cherry-ui-kit__content cherry-settings__content" role="group" id="<?php echo esc_attr( $__data['id'] ); ?>"  >
			<?php if ( ! empty( $__data['description'] ) ) { ?>
				<div class="cherry-ui-kit__description cherry-settings__description" role="note" ><?php echo wp_kses_post( $__data['description'] ); ?></div>
			<?php } ?>
			<?php if ( ! empty( $__data['children'] ) ) { ?>
				<?php echo $__data['children']; ?>
			<?php } ?>
		</div>
	<?php } ?>
</div>
