/**
 * Stepper
 */
(function($){
	"use strict";

	CHERRY_API.utilites.namespace('ui_elements.stepper');
	CHERRY_API.ui_elements.stepper = {
		init: function ( target ) {
			var self = this;
			if( CHERRY_API.status.document_ready ){
				self.render( target );
			}else{
				CHERRY_API.variable.$document.on('ready', self.render( target ) );
			}
		},
		render: function ( target ) {
			$('.step-up', target).on('click', function () {
				var
					item = $(this).parent().prev('.cherry-ui-stepper-input')
				,	values = get_value(item)
				,	change_value = parseFloat( values['input_value'] + values['step_value'] )
				;
				if( isNaN( change_value ) ){ change_value = 0 }
				if(change_value <= values['max_value']){
					item.val(change_value);
					item.trigger('change');
				}
			})
			$('.step-down', target).on('click', function () {
				var
					item = $(this).parent().prev('.cherry-ui-stepper-input')
				,	values = get_value(item)
				,	change_value = values['input_value'] - values['step_value']
				;
				if( isNaN( change_value ) ){ change_value = 0 }
				if(change_value >= values['min_value']){
					item.val(change_value);
					item.trigger('change');
				}
			})
			$('.cherry-ui-stepper-input', target).on('change', function () {
				var
					item = $(this)
				,	values = get_value(item)
				;

				if(values['input_value'] > values['max_value']){
					item.val(values['max_value']);
				}
				if(values['input_value'] < values['min_value']){
					item.val(values['min_value']);
				}
			})
			function get_value (item) {
				var values = [];
					values['max_value'] = parseFloat(item.data('max-value'));
					values['min_value'] = parseFloat(item.data('min-value'));
					values['step_value'] = parseFloat(item.data('step-value'));
					values['input_value'] = parseFloat(item.attr('value'));
				return values;
			}
		}
	}
	$( window ).on( 'cherry-ui-elements-init',
		function( event, data ) {
			CHERRY_API.ui_elements.stepper.init( data.target );
		}
	);
}(jQuery));
