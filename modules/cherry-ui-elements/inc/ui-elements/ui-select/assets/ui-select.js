/**
 * Select
 */
( function( $, CherryJsCore ){
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.select');
	CherryJsCore.ui_elements.select = {
		selectClass: '.cherry-ui-select[data-filter="true"]:not([name*="__i__"]), .cherry-ui-select[multiple]:not([name*="__i__"])',
		wrapperClass: '.widget, .postbox, .cherry-form, .cherry-ui-repeater-item',

		init: function () {
			$( document )
				.on( 'ready.cherry-ui-elements-init', this.render.bind( this ) )
				.on( 'cherry-ui-elements-init', this.render.bind( this ) );
		},
		render: function ( event ) {
			var target = ( event._target ) ? event._target : $( 'body' );

			// init filter-select
			$( this.selectClass , target ).each( this.select2Init.bind( this ) );
		},
		select2Init: function ( index, element ) {
			var $this   = $( element ),
				options = {
					placeholder: $this.attr('placeholder')
				};

			$this
				.select2( options )
				.on('change.cherrySelect2', this.changeEvent.bind( this ) )
				.trigger('change.cherrySelect2');
		},
		changeEvent: function ( event ) {
			this.switchState( event.currentTarget );
		},
		switchState: function ( item ) {
			var items = $( item ),
				i    = items[0].length,
				option,
				data,
				wrapper;

			for (; i >= 0; i--) {
				option  = $( items[0][ i ] );
				data    = option.data();
				wrapper = $( items[0] ).closest( this.wrapperClass );

				if ( jQuery.isEmptyObject( data ) ) {
					continue;
				} else {
					$( '.' + data.slave, wrapper )[ ( option[ 0 ].selected ) ? 'removeClass' : 'addClass' ]( 'hide' );
				}
			}

		}
	};

	CherryJsCore.ui_elements.select.init();

}( jQuery, window.CherryJsCore ) );
