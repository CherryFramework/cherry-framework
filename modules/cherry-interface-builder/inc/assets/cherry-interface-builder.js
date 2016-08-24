/**
 * Radio
 */
;( function( $, CherryJsCore ){
	'use strict';

	CherryJsCore.utilites.namespace('interfaceBuilder');

	CherryJsCore.interfaceBuilder = {
		init: function () {
			this.component.init();
		},
		component: {
			tabClass:           '.cherry-tab',
			accordionClass:     '.cherry-accordion',
			toggleClass:        '.cherry-toggle',

			buttonClass:        '.cherry-component__button',
			contentClass:       '.cherry-settings__content',

			buttonActiveClass:  'active',
			showClass:          'show',

			localStorage:        {},

			init: function () {
				this.localStorage = this.getState() || {};

				this.componentInit( this.tabClass );
				this.componentInit( this.accordionClass );
				this.componentInit( this.toggleClass );

				this.addEvent();
			},

			addEvent: function () {
				$( 'body' )
					.on( 'click',
						this.tabClass + ' ' + this.buttonClass + ', ' +
						this.toggleClass + ' ' + this.buttonClass + ', ' +
						this.accordionClass + ' ' + this.buttonClass,

						this.componentClick.bind( this )
					)
			},

			componentInit: function ( componentClass ) {
				var _this = this,
					components = $( componentClass ),
					componentId = null,
					button = null,
					contentId = null,
					notShow = '';

				components.each( function( index, component ) {
					component = $( component );
					componentId = component.data('compotent-id');


					switch ( componentClass ) {
						case _this.toggleClass:
							if ( _this.localStorage[ componentId ] && _this.localStorage[ componentId ].length ) {
								notShow = _this.localStorage[ componentId ].join(', ');
							}

							$( _this.contentClass, component )
								.not( notShow )
								.addClass( _this.showClass )
								.prevAll( _this.buttonClass )
								.addClass( _this.buttonActiveClass );
						break;

						case _this.tabClass:
						case _this.accordionClass:
							if( _this.localStorage[ componentId ] ){
								contentId = _this.localStorage[ componentId ][ 0 ];
								button = $( '[data-content-id="' + contentId + '"]', component );
							}else{
								button = $( _this.buttonClass, component ).eq( 0 );
								contentId = button.data( 'content-id' );
							}

							_this.showElement( button, component, contentId );
						break;
					}
				} );
			},

			componentClick: function ( event ) {
				var $_target      = $( event.target ),
					$_parent      = $_target.closest( this.tabClass + ', ' + this.accordionClass + ', ' + this.toggleClass ),
					expr          = new RegExp( this.tabClass + '|' + this.accordionClass + '|' + this.toggleClass ),
					componentName = $_parent[0].className.match( expr )[ 0 ].replace( ' ', '.' ),
					contentId     = $_target.data( 'content-id' ),
					componentId   = $_parent.data( 'compotent-id' ),
					activeFlag    = $_target.hasClass( this.buttonActiveClass ),
					itemClosed;

				switch ( componentName ) {
					case this.tabClass:
						if ( ! activeFlag ) {
							this.hideElement( $_parent );
							this.showElement( $_target, $_parent, contentId );

							this.localStorage[ componentId ] = new Array ( contentId );
							this.setState();
						}
					break;

					case this.accordionClass:
						this.hideElement( $_parent );

						if( ! activeFlag ){
							this.showElement( $_target, $_parent, contentId );

							this.localStorage[ componentId ] = new Array ( contentId );
						}else{
							this.localStorage[ componentId ] = {};
						}
						this.setState();
					break;

					case this.toggleClass:
						$_target
							.toggleClass( this.buttonActiveClass )
							.nextAll( contentId )
							.toggleClass( this.showClass );

						if ( Array.isArray( this.localStorage[ componentId ] ) ) {
							itemClosed = this.localStorage[ componentId ].indexOf( contentId );

							if ( -1 !== itemClosed ) {
								this.localStorage[ componentId ].splice( itemClosed, 1 );
							}else{
								this.localStorage[ componentId ].push( contentId );
							}

						}else{
							this.localStorage[ componentId ] = new Array ( contentId );
						}

						this.setState();
					break;
				}
				$_target.blur();
			},

			showElement: function ( button, holder, contentId ) {
				button
					.addClass( this.buttonActiveClass );

				holder.data( 'content-id', contentId );

				$( contentId, holder )
					.addClass( this.showClass );
			},

			hideElement: function ( holder ) {
				var contsntId = holder.data( 'content-id' );

				$( '[data-content-id="' + contsntId + '"]', holder )
					.removeClass( this.buttonActiveClass );

				$( contsntId, holder )
					.removeClass( this.showClass );
			},

			getState: function(){
				if ( localStorage ) {
					return JSON.parse( localStorage.getItem( 'interface-builder' ) );
				}
			},

			setState: function(){
				if ( localStorage ) {
					localStorage.setItem( 'interface-builder', JSON.stringify( this.localStorage ) );
				}
			}
		}
	};

	CherryJsCore.interfaceBuilder.init();
}( jQuery, window.CherryJsCore ) );
