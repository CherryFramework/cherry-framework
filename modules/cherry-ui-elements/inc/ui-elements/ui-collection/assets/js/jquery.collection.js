/**
 * Collection control
 * Author: Guriev Eugen
 */
( function( $ ){
	$.fn.Ininite_Control = function( options ) {
		var me = this;

		me.options = $.extend(
			{
				"container":   ".cherry-infinite-container",
				"sortable":    ".cherry-infinite-sortable",
				"row":         ".cherry-infinite-row",
				"add":         ".cherry-infinite-main-add",
				"insert":      ".cherry-infinite-add",
				"remove":      ".cherry-infinite-remove",
				"placeholder": ".cherry-ui-state-highlight",
				"handle":      ".cherry-infinite-order",
				"order":       ".cherry-infinite-order"
			},
			options
		);

		/**
		 * Initialize my plugin
		 */
		me.init = function() {
			$( me.options.sortable ).sortable({
				helper : function(e, ui) {
					ui.children().each(function() {
						$( this ).width( $( this ).width() );
					});
					return ui;
				},
				forcePlaceholderSize: true,
				placeholder:          me.options.placeholder,
				handle:               me.options.handle
			});
		};

		/**
		 * Run after change rows
		 */
		me.after_change = function(){
			$( me.options.sortable + ' ' + me.options.row ).each(
				function( index ) {
					$( this ).find( me.options.order ).html( '<span>' + ( index + 1 ) + '</span>' );
				}
			);
		};

		/**
		 * Get new row
		 * @return {[type]} jquery dom element
		 */
		me.get_new_row = function(){
			var new_obj = $( me.options.row ).first().clone();
			$( new_obj ).find(':input').each(
				function() {
					switch(this.type) {
						case 'password':
						case 'select-multiple':
						case 'select-one':
						case 'text':
						case 'textarea':
							$(this).val('');
							break;
						case 'checkbox':
						case 'radio':
							this.checked = false;
					}
				}
			);
			return new_obj;
		};

		me.init();

		/**
		 * Append row to me.options.sortable
		 */
		$( document ).on(
			'click',
			me.options.add,
			function( e ) {
				var new_obj = me.get_new_row();
				new_obj.appendTo( $( me.options.row ).parent() );
				me.after_change();
				e.preventDefault();
			}
		);

		/**
		 * Insert before
		 */
		$( document ).on(
			'click',
			me.options.insert,
			function( e ) {
				var new_obj = me.get_new_row();
				$( this ).parent().parent().before( new_obj );
				me.after_change();
				e.preventDefault();
			}
		);

		/**
		 * Remove one row
		 */
		$( document ).on(
			'click',
			me.options.remove,
			function( e ) {
				var $row = $( this ).parent().parent(),
					$container = $row.parent();
				if ( $container.find( me.options.row ).length > 1 ) {
					if ( confirm( 'Are you sure?' ) ) {
						$row.remove();
					}
				}
			}
		);

	};

} )( jQuery );