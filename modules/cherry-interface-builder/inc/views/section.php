<?php
/**
 * Section template.
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
<div class="cherry-ui-kit cherry-section <?php echo $args['class']; ?>" onclick="void(0)">
	<div class="cherry-section__holde">
		<div class="cherry-section__inner">
			<?php if ( ! empty( $args['title'] ) ) { ?>
				<h1 class="cherry-ui-kit__title cherry-section__title" role="banner" ><?php echo $args['title']; ?></h1>
			<?php } ?>
			<?php if ( ! empty( $args['description'] ) ) { ?>
				<div class="cherry-ui-kit__description cherry-section__description " role="note" ><?php echo $args['description']; ?></div>
			<?php } ?>
			<?php if ( ! empty( $args['children'] ) ) { ?>
				<div class="cherry-ui-kit__content cherry-section__content" role="group" >
					<?php echo $args['children']; ?>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
