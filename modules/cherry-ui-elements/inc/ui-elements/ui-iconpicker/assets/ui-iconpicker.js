/**
 * Iconpicker
 */
(function( $, CherryJsCore ) {
	'use strict';

	CherryJsCore.utilites.namespace( 'ui_elements.iconpicker' );
	CherryJsCore.ui_elements.iconpicker = {
		init: function( target ) {
			var self = this;
			if ( CherryJsCore.status.document_ready ) {
				self.render( target );
			} else {
				CherryJsCore.variable.$document.on( 'ready', self.render( target ) );
			}
		},
		render: function( target ) {

			var $picker = $( '.cherry-ui-iconpicker', target ),
				set     = $picker.data( 'set' ),
				setData = window[set];

			if ( $picker.length ) {
				$picker.iconpicker({
					icons: setData.icons,
					iconBaseClass: setData.iconBase,
					iconClassPrefix: setData.iconPrefix
				});
			}

			if ( setData ) {
				$( 'body' ).append( '<link rel="stylesheet" type="text/css" href="' + setData.iconCSS + '"">' );
			}
		}

	};

	$( window ).on( 'cherry-ui-elements-init',
		function( event, data ) {
			CherryJsCore.ui_elements.iconpicker.init( data.target );
		}
	);

}( jQuery, window.CherryJsCore ) );
