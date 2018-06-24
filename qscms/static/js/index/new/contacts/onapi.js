"use strict";
var Api = function(urlPrefix) {
    var self = this;
    self._urlPrefix = urlPrefix;
    self._lastCaptchaId = null;
};
Api.prototype.getCaptcha = function() {
    var self = this;
    return $.get(webUrl + 'qscms/static/images/vcode3.php').then(function(data) {
        self._lastCaptchaId = data.id;
        return data;
    });
};
Api.prototype.send = function(captchaCode, data) {
    var self = this;
    data._captcha = {
        id: self._lastCaptchaId,
        code: captchaCode
    };
    return $.post(webUrl + 'new/cn/contacts', data);
};
$(function() {
    var api = new Api(window.REGISTRATION_ONAPI_URL_PREFIX);
    var reloadCaptcha = function() {
        api.getCaptcha().then(function(data) {
            var captchaData = data.captcha;
            var canvas = $('#reg-canvas')[0];
            var context = canvas.getContext('2d');
            context.clearRect(0, 0, 100, 100);
            var x = -1,
            y = -1;
            captchaData.split('\n').forEach(function(line) {
                y++;
                x = -1;
                line.split('').forEach(function(char) {
                    x++;
                    if (char == '#') {
                        context.fillRect(x, y, 1, 1);
                    }
                });
            });
            window.captchaData = captchaData;
        });
    };
    var addError = function(form, text, inputName) {
        form = form || $('#contact_form').get(0);
        $(form).find('[data-form-errors-container]').append('<p class="error">' + $.langLabelTranslate(text, text) + '</p>');
        if (inputName) {
            $(form).find('input[name=' + inputName + ']').addClass('warn');
        }
    };
    var clearError = function(form) {
        form = form || $('#contact_form').get(0);
        $(form).find('.warn').removeClass('warn');
        $(form).find('[data-form-errors-container]').html('');
    };
    var contactSend = function(form) {
        form = form || $('.register-feedback-form:first').get(0);
        clearError(form);
        var params = {};
        $(form).find('input[name],select,textarea').each(function() {
            var input = $(this);
            input.removeClass('warn');
            var name = input.attr('name');
            var val = input.val();
            if (input.attr('type') === 'checkbox') {
                val = input.is(':checked');
            }
            params[name] = val;
        });
        params.isRecaptcha = 0;
        var isError = false;
        if (!params.vcode) {
            addError(form, 'error_not_input_picture_code', 'captcha_code');
            isError = true;
        } else {
            params.vcode = params.vcode.trim();
            if (!/^\d{5}$/.test(params.vcode)) {
                addError(form, 'error_invalid_image_code', 'captcha_code');
                isError = true;
            }
        }
        if (!params.theme) {
            addError(form, 'Not select theme', 'theme');
        }
        if (!params.message) {
            addError(form, 'error_not_input_name', 'name');
        }
        if (!params.email) {
            addError(form, 'error_not_input_email', 'email');
            isError = true;
        }
        if (!isError) {
            window.loadingLayer.show();
            api.send(params.vcode, params).then(function(data) {
                window.loadingLayer.hide();
                alert($.langLabelTranslate('message_contact_form_send_success'));
                window.location.href = './..'
            }).fail(function(dataErr) {
                window.loadingLayer.hide();
                var errTextLabel = dataErr.responseText;
                reloadCaptcha();
                addError(null, errTextLabel);
            });
        }
    }
    reloadCaptcha();
    $('#captcha-reload-element').on('click',
    function() {
        reloadCaptcha();
        return false;
    });
    $('#contact_form').submit(function() {
        try {
            contactSend(this);
        } catch(e) {
            console.error(e);
        } finally {
            return false;
        }
    });
    window.api = api;
    window.reloadCaptcha = reloadCaptcha;
    window.addError = addError;
});
var formIsSendProcess = false;