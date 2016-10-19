/**
 * Handler for CSS Collector
 */
function CherryCSSCollector() {

	'use strict';

	var style,
		collectedCSS = window.CherryCollectedCSS;

	if ( undefined !== collectedCSS ) {

		style = document.createElement( 'style' );

		style.setAttribute( 'title', collectedCSS.title );
		style.setAttribute( 'type', collectedCSS.type );

		style.textContent = collectedCSS.css;

		document.head.appendChild( style );
	}
}

CherryCSSCollector();
