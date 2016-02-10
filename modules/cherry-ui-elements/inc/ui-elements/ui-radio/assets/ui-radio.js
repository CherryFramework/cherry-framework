/**
 * Radio
 */
(function($){
	"use strict";

	CHERRY_API.utilites.namespace('ui_elements.radio');
	CHERRY_API.ui_elements.radio = {
		init: function ( target ) {
			var self = this;
			if( CHERRY_API.status.document_ready ){
				self.render( target );
			}else{
				CHERRY_API.variable.$document.on('ready', self.render( target ) );
			}
		},
		render: function ( target ) {
			$('.cherry-radio-input[type="radio"]', target).on('change', function(event){
				var
					$this = $(this)
				,	slave = $this.data('slave')
				,	radio_group = $this.parents('.cherry-radio-group')
				,	radio_group_list = $('.cherry-radio-input[type="radio"]', radio_group)
				;
				$this.parents('.cherry-radio-group').find('.checked').removeClass('checked');
				$this.parent().addClass('checked');

				$this.trigger( 'radio_change_event', [slave, radio_group_list] );
			})
		}
	}
	$( window ).on( 'cherry-ui-elements-init',
		function( event, data ) {
			CHERRY_API.ui_elements.radio.init( data.target );
		}
	);
}(jQuery));
