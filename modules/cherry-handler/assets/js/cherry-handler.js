( function( $ ) {
	"use strict";

	/**
	 * CherryAjaxHandler class
	 *
	 * @param {object} options Handler options
	 */

	CherryJsCore.utilites.namespace('CherryAjaxHandler');
	CherryJsCore.CherryAjaxHandler = function( options ) {
		var settings = {
			'handlerId': '',
			'beforeSendCallback': function() {},
			'successCallback': function() {},
		}

		/**
		 * Checking options, settings and options merging
		 *
		 */
		if ( options ) {
			$.extend( settings, options );
		}

		/**
		 * Check if handlerId ready to using
		 *
		 */
		if ( ! window[ settings.handlerId ] ) {
			CherryJsCore.cherryHandlerUtils.consoleMessage( 'warn', 'Handler id not found' );

			return false;
		}

		this.handlerSettings = window[ settings.handlerId ] || {};

		this.ajaxRequest = null;

		this.ajaxProcessing = false;

		this.data = {
			'action': this.handlerSettings.action,
			'nonce': this.handlerSettings.nonce,
		}

		this.runAjaxRequst = function( beforeSendCallback, successCallback ) {
			this.ajaxProcessing = true;

			this.ajaxRequest = jQuery.ajax( {
				type: this.handlerSettings.type.toUpperCase(),
				url: ajaxurl,
				data: this.data,
				cache: false,
				beforeSend: function( jqXHR ) {

					if ( this.ajaxProcessing ) {
						//jqXHR.abort();
					}
					console.log('beforeSend');
					if ( beforeSendCallback && 'function' === typeof( beforeSendCallback ) ) {
						beforeSendCallback();
					}
				},
				success: function( response ) {
					console.log( response );
					this.ajaxProcessing = false;
					console.log('successCallback');
					if ( successCallback && 'function' === typeof( successCallback ) ) {
						successCallback();
					}

					CherryJsCore.cherryHandlerUtils.noticeCreate( response.type, response.message );
				},
				dataType: this.handlerSettings.data_type
			} );
		}
	}


	CherryJsCore.utilites.namespace('cherryHandlerUtils');
	CherryJsCore.cherryHandlerUtils = {

		noticeCreate: function( type, message ) {
			var notice = $( '<div class="cherry-handler-notice ' + type + '"><span class="dashicons"></span><div class="inner">' + message + '</div></div>' ),
				rightDelta = 0,
				timeoutId;

			$( 'body' ).prepend( notice );
			reposition();
			rightDelta = -1 * ( notice.outerWidth( true ) + 10 );
			notice.css( {'right' : rightDelta } );

			timeoutId = setTimeout( function () { notice.css( { 'right' : 10 } ).addClass( 'show-state' ) }, 100 );
			timeoutId = setTimeout( function () {
				rightDelta = -1 * ( notice.outerWidth( true ) + 10 );
				notice.css( { right: rightDelta } ).removeClass( 'show-state' );
			}, 4000 );
			timeoutId = setTimeout( function () {
				notice.remove();
				clearTimeout( timeoutId );
			}, 4500 );

			function reposition(){
				var topDelta = 100;

				$( '.cherry-handler-notice' ).each( function( index ) {
					$( this ).css( { top: topDelta } );
					topDelta += $( this ).outerHeight( true );
				} );
			}
		},

		consoleMessage: function( type, message ) {
			var type = type || 'log',
				message = message || 'BlaBla';

			if ( window.console ) {
				switch ( type ) {
					case 'log':
						window.console.log( message );
						break
					case 'warn':
						window.console.warn( message );
						break
					case 'info':
						window.console.info( message );
						break
					case 'error':
						window.console.error( message );
						break
					default:
						window.console.log( message );
				}
			}
		}

	}

} ( jQuery ) );
