/**
 * Switcher
 */
( function( $, CherryJsCore ){
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.switcher');
	CherryJsCore.ui_elements.switcher = {
		switcherClass:   '.cherry-switcher-wrap',
		inputTrueClass:  '.cherry-input-switcher-true',
		inputFalseClass: '.cherry-input-switcher-false',
		containerClass:  '.cherry-ui-container',
		wrapperClass: '.widget, .postbox, .cherry-form, .cherry-ui-repeater-item',

		init: function () {
			$( document )
				.on( 'ready.cherry-ui-elements-init', this.addEvent.bind( this ) )
				.on( 'cherry-ui-elements-init', this.initState.bind( this ) );
		},
		addEvent: function () {
			$( 'body' ).on( 'click', this.switcherClass, this.switchState.bind( this ) );
			this.initState();
		},
		initState: function(){
			var switchers = $( this.switcherClass ),
				i         = switchers.length - 1,
				switcher;

			for (; i >= 0; i--) {
				switcher = $( switchers[ i ] );

				this.switchState( { currentTarget: switcher, initState: true } );
			}
		},
		switchState: function ( event ) {
			var switcher   = $( event.currentTarget ),
				inputTrue  = $( this.inputTrueClass, switcher ),
				inputFalse = $( this.inputFalseClass, switcher ),
				dataTrue   = inputTrue.data(),
				dataFalse  = inputFalse.data(),
				flag,
				wrapper = switcher.closest( this.wrapperClass );

			if( ! event.initState ){
				this.checkedRadio( inputTrue, inputFalse, inputTrue[ 0 ].checked );
			}

			flag = inputTrue[ 0 ].checked;

			if ( ! jQuery.isEmptyObject( dataTrue ) ) {
				$( '.' + dataTrue.slave, wrapper )[ ( flag ) ? 'removeClass' : 'addClass' ]( 'hide' );
			}
			if ( ! jQuery.isEmptyObject( dataFalse ) ) {
				$( '.' + dataFalse.slave, wrapper )[ ( ! flag ) ? 'removeClass' : 'addClass' ]( 'hide' );
			}
		},
		checkedRadio:function( inputTrue, inputFalse, flag ){
			inputTrue.attr( 'checked', ( flag ) ? false : true );

			inputFalse
				.attr( 'checked', ( ! flag ) ? false : true )
				.trigger('change');
		}
	};

	CherryJsCore.ui_elements.switcher.init();
}( jQuery, window.CherryJsCore ) );
