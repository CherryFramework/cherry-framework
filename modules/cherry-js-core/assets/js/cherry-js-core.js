var CherryJsCore;

(function($){
	'use strict';

	CherryJsCore = {
		name : 'Cherry Js Core',
		varsion : '1.0.0',
		author : 'Cherry Team',

		variable : {
			$document : $( document ),
			$window : $( window ),
			browser : $.browser,
			browser_supported : true,
			security : cherry_ajax,
			loaded_assets : {
				script : wp_load_script,
				style : wp_load_style
			},
			ui_auto_init: ( 'true' == ui_init_object.auto_init ) ? true : false,
			ui_auto_target: ui_init_object.targets
		},

		status : {
			on_load : false,
			is_ready : false
		},

		init : function(){

			CherryJsCore.set_variable ();

			$( document ).ready( CherryJsCore.ready );

			$( window ).load( CherryJsCore.load );
		},

		set_variable : function(){
			//set variable browser_supported
			CherryJsCore.variable.browser_supported = ( function (){
				var uset_browser = CherryJsCore.variable.browser,
					not_supported = { 'msie' : [8] };

				for ( var browser in not_supported ) {
					if( uset_browser.browser  != 'undefined' ){
						for ( var version in not_supported[ browser ] ) {
							if( uset_browser.version <= not_supported [ browser ] [ version ] ){
								return false;
							}
						}
					}
				};

				return true;
			}() )
		},

		ready : function(){
			CherryJsCore.status.is_ready = true;

			// Auto ui init if `ui_auto_init` is true
			if ( CherryJsCore.variable.ui_auto_init ) {
				CherryJsCore.expressions.ui_init();
			}

			// UI init after widget adding to sidebar
			CherryJsCore.expressions.widget_added_ui_init();

			// UI init after widget saving
			CherryJsCore.expressions.widget_updated_ui_init();
		},

		load : function(){
			CherryJsCore.status.on_load = true;
		},

		expressions : {
			ui_init : function() {
				CherryJsCore.variable.ui_auto_target.forEach( function( target ) {
					CherryJsCore.variable.$window.trigger( 'cherry-ui-elements-init', { 'target': $( target ) } );
				});
			},
			widget_added_ui_init : function() {
				$( document ).on( 'widget-added', function( event, data ){
					$( window ).trigger( 'cherry-ui-elements-init', { 'target': data } );
				} );
			},
			widget_updated_ui_init : function() {
				$( document ).on( 'widget-updated', function( event, data ){
					$( window ).trigger( 'cherry-ui-elements-init', { 'target': data } );
				} );
			},
		},

		utilites : {
			namespace : function( space_path ){
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
			},
			get_compress_assets: function ( url, callback ){
				var data = {
					action : 'get_compress_assets',
					security : CherryJsCore.variable.security,
					style : [],
					script : []
					},
					reg_name = /([\S.]+\/)/gmi,
					reg_type = /(\.js|\.css)/gmi,
					callback_function = callback || function(){};

				if( !$.isArray( url ) ){
					url = [ url ];
				}

				for( var index in url ){
					var file_url = url[ index ],
						file_name = file_url.replace( reg_name, '' ),
						file_type = file_url.match( reg_type )[ 0 ];

					if( file_type === '.js' && $.inArray( file_name, CherryJsCore.variable.loaded_assets.script ) == -1 ){
						data.script.push( file_url );
						CherryJsCore.variable.loaded_assets.script.push( file_name );
					}

					if( file_type === '.css' && $.inArray( file_name, CherryJsCore.variable.loaded_assets.style ) == -1 ){
						data.style.push( file_url );
						CherryJsCore.variable.loaded_assets.style.push( file_name );
					}
				}

				$.get(ajaxurl, data, function(response) {
					var json = $.parseJSON(response),
						compress_style = json.style,
						compress_script = json.script;

					if(compress_style){
						var style = document.createElement('style'),
							styleSheet;

						style.type = 'text/css';
						style.media = 'all';
						style.innerHTML = compress_style;

						$('body', document).append(style);

					}

					if(compress_script){
						var script = new Function(compress_script)();
					}

					return callback_function();
				});
			}
		}
	};

	CherryJsCore.init();
}(jQuery));