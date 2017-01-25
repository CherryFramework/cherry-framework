( function( $, CherryJsCore ) {
	'use strict';

	/**
	 * CherryAjaxHandler class
	 *
	 * @param {object} options Handler options
	 */

	CherryJsCore.utilites.namespace( 'CherryAjaxHandler' );
	CherryJsCore.CherryAjaxHandler = function( options ) {

		/**
		 * General default settings
		 *
		 * @type {Object}
		 */
		var self     = this,
			settings = {
				'handlerId': '',
				'cache': false,
				'processData': true,
				'url': '',
				'async': false,
				'beforeSendCallback': function() {},
				'errorCallback': function() {},
				'successCallback': function() {},
				'completeCallback': function() {}
			};

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
			if ( window.console ) {
				window.console.warn( 'Handler id not found' );
			}
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
		};

		/**
		 * Check ajax url is empty
		 */
		if ( '' === settings.url ) {

			// Check public request
			if ( 'false' === self.handlerSettings.is_public ) {
				settings.url = window.ajaxurl;
			} else {
				settings.url = window.cherryHandlerAjaxUrl.ajax_url;
			}
		}

		/**
		 * Init ajax request
		 *
		 * @return {Void}
		 */
		self.send = function() {
			if ( self.ajaxProcessing ) {
				CherryJsCore.cherryHandlerUtils.noticeCreate( 'error-notice', self.handlerSettings.sys_messages.wait_processing, self.handlerSettings.is_public );
			}
			self.ajaxProcessing = true;

			self.ajaxRequest = jQuery.ajax( {
				type: self.handlerSettings.type,
				url: settings.url,
				data: self.data,
				cache: settings.cache,
				dataType: self.handlerSettings.data_type,
				processData: settings.processData,
				beforeSend: function( jqXHR, ajaxSettings ) {
					if ( null !== self.ajaxRequest && ! settings.async ) {
						self.ajaxRequest.abort();
					}

					if ( settings.beforeSendCallback && 'function' === typeof( settings.beforeSendCallback ) ) {
						settings.beforeSendCallback( jqXHR, ajaxSettings );
					}
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					$( document ).trigger( {
						type: 'cherry-ajax-handler-error',
						jqXHR: jqXHR,
						textStatus: textStatus,
						errorThrown: errorThrown
					} );

					if ( settings.errorCallback && 'function' === typeof( settings.errorCallback ) ) {
						settings.errorCallback( jqXHR, textStatus, errorThrown );
					}
				},
				success: function( data, textStatus, jqXHR ) {
					self.ajaxProcessing = false;

					$( document ).trigger( {
						type: 'cherry-ajax-handler-success',
						response: data,
						jqXHR: jqXHR,
						textStatus: textStatus
					} );

					if ( settings.successCallback && 'function' === typeof( settings.successCallback ) ) {
						settings.successCallback( data, textStatus, jqXHR );
					}

					CherryJsCore.cherryHandlerUtils.noticeCreate( data.type, data.message, self.handlerSettings.is_public );
				},
				complete: function( jqXHR, textStatus ) {
					$( document ).trigger( {
						type: 'cherry-ajax-handler-complete',
						jqXHR: jqXHR,
						textStatus: textStatus
					} );

					if ( settings.completeCallback && 'function' === typeof( settings.completeCallback ) ) {
						settings.completeCallback( jqXHR, textStatus );
					}
				}

			} );
		};

		/**
		 * Send data ajax request
		 *
		 * @param  {Object} data User data
		 * @return {Void}
		 */
		self.sendData = function( data ) {
			var sendData = data || {};
				self.data = {
					'action': self.handlerSettings.action,
					'nonce': self.handlerSettings.nonce,
					'data': sendData
				};

			self.send();
		};

		/**
		 * Send form serialized data
		 * @param  {String} formId Form selector
		 * @return {Void}
		 */
		self.sendFormData = function( formId ) {
			var form = $( formId ),
				data;

			data = CherryJsCore.cherryHandlerUtils.serializeObject( form );

			self.sendData( data );
		};
	};

	CherryJsCore.utilites.namespace( 'cherryHandlerUtils' );
	CherryJsCore.cherryHandlerUtils = {
		/**
		 * Rendering notice message
		 *
		 * @param  {String} type    Message type
		 * @param  {String} message Message content
		 * @return {Void}
		 */
		noticeCreate: function( type, message, isPublicPage ) {
			var notice,
				rightDelta = 0,
				timeoutId,
				isPublic = isPublicPage || false;

			if ( ! message || 'true' === isPublic ) {
				return false;
			}

			notice = $( '<div class="cherry-handler-notice ' + type + '"><span class="dashicons"></span><div class="inner">' + message + '</div></div>' );

			$( 'body' ).prepend( notice );
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
				var topDelta = 100;

				$( '.cherry-handler-notice' ).each( function() {
					$( this ).css( { top: topDelta } );
					topDelta += $( this ).outerHeight( true );
				} );
			}
		},

		/**
		 * Serialize form into
		 *
		 * @return {Object}
		 */
		serializeObject: function( form ) {

			var self = this,
				json = {},
				pushCounters = {},
				patterns = {
					'validate': /^[a-zA-Z][a-zA-Z0-9_-]*(?:\[(?:\d*|[a-zA-Z0-9_-]+)\])*$/,
					'key':      /[a-zA-Z0-9_-]+|(?=\[\])/g,
					'push':     /^$/,
					'fixed':    /^\d+$/,
					'named':    /^[a-zA-Z0-9_-]+$/
				};

			this.build = function( base, key, value ) {
				base[ key ] = value;

				return base;
			};

			this.push_counter = function( key ) {
				if ( undefined === pushCounters[ key ] ) {
					pushCounters[ key ] = 0;
				}

				return pushCounters[ key ]++;
			};

			$.each( form.serializeArray(), function() {
				var k, keys, merge, reverseKey;

				// Skip invalid keys
				if ( ! patterns.validate.test( this.name ) ) {
					return;
				}

				keys = this.name.match( patterns.key );
				merge = this.value;
				reverseKey = this.name;

				while ( undefined !== ( k = keys.pop() ) ) {

					// Adjust reverseKey
					reverseKey = reverseKey.replace( new RegExp( '\\[' + k + '\\]$' ), '' );

					// Push
					if ( k.match( patterns.push ) ) {
						merge = self.build( [], self.push_counter( reverseKey ), merge );
					} else if ( k.match( patterns.fixed ) ) {
						merge = self.build( [], k, merge );
					} else if ( k.match( patterns.named ) ) {
						merge = self.build( {}, k, merge );
					}
				}

				json = $.extend( true, json, merge );
			});

			return json;
		}
	};
}( jQuery, window.CherryJsCore ) );
