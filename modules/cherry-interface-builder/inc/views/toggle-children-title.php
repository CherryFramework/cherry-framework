<?php
/**
 * Toggle title template.
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
<button class="cherry-toggle__header cherry-component__button" role="button" aria-expanded="false" data-content-id="#<?php echo esc_attr( $__data['id'] ); ?>">
	<h3 class="cherry-ui-kit__title cherry-toggle__title" aria-grabbed="true" role="banner" ><?php echo wp_kses_post( $__data['title'] ); ?></h3>
	<span class="dashicons dashicons-arrow-down hide-icon"></span>
	<span class="dashicons dashicons-arrow-up show-icon"></span>
</button>
