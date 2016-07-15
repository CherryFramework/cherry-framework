/**
 * Radio
 */
( function( $, CherryJsCore ){
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.radio');
	CherryJsCore.ui_elements.radio = {
		init: function () {
			$( document ).on( 'ready', this.render );
			$( window ).on( 'cherry-ui-elements-init', this.render );
		},
		render: function ( event ) {
			var target = ( event._target ) ? event._target : $( 'body' );

			$( '.cherry-radio-group', target ).each( function() {
				$( '.cherry-radio-input[type="radio"]', this ).each( function() {
					var $this = $(this),
						this_slave = $this.data('slave');

					if ( ! $this.is( ':checked' ) ) {
						$( '.' + this_slave, target ).stop().hide();
					}
				} );
			} );

			$( '.cherry-radio-input[type="radio"]', target ).on( 'change', function() {
				var $this = $(this),
					slave = $this.data('slave'),
					radio_group = $this.parents('.cherry-radio-group'),
					radio_group_list = $('.cherry-radio-input[type="radio"]', radio_group);

				$this.parents('.cherry-radio-group').find('.checked').removeClass('checked');
				$this.parent().addClass('checked');

				$('.' + slave, target).show();
				radio_group_list.each(function(){
					var $this = $(this),
						this_slave = $this.data('slave');

					if( this_slave !== slave ){
						$('.' + this_slave, target).hide();
					}
				});

				$this.trigger( 'radio_change_event', [slave, radio_group_list] );
			});
		}
	};

	CherryJsCore.ui_elements.radio.init();

}( jQuery, window.CherryJsCore ) );
