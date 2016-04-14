<?php
/**
 * View for choose icons control
 *
 * @package    Cherry_Framework
 * @subpackage View
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
?>
<div class="ui-choose-icons">
	<div class="ui-choose-icons__title">
		<?php if ( '' != $__data['label'] ) : ?>
			<label for="<?php echo $__data['id']; ?>"><?php echo $__data['label']; ?></label>
		<?php endif; ?>
		<input type="text" name="<?php echo $__data['name']; ?>" class="ui-choose-icons-input <?php echo $__data['class']; ?>" id="<?php echo $__data['id']; ?>" placeholder="<?php echo $__data['placeholder']; ?>" value="<?php echo $__data['value']; ?>" <?php echo $__data['required']; ?>>
	</div>
	<?php if ( count( $__data['icons'] ) ) : ?>
	<div class="ui-choose-icons__content">
		<div class="ui-choose-icons__content__wrap">
			<?php foreach ( $__data['icons'] as $icon ) : ?>
				<a href="#<?php echo $icon['class']; ?>" title="<?php echo $icon['class']; ?>">
					<?php echo $icon['html']; ?>
				</a>
			<?php endforeach; ?>
			<span><?php echo $this->settings['end_text']; ?></span>
		</div>
	</div>
	<?php endif; ?>
</div>
