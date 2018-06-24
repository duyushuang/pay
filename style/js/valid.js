
(function($){
    // ['number','money','require']
    var _validTypes = {
        number: {
            reg: /^\d+$/,
            msg: '必须是数字'
        },
        money: {
            reg: /^\d+(\.\d{0,2})?$/,
            msg: '金额不合法（精确到小数点后两位）'
        },
        require: {
            reg: /^.+$/,
            msg: '必填项'
        },
        mobile: {
            reg: /^[1]{1}[0-9]{10}$/,
            msg: '手机号不合法'
        }
    };

    $.fn.valid = function(){
        var inputs = $(this).find('input');
        var item = null, pre_item = null, validType = null, validReg = null, val = null, validMsg = null;
        var _validElement = null, pre_validElement = null;
        for (var i = 0; i < inputs.length; i ++){
            item = $(inputs[i]);
            val = item.val();
            validType = item.data("validtype");
            validReg = item.data("validreg");
            validMsg = item.data("validmsg");
            placeholder = item.attr('placeholder');

            if(_validTypes[validType]){
                _validElement = {
                    reg: _validTypes[validType].reg,
                    msg: _validTypes[validType].msg
                };;
            }else if(!!validReg){
                _validElement = {
                    reg: validReg,
                    msg: validMsg
                };
            }
            if(!!placeholder && !!_validElement){
                _validElement.msg = placeholder+'，'+_validElement.msg;
            }

            if(validType == 'same'){
                _validElement = pre_validElement;
            }

            if(_validElement != null && !eval(_validElement.reg).test(val)){
                layer.open({
                    content: _validElement.msg
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
                item.focus();
                return false;
            }

            if(validType == 'same' && val != pre_item.val()){
                layer.open({
                    content: _validElement.msg + '，与上面输入不一致'
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
                item.focus();
                return false;
            }

            pre_validElement = _validElement;
            _validElement = null;
            pre_item = item;
        }

        return true;
    };

    $.fn.addBankMessage = function(){
        var inputs = $(this).find('input');
        var item = null, pre_item = null, validType = null, validReg = null, val = null, validMsg = null;
        var _validElement = null, pre_validElement = null;
        for (var i = 0; i < inputs.length; i ++){
            item = $(inputs[i]);
            val = item.val();
            validType = item.data("validtype");
            validReg = item.data("validreg");
            validMsg = item.data("validmsg");
            placeholder = item.attr('placeholder');

            if(_validTypes[validType]){
                _validElement = {
                    reg: _validTypes[validType].reg,
                    msg: _validTypes[validType].msg
                };;
            }else if(!!validReg){
                _validElement = {
                    reg: validReg,
                    msg: validMsg
                };
            }
            if(!!placeholder && !!_validElement){
                _validElement.msg = placeholder+'，'+_validElement.msg;
            }

            if(validType == 'same'){
                _validElement = pre_validElement;
            }

            if(_validElement != null && !eval(_validElement.reg).test(val)){
                layer.open({
                    content: _validElement.msg
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
                item.focus();
                return false;
            }

            pre_validElement = _validElement;
            _validElement = null;
            pre_item = item;
        }

        return true;
    };

    $.fn.validUpdate = function(){
        var inputs = $(this).find('input');
        var item = null, pre_item = null, validType = null, validReg = null, val = null, validMsg = null;
        var _validElement = null, pre_validElement = null;
        for (var i = 0; i < inputs.length; i ++){
            item = $(inputs[i]);
            val = item.val();
            validType = item.data("validtype");
            validReg = item.data("validreg");
            validMsg = item.data("validmsg");
            placeholder = item.attr('placeholder');

            if(_validTypes[validType]){
                _validElement = {
                    reg: _validTypes[validType].reg,
                    msg: _validTypes[validType].msg
                };;
            }else if(!!validReg){
                _validElement = {
                    reg: validReg,
                    msg: validMsg
                };
            }
            if(!!placeholder && !!_validElement){
                _validElement.msg = placeholder+'，'+_validElement.msg;
            }

            if(validType == 'same'){
                _validElement = pre_validElement;
            }

            if(_validElement != null && !eval(_validElement.reg).test(val)){
                layer.open({
                    content: _validElement.msg
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
                item.focus();
                return false;
            }

            pre_validElement = _validElement;
            _validElement = null;
            pre_item = item;
        }

        return true;
    };

    $.fn.obj = function(){
        var inputs = $(this).find('input,select');
        var item = null, attrName = null;
        var obj = {};
        for (var i = 0; i < inputs.length; i ++) {
            item = $(inputs[i]);
            attrName = item.attr('name');
            if(!!attrName){
                obj[attrName] = item.val();
            }
            attrName = null;
        }
        return obj;
    }
})(jQuery);
