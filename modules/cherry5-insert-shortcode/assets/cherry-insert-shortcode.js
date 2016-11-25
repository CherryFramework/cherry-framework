( function( $, CherryJsCore ) {

	CherryJsCore.utilites.namespace('cherry5InsertShortcode');
	CherryJsCore.cherry5InsertShortcode = {
		openPopUpButton: '.cherry5-is__open-button',
		closePopUpButton: '.cherry5-is__close-button',
		insertionWindow: '.cherry5-is__modal-window',
		insertionPopUp: '.cherry5-is__popup',
		insertionPopUpBg: '.cherry5-is__background',
		shortcodeOptionHolder: '.cherry5-is__shortcodes-options',
		shortcodeOptionHolderClass: '.cherry5-is__shortcode-section.show',

		insertShortcodeId: 'cherry5_insert_shortcode',
		getShortcodeOptionButton: '.cherry5-is__get-shotcode',

		getShortcodeOptionInstance: null,
		init: function(){
			this.getShortcodeOptionInstance= new CherryJsCore.CherryAjaxHandler(
				{
					handlerId: this.insertShortcodeId,
					successCallback: this.getShortcodeOptionCallback.bind( this )
				}
			);

			this.addEvent();
		},
		addEvent: function () {
			$( 'body' )
				.on( 'click.cherry5InsertShortcode', this.openPopUpButton, this.showPopUp.bind( this ) )
				.on( 'click.cherry5InsertShortcode', this.closePopUpButton , this.hidePopUp.bind( this ) )
				.on( 'click.cherry5InsertShortcode', this.insertionPopUpBg , this.hidePopUp.bind( this ) )
				.on( 'click.cherry5InsertShortcode', this.getShortcodeOptionButton, this.getShortcodeOption.bind( this ) );
		},
		showPopUp: function(){
			$( this.insertionWindow ).addClass('open show');
			$( this.insertionPopUp, this.insertionWindow ).off( 'animationend' );
		},
		hidePopUp: function(){
			$( this.insertionWindow ).removeClass('open');
			$( this.insertionPopUp, this.insertionWindow ).on( 'animationend', this.hideModalWindow.bind( this ) )
		},
		hideModalWindow: function() {
			$( this.insertionWindow ).removeClass('show');
		},
		getShortcodeOption: function( event ) {
			var target           = $( event.target ),
				pluginSlug       = target.data('plugin-slug'),
				shortcodeSlug    = target.data('shortcode-slug'),
				shortcodeSection = $( '#' + pluginSlug + '-' + shortcodeSlug );

			this.hideOptionSection( $( this.shortcodeOptionHolderClass ) );

			if ( shortcodeSection[0] ) {

				this.showOptionSection( shortcodeSection );
				return false;
			}

			this.getShortcodeOptionInstance.sendData( { 'plugin_slug': pluginSlug, 'shortcode_slug': shortcodeSlug } );
			return false;
		},
		getShortcodeOptionCallback: function( response ) {
			var data = response.data,
				error = data.error,
				holder;

			if ( error ) {
				console.log(data.message);
				return;
			}

			holder = $( this.shortcodeOptionHolder, 'body' );
			holder.append( data.html );

		},

		showOptionSection: function( target ) {
			target.addClass('show');
		},

		hideOptionSection: function( target ) {
			target.removeClass('show');
		},

	};

	CherryJsCore.cherry5InsertShortcode.init();

} )( jQuery, window.CherryJsCore ) ;
