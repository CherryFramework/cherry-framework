/**
 * Checkbox
 */
(function($){
	"use strict";

	CherryJsCore.utilites.namespace('ui_elements.checkbox');
	CherryJsCore.ui_elements.checkbox = {
		init: function ( target ) {
			var self = this;

			if ( CherryJsCore.status.document_ready ) {
				self.render( target );
			} else {
				CherryJsCore.variable.$document.on(' ready', self.render( target ) );
			}
		},
		render: function ( target ) {
			$( '.cherry-checkbox-input[type="hidden"]', target ).each( function() {
				var $this = $( this ),
					this_slave = $this.data( 'slave' ),
					state = ( $this.val() === 'true' );

				if ( ! state ) {
					$( '.'+ this_slave, target ).stop().hide();
				}
			})

			$( '.cherry-checkbox-item', target ).on( 'click', function( event ) {
				var input = $( this ).siblings( '.cherry-checkbox-input[type="hidden"]' ),
					slave = input.data( 'slave' ),
					state = ( input.val() === 'true' );

				if ( $( this ).hasClass( 'checked' ) ) {
					$( this ).removeClass( 'checked' );
					input.val( 'false' );
					state = false;

					$( '.' + slave, target ).hide();
				} else {
					$( this ).addClass( 'checked' );
					input.val( 'true' );
					state = true;

					$( '.' + slave, target ).show();
				}
				//input.trigger( 'checkbox_change_event', [slave, state] );
				input.trigger( 'change' );
			} );

			$( '.cherry-checkbox-label', target ).on( 'click', function( event ) {
				var input = $( this ).siblings( '.cherry-checkbox-input[type="hidden"]' ),
					item = $( this ).siblings( '.cherry-checkbox-item' ),
					slave = input.data( 'slave' ),
					state = ( input.val() === 'true' );

				if ( item.hasClass( 'checked' ) ) {
					item.removeClass( 'checked' );
					input.val( 'false' );
					state = false;

					$( '.' + slave, target ).hide();
				} else {
					item.addClass( 'checked' );
					input.val( 'true' );
					state = true;

					$( '.' + slave, target ).show();
				}
				//input.trigger( 'checkbox_change_event', [slave, state] );
				input.trigger( 'change' );
			} );
		}
	}
	$( window ).on( 'cherry-ui-elements-init',
		function( event, data ) {
			CherryJsCore.ui_elements.checkbox.init( data.target );
		}
	);
}(jQuery));
