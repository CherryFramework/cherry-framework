/// <reference path="jquery.d.ts" />

declare var CherryJsCore: any;

( function( $:any ) {

	class UI_Chooseicons {

		/**
		 * Timeout handler.
		 * @type {any}
		 */
		timer_id: any      = 0;

		/**
		 * Input old value.
		 * @type {string}
		 */
		old_value: string  = '';

		/**
		 * Icons content wrap class.
		 * @type {String}
		 */
		content_wrap_class = '.ui-choose-icons__content__wrap';

		/**
		 * Fade speed animation.
		 * @type {String}
		 */
		animation_speed    = 'fast';

		/**
		 * Init our element
		 */
		init: any = function() {
			$(document).on(
				'keyup',
				'.ui-choose-icons-input',
				(e:any) => this.change( e )
			);
			$(document).on(
				'blur',
				'.ui-choose-icons-input',
				(e:any) => this.leaveFocus( e )
			);
			$(document).on(
				'click',
				this.content_wrap_class + ' a',
				(e:any) => this.clickedToIcon( e )
			);
		}

		/**
		 * Click to icon event
		 *
		 * @param {any} e event handler;
		 */
		clickedToIcon: any = function( e:any ) {
			var me = e.currentTarget,
				$input = $(me).parents('.ui-choose-icons').find('input');

			$input.val(me.getAttribute('href').replace('#', ''));
			e.preventDefault();
		}

		/**
		 * Main input change event
		 *
		 * @param {any} e input change event.
		 */
		change: any = function( e:any ) {
			var $objects_wrap = $(e.target.parentNode.parentNode).find(this.content_wrap_class);
			clearTimeout(this.timer_id);
			this.timer_id = setTimeout(() => this.filter(e.target.value, $objects_wrap), 300);
		}

		/**
		 * Leave input focus
		 *
		 * @param {any} e event.
		 */
		leaveFocus:any = function( e:any ) {
			$(e.target.parentNode.parentNode).find(this.content_wrap_class).parent().fadeOut(this.animation_speed);
		}

		/**
		 * Filter objects
		 *
		 * @param {string} val [description]
		 * @param {any} $objects_wrap jquery objects wrap.
		 */
		filter:any = function(val: string, $objects_wrap: any) {
			$objects_wrap.parent().fadeIn(this.animation_speed);
			$objects_wrap.find('a').each(
				(e: any, me: any) => this.filter_object(e, $(me), val)
			);
		}

		/**
		 * Filter object by href value
		 *
		 * @param {any}    i   index.
		 * @param {any}    $me jquery object.
		 * @param {string} val query.
		 */
		filter_object:any = function(i:any, $me:any, val:string) {
			var rx = new RegExp('.*?' + val + '.*?');
			var match = $me.attr('href').match(rx);

			if ( null === match ) {
				$me.fadeOut(this.animation_speed);
			} else {
				$me.fadeIn(this.animation_speed);
			}
		}
	}

	/**
	 * Add ui element to core tree
	 */
	CherryJsCore.utilites.namespace('ui_elements.ui_chooseicons');
	CherryJsCore.ui_elements.ui_chooseicons = new UI_Chooseicons
	CherryJsCore.ui_elements.ui_chooseicons.init();

})( jQuery );
