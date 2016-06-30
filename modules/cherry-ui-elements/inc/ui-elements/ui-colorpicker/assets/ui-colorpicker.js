/**
 * ColorPicker
 */
( function( $, CherryJsCore ) {
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.colorpicker');
	CherryJsCore.ui_elements.colorpicker = {
		init: function () {
			$( window ).on( 'cherry-ui-elements-init', this.render.bind( this ) );
		},
		render: function ( event, data ) {
			var target = data.target,
				input = $( 'input.cherry-ui-colorpicker:not([name*="__i__"])', target );

			if ( input[0] ) {
				input.wpColorPicker();
			}
		}
	};

	CherryJsCore.ui_elements.colorpicker.init();

}( jQuery, window.CherryJsCore ));
