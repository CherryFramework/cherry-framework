/**
 * Radio
 */
(function($, CherryJsCore){
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.radio');
	CherryJsCore.ui_elements.radio = {
		inputClass: '.cherry-radio-input:not([name*="__i__"])',
		containerClass: '.cherry-ui-container',
		wrapperClass: '.widget, .postbox, .cherry-form, .cherry-ui-repeater-item',

		init: function () {
			$( document )
				.on( 'ready.cherry-ui-elements-init', this.addEvent.bind( this ) )
				.on( 'cherry-ui-elements-init', this.setState.bind( this ) );
		},
		addEvent: function () {
			$( 'body' ).on( 'click.masterSlave', this.inputClass, this.switchState.bind( this ) );
			this.setState( { '_target': $( 'body' ) } );
		},
		setState: function ( event ) {
			this.switchState( { 'currentTarget': $( this.inputClass, event._target ) } );
		},
		switchState: function ( event ) {
			var parent   = $( event.currentTarget ).closest( this.containerClass ),
				children = $( this.inputClass, parent ),
				i        = children.length - 1,
				$_target,
				wrapper,
				data;

			for (; i >= 0; i--) {
				$_target = $( children[ i ] );
				data     = $_target.data();
				wrapper  = $_target.closest( this.wrapperClass );

				if ( jQuery.isEmptyObject( data ) ) {
					continue;
				} else {
					$( '.' + data.slave, wrapper )[ ( $_target[ 0 ].checked ) ? 'removeClass' : 'addClass' ]( 'hide' );
				}
			}
		}
	};

	CherryJsCore.ui_elements.radio.init();
}(jQuery, window.CherryJsCore));
