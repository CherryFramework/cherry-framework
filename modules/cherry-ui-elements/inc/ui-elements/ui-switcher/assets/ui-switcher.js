/**
 * Switcher
 */
(function($){
	"use strict";

	CHERRY_API.utilites.namespace('ui_elements.switcher');
	CHERRY_API.ui_elements.switcher = {
		init: function ( target ) {
			var self = this;
			if( CHERRY_API.status.document_ready ){
				self.render( target );
			}else{
				CHERRY_API.variable.$document.on('ready', self.render( target ) );
			}
		},
		render: function ( target ) {

			$('.cherry-switcher-wrap', target).each(function(){
				var
					input = $('.cherry-input-switcher', this)
				,	inputValue = ( input.val() === "true" )
				;

				if( !inputValue ){
					$('.sw-enable', this).removeClass('selected');
					$('.sw-disable', this).addClass('selected');

				}else{
					$('.sw-enable', this).addClass('selected');
					$('.sw-disable', this).removeClass('selected');
				}
			})

			$('.cherry-switcher-wrap', target).on('click', function () {
				var
					input = $('.cherry-input-switcher', this)
				,	inputValue = ( input.val() === "true" )
				,	true_slave = ( typeof input.data('true-slave') != 'undefined' ) ? input.data('true-slave') : null
				,	false_slave = ( typeof input.data('false-slave') != 'undefined' ) ? input.data('false-slave') : null
				;

				if( !inputValue ){
					$('.sw-enable', this).addClass('selected');
					$('.sw-disable', this).removeClass('selected');
					input.attr('value', true ).trigger('change');
					input.trigger('change');

					input.trigger('switcher_enabled_event', [true_slave, false_slave]);
				}else{
					$('.sw-disable', this).addClass('selected');
					$('.sw-enable', this).removeClass('selected');
					input.attr('value', false ).trigger('change');

					input.trigger('switcher_disabled_event', [true_slave, false_slave]);
				}
			})
		}
	}
	$( window ).on( 'cherry-ui-elements-init',
		function( event, data ) {
			CHERRY_API.ui_elements.switcher.init( data.target );
		}
	);
}(jQuery));
