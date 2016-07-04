/**
 * Select
 */
( function( $, CherryJsCore ){
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.select');
	CherryJsCore.ui_elements.select = {
		init: function () {
			$( window ).on( 'cherry-ui-elements-init', this.render.bind( this ) );
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
