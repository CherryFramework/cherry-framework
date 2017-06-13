<?php
/**
 * HTML template.
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
<div class="cherry-ui-kit <?php echo esc_attr( $__data['class'] ); ?>">
	<?php if ( ! empty( $__data['children'] ) ) { ?>
		<div class="cherry-ui-kit__content" role="group" >
			<?php echo $__data['children']; ?>
		</div>
	<?php } ?>
</div>
