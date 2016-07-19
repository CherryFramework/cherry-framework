/**
 * Switcher
 */
( function( $, CherryJsCore ){
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.switcher');
	CherryJsCore.ui_elements.switcher = {
		init: function () {
			$( document ).on( 'ready', this.render.bind( this ) );
			$( window ).on( 'cherry-ui-elements-init', this.master_slave_init );
		},
		render: function ( event, data ) {
			$( 'body' ).on( 'click', '.cherry-switcher-wrap', this.swiperHandler );
		},
		master_slave_init: function ( event, data ) {
			var target = ( event._target ) ? event._target : $( 'body' );

			$( '.cherry-switcher-wrap', target ).each( function() {
				var $this = $( this ),
					$input = $( '.cherry-input-switcher', $this ),
					inputValue = ( $input.val() === 'true' ),
					true_slave = ( typeof $input.data('true-slave') !== 'undefined' ) ? $input.data( 'true-slave' ) : null,
					false_slave = ( typeof $input.data('false-slave') !== 'undefined' ) ? $input.data( 'false-slave' ) : null;

				if ( ! inputValue ) {
					if ( $( '.' + true_slave, target )[0] ) {
						$( '.' + true_slave, target ).hide();
					}
				} else {
					if ( $( '.' + false_slave, target )[0] ) {
						$( '.' + false_slave, target ).hide();
					}
				}
			});
		},
		swiperHandler: function ( event ) {
			var $this = $( this ),
				$input = $( '.cherry-input-switcher', $this ),
				true_slave = $input.data('true-slave'),
				false_slave = $input.data('false-slave');

			$this.toggleClass('selected');

			$input
				.attr( 'value', ( $input.val() === 'true' ) ? false : true )
				.trigger( 'change' )
				.trigger( 'switcher_disabled_event', [ true_slave, false_slave ] );


			$( '.' + true_slave  ).toggle();
			$( '.' + false_slave ).toggle();
		}
	};

	CherryJsCore.ui_elements.switcher.init();
}( jQuery, window.CherryJsCore ) );
