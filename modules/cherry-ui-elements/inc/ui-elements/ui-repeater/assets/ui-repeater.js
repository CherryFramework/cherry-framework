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

			$( '.cherry-ui-repeater-container', target ).each( function() {
				var $this        = $( this ),
					$list        = $( '.cherry-ui-repeater-list', $this ),
					tmplName     = $list.data( 'name' ),
					titleField   = $list.data( 'title-field' ),
					rowTemplate  = wp.template( tmplName );

				$this.on( 'click', '.cherry-ui-repeater-add', function( event ) {
					var index = $list.data( 'index' ),
						$target = $list.append( rowTemplate( { index: index } ) ).find( '.cherry-ui-repeater-item:last' );

					event.preventDefault();

					CherryJsCore.variable.$window.trigger( 'cherry-ui-elements-init', { 'target': $target } );
					index++;
					$list.data( 'index', index );
				});

				$list.on( 'click', '.cherry-ui-repeater-remove', function( event ) {
					event.preventDefault();
					$( this ).closest( '.cherry-ui-repeater-item' ).remove();
				});

				$list.on( 'click', '.cherry-ui-repeater-toggle', function( event ) {
					var $container = $( this ).closest( '.cherry-ui-repeater-item' ),
						minClass   = 'cherry-ui-repeater-min';

					event.preventDefault();

					if ( $container.hasClass( minClass ) ) {
						$container.removeClass( minClass );
					} else {
						$container.addClass( minClass );
					}

				});

				$list.on( 'change', '.' + titleField + '-wrap input, textarea, select', function() {
					var $this = $( this ),
						value = $this.val(),
						$actionsBox = $this.closest( '.cherry-ui-repeater-item' ),
						$title = $( '.cherry-ui-repeater-title', $actionsBox );

						$title.html( value );
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
			} );

		}
	};

	$( window ).on( 'cherry-ui-elements-init',
		function( event, data ) {
			CherryJsCore.ui_elements.repeater.init( data.target );
		}
	);

}( jQuery, window.CherryJsCore ) );
