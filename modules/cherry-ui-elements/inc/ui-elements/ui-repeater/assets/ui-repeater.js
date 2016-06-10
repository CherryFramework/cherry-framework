/**
 * Repeater
 */
(function( $, CherryJsCore ) {
	'use strict';

	CherryJsCore.utilites.namespace( 'ui_elements.repeater' );
	CherryJsCore.ui_elements.repeater = {
		init: function( target ) {
			if ( CherryJsCore.status.is_ready ) {
				this.render( target, this );
			} else {
				CherryJsCore.variable.$document.on( 'ready', this.render( target, this ) );
			}
		},

		triggerChange: function ( $target ) {
			var $input = $target.find( 'input[name]:first, select[name]:first' );
			if ( undefined !== wp.customize ) {
				$input.trigger( 'change' );
				$input.trigger( 'keydown' );
				$input.trigger( 'propertychange' );
			}
		},

		render: function( target, parent ) {

			var repeater = $( '.cherry-ui-repeater-container', target );

			repeater.each( function( event ) {
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
					parent.triggerChange( $target );

				});

				$list.on( 'click', '.cherry-ui-repeater-remove', function( event ) {
					event.preventDefault();
					$( this ).closest( '.cherry-ui-repeater-item' ).remove();
					parent.triggerChange( $list );
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
					placeholder: 'sortable-placeholder',
					update: function() {
						parent.triggerChange( $( this ) );
					}
				});
			} );
		},
	};

	$( window ).on( 'cherry-ui-elements-init',
		function( event, data ) {
			CherryJsCore.ui_elements.repeater.init( data.target );
		}
	);

}( jQuery, window.CherryJsCore ) );
