jQuery( document ).on( 'ready', function( /*$, CherryJsCore*/ ) {
	var $            = jQuery,
		CherryJsCore = window.CherryJsCore;

	CherryJsCore.utilites.namespace( 'cherry5InsertShortcode' );
	CherryJsCore.cherry5InsertShortcode = {
		openPopUpButton: '.cherry5-is__open-button',
		closePopUpButton: '.cherry5-is__close-button',
		insertionWindow: '.cherry5-is__modal-window',
		insertionPopUp: '.cherry5-is__popup',
		insertionPopUpBg: '.cherry5-is__background',
		shortcodeOptionHolder: '.cherry5-is__shortcodes-options',
		shortcodeOptionHolderClass: '.cherry5-is__shortcode-form.show',
		insertButton: '.cherry5-is__insert-button',
		spinner: '.cherry-loader-wrapper',
		contentArea: '.cherry5-is__shortcode-content',
		sidebar: '.cherry5-is__popup-sidebar',
		sidebarButton: '.cherry5-is__sidebar-button',

		insertShortcodeId: 'cherry5_insert_shortcode',
		getShortcodeOptionButton: '.cherry5-is__get-shotcode',

		getShortcodeOptionInstance: null,
		selectedContent: '',
		openedShortcode: [],

		devMode: false,

		sessionStorage:{
			optionsTemplate: {},
			activeShortcode: {}
		},

		init: function() {
			this.devMode                    = ( 'true' === window.cherry5InsertShortcode.devMode ) ? true : false ;
			this.sessionStorage             = ( ! this.devMode ) ? this.getState() || this.sessionStorage : this.sessionStorage ;
			this.getShortcodeOptionInstance = new CherryJsCore.CherryAjaxHandler(
				{
					handlerId: this.insertShortcodeId,
					successCallback: this.getShortcodeOptionCallback.bind( this )
				}
			);

			this.addEvent();
			this.switchFirstShortcode();
		},
		addEvent: function() {
			$( 'body' )
				.on( 'click.cherry5InsertShortcode', this.openPopUpButton, this.showPopUp.bind( this ) )
				.on( 'click.cherry5InsertShortcode', this.closePopUpButton, this.hidePopUp.bind( this ) )
				.on( 'click.cherry5InsertShortcode', this.insertionPopUpBg, this.hidePopUp.bind( this ) )
				.on( 'click.cherry5InsertShortcode', this.insertButton, this.insertShortcode.bind( this ) )
				.on( 'click.cherry5InsertShortcode', this.insertButton, this.hidePopUp.bind( this ) )
				.on( 'click.cherry5InsertShortcode', this.getShortcodeOptionButton, this.getShortcodeOption.bind( this ) )
				.on( 'click.cherry5InsertShortcode', this.sidebarButton, this.openSodebar.bind( this ) );
		},

		showPopUp: function() {
			var activeShortcode;

			if ( window.tinymce ) {
				if ( window.tinymce.get( 'content' ) && window.tinymce.get( 'content' ).selection ) {
					this.selectedContent = window.tinymce.get( 'content' ).selection.getContent( { format: 'text' } );
				}

				activeShortcode = this.sessionStorage.activeShortcode;
				this.afterShowShortcode( activeShortcode.pluginSlug + '-' + activeShortcode.shortcodeSlug, activeShortcode.enclosing );
			}

			$( this.insertionWindow ).addClass( 'open show' );
			$( this.insertionPopUp, this.insertionWindow ).off( 'animationend' );
		},
		hidePopUp: function() {
			this.selectedContent = '';
			this.openedShortcode = [];

			$( this.insertionWindow ).removeClass( 'open' );
			$( this.insertionPopUp, this.insertionWindow ).on( 'animationend', this.hideModalWindow.bind( this ) );
		},
		hideModalWindow: function() {
			$( this.insertionWindow ).removeClass( 'show' );
		},

		switchFirstShortcode: function() {
			var data   = this.sessionStorage.activeShortcode,
				target = null ;

			if ( data.pluginSlug && data.shortcodeSlug ) {
				target = $( '#button-' + data.pluginSlug + '-' + data.shortcodeSlug );
			} else {
				target = $( '.cherry5-is__shortcode-list:first > li:first .cherry5-is__get-shotcode' );
			}

			this.switchShortcode( target );
		},

		getShortcodeOption: function( event ) {
			this.switchShortcode( $( event.target ) );
			return false;
		},
		switchShortcode: function( targetButton ) {
			var pluginSlug       = targetButton.data( 'plugin-slug' ),
				shortcodeSlug    = targetButton.data( 'shortcode-slug' ),
				shortcodeId      = pluginSlug + '-' + shortcodeSlug,
				shortcodeSection = $( '#' + shortcodeId ),
				data;

			$( this.insertButton, this.insertionPopUp ).attr( 'disabled', 'disabled' );

			// Remove class to active button
			this.hide( $( this.getShortcodeOptionButton + '.show' ) );

			// Add class to active button
			this.show( targetButton );
			this.hide( $( this.shortcodeOptionHolderClass ) );

			if ( shortcodeSection[0] ) {
				data = this.sessionStorage.optionsTemplate[ shortcodeId ];
				this.setProxyStorage( data, shortcodeId );
				this.show( shortcodeSection );
				this.afterShowShortcode( shortcodeId, data.enclosing );
				return;
			}

			if ( ! this.devMode && this.sessionStorage.optionsTemplate[ shortcodeId ] ) {
				this.getShortcodeOptionCallback( { data: this.sessionStorage.optionsTemplate[ shortcodeId ] } );
				return;
			}

			this.show( $( this.spinner, this.shortcodeOptionHolder ) );

			this.getShortcodeOptionInstance.sendData( { 'plugin_slug': pluginSlug, 'shortcode_slug': shortcodeSlug } );
		},
		getShortcodeOptionCallback: function( response ) {
			var data          = response.data,
				holder        = $( this.shortcodeOptionHolder, 'body' ),
				shortcodeId;

			if ( data.error ) {
				window.console.log( data.message );
				return;
			}

			shortcodeId = data.pluginSlug + '-' + data.shortcodeSlug;

			this.setProxyStorage( data, shortcodeId );

			this.hide( $( this.spinner, this.shortcodeOptionHolder ) );
			holder.append( data.html );

			this.afterShowShortcode( shortcodeId, data.enclosing );

			$( document ).trigger( 'cherryInterfaceBuilder' );
			$( 'body' ).trigger( { type: 'cherry-ui-elements-init', _target: $( '#' + shortcodeId, holder ) } );
		},
		setProxyStorage: function( data, id ) {
			if ( data ) {
				this.sessionStorage.optionsTemplate[ id ] = data;
				this.sessionStorage.activeShortcode = data;
			}

			if ( ! this.devMode ) {
				this.setState();
			}
		},
		afterShowShortcode: function( shortcodeId, enclosing ) {
			var defaultContent,
				content = '';

			if ( enclosing && -1 === this.openedShortcode.indexOf( shortcodeId ) ) {
				defaultContent = this.sessionStorage.activeShortcode.defaultContent;

				if ( this.selectedContent ) {
					content = this.selectedContent;
				} else if ( defaultContent ) {
					content = defaultContent;
				}

				$( '#' + shortcodeId + '-content' ).val( content );

				this.openedShortcode.push( shortcodeId );
			}

			$( this.insertButton, this.insertionPopUp ).removeAttr( 'disabled' );
		},

		insertShortcode: function() {
			var activeShortcode = this.sessionStorage.activeShortcode,
				slug            = activeShortcode.shortcodeSlug,
				pluginSlug      = activeShortcode.pluginSlug,
				shortcodeId     = pluginSlug + '-' + slug,
				enclosing       = activeShortcode.enclosing,
				tepmlate        = ( enclosing ) ? '[$1$2]$3[/$1]' : '[$1$2]',
				attrs           = $( 'form#' + shortcodeId ).serializeArray(),
				sortedAttra     = {},
				outputAttr      = '',
				content         = '',
				key,
				attr,
				attrName,
				output;

			if ( attrs[0] ) {
				for ( key in attrs ) {
					attr = attrs[ key ];
					attrName = attr.name.replace( /\[\S*\]/g, '' );

					if ( 'cherry5-is__shortcode-content' === attrName ) {
						continue;
					}

					if ( ! sortedAttra[ attrName ] ) {
						sortedAttra[ attrName ] = attr.value;
						continue;
					} else {
						sortedAttra[ attrName ] += ',' +  attr.value;
						continue;
					}
				}

				$.map( sortedAttra, function( elem, index ) {
					if ( elem ) {
						outputAttr += ' ' + index + '="' + elem + '"' ;
					}
				} );
			}

			if ( enclosing ) {
				content = $( '#' + shortcodeId + '-content' ).val();
			}

			output = tepmlate
						.replace( /\$1/g, slug )
						.replace( /\$2/g, outputAttr )
						.replace( /\$3/g, content );

			window.wp.media.editor.insert( output );
		},

		getState: function() {
			try {
				return JSON.parse( sessionStorage.getItem( 'cherry5-insert-shortcode' ) );
			} catch ( e ) {
				return false;
			}
		},
		setState: function() {
			try {
				sessionStorage.setItem( 'cherry5-insert-shortcode', JSON.stringify( this.sessionStorage ) );
			} catch ( e ) {
				return false;
			}
		},

		show: function( target ) {
			target.addClass( 'show' );
		},

		hide: function( target ) {
			target.removeClass( 'show' );
		},

		openSodebar: function() {
			$( this.sidebar ).toggleClass( 'open' );
		}

	};

	CherryJsCore.cherry5InsertShortcode.init();

} );
