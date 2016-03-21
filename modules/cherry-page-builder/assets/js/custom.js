jQuery( document ).ready( function( $ ) {

	var pageBuilder = function() {

		var pb = this;

		pb.init = function() {
			pb.tabs( '.cherry-settings-tabs' );
			pb.saveEvent( '.cherry-settings-tabs form' );
		};

		pb.tabs = function( selectors ) {
			jQuery( selectors + ' .tabs-section a' ).each( function( index ) {
				var id = jQuery( this ).attr( 'href' );
				if ( ! index ) {
					jQuery( this ).addClass( 'nav-tab-active' );
				} else {
					jQuery( selectors + ' .section' + id ).hide();
				}
			});
			jQuery( selectors + ' .tabs-section a' ).click( function( e ) {
				var id = jQuery( this ).attr( 'href' );
				jQuery( selectors + ' .section' ).hide();
				jQuery( selectors + ' .section' + id ).show();
				jQuery( selectors + ' .tabs-section a' ).removeClass( 'nav-tab-active' );
				jQuery( this ).addClass( 'nav-tab-active' );
				e.preventDefault();
			});
		};

		pb.saveEvent = function( selectors ) {
			jQuery( selectors ).submit( function( e ) {
				jQuery( this ).ajaxSubmit({
					success: function() {
						pb.noticeCreate( 'success', window.TMRealEstateMessage.success );
					},
					error: function() {
						pb.noticeCreate( 'failed', window.TMRealEstateMessage.failed );
					},
					timeout: 5000
				});

				e.preventDefault();
			});
		};

		pb.noticeCreate = function( type, message ) {
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
		};
	};

	var pb = new pageBuilder();
	pb.init();
} );
