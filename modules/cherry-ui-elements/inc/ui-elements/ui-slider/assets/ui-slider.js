/**
 * Slider
 */
( function( $, CherryJsCore ) {
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.slider');
	CherryJsCore.ui_elements.slider = {
		init: function ( target ) {
			var self = this;

			if( CherryJsCore.status.document_ready ){
				self.render( target );
			}else{
				CherryJsCore.variable.$document.on('ready', self.render( target ) );
			}
		},
		render: function ( target ) {
			target.on( 'input change', '.cherry-slider-unit', function() {
				var $this = $( this ),
					$sliderWrapper = $this.closest('.cherry-slider-wrap');

				$( '.cherry-ui-stepper-input', $sliderWrapper ).val( $this.val() );
			} );
			target.on( 'change', '.cherry-ui-stepper-input', function() {
				var $this = $( this ),
					$sliderWrapper = $this.closest('.cherry-slider-wrap');

				$( '.cherry-slider-unit', $sliderWrapper ).val( $this.val() );
			} );
		}
	};
	$( window ).on( 'cherry-ui-elements-init',
		function( event, data ) {
			CherryJsCore.ui_elements.slider.init( data.target );
		}
	);
}( jQuery, window.CherryJsCore ) );
