/**
 * Slider
 */
( function( $, CherryJsCore ) {
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.slider');
	CherryJsCore.ui_elements.slider = {
		init: function () {
			$( document ).on( 'ready.cherry-ui-elements-init', this.render.bind( this ) );
		},
		render: function () {
			$( 'body' ).on( 'input.cherry-ui-elements-init change.cherry-ui-elements-init', '.cherry-slider-unit, .cherry-ui-stepper-input', this.changeHandler );
		},
		changeHandler: function () {
			var $this = $( this ),
				targetClass = ( ! $this.hasClass('cherry-slider-unit') ) ? '.cherry-slider-unit' : '.cherry-ui-stepper-input' ,
				$sliderWrapper = $this.closest('.cherry-slider-wrap');

			$( targetClass, $sliderWrapper ).val( $this.val() );
		}
	};

	CherryJsCore.ui_elements.slider.init();
}( jQuery, window.CherryJsCore ) );
