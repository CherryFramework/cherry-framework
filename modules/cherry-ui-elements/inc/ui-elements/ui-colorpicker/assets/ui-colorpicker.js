/**
 * ColorPicker
 */
(function($){
	"use strict";

	CherryJsCore.utilites.namespace('ui_elements.colorpicker');
	CherryJsCore.ui_elements.colorpicker = {
		init: function ( target ) {
			var self = this;
			if( CherryJsCore.status.document_ready ){
				self.render( target );
			}else{
				CherryJsCore.variable.$document.on('ready', self.render( target ) );
			}
		},
		render: function ( target ) {
			if($('.cherry-ui-colorpicker', target)[0]){
				$('.cherry-ui-colorpicker', target).wpColorPicker();
			}
		}
	}
	$( window ).on( 'cherry-ui-elements-init',
		function( event, data ) {
			CherryJsCore.ui_elements.colorpicker.init( data.target );
		}
	);
}(jQuery));
