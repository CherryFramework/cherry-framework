/**
 * ColorPicker
 */
( function( $, CherryJsCore ) {
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.colorpicker');
	CherryJsCore.ui_elements.colorpicker = {
		init: function () {
			$( document )
				.on( 'ready', this.render )
				.on( 'cherry-ui-elements-init', this.render );
		},
		render: function ( event ) {
			var target = ( event._target ) ? event._target : $( 'body' ),
				input = $( 'input.cherry-ui-colorpicker:not([name*="__i__"])', target );

			if ( input[0] ) {
				input.wpColorPicker();
			}
		}
	};

	CherryJsCore.ui_elements.colorpicker.init();

}( jQuery, window.CherryJsCore ));
