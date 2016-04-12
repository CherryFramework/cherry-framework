(function ($) {
    var UI_Chooseicons = (function () {
        function UI_Chooseicons() {
            this.timer_id = 0;
            this.old_value = '';
            this.content_wrap_class = '.ui-choose-icons__content__wrap';
            this.animation_speed = 'fast';
            this.init = function () {
                var _this = this;
                $(document).on('keyup', '.ui-choose-icons-input', function (e) { return _this.change(e); });
                $(document).on('blur', '.ui-choose-icons-input', function (e) { return _this.leaveFocus(e); });
                $(document).on('click', this.content_wrap_class + ' a', function (e) { return _this.clickedToIcon(e); });
            };
            this.clickedToIcon = function (e) {
                var me = e.currentTarget, $input = $(me).parents('.ui-choose-icons').find('input');
                $input.val(me.getAttribute('href').replace('#', ''));
                e.preventDefault();
            };
            this.change = function (e) {
                var _this = this;
                var $objects_wrap = $(e.target.parentNode.parentNode).find(this.content_wrap_class);
                clearTimeout(this.timer_id);
                this.timer_id = setTimeout(function () { return _this.filter(e.target.value, $objects_wrap); }, 300);
            };
            this.leaveFocus = function (e) {
                $(e.target.parentNode.parentNode).find(this.content_wrap_class).parent().fadeOut(this.animation_speed);
            };
            this.filter = function (val, $objects_wrap) {
                var _this = this;
                $objects_wrap.parent().fadeIn(this.animation_speed);
                $objects_wrap.find('a').each(function (e, me) { return _this.filter_object(e, $(me), val); });
            };
            this.filter_object = function (i, $me, val) {
                var rx = new RegExp('.*?' + val + '.*?');
                var match = $me.attr('href').match(rx);
                if (null === match) {
                    $me.fadeOut(this.animation_speed);
                }
                else {
                    $me.fadeIn(this.animation_speed);
                }
            };
        }
        return UI_Chooseicons;
    }());
    CherryJsCore.utilites.namespace('ui_elements.ui_chooseicons');
    CherryJsCore.ui_elements.ui_chooseicons = new UI_Chooseicons;
    CherryJsCore.ui_elements.ui_chooseicons.init();
})(jQuery);
//# sourceMappingURL=ui-chooseicons.js.map