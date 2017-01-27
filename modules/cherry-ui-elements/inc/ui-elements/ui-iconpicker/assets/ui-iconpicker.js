/**
 * Iconpicker
 */
(function( $, CherryJsCore, underscore ) {
	'use strict';

	CherryJsCore.utilites.namespace( 'ui_elements.iconpicker' );
	CherryJsCore.ui_elements.iconpicker = {
		iconSets: {},
		iconSetsKey: 'cherry5-icon-sets',

		init: function() {
			$( document )
				.on( 'cherry-ajax-handler-success', this.setIconsSets.bind( this ) )
				.on( 'ready.iconpicker', this.setIconsSets.bind( this, window.cherry5IconSets ) )
				.on( 'ready.iconpicker', this.render.bind( this ) )
				.on( 'cherry-ui-elements-init', this.render.bind( this ) );
		},

		setIconsSets: function( iconSets ) {
			var icon,
				_this = this;

			if ( iconSets ) {
				icon  = ( iconSets.response ) ? iconSets.response.cherry5IconSets : iconSets;

				underscore.each(
					icon,
					function( element, index ) {
						_this.iconSets[ index ] = element;
					}
				);

				_this.setState( _this.iconSetsKey, _this.iconSets );
			}
		},

		getIconsSets: function() {
			var iconSets = this.getState( this.iconSetsKey );

			if ( iconSets ) {
				this.iconSets = iconSets;
			}
		},

		render: function( event ) {
			var target = ( event._target ) ? event._target : $( 'body' ),
				$picker = $( '.cherry-ui-iconpicker:not([name*="__i__"])', target ),
				$this,
				set,
				setData,
				_this = this;

			if ( $picker[0] ) {
				this.getIconsSets();

				$picker.each( function() {
					$this   = $( this );
					set     = $this.data( 'set' );
					setData = _this.iconSets[set];

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
						$( 'head' ).append( '<link rel="stylesheet" type="text/css" href="' + setData.iconCSS + '"">' );
					}
				} );
			}
		},

		getState: function( key ) {
			try {
				return JSON.parse( window.sessionStorage.getItem( key ) );
			} catch ( e ) {
				return false;
			}
		},

		setState: function( key, data ) {
			try {
				window.sessionStorage.setItem( key, JSON.stringify( data ) );
			} catch ( e ) {
				return false;
			}
		}
	};

	CherryJsCore.ui_elements.iconpicker.init();

}( jQuery, window.CherryJsCore, window._ ) );
