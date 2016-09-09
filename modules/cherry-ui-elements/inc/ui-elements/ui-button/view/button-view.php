<?php
/**
 * Ui-Button view
 *
 * @package    Cherry_Framework
 * @subpackage View
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

$style      = ! empty( $__data['style'] ) ? esc_attr( ' ui-button-' . $__data['style'] . '-style' ) : '';
$class      = ! empty( $__data['class'] ) ? esc_attr( ' ' . $__data['class'] ) : '';
$disabled   = filter_var( $__data['disabled'], FILTER_VALIDATE_BOOLEAN ) ? ' disabled="true"' : '';
$form       = ! empty( $__data['form'] ) ? esc_attr( ' form="' . $this->settings['class'] . '"'  ) : '';
$formaction = ! empty( $__data['formaction'] ) ? esc_attr( ' formaction="' . $this->settings['formaction'] . '"'  ) : '';
?>

<button type="<?php echo $__data['type']; ?>" id="<?php echo $__data['id']; ?>" name="<?php echo $__data['name']; ?>" class="ui-button<?php echo $style; ?>"<?php echo $disabled; echo $form; echo $formaction; ?>><?php echo $__data['content']; ?></button>
