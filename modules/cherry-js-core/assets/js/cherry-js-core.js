 var CherryJsCore = {};

( function( $ ) {
	'use strict';

	CherryJsCore = {
		name: 'Cherry Js Core',
		version: '1.0.0',
		author: 'Cherry Team',

		variable: {
			$document: $( document ),
			$window: $( window ),
			browser_supported: true,
			security: window.cherry_ajax,
			loaded_assets: {
				script: window.wp_load_script,
				style: window.wp_load_style
			},
			ui_auto_init: ( 'true' === window.ui_init_object.auto_init ) ? true : false,
			ui_auto_target: window.ui_init_object.targets
		},

		status: {
			on_load: false,
			is_ready: false
		},

		init: function(){

			$( document ).on( 'ready', CherryJsCore.ready );

			$( window ).on( 'load', CherryJsCore.load );
		},

		ready: function() {
			CherryJsCore.status.is_ready = true;

			// UI init after widget adding to sidebar
			CherryJsCore.expressions.widget_ui_init();
		},

		load: function() {
			CherryJsCore.status.on_load = true;
		},

		expressions: {
			widget_ui_init: function() {
				$( document ).on( 'widget-added widget-updated', function( event, data ) {
					$( 'body' ).trigger( {
						type: 'cherry-ui-elements-init',
						_target: data
					} );
				} );
			},
		},

		utilites: {
			namespace: function( space_path ) {
				var parts = space_path.split( '.' ),
					parent = CherryJsCore,
					length = parts.length,
					i = 0;

					for(i = 0; i < length; i += 1 ){
						if( typeof parent[ parts[ i ] ] === 'undefined' ){
							parent[ parts[ i ] ] = {};
						}
						parent = parent[ parts[ i ] ];
					}
				return parent;
			}
		}
	};

	CherryJsCore.init();
}(jQuery));
