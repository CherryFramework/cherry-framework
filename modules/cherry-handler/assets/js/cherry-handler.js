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
			},
			self = this;

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
		self.handlerSettings = window[ settings.handlerId ] || {};

		/**
		 * Ajax request instance
		 *
		 * @type {Object}
		 */
		self.ajaxRequest = null;

		/**
		 * Ajax processing state
		 *
		 * @type {Boolean}
		 */
		self.ajaxProcessing = false;

		/**
		 * Set ajax request data
		 *
		 * @type {Object}
		 */
		self.data = {
			'action': this.handlerSettings.action,
			'nonce': this.handlerSettings.nonce
		}

		/**
		 * Check ajax url is empty
		 */
		if ( '' === settings.url ) {

			// Check public request
			if ( 'false' === self.handlerSettings.public) {
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
		self.send = function() {

			self.ajaxProcessing = true;
			self.ajaxRequest = jQuery.ajax( {
				type: self.handlerSettings.type.toUpperCase(),
				url: settings.url,
				data: self.data,
				cache: settings.cache,
				dataType: self.handlerSettings.data_type,
				processData: settings.processData,
				beforeSend: function( jqXHR, ajaxSettings ) {
					if ( null !== self.ajaxRequest ) {
						self.ajaxRequest.abort();
					}

					if ( settings.beforeSendCallback && 'function' === typeof( settings.beforeSendCallback ) ) {
						settings.beforeSendCallback( jqXHR, ajaxSettings );
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

		self.sendData = function( data ) {
			var data = data || {};
				self.data = {
					'action': self.handlerSettings.action,
					'nonce': self.handlerSettings.nonce,
					'data': data
				}

			self.send();
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

	$( document ).trigger( 'CherryHandlerInit' );
} ( jQuery ) );
