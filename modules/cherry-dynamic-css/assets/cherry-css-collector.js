/**
 * Handler for CSS Collector
 */
function CherryCSSCollector() {

	'use strict';

	var style;

	if ( undefined !== window.CherryCollectedCSS ) {

		style = document.createElement( 'style' );

		style.setAttribute( 'title', window.CherryCollectedCSS.title );
		style.setAttribute( 'type', window.CherryCollectedCSS.type );

		style.textContent = window.CherryCollectedCSS.css;

		document.head.appendChild( style );
	}
}

CherryCSSCollector();
