/**
 * Repeater
 */
(function( $, CherryJsCore ) {
	'use strict';

	CherryJsCore.utilites.namespace( 'ui_elements.repeater' );
	CherryJsCore.ui_elements.repeater = {
		init: function( target ) {
			var self = this;
			if ( CherryJsCore.status.document_ready ) {
				self.render( target );
			} else {
				CherryJsCore.variable.$document.on( 'ready', self.render( target ) );
			}
		},
		render: function( target ) {

			var $list       = $( '.cherry-ui-repeater-list', target ),
				tmplName    = $list.data( 'name' ),
				rowTemplate = wp.template( tmplName );

			target.on( 'click', '.cherry-ui-repeater-add', function( event ) {
				var index = $list.data( 'index' ),
					$target = $list.append( rowTemplate( { index: index } ) ).find( '.cherry-ui-repeater-item:last' );
				event.preventDefault();
				CherryJsCore.variable.$window.trigger( 'cherry-ui-elements-init', { 'target': $target } );
				index++;
				$list.data( 'index', index );
			});

			$( '.cherry-ui-repeater-remove', target ).on( 'click', function( event ) {
				event.preventDefault();
				$( this ).closest( '.cherry-ui-repeater-item' ).remove();
			});

			$( '.cherry-ui-repeater-toggle', target ).on( 'click', function( event ) {

				var $container = $( this ).closest( '.cherry-ui-repeater-item' ),
					minClass   = 'cherry-ui-repeater-min';

				event.preventDefault();

				if ( $container.hasClass( minClass ) ) {
					$container.removeClass( minClass );
				} else {
					$container.addClass( minClass );
				}

			});

			$list.sortable({
				items: '.cherry-ui-repeater-item',
				handle: '.cherry-ui-repeater-actions-box',
				cursor: 'move',
				scrollSensitivity: 40,
				forcePlaceholderSize: true,
				forceHelperSize: false,
				helper: 'clone',
				opacity: 0.65,
				placeholder: 'sortable-placeholder'
			});
		}
	};

	$( window ).on( 'cherry-ui-elements-init',
		function( event, data ) {
			CherryJsCore.ui_elements.repeater.init( data.target );
		}
	);

}( jQuery, window.CherryJsCore ) );
