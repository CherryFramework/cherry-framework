<?php
/**
 * Colection view
 *
 * @package    Cherry_Framework
 */?>
<div class="cherry-infinite-container">
	<table class="cherry-infinite">
		<tbody class="cherry-infinite-sortable">

		<?php foreach ( $__data['rendered_controls'] as $row => $elements ) : ?>
			<tr class="cherry-infinite-row">
				<td class="cherry-infinite-order"><span><?php echo $row + 1 ?></span></td>
				<td class="cherry-infinite-inner">
					<table>
						<tbody>
							<?php if ( is_array( $elements ) && count( $elements ) ) : ?>
								<?php foreach ( $elements as $el ) : ?>
									<tr>
										<th><label for="<?php echo $el['args']['name']; ?>"><?php echo $el['args']['left_label']; ?></label></th>
										<td><?php echo $el['html']; ?></td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</td>
				<td class="cherry-infinite-options">
					<span class="cherry-infinite-add"></span>
					<span class="cherry-infinite-remove"></span>
				</td>
			</tr>
		<?php endforeach; ?>

		</tbody>
	</table>
	<?php if ( isset( $__data['info'] ) ) : ?>
		<div class="cherry-field-info">
			<p><?php echo $__data['info'] ?></p>
		</div>
	<?php endif; ?>
	<div class="cherry-infinite-add-field-container">
		<button type="button" class="button-primary cherry-infinite-main-add"><?php echo $this->settings['button_label'] ?></button>
	</div>
</div>
