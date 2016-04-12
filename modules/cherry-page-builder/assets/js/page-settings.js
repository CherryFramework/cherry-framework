/**
 * Page settings
 */
(function( $ ) {
	'use strict';

	window.CherryJsCore.utilites.namespace( 'page_builder' );
	window.CherryJsCore.page_builder = {

		init: function() {
			var self = this;
			self.tabs( '.cherry-settings-tabs' ).saveEvent( '.cherry-settings-tabs form' );
		},

		tabs: function( selectors ) {
			jQuery( selectors + ' .tabs-section a' ).each( function( index ) {
				var $this = jQuery( this );
				var id = $this.attr( 'href' );
				if ( ! index ) {
					$this.addClass( 'nav-tab-active' );
				} else {
					jQuery( selectors + ' .section' + id ).hide();
				}
			});
			jQuery( selectors + ' .tabs-section a' ).on( 'click', function( e ) {
				var $this = jQuery( this );
				var id = $this.attr( 'href' );
				jQuery( selectors + ' .section' ).hide();
				jQuery( selectors + ' .section' + id ).show();
				jQuery( selectors + ' .tabs-section a' ).removeClass( 'nav-tab-active' );
				$this.addClass( 'nav-tab-active' );
				e.preventDefault();
			});
			return this;
		},

		saveEvent: function( selectors ) {
			var self = this;
			jQuery( selectors ).submit( function( e ) {
				var $this = jQuery( this );
				$this.ajaxSubmit({
					success: function() {
						self.noticeCreate( 'success', window.TMRealEstateMessage.success );
					},
					error: function() {
						self.noticeCreate( 'failed', window.TMRealEstateMessage.failed );
					},
					timeout: 5000
				});

				e.preventDefault();
			});
			return this;
		},

		noticeCreate: function( type, message ) {
			var
				notice = $( '<div class="notice-box ' + type + '-notice"><span class="dashicons"></span><div class="inner">' + message + '</div></div>' ),
				rightDelta = 0,
				timeoutId;

			jQuery( 'body' ).prepend( notice );
			reposition();
			rightDelta = -1 * ( notice.outerWidth( true ) + 10 );
			notice.css( { 'right': rightDelta } );

			timeoutId = setTimeout( function() {
				notice.css( { 'right': 10 } ).addClass( 'show-state' );
			}, 100 );
			timeoutId = setTimeout( function() {
				rightDelta = -1 * ( notice.outerWidth( true ) + 10 );
				notice.css( { right: rightDelta } ).removeClass( 'show-state' );
			}, 4000 );
			timeoutId = setTimeout( function() {
				notice.remove();
				clearTimeout( timeoutId );
			}, 4500 );

			function reposition() {
				var
					topDelta = 100;
				$( '.notice-box' ).each(function() {
					$( this ).css( { top: topDelta } );
					topDelta += $( this ).outerHeight( true );
				});
			}
			return this;
		}
	};

	window.CherryJsCore.page_builder.init();
}( jQuery ) );
