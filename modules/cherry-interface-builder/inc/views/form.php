<?php
/**
 * Form template.
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
<form class="cherry-form <?php echo esc_attr( $__data['class'] ); ?>" id="<?php echo esc_attr( $__data['id'] ); ?>" name="<?php echo esc_attr( $__data['id'] ); ?>" accept-charset="<?php echo esc_attr( $__data['accept-charset'] ); ?>" action="<?php echo esc_attr( $__data['action'] ); ?>" autocomplete="<?php echo esc_attr( $__data['autocomplete'] ); ?>" enctype="<?php echo esc_attr( $__data['enctype'] ); ?>" method="<?php echo esc_attr( $__data['method'] ); ?>" target="<?php echo esc_attr( $__data['target'] ); ?>" <?php echo esc_attr( $__data['novalidate'] ); ?> >
	<?php
		if ( ! empty( $__data['children'] ) ) {
			echo $__data['children'];
		}
	?>
</form>
