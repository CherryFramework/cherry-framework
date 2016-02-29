/**
 * Select
 */
(function($){
	"use strict";

	CherryJsCore.utilites.namespace('ui_elements.select');
	CherryJsCore.ui_elements.select = {
		init: function ( target ) {
			var self = this;
			if ( CherryJsCore.status.document_ready ) {
				self.render( target );
			} else {
				CherryJsCore.variable.$document.on('ready', self.render( target ) );
			}
		},
		render: function ( target ) {
			// init filter-select
			$( '.cherry-ui-select[data-filter="true"]', target ).each(function(){
				var $this = $(this),
					placeholder = $this.attr('placeholder');

				$this.select2({
					placeholder: placeholder
				});
			});
			// init multi-select
			$( '.cherry-ui-select[multiple="multiple"]', target ).each(function(){
				var $this = $(this),
					placeholder = $this.attr('placeholder');

				$this.select2({
					placeholder: placeholder
				});
			});
		}
	}
	$( window ).on( 'cherry-ui-elements-init',
		function( event, data ) {
			CherryJsCore.ui_elements.select.init( data.target );
		}
	);
}(jQuery));
