/**
 * Handler for Assets loader
 */
function CherryAssetsLoader( tags, context ) {

	'use strict';

	tags.forEach( function( item ) {
		if ( 'body' === context ) {
			jQuery( 'body' ).append( item );
		} else {
			jQuery( 'head' ).append( item );
		}
	} );

}
