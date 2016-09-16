( function( $ ) {
	"use strict";

	/**
	 * CherryAjaxHandler class
	 *
	 * @param {object} options Handler options
	 */

	CherryJsCore.utilites.namespace('CherryAjaxHandler');
	CherryJsCore.CherryAjaxHandler = function( options ) {
		/**
		 * General default settings
		 *
		 * @type {Object}
		 */
		var settings = {
			'handlerId': '',
			'ifModified': false,
			'cache': false,
			'processData': true,
			'url': '',
			'beforeSendCallback': function() {},
			'errorCallback': function() {},
			'successCallback': function() {},
			'completeCallback': function() {},
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

		/**
		 * Set handler settings from localized global variable
		 *
		 * @type {Object}
		 */
		this.handlerSettings = window[ settings.handlerId ] || {};

		/**
		 * Ajax request instance
		 *
		 * @type {Object}
		 */
		this.ajaxRequest = null;

		/**
		 * Ajax processing state
		 *
		 * @type {Boolean}
		 */
		this.ajaxProcessing = false;

		/**
		 * Set ajax request data
		 *
		 * @type {Object}
		 */
		this.data = {
			'action': this.handlerSettings.action,
			'nonce': this.handlerSettings.nonce,
		}

		/**
		 * Check ajax url is empty
		 */
		if ( '' === settings.url ) {
			console.log(ajaxurl);
			// Check public request
			if ( 'false' === this.handlerSettings.public) {
				settings.url = ajaxurl;
			} else{
				settings.url = cherryHandlerAjaxUrl.ajax_url;
			}
		}

		/**
		 * Init ajax request
		 *
		 * @return {void}
		 */
		this.runAjaxRequst = function() {

			this.ajaxProcessing = true;

			this.ajaxRequest = jQuery.ajax( {
				type: this.handlerSettings.type.toUpperCase(),
				url: settings.url,
				data: this.data,
				cache: settings.cache,
				dataType: this.handlerSettings.data_type,
				processData: settings.processData,
				beforeSend: function( jqXHR, settings ) {
					if ( this.ajaxProcessing ) {
						//jqXHR.abort();
					}

					if ( settings.beforeSendCallback && 'function' === typeof( settings.beforeSendCallback ) ) {
						settings.beforeSendCallback( jqXHR, settings );
					}
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					if ( settings.errorCallback && 'function' === typeof( settings.errorCallback ) ) {
						settings.errorCallback( jqXHR, textStatus, errorThrown );
					}
				},
				success: function( data, textStatus, jqXHR ) {
					this.ajaxProcessing = false;
					if ( settings.successCallback && 'function' === typeof( settings.successCallback ) ) {
						settings.successCallback( data, textStatus, jqXHR );
					}

					CherryJsCore.cherryHandlerUtils.noticeCreate( data.type, data.message );
				},
				complete: function( jqXHR, textStatus ) {
					if ( settings.completeCallback && 'function' === typeof( settings.completeCallback ) ) {
						settings.completeCallback( jqXHR, textStatus );
					}
				}

			} );
		}
	}

	CherryJsCore.utilites.namespace('cherryHandlerUtils');
	CherryJsCore.cherryHandlerUtils = {
		/**
		 * Rendering notice message
		 *
		 * @param  {string} type    Message type
		 * @param  {string} message Message content
		 * @return {void}
		 */
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

		/**
		 * Console message and console avaliable checking
		 *
		 * @param  {string} type    Console method type
		 * @param  {string} message Console message
		 * @return {void}
		 */
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
