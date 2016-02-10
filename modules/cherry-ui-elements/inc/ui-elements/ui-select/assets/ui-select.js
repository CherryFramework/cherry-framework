/**
 * Select
 */
(function($){
	"use strict";

	CHERRY_API.utilites.namespace('ui_elements.select');
	CHERRY_API.ui_elements.select = {
		init: function ( target ) {
			var self = this;
			if ( CHERRY_API.status.document_ready ) {
				self.render( target );
			} else {
				CHERRY_API.variable.$document.on('ready', self.render( target ) );
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
			CHERRY_API.ui_elements.select.init( data.target );
		}
	);
}(jQuery));
