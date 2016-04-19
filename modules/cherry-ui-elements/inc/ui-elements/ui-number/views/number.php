<?php
/**
 * Number view
 *
 * @package    Cherry_Framework
 * @subpackage View
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
?>
<div class="<?php echo $__data['master']; ?>">
	<?php if ( '' !== $__data['label'] ) : ?>
		<label class="cherry-label" for="<?php echo esc_attr( $__data['atts']['id'] ) ?>"><?php echo esc_html( $__data['atts']['label'] ) ?></label>
	<?php endif; ?>
	<input <?php echo $__data['atts_str']; ?> >
</div>
