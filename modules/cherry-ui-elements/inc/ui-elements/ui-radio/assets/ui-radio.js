/**
 * Radio
 */
(function($, CherryJsCore){
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.radio');
	CherryJsCore.ui_elements.radio = {
		inputClass: '.cherry-radio-input',
		containerClass: '.cherry-ui-container',

		init: function () {
			$( document ).on( 'ready.cherry-ui-elements-init', this.addEvent.bind( this ) );
			this.switchState( { currentTarget: $( this.inputClass ) } );
		},
		addEvent: function () {
			$( 'body' ).on( 'click.masterSlave', this.inputClass, this.switchState.bind( this ) );
		},
		switchState: function ( event ) {
			var parent        = $( event.currentTarget ).closest( this.containerClass ),
				children      = $( this.inputClass, parent ),
				i             = children.length - 1,
				$_target,
				data;

			for (; i >= 0; i--) {
				$_target = $( children[ i ] );
				data     = $_target.data();

				if ( jQuery.isEmptyObject( data ) ) {
					continue;
				} else {
					$( '.' + data.slave )[ ( $_target[ 0 ].checked ) ? 'removeClass' : 'addClass' ]( 'hide' );
				}
			}
		}
	};

	CherryJsCore.ui_elements.radio.init();
}(jQuery, window.CherryJsCore));
