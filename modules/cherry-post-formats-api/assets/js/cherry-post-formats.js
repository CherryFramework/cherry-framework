/**
 * Post formats related scripts
 * @use Cherry_API
 */
(function($){
	'use strict';

	CHERRY_API.utilites.namespace( 'post_formats' );
	CHERRY_API.post_formats = {

		init: function () {

			var self = this;

			if ( CHERRY_API.status.document_ready ) {
				self.render( self );
			} else {
				CHERRY_API.variable.$document.on( 'ready', self.render( self ) );
			}
		},

		render: function ( self ) {

			// Init slider scripts
			self.initalize( 'slider' );

			// Init popup scripts
			self.initalize( 'popup' );

		},

		initalize: function( object ) {

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

		}
	}

	$(function(){
		CHERRY_API.post_formats.init();
	})

}(jQuery));