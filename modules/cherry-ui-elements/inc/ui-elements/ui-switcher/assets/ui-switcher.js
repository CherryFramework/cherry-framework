/**
 * Switcher
 */
(function($){
	"use strict";

	CherryJsCore.utilites.namespace('ui_elements.switcher');
	CherryJsCore.ui_elements.switcher = {
		init: function ( target ) {
			var self = this;
			if( CherryJsCore.status.document_ready ){
				self.render( target );
			}else{
				CherryJsCore.variable.$document.on('ready', self.render( target ) );
			}
		},
		render: function ( target ) {

			$('.cherry-switcher-wrap', target).each(function(){
				var
					input = $('.cherry-input-switcher', this)
				,	inputValue = ( input.val() === 'true' )
				,	true_slave = ( typeof input.data('true-slave') != 'undefined' ) ? input.data('true-slave') : null
				,	false_slave = ( typeof input.data('false-slave') != 'undefined' ) ? input.data('false-slave') : null
				;

				if ( ! inputValue ) {
					$('.sw-enable', this).removeClass('selected');
					$('.sw-disable', this).addClass('selected');

					if ( $( '.' + true_slave, target )[0] ) {
						$( '.' + true_slave, target ).hide();
					}
				} else {
					$( '.sw-enable', this ).addClass('selected');
					$( '.sw-disable', this ).removeClass('selected');

					if ( $( '.' + false_slave, target )[0] ) {
						$( '.' + false_slave, target ).hide();
					}
				}
			})

			$('.cherry-switcher-wrap', target).on('click', function () {
				var
					input = $('.cherry-input-switcher', this)
				,	inputValue = ( input.val() === "true" )
				,	true_slave = ( typeof input.data('true-slave') != 'undefined' ) ? input.data('true-slave') : null
				,	false_slave = ( typeof input.data('false-slave') != 'undefined' ) ? input.data('false-slave') : null
				;

				if ( ! inputValue ) {
					$('.sw-enable', this).addClass('selected');
					$('.sw-disable', this).removeClass('selected');
					input.attr('value', true ).trigger('change');
					input.trigger('change');

					if ( $( '.' + true_slave, target )[0] ) {
						$( '.' + true_slave , target ).show();
					}
					if ( $( '.' + false_slave, target )[0] ){
						$( '.' + false_slave, target ).hide();
					}

					input.trigger('switcher_enabled_event', [true_slave, false_slave]);
				} else {
					$('.sw-disable', this).addClass('selected');
					$('.sw-enable', this).removeClass('selected');
					input.attr('value', false ).trigger('change');

					if ( $( '.' + true_slave, target)[0] ) {
						$( '.' + true_slave, target).hide();
					}
					if ( $( '.' + false_slave, target )[0] ) {
						$( '.' + false_slave, target ).show();
					}

					input.trigger('switcher_disabled_event', [true_slave, false_slave]);
				}
			})
		}
	}
	$( window ).on( 'cherry-ui-elements-init',
		function( event, data ) {
			CherryJsCore.ui_elements.switcher.init( data.target );
		}
	);
}(jQuery));
