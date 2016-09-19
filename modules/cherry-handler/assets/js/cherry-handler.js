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
			'action': self.handlerSettings.action,
			'nonce': self.handlerSettings.nonce
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
					self.ajaxProcessing = false;

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

		/**
		 * Send data ajax request
		 *
		 * @param  {object} data User data
		 * @return {void}
		 */
		self.sendData = function( data ) {
			var data = data || {};
				self.data = {
					'action': self.handlerSettings.action,
					'nonce': self.handlerSettings.nonce,
					'data': data
				}

			self.send();
		}

		/**
		 * Send form serialized data
		 * @param  {string} formId Form selector
		 * @return {void}
		 */
		self.sendFormData = function( formId ) {
			var form = $( formId ),
				data;

			data = CherryJsCore.cherryHandlerUtils.serializeObject( form );

			self.sendData( data );
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
		},

		/**
		 * Serialize form into
		 *
		 * @return {object} [description]
		 */
		serializeObject: function( form ) {

			var self = this,
				json = {},
				push_counters = {},
				patterns = {
					"validate": /^[a-zA-Z][a-zA-Z0-9_-]*(?:\[(?:\d*|[a-zA-Z0-9_-]+)\])*$/,
					"key":      /[a-zA-Z0-9_-]+|(?=\[\])/g,
					"push":     /^$/,
					"fixed":    /^\d+$/,
					"named":    /^[a-zA-Z0-9_-]+$/
				};

			this.build = function( base, key, value ) {
				base[ key ] = value;

				return base;
			};

			this.push_counter = function( key ) {
				if ( push_counters[ key ] === undefined ) {
					push_counters[ key ] = 0;
				}

				return push_counters[ key ]++;
			};

			$.each( form.serializeArray(), function() {
				// skip invalid keys
				if ( ! patterns.validate.test( this.name ) ) {
					return;
				}

				var k,
					keys = this.name.match( patterns.key ),
					merge = this.value,
					reverse_key = this.name;

				while( ( k = keys.pop() ) !== undefined ) {

					// adjust reverse_key
					reverse_key = reverse_key.replace( new RegExp( "\\[" + k + "\\]$" ), '' );

					// push
					if ( k.match( patterns.push ) ) {
						merge = self.build( [], self.push_counter( reverse_key ), merge );
					}

					// fixed
					else if( k.match( patterns.fixed ) ) {
						merge = self.build( [], k, merge );
					}

					// named
					else if( k.match( patterns.named ) ) {
						merge = self.build( {}, k, merge );
					}
				}

				json = $.extend( true, json, merge );
			});

			return json;
		}
	}

	$( document ).trigger( 'CherryHandlerInit' );
} ( jQuery ) );
