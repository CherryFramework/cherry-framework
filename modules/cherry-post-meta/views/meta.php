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
				<th><label for="<?php echo $el['field']['name']; ?>"><?php echo $el['field']['left_label']; ?></label></th>
				<td><?php echo $el['html']; ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif;
