<?php
/**
 * View for options page
 *
 * @package  TM Real Estate
 * @author   Guriev Eugen & Sergyj Osadchij
 * @license  GPL-2.0+
 */
?>
<div class="wrap cherry-settings-page">
	<h2><?php echo $__data['title'] ?></h2>
	<?php if ( ! empty( $__data['page_before'] ) ) : ?>
	<div class="description"><?php echo $__data['page_before'] ?></div>
	<?php endif; ?>
	<?php if ( ! empty( $__data['sections'] ) && is_array( $__data['sections'] ) ) : ?>
	<div class="cherry-settings-tabs">
		<h2 class="nav-tab-wrapper tabs-section">
			<?php foreach ( $__data['sections'] as $section_slug => $section ) : ?>
			<a href="#<?php echo $section_slug ?>" class="nav-tab"><?php echo $section['name'] ?></a>
			<?php endforeach; ?>
		</h2>

		<?php foreach ( $__data['sections'] as $section_slug => $section ) : ?>
		<div id="<?php echo $section_slug ?>" class="section">
			<form method="POST" action="options.php" id="form-<?php echo $section_slug ?>">
				<?php settings_fields( $section_slug ); ?>
				<?php do_settings_sections( $section_slug ); ?>

				<?php if ( ! empty( $__data['button_before'] ) ) : ?>
				<?php echo $__data['button_before'] ?>
				<?php endif; ?>

				<?php submit_button( 'Save ' . $section['name'], 'primary small', null, true, array( 'data-ajax' => true ) ); ?>

				<?php if ( ! empty( $__data['button_after'] ) ) : ?>
				<?php echo $__data['button_after'] ?>
				<?php endif; ?>
			</form>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<?php if ( ! empty( $__data['page_after'] ) ) : ?>
	<div class="description"><?php echo $__data['page_after'] ?></div>
	<?php endif; ?>
</div>
