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
<form class="cherry-form <?php echo $args['class']; ?>" id="<?php echo $args['id']; ?>" name="<?php echo $args['id']; ?>" <?php echo $args['accept-charset'] . $args['action'] . $args['autocomplete'] . $args['enctype'] . $args['method'] . $args['novalidate'] . $args['target']; ?>>
	<?php
		if ( ! empty( $args['children'] ) ) {
			echo $args['children'];
		}
	?>
</form>
