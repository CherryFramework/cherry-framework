/**
 * Iconpicker
 */
(function( $, CherryJsCore ) {
	'use strict';

	CherryJsCore.utilites.namespace( 'ui_elements.iconpicker' );
	CherryJsCore.ui_elements.iconpicker = {
		init: function() {
			$( document )
				.on( 'ready', this.render )
				.on( 'cherry-ui-elements-init', this.render );
		},
		render: function( event ) {
			var target = ( event._target ) ? event._target : $( 'body' ),
				$picker = $( '.cherry-ui-iconpicker:not([name*="__i__"])', target ),
				$this,
				set,
				setData;

				$picker.each( function() {
					$this   = $( this );
					set     = $this.data( 'set' );
					setData = window[set];

					if ( $this.length && setData.icons ) {
						$this.iconpicker({
							icons: setData.icons,
							iconBaseClass: setData.iconBase,
							iconClassPrefix: setData.iconPrefix,
							animation: false,
							fullClassFormatter: function( val ) {
								return setData.iconBase + ' ' + setData.iconPrefix + val;
							}
						}).on( 'iconpickerUpdated', function() {
							$( this ).trigger( 'change' );
						});
					}

					if ( setData ) {
						$( 'body' ).append( '<link rel="stylesheet" type="text/css" href="' + setData.iconCSS + '"">' );
					}
				} );
		}
	};

	CherryJsCore.ui_elements.iconpicker.init();

}( jQuery, window.CherryJsCore ) );
