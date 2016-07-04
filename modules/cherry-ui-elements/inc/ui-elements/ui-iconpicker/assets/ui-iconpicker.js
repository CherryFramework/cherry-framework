/**
 * Iconpicker
 */
(function( $, CherryJsCore ) {
	'use strict';

	CherryJsCore.utilites.namespace( 'ui_elements.iconpicker' );
	CherryJsCore.ui_elements.iconpicker = {
		init: function() {
			$( window ).on( 'cherry-ui-elements-init', this.render.bind( this ) );
		},
		render: function( event, data ) {
			var target = data.target,
				$picker = $( '.cherry-ui-iconpicker', target ),
				set     = $picker.data( 'set' ),
				setData = window[set];

			if ( $picker.length ) {
				$picker.iconpicker({
					icons: setData.icons,
					iconBaseClass: setData.iconBase,
					iconClassPrefix: setData.iconPrefix
				}).on( 'iconpickerUpdated', function() {
					$( this ).trigger( 'change' );
				});
			}

			if ( setData ) {
				$( 'body' ).append( '<link rel="stylesheet" type="text/css" href="' + setData.iconCSS + '"">' );
			}
		}

	};

	CherryJsCore.ui_elements.iconpicker.init();

}( jQuery, window.CherryJsCore ) );
