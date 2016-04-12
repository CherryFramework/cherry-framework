<?php
/**
 * POst meta view
 *
 * @package    Cherry_Framework
 */?>
<?php if ( is_array( $__data['elements'] ) && count( $__data['elements'] ) ) : ?>
	<table class="form-table">
		<tbody>
			<?php foreach ( $__data['elements'] as $el ) : ?>
			<tr>
				<?php if ( array_key_exists( 'name', $el['field'] ) && array_key_exists( 'left_label', $el['field'] ) ) : ?>
					<th><label for="<?php echo $el['field']['name']; ?>"><?php echo $el['field']['left_label']; ?></label></th>
				<?php endif; ?>
				<td><?php echo $el['html']; ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif;
