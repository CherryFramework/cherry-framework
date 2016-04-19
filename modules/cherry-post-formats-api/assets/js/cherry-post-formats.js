/**
 * Post formats related scripts
 * @use CherryJsCore
 */
( function($, CherryJsCore){
	'use strict';

	CherryJsCore.utilites.namespace( 'post_formats' );
	CherryJsCore.post_formats = {

		init: function () {

			var self = this;

			if ( CherryJsCore.status.document_ready ) {
				self.render( self );
			} else {
				CherryJsCore.variable.$document.on( 'ready', self.render( self ) );
			}
		},

		render: function ( self ) {

			// Init slider scripts
			self.initalize( 'slider' );

			// Init popup scripts
			self.initalize( 'popup' );

		},

		initalize: function( object ) {

			$(window).load(function () {

				$( '*[data-cherry' + object + '="1"]' ).each( function() {
					var plugin = $( this ).data( object ),
						init   = $( this ).data( 'init' );

					$( this ).data( 'initalized', false );
					$( this ).trigger({
						type: 'cherry-post-formats-custom-init',
						item: $( this ),
						object: object
					});

					if ( true === $( this ).data( 'initalized' ) ) {
						return 1;
					}

					if ( ! plugin ) {
						return !1;
					}

					if ( ! $.isFunction( jQuery.fn[ plugin ] ) ) {
						return !1;
					}

					$( this )[ plugin ]( init );
				});
			});

		}
	};

	CherryJsCore.post_formats.init();

} (jQuery, window.CherryJsCore) );