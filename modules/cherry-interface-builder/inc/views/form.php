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
<form class="cherry-form <?php echo $__data['class']; ?>" id="<?php echo $__data['id']; ?>" name="<?php echo $__data['id']; ?>" <?php echo $__data['accept-charset'] . $__data['action'] . $__data['autocomplete'] . $__data['enctype'] . $__data['method'] . $__data['novalidate'] . $__data['target']; ?>>
	<?php
		if ( ! empty( $__data['children'] ) ) {
			echo $__data['children'];
		}
	?>
</form>
