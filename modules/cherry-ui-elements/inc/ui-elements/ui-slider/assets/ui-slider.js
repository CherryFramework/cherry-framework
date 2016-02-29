/**
 * Slider
 */
(function($){
	"use strict";

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

			var
				sliderSelector = $( ".cherry-slider-unit", target )
			;
			sliderSelector.slider({
				range: "min",
				animate: true,
				create: function( event, ui ) {
					$( this ).slider( "option", "min", $( this ).data('left-limit') );
					$( this ).slider( "option", "max", $( this ).data('right-limit') );
					$( this ).slider( "option", "value", $( this ).data('value') );
				},
				slide: function( event, ui ) {
					$( this ).parent().siblings('.cherry-slider-input').find('input').val(ui.value).trigger('change');
				}
			});
			$('.cherry-ui-stepper-input', target).on('change', function(){
				$(this).parent().parent().siblings('.cherry-slider-holder').find('.cherry-slider-unit').slider( "option", "value", $(this).val() );
			})
		}
	}
	$( window ).on( 'cherry-ui-elements-init',
		function( event, data ) {
			CherryJsCore.ui_elements.slider.init( data.target );
		}
	);
}(jQuery));
