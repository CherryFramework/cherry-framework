/**
 * Select
 */
( function( $, CherryJsCore ){
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.select');
	CherryJsCore.ui_elements.select = {
		init: function () {
			$( document ).on( 'ready', this.render );
			$( window ).on( 'cherry-ui-elements-init', this.render );
		},
		render: function ( event, data ) {
			var target = ( event._target ) ? event._target : $( 'body' );

			// init filter-select
			$( '.cherry-ui-select[data-filter="true"]:not([name*="__i__"]), .cherry-ui-select[multiple]:not([name*="__i__"])', target ).each( function() {
				var $this = $( this );

				$this.select2( {
					placeholder: $this.attr('placeholder')
				} );
			} );
		}
	};

	CherryJsCore.ui_elements.select.init();

}( jQuery, window.CherryJsCore ) );
