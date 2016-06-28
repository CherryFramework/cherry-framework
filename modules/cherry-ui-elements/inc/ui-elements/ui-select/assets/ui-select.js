/**
 * Select
 */
( function( $, CherryJsCore ){
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.select');
	CherryJsCore.ui_elements.select = {
		init: function () {
			$( document ).on('ready', this.ready );
			//$( document ).on('ready', this.render.bind( this, { target: $( 'body' ) } ) );
			$( window ).on( 'cherry-ui-elements-init', this.render.bind( this ) );
		},
		ready: function ( event, data ) {
			if ( CherryJsCore.variable.ui_auto_init ) {
				CherryJsCore.variable.ui_auto_target.forEach( function( target ) {
					CherryJsCore.variable.$window.trigger( 'cherry-ui-elements-init', { 'target': $( target ) } );
				});
			}
		},
		render: function ( event, data ) {
			var target = data.target;

			// init filter-select
			$( '.cherry-ui-select[data-filter="true"], .cherry-ui-select[multiple]', target ).each( function() {
				var $this = $( this );

				$this.select2( {
					placeholder: $this.attr('placeholder')
				} );
			} );
		}
	};

	CherryJsCore.ui_elements.select.init();

}( jQuery, window.CherryJsCore ) );
