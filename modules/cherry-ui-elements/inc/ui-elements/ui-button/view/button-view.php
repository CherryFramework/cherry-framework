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

$attrs = Cherry_Toolkit::join(
	array(
		'type'       => $__data['type'],
		'id'         => $__data['id'],
		'name'       => $__data['name'],
		'class'      => 'ui-button ui-button-' . $__data['style'] . '-style ' . $__data['master'] . $__data['class'],
		'disabled'   => filter_var( $__data['disabled'], FILTER_VALIDATE_BOOLEAN ),
		'form'       => $__data['form'],
		'formaction' => $__data['formaction'],
	)
);
?>

<button <?php echo $attrs; ?>><?php echo $__data['content']; ?></button>
