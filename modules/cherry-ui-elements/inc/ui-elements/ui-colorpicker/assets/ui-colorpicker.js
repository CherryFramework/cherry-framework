/**
 * ColorPicker
 */
(function($){
	"use strict";

	CHERRY_API.utilites.namespace('ui_elements.colorpicker');
	CHERRY_API.ui_elements.colorpicker = {
		init: function ( target ) {
			var self = this;
			if( CHERRY_API.status.document_ready ){
				self.render( target );
			}else{
				CHERRY_API.variable.$document.on('ready', self.render( target ) );
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
			CHERRY_API.ui_elements.colorpicker.init( data.target );
		}
	);
}(jQuery));
