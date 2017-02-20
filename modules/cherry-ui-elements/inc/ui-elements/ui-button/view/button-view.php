<?php
/**
 * Ui-Button view
 *
 * @package    Cherry_UI_Elements
 * @subpackage View
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$class = trim( implode( ' ', array( 'cherry5-ui-button', 'cherry5-ui-button-' . $__data['style'] . '-style ', $__data['master'], $__data['class'], 'ui-button' ) ) );
$attrs = Cherry_Toolkit::join(
	array(
		'type'       => esc_attr( $__data['button_type'] ),
		'id'         => esc_attr( $__data['id'] ),
		'name'       => esc_attr( $__data['name'] ),
		'class'      => esc_attr( $class ),
		'disabled'   => filter_var( $__data['disabled'], FILTER_VALIDATE_BOOLEAN ),
		'form'       => esc_attr( $__data['form'] ),
		'formaction' => esc_attr( $__data['formaction'] ),
	)
);
?>

<button <?php echo $attrs; ?>><?php echo $__data['content']; ?></button>
