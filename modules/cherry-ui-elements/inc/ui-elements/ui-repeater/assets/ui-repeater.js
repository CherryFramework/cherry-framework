/**
 * Repeater
 */
(function( $, CherryJsCore ) {
	'use strict';

	CherryJsCore.utilites.namespace( 'ui_elements.repeater' );

	CherryJsCore.ui_elements.repeater = {

		repeaterContainerClass: '.cherry-ui-repeater-container',
		repeaterListClass: '.cherry-ui-repeater-list',
		repeaterItemClass: '.cherry-ui-repeater-item',
		repeaterItemHandleClass: '.cherry-ui-repeater-actions-box',
		repeaterTitleClass: '.cherry-ui-repeater-title',

		addItemButtonClass: '.cherry-ui-repeater-add',
		removeItemButtonClass: '.cherry-ui-repeater-remove',
		toggleItemButtonClass: '.cherry-ui-repeater-toggle',

		minItemClass: 'cherry-ui-repeater-min',
		sortablePlaceholderClass: 'sortable-placeholder',

		init: function() {
			$( document ).on( 'ready', this.addEvents.bind( this ) );
		},

		addEvents: function() {
			$( 'body' )

			// Delegate events
				.on( 'click', this.addItemButtonClass, { 'self': this }, this.addItem )
				.on( 'click', this.removeItemButtonClass, { 'self': this }, this.removeItem )
				.on( 'click', this.toggleItemButtonClass, { 'self': this }, this.toggleItem )
				.on( 'change', this.repeaterListClass + ' input, ' + this.repeaterListClass + ' textarea, ' + this.repeaterListClass + ' select', { 'self': this }, this.changeWrapperLable )

			// Custom events
				.on( 'sortable-init', { 'self': this }, this.sortableItem );

			$( document )
				.on( 'cherry-ui-elements-init', { 'self': this }, this.sortableItem );

			this.triggers();
		},

		triggers: function( $target ) {
			$( 'body' ).trigger( 'sortable-init' );

			if ( $target ) {
				$( document ).trigger( 'cherry-ui-elements-init', { 'target': $target } );
			}

			return this;
		},

		addItem: function( event ) {
			var self        = event.data.self,
				$list       = $( this ).prev( self.repeaterListClass ),
				index       = $list.data( 'index' ),
				tmplName    = $list.data( 'name' ),
				rowTemplate = wp.template( tmplName ),
				widgetId    = $list.data( 'widget-id' ),
				data        = { index: index };

			widgetId = '__i__' !== widgetId ? widgetId : $list.attr( 'id' ) ;

			if ( widgetId ) {
				data.widgetId = widgetId;
			}

			$list.append( rowTemplate( data ) );

			index++;
			$list.data( 'index', index );

			self
				.triggers( $( self.repeaterItemClass + ':last', $list ) )
				.stopDefaultEvent( event );
		},

		removeItem: function( event ) {
			var self  = event.data.self,
				$list = $( this ).closest( self.repeaterListClass );

			self.applyChanges( $list );

			$( this ).closest( self.repeaterItemClass ).remove();

			self
				.triggers()
				.stopDefaultEvent( event );
		},

		toggleItem: function( event ) {
			var self = event.data.self,
				$container = $( this ).closest( self.repeaterItemClass );

			$container.toggleClass( self.minItemClass );

			self.stopDefaultEvent( event );
		},

		sortableItem: function( event ) {
			var self  = event.data.self,
				$list = $( self.repeaterListClass ),
				$this,
				initFlag;

			$list.each( function( indx, element ) {
				$this    = $( element );
				initFlag = $( element ).data( 'sortable-init' );

				if ( ! initFlag ) {
					$this.sortable( {
						items: self.repeaterItemClass,
						handle: self.repeaterItemHandleClass,
						cursor: 'move',
						scrollSensitivity: 40,
						forcePlaceholderSize: true,
						forceHelperSize: false,
						helper: 'clone',
						opacity: 0.65,
						placeholder: self.sortablePlaceholderClass,
						create: function() {
							$this.data( 'sortable-init', true );
						},
						update: function( event ) {
							var target = $( event.target );

							self.applyChanges( target );
						}
					} );
				} else {
					$this.sortable( 'refresh' );
				}
			} );
		},

		changeWrapperLable: function( event ) {
			var self        = event.data.self,
				$list       = $( self.repeaterListClass ),
				titleFilds  = $list.data( 'title-field' ),
				$this       = $( this ),
				value,
				parentItem;

			if ( titleFilds && $this.closest( '.' + titleFilds + '-wrap' )[0] ) {
				value       = $this.val(),
				parentItem  = $this.closest( self.repeaterItemClass );

				$( self.repeaterTitleClass, parentItem ).html( value );
			}

			self.stopDefaultEvent( event );
		},

		applyChanges: function( target ) {
			if ( undefined !== wp.customize ) {
				$( 'input[name]:first, select[name]:first', target ).change();
			}

			return this;
		},

		stopDefaultEvent: function( event ) {
			event.preventDefault();
			event.stopImmediatePropagation();
			event.stopPropagation();

			return this;
		}
	};

	CherryJsCore.ui_elements.repeater.init();

}( jQuery, window.CherryJsCore ) );
