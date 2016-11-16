/**
 * Checkbox
 */
( function( $, CherryJsCore ) {
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.checkbox');
	CherryJsCore.ui_elements.checkbox = {
		inputClass: '.cherry-checkbox-input[type="hidden"]:not([name*="__i__"])',
		labelClass: '.cherry-checkbox-label, .cherry-checkbox-item',

		init: function() {
			$( document )
				.on( 'ready.cherry-ui-elements-init', this.addEvent.bind( this ) )
				.on( 'cherry-ui-elements-init', this.initState.bind( this ) );
		},
		addEvent: function() {
			$( 'body' ).on( 'click.masterSlave', this.labelClass, this.switchState.bind( this ) );
			this.initState();
		},
		initState: function(){
			var $_input = $( this.inputClass ),
				i       = $_input.length - 1,
				$_target,
				data,
				wrapper;

			for ( ; i >= 0; i-- ) {
				$_target = $( $_input[ i ] );
				data     = $_target.data();
				wrapper   = $_target.closest('form');

				if ( jQuery.isEmptyObject( data ) ) {
					continue;
				} else {
					$( '.' + data.slave, wrapper )[ ( $_target[0].checked ) ? 'removeClass' : 'addClass' ]( 'hide' );
				}
			}
		},
		switchState: function( event ) {
			var $_input = $( event.currentTarget ).siblings( this.inputClass ),
				data    = $_input.data(),
				flag    = $_input[0].checked,
				wrapper  = $_input.closest('form');

			$_input
				.val( ( flag ) ? 'false' : 'true' )
				.attr( 'checked', ( flag ) ? false : true )
				.trigger( 'change' );

			if ( ! jQuery.isEmptyObject( data ) ) {
				$( '.' + data.slave, wrapper )[ ( flag ) ? 'addClass' : 'removeClass' ]( 'hide' );
			}
		}
	};

	CherryJsCore.ui_elements.checkbox.init();
} ( jQuery, window.CherryJsCore ) );
