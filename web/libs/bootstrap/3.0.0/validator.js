define(function(require, exports, module) {
    var Validator = require('arale.validator');

    Validator.addRule(
        'idcard',
        /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/,
        '{{display}}格式不正确!'
    );

    Validator.addRule(
        'truename',
        function(options) {
            /^([\u4E00-\uFA29]|[\uE7C7-\uE7F3])*$/i,

            var l = element.val().length;
            if (l > 5 or l < 2) {
                return false;
            }
            return true;
        },
        '{{display}}只能由中文字组成!'
    );

    Validator.addRule(
        'username',
        /^([\u4E00-\uFA29]|[\uE7C7-\uE7F3]|[a-zA-Z0-9_])*$/i,
        '{{display}}只能由中文字、英文字母、数字及下划线组成!'
    );

    var BootstrapValidator = Validator.extend({
        attrs: {
            explainClass: "help-block",
            itemClass: "control-group",
            itemHoverClass: "on-hover",
            itemFocusClass: "in-focus",
            itemErrorClass: "has-error",
            inputClass: "input-with-feedback",
            textareaClass: "input-with-feedback",
            showMessage: function(message, element) {
                message = '<span class="text-error">' + message + '</span>';
                this.getExplain(element).html(message);
                this.getItem(element).addClass(this.get("itemErrorClass"));
            }
        },
        getExplain: function(ele) {
            var item = this.getItem(ele);
            var explain = item.find("." + this.get("explainClass"));
            if (explain.length == 0) {
                var explain = $('<div class="' + this.get("explainClass") + '"></div>').appendTo(item.find('.controls'));
            }
            return explain;
        }
    });

    module.exports = BootstrapValidator;
});
