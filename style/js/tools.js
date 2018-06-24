/**
 * 打开layerLoading
 * @param type
 * @param text
 */
function alertLayer(type, text) {
    var len = arguments.length;
    if (1 == len) {
        text = arguments[0];
        type = 2;
    }
    var text = text ? text : "加载中";
    //var loadingStyle = '<div class="PageLoading" style="margin:-8px;padding:45px 0 0; font-size:16px;text-align:center;line-height:25px;background:url(' + ctxStatic + '/images/icons/loading3.gif) no-repeat center 0px; background-size:35px 35px; border-radius:5px;">' + text + '</div>';
    var layerSettings = {
        type: 3,
        shadeClose: false,
        style: 'width:60%;background:rgba(0,0,0,0.7);color:#fff;text-align:center',
        content: ''
    };
    if (type == 1) {
        layerSettings.content = text;
    } else if (type == 2) {
        layerSettings.time = 2;
        layerSettings.content = text;
        layerSettings.shadeClose = true;
    } else {
        layerSettings.content = text;
    }
    layer.open(layerSettings);
}

(function ($) {
    //备份jquery的ajax方法
    var _ajax = $.ajax;
    //重写jquery的ajax方法
    $.ajax = function (opt) {
        opt.useDefaultError = typeof opt.useDefaultError === 'undefined' ? true : opt.useDefaultError;
        //备份opt中error和success方法
        var fn = {
            error: function (XMLHttpRequest, textStatus, errorThrown) {
            },
            success: function (data, textStatus) {
            },
            beforeSend: function (XMLHttpRequest, o) {
            },
            complete: function (XMLHttpRequest, textStatus) {
            }
        }
        if (opt.error) {
            fn.error = opt.error;
        }
        if (opt.success) {
            fn.success = opt.success;
        }
        if (opt.beforeSend) {
            fn.beforeSend = opt.beforeSend;
        }
        if (opt.complete) {
            fn.complete = opt.complete;
        }
        //扩展增强处理
        var _opt = $.extend(opt, {
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                var msg;
                if(XMLHttpRequest.status == 401){
                    // window.location.href = ctxWap+'/loginPage';
                    return ;
                }
                try{
                    var entity = JSON.parse(XMLHttpRequest.responseText);
                    if(entity.code != '14'){
                        msg = entity.error;
                    }
                }catch (e){
                    msg = errorThrown;
                }
                if (opt.useDefaultError && !this.hideLoading) {
                    if(msg){
                        layer.msg(msg);
                    }
                }
                fn.error(XMLHttpRequest, textStatus, errorThrown, entity);
            },
            success: function (data, textStatus) {
                fn.success(data, textStatus);
            },
            beforeSend: function (XMLHttpRequest, o) {
                //o.url = "http://192.168.1.82:8080" +o.url;
                // var loginHeader = $.cookie("login_header");
                // XMLHttpRequest.setRequestHeader("Content-Type","application/json");
                // if(loginHeader){
                //     var login_header = JSON.parse(loginHeader);
                //     if(!login_header.terminal) login_header.terminal = "3";
                //     XMLHttpRequest.setRequestHeader("Authorization",JSON.stringify(login_header));
                // }else {
                //     XMLHttpRequest.setRequestHeader("Authorization",JSON.stringify({
                //         terminal : '3'
                //     }));
                // }
                //XMLHttpRequest.setRequestHeader("terminal", $.cookie("terminal"));
                // if (!this.hideLoading) {
                //     alertLayer(1, "请稍后");
                // }
                fn.beforeSend(XMLHttpRequest, o);
            },
            complete: function (XMLHttpRequest, textStatus) {
                fn.complete(XMLHttpRequest, textStatus);
            }

        });
        return _ajax(_opt);
    };
})(jQuery);

function getLayerCenter() {
    return (top.document.body.clientHeight - 100) / 2;
}

layer.msg = function(msg, end){
    layer.open({
        content: msg
        ,skin: 'msg'
        ,time: 2,
        end: (end || function(){})()
    });
}

function refreshUserInfo(callBack){
    $.ajax({
        url: ctx + '/users/info',
        type: 'GET',
        success: function(data){
            (callBack || function(){})(data);
        }
    });
}

// 不需要登录
var isSendSms = false;
function sendSMSValidCode(type, mobile, callBack){
    if(isSendSms){
        return;
    }
    isSendSms = true;
    $.ajax({
        url: ctx + '/sms/validatecode/'+type+'_'+mobile,
        type: 'GET',
        success: function(data){
            (callBack || function(){})(data);
            isSendSms = false;
        },
        error: function () {
            isSendSms = false;
        }
    });
}

// 需要登录的
function sendSMSValidCodeLogin(type, mobile, callBack){
    if(isSendSms){
        return;
    }
    isSendSms = true;
    $.ajax({
        url: ctx + '/sms/user/validatecode/'+type+'_'+mobile,
        type: 'GET',
        success: function(data){
            (callBack || function(){})(data);
            isSendSms = false;
        },
        error: function () {
            isSendSms = false;
        }
    });
}

var countdown=60;
function settime(obj) {
    if (countdown == 0) {
        obj.removeAttribute("disabled");
        obj.value="获取验证码";
        countdown = 60;
        return;
    } else {
        obj.setAttribute("disabled", true);
        obj.value="重新发送(" + countdown + ")";
        countdown--;
    }
    setTimeout(function() {
            settime(obj) }
        ,1000)
}

function decimalAfter2(val){
    if(typeof val === 'number'){
        val = Number(val);
    }
    return Math.round(val * 100)/100;
}

/**
 * 格式化数字
 * @param num
 * @param decimal
 * @returns {string}
 */
function formatNumber(num, decimal){
    var _num = Number(num);
    var _orig = _num+'';
    var _position = _orig.indexOf('.');
    var _suffix = '';
    var _len = 0;
    var _arr = [];
    if(_position != -1){
        _len = decimal - (_orig.length - 1 - _position);
    }else{
        _len = decimal;
        _position = _orig.length;
        _suffix = '.';
    }
    for(var i = 0; i < _len; i++){
        _arr.push('0');
    }
    return _orig.concat(_suffix).concat(_arr.join('')).substring(0, decimal == 0 ? _position+decimal : _position+decimal+1);
}