/**
 * Switcher
 */
( function( $, CherryJsCore ){
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.switcher');
	CherryJsCore.ui_elements.switcher = {
		init: function () {
			$( window ).on( 'cherry-ui-elements-init', this.render.bind( this ) );
		},
		render: function ( event, data ) {
			var target = data.target;

			$( '.cherry-switcher-wrap', target ).each( function() {
				var $this = $( this ),
					$input = $( '.cherry-input-switcher', $this ),
					inputValue = ( $input.val() === 'true' ),
					true_slave = ( typeof $input.data('true-slave') !== 'undefined' ) ? $input.data( 'true-slave' ) : null,
					false_slave = ( typeof $input.data('false-slave') !== 'undefined' ) ? $input.data( 'false-slave' ) : null;

				if ( ! inputValue ) {
					$this.removeClass('selected');

					if ( $( '.' + true_slave, target )[0] ) {
						$( '.' + true_slave, target ).hide();
					}
				} else {
					$this.addClass('selected');

					if ( $( '.' + false_slave, target )[0] ) {
						$( '.' + false_slave, target ).hide();
					}
				}
			});

			$( '.cherry-switcher-wrap', target ).on( 'click', { target: target }, this.swiperHandler );
		},
		swiperHandler: function ( event ) {
			var $this = $( this ),
				$input = $( '.cherry-input-switcher', $this ),
				true_slave = $input.data('true-slave'),
				false_slave = $input.data('false-slave'),
				target = event.data.target;

			$this.toggleClass('selected');

			$input
				.attr( 'value', ( $input.val() === 'true' ) ? false : true )
				.trigger( 'change' )
				.trigger( 'switcher_disabled_event', [ true_slave, false_slave ] );


			$( '.' + true_slave , target ).toggle();
			$( '.' + false_slave, target ).toggle();
		}
	};

	CherryJsCore.ui_elements.switcher.init();
}( jQuery, window.CherryJsCore ) );
