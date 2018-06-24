var common = {
	datas : {},
	backupCss : function(obj, name, keys){
		var data = {};
		for (var k in keys) {
			var v = keys[k];
			var v2 = $(obj).css(v);
			if (!v2) v2 = '';
			data[v] = v2;
		}
		this.datas[name] = data;
	},
	comebackCss : function(obj, name){
		var data = this.datas[name];
		
		for (var k in data) {
			var v = data[k];//alert(k + '|' + v);
			//if (k == 'position') $(obj).css({'position' : v});
			$(obj).css(k, v);
			//$.curCSS(obj[0], k, v);
		}
	}
};
/*
table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).attr("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).attr("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            jQuery.uniform.update(set);
        });
*/
var check_all=function(e,name){
	name = name.replace(/([\[\]])/g, '\\$1');
	var check=$(e).is(':checked');
	$('[name='+name+']').each(function(){
		if (check) {
			$(this).prop ? $(this).prop('checked', true) : $(this).attr('checked', true);
			$(this).parents('tr').addClass('active');
		} else {
			$(this).prop ? $(this).prop('checked', false) : $(this).attr('checked', false);
			$(this).parents('tr').removeClass('active');
		}
	});
	$.uniform.update('[name='+name+']');
	return;
	var all_check=document.getElementsByName(name);
	for(var i=0;i<all_check.length;i++){
		if(!all_check[i].disabled){
			all_check[i].checked=check;
		}
	}
};
$.fn.waitImg=function(show){
	if(show==void(0))show=true;
	var obj=$(this);
	var id='waitImg_'+obj.attr('id');
	if(show){
		//show
		if($('#'+id).length==0){
			var offset=obj.offset();
			var left=offset.left+obj.width()/2-16;
			var top =offset.top+obj.height()/2-16;
			$(document.body).append('<img src="'+su+'images/wait.gif" id="'+id+'" style="width:32px; height:32px; left:'+left+'px; top:'+top+'px; position:absolute;" />');
		}
	} else {
		//hidden
		if($('#'+id).length>0){
			$('#'+id).remove();
		}
	}
	return obj;
};
var isProp = $.prop ? true : false;
var attrName = isProp ? 'prop' : 'attr';
$.fn.overflow = function(show){
	if (show == void(0)) show = true;
	var id = 'css_' + $(this).attr('sourceIndex');
	if (show) {	
		common.backupCss(this, id, ['left', 'top', 'width', 'height', 'overflow', 'position', 'background', 'border']);
		var offset = $(this).offset();
		$(this).css({
			left       : (offset.left - 1) + 'px', 
			top        : (offset.top  - 1) + 'px', 
			width      : 'auto', 
			height     : 'auto', 
			position   : 'absolute', 
			background : '#FFFBFB',
			border     : '1px solid #FFAAAA'
		});
	} else {
		common.comebackCss(this, id);
	}
};
$.fn.getData=function(url,postData,callBack){
	var post=false;
	if(postData==void(0))postData=false;
	if(postData)post=true;
	if(callBack==void(0))callBack=false;
	var obj=$(this);
	if(!post){
		$.ajax({
			type:'GET',
			url:url,
			success:function(data){
				obj.val(data);
				if(callBack)callBack();
			},
			error:function(){
				if(callBack)callBack();
			}
		});
	} else {
		$.ajax({
			type:'POST',
			url:url,
			data:postData,
			success:function(data){
				obj.val(data);
				if(callBack)callBack();
			},
			error:function(){
				if(callBack)callBack();
			}
		});
	}
	return obj;
};
$.fn.getRsData=function(url,postData,callBack){
	var post=false;
	if(postData==void(0))postData=false;
	if(postData)post=true;
	if(callBack==void(0))callBack=false;
	var obj=$(this);
	if(!post){
		$.ajax({
			type:'GET',
			url:url,
			dataType:'json',
			success:function(rs){
				if (rs.status)obj.val(rs.msg);
				if(callBack)callBack();
			},
			error:function(){
				if(callBack)callBack();
			}
		});
	} else {
		$.ajax({
			type:'POST',
			url:url,
			dataType:'json',
			data:postData,
			success:function(data){
				if (rs.status)obj.val(rs.msg);
				if(callBack)callBack();
			},
			error:function(){
				if(callBack)callBack();
			}
		});
	}
	return obj;
};
$.fn.resetPwd2=function(){
	if (confirm('您确认顶要重置操作码吗？')) {
		var obj=$(this);
		obj.attr({disabled: true});
		$.ajax({
			type:'POST',
			url:weburl2+'ajax/resetPwd2.php',
			data:'hash2='+sys_hash2,
			success:function(data){
				alert(data);
				obj.attr({disabled: false});
			},
			error:function(){
				alert('系统遇到错误，请重试！');
				obj.attr({disabled: false});
			}
		});
	}
};
$.fn.checked=function(checked){
	if (checked == void(0)) checked = -1;
	if (checked >=0 ) {
		var obj = $(this)[checked];
		if (obj) {
			$(obj).attr({checked:true});
			$(obj).click();
			$(obj).change();
			return $(obj).val();
		}
	} else {
		var ck = false;
		$(this).each(function(){
			if ($(this).attr('checked')) {
				ck = true;
				return false;
			}
		});
		return ck;
	}
};
$.fn.rVal=function(){
	var v='';
	$(this).each(function(){
		if ($(this)[attrName]('checked')) {
			v = $(this).val();
			return false;
		}
	});
	return v;
};
$.fn.checkAll = function(source, checked){
	if (checked == void(0)) checked = $(source).is(':checked');
	$(this).each(function(){
		$(this).prop ? $(this)	.prop('checked', checked) : $(this)	.attr('checked', checked);
	});
	$.uniform.update($(this));
};
$.fn.clicks = function(objs){
	$(this).each(function(){
		$(this).click();
	});
};
$.fn.shake = function(color, borderWidth, times){
	if (color == void(0)) color = '#ff0000';
	if (borderWidth == void(0)) borderWidth = 3;
	if (times == void(0)) times = 3;
	$(this).each(function() {
		var html = '<div style="border:'+borderWidth+'px solid '+color+';width:'+($(this).outerWidth() + borderWidth * 2)+'px;height:'+($(this).outerHeight() + borderWidth * 2)+'px;position:absolute;left:'+($(this).position().left - borderWidth)+'px;top:'+($(this).position().top - borderWidth)+'px;z-index:9999"></div>';
		var obj = $(html);
		$(this).parent().append(obj);
		var shakeTime = 200;
		var timesCount = 0;
		var t = setInterval(function(){
			if (obj.is(':hidden')) obj.show();
			else {
				obj.hide();
				timesCount++;
				if (timesCount == times) {
					obj.remove();
					clearInterval(t);
				}
			}
		}, shakeTime);
	});
	return this;
};
$.fn.scrollShake = function(obj, time, shakeObj){
	if (shakeObj == void(0) || shakeObj == '') shakeObj = $(this);
	if (obj == void(0) || obj == '') obj = shakeObj;
	if (time == void(0) || time == '') time = 500;
	var objThis = $(this);
	if ($(window).scrollTop() > $(obj).offset().top) {
		$("html,body").animate({scrollTop:$(obj).offset().top}, time, function(){
			shakeObj.shake();
		});
	} else shakeObj.shake();
	return $(this);
};
$.fn.checkVal = function(pattern, msg, shakeObj){
	if (msg == void(0)) msg = '';
	else msg = msg.replace(/\{val\}/g, $(this).val());
	if (shakeObj == void(0)) shakeObj = $(this);
	if (typeof(pattern) == 'object') {
		if (pattern.test($(this).val())) return true;
		else {
			if (msg) alert(msg);
			//$(this).focus().shake();
			$(this).focus().scrollShake('', '', shakeObj);
			return false;
		}
	} else {
		if ($(this).val() != pattern) return true;
		else {
			if (msg) alert(msg);
			//$(this).focus().shake();
			$(this).focus().scrollShake('', '', shakeObj);
			return false;
		}
	}
};
var dragStatus = {};
$.getMousePosition = function(e){
	var posx = 0;
	var posy = 0;

	if (!e) var e = window.event;

	if (e.pageX || e.pageY) {
		posx = e.pageX;
		posy = e.pageY;
	}
	else if (e.clientX || e.clientY) {
		posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
		posy = e.clientY + document.body.scrollTop  + document.documentElement.scrollTop;
	}

	return { 'x': posx, 'y': posy };
};
$.updatePosition = function(e) {
	var pos = $.getMousePosition(e);

	var spanX = (pos.x - lastMouseX);
	var spanY = (pos.y - lastMouseY);

	$(currentElement).css("top",  (lastElemTop + spanY));
	$(currentElement).css("left", (lastElemLeft + spanX));

};
$.fn.easydrag = function(allowBubbling){

	return this.each(function(){

		// if no id is defined assign a unique one
		if(undefined == this.id || !this.id.length) this.id = "easydrag"+(new Date().getTime());

		// set dragStatus 
		dragStatus[this.id] = "on";
		
		// change the mouse pointer
		$(this).css("cursor", "move");

		// when an element receives a mouse press
		$(this).mousedown(function(e){

			// set it as absolute positioned
			$(this).css("position", "absolute");

			// set z-index
			$(this).css("z-index", "10000");

			// update track variables
			isMouseDown    = true;
			currentElement = this;

			// retrieve positioning properties
			var pos    = $.getMousePosition(e);
			lastMouseX = pos.x;
			lastMouseY = pos.y;

			lastElemTop  = this.offsetTop;
			lastElemLeft = this.offsetLeft;

			$.updatePosition(e);

			return allowBubbling ? true : false;
		});
	});
};
$.fn.runfloatwin=function() {
	this.hide();
	this.easydrag(true);
	//$("#floatwin").ondrop(function(e, element){ alert(element + " Dropped"); });

	mleft=(document.documentElement.clientWidth-parseFloat (this.width()))/2+document.documentElement.scrollLeft;
	mtop=(document.documentElement.clientHeight -parseFloat (this.height()))/2+document.documentElement.scrollTop;

	this.css({ 'left': mleft, 'top': mtop, 'cursor':'default' });

	this.show("normal");
};
var setInt = function(str){
	str = str.trim();
	if (str == '') return 0;
	var rs = parseInt(str);
	if (isNaN(rs)) return 0;
	return rs;
};
var setFloat = function(str){
	if (typeof(str) != 'number') {
		str = str.trim();
		if (str == '') return 0;
		var rs = parseFloat(str);
		if (isNaN(rs)) return 0;
	} else {
		var rs = str;
	}
	rs = Math.floor(rs * 100 + 0.5) / 100;
	return rs;
};
String.prototype.trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g, "");
};
String.prototype.int = function() {
	return setInt(this);
};
String.prototype.float = function() {
	return setFloat(this);
};
String.prototype.format = function(args) {
	if (arguments.length > 0) {
		var result = this;
		if (arguments.length == 1 && typeof (args) == "object") {
			for (var key in args) {
				var reg=new RegExp ("({"+key+"})","g");
				result = result.replace(reg, args[key]);
			}
		} else {
			for (var i = 0; i < arguments.length; i++) {
				if(arguments[i] == undefined) {
					return this;
				} else {
					var reg=new RegExp ("(\\{"+i+"\\})","g");
					result = result.replace(reg, arguments[i]);
				}
			}
		}
		return result;
	} else {
		return this;
	}
};
function copy(a) {
	if ($.browser.msie) {
		clipboardData.setData("Text", a);
		alert("复制成功");
	} else {
		if (prompt("请你使用 Ctrl+C 复制到剪贴板", a)) {
			alert("复制成功")
		}
	}
	return false;
};
var copyText=function(text){
	var clipBoardContent,clip,trans,str,len,str,copytext,clipid;
	clipBoardContent = text;
	if(window.clipboardData){
	   window.clipboardData.clearData();
	   window.clipboardData.setData("Text", clipBoardContent);
	} else if(navigator.userAgent.indexOf("Opera") != -1){
	   window.location = clipBoardContent;
	} else if (window.netscape){
		try{
			netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	  	}catch (e){
			alert("您的当前浏览器设置已关闭此功能！请按以下步骤开启此功能！\n新开一个浏览器，在浏览器地址栏输入'about:config'并回车。\n然后找到'signed.applets.codebase_principal_support'项，双击后设置为'true'。\n声明：本功能不会危极您计算机或数据的安全！");
		}
		clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
		if (!clip) return;
		trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
		if (!trans) return;
		trans.addDataFlavor('text/unicode');
		str = new Object();
		len = new Object();
		str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
		copytext = clipBoardContent;
		str.data = copytext;
		trans.setTransferData("text/unicode",str,copytext.length*2);
		clipid = Components.interfaces.nsIClipboard;
		if (!clip) return false;
		clip.setData(trans,null,clipid.kGlobalClipboard);
	}
	return true;
};
var htmlspecialchars = function(string){
	var data = [];
	for(var i = 0 ;i <string.length;i++) {
		data.push( "&#"+string.charCodeAt(i)+";");
	}
	return data.join("");
};
function scrollText(txt, boxId, time){
	if (time == void(0)) time = 150;
	this.txt    = txt;
	this.length = txt.length;
	this.box    = $('#'+boxId);
	this.index  = 0;
	this.timeId = 0;
	this.time   = time;
	var obj     = this;
	this.start = function(){
		obj.index = 0;
		this.timeId = setInterval(function(){
			obj.index++;
			if (obj.index > obj.length) obj.index = 1;
			obj.box.html(obj.txt.substr(0, obj.index));
		}, obj.time);
	}
	this.stop = function(){
		if (this.timeId > 0) {
			clearInterval(this.timeId);
		}
	}
};
var rand = function(start, end){
	return Math.floor(Math.random() * (end - start + 1)) + start;
};
var chr = function(code) {
	return String.fromCharCode(code);
};
var getRandStr = function(len, type){
	if (len == void(0)) len = 4;
	if (type == void(0)) type = 7;
	var rs = '';
	var set_C_list = [];
	if ((type & 1) > 0) set_C_list[set_C_list.length] = 1;
	if ((type & 2) > 0) set_C_list[set_C_list.length] = 2;
	if ((type & 4) > 0) set_C_list[set_C_list.length] = 3;
	var set_C = set_C_list.length;
	if (set_C > 0) set_C--;
	for(var i = 0; i < len; i++){
		switch(set_C_list[rand(0, set_C)]){
			case 1:
				//数字
				rs += chr(rand(0x30,0x39));
			break;
			case 2:
				//大写字母
				rs += chr(rand(0x41,0x5A));
			break;
			case 3:
				//小写字母
				rs += chr(rand(0x61,0x7A));
			break;
		}
	}
	return rs;
};
/*刷钻部分*/

var isSubmitFlag = false;
var isAlert = true;
var userAgent = navigator.userAgent.toLowerCase();
var is_opera = userAgent.indexOf("opera") != -1 && opera.version();
var is_moz = (navigator.product == "Gecko") && userAgent.substr(userAgent.indexOf("firefox") + 8, 3);
var is_ie = (userAgent.indexOf("msie") != -1 && !is_opera) && userAgent.substr(userAgent.indexOf("msie") + 5, 3);
var is_ie7 = parseFloat(userAgent.substr(userAgent.indexOf("msie") + 5, 3)) > 6;
function avoidReSubmit(a) {
	dialog(400, 250, "正在提交数据", "", "<div class='submiting'>正在提交数据，请耐心等待</div>");
	if (a) {
		$('#'+a).attr({disabled: true});
	}
	allReadonly();
	if (isSubmitFlag) {
		alert("正在提交数据，请耐心等待，无需重复提交");
		return false
	} else {
		isSubmitFlag = true
	}
	return true
}
function avoidReSubmit1(a) {
	dialog(510, 240, "正在提交数据", "", "<div class='xbox'><h2>充值遇到问题？ </h2><div class='ui-tip ui-tip-info'><span class='ui-tip-icon'></span><div class='ui-tip-text'>充值完成前请不要关闭此窗口。完成充值后请根据你的情况点击下面的按钮：</div></div><p class='clearf'><strong>请在新开网上储蓄卡页面完成付款后再选择。</strong></p><div class='active-link'><a class='ui-round-btn' href='/user/topupLog/' target='_parent'><span>已完成充值</span></a>　　　　　　<a class='ui-round-btn' href='http://wpa.qq.com/msgrd?v=3&uin=188239038&site=qq&menu=yes' target='_blank' ><span>充值遇到问题</span></a></div><p><a href='/user/topup/' target='_parent'><span class='ui-black'>返回重新选择充值方式</span></a></p></div>");
	if (a) {
		$('#'+a).attr({disabled: true});
	}
	allReadonly();
	if (isSubmitFlag) {
		alert("正在提交数据，请耐心等待，无需重复提交");
		return false
	} else {
		isSubmitFlag = true
	}
	return true
}
function safequestion(a,u,p,hash,time){
     comm_fram(413, 205, "安全问题", "", "<form id=qtform action='/user/login/' method=post name=myform><div class=safetyvalidate><div id=uv_content class=content><div class=in_content><p id=top_tip class=title>绑定安全问题到账号，请您验证安全问题。</p><div class='check clearfix'><div id=selsecter><ul><li><span class=label>选择安全问题：</span><select id=questionid class=ipt_select tabindex=3 name=questionid><option value='0' selected='selected'>无安全问题</option><option value='1'>早上几点起床？</option><option value='2'>最爱吃的菜？</option><option value='3'>好朋友的名字？</option><option value='4'>你的理想体重？</option><option value='5'>爱人的生日？</option></select></select></li></ul></div><div id=mbitems><div id=question style=><ul><li><span class=label>答案：</span><input class=inputstyle type=text name='answer' tabindex=4></li></ul></div></div><div id='answer_msg'></div></div></div><p id='question_reset_tip' class='tips_area li_warn'><a tabindex=11 target=_blank href='http://wpa.qq.com/msgrd?v=3&uin=188239038&site=qq&menu=yes'>忘记安全问题，无法验证？</a></p></div><div class=btn><input type=hidden value="+hash+" name='hash2'><input type=hidden value="+time+" name='login_cookietime'><input type=hidden value="+u+" name='username'><input type=hidden value="+p+" name='password'><input class=btn_em type='submit' tabindex='8' value='确 定'><input onclick='doCut();' class=btn_dft type='button' tabindex='9' value='取 消'></div></div></form>");
	if (a) {
		$('#'+a).attr({disabled: true});
	}
	if (isSubmitFlag) {
		alert("正在提交数据，请耐心等待，无需重复提交");
		return false
	} else {
		isSubmitFlag = true
	}
	return true
}
function dialog(k, c, j, a, e) {
	var k=615;
	if ($('#'+"fulldiv").length > 0) {
		return false;
	}
	var l = $('<div id="fulldiv" style="position:absolute; z-index:1000;left:0px;clear:both; top:0px;width:'+$(document).width()+'px;height:'+$(document).height()+'px;"></div>');
	$(document.body).append(l);
	var f = $('#'+"comm_615fram");
	if (f.length > 0) {
		f.remove();
	}
	f = $('<div id="comm_615fram" class="comm_615fram"></div>');
	f.css({
		marginLeft: "-" + k / 2 + "px",
		width     : k + 'px'
	});
	if (is_ie7 || is_moz) {
		f.css({
			marginTop: "-100px",
			position : "fixed"
		});
	} else {
		var d = parent.document.body.scrollTop + parent.document.documentElement.scrollTop;
		var b = d + 80;
		var g = b > 0 ? b: 0;
		f.css({top: g + 'px'});
	}
	var m = '<div class="comm_615fram_top"></div><div class="fram_615container"><div class="r_615title"><span id="round_615container">'+ j +'</span><a href="javascript:;" class="r_close" onclick="doCut();"></a></div><div class="fram_content">';
	if (a) {
		if (a.indexOf("?") < 0) {
			a += "?";
		}
		a += "&thime=" + Math.random();
		if(e=='scrool'){
			m += '<iframe src="' + a + '" width="' + (k - 16) + 'px" height="' + (c - 16) + 'px" frameborder="0" scrolling="yes"></iframe>';
		}else
		{
			m += '<iframe src="' + a + '" width="' + (k - 16) + 'px" height="' + (c - 16) + 'px" frameborder="0" scrolling="no"></iframe>';
		}
	} else {
		m += e;
	}
	m += '</div></div><div class="comm_615fram_bottom"></div>';
	f.html(m);
	$(document.body).append(f);
	this.doCut = function() {
		//f.style.display = "none";
		//document.body.removeChild(l);
		f.hide();
		l.remove();
	};
	this.doCut2 = function(h) {
		reflesh(h)
		/*if (h)reflesh(h);
		else {
			f.hide();
			l.remove();
		}*/
	}
}

function reflesh(a) {
	if (a) {
		window.location.href = a;
	} else {
		//window.location.href = window.location.href;
		window.location.reload();
	}
}

function allReadonly() {
	/*var b = document.getElementsByTagName("input");
	for (var a = 0; a < b.length; a++) {
		b[a].readOnly = true
	}
	b = document.getElementsByTagName("textarea");
	for (var a = 0; a < b.length; a++) {
		b[a].readOnly = true
	}*/
	$('input').each(function(){
		$(this)	.attr({readOnly: true});
	});
	$('textarea').each(function(){
		$(this).attr({readOnly: true});
	});
}

function goBack() {
	window.location.href = document.referrer;
	return false
}
function getObj(a) {
	return (typeof(a) == "object") ? a: document.getElementById(a)
}
function getValue(a) {
	return $('#'+a).val();
}
function getRV(b) {
	var d = "";
	var a = document.getElementsByName(b);
	for (var c = 0; c < a.length; c++) {
		if (a[c].checked) {
			d = a[c].value
		}
	}
	return d
}
function setValue(b, a) {
	$('#'+b).val(a);
}
function hide(b) {
	var a = $('#'+b);
	if (a) {
		a.style.display = "none"
	}
}
function show(b) {
	var a = $('#'+b);
	if (a) {
		a.style.display = ""
	}
}
function PageQuery(a) {
	if (a.length > 1) {
		this.q = a.substr(1)
	} else {
		this.q = null
	}
	this.keyValuePairs = new Array();
	if (this.q) {
		for (var b = 0; b < this.q.split("&").length; b++) {
			this.keyValuePairs[b] = this.q.split("&")[b]
		}
	}
	this.getKeyValuePairs = function() {
		return this.keyValuePairs
	};
	this.getValue = function(d) {
		for (var c = 0; c < this.keyValuePairs.length; c++) {
			if (this.keyValuePairs[c].split("=")[0] == d) {
				return this.keyValuePairs[c].split("=")[1]
			}
		}
		return false
	};
	this.getParameters = function() {
		var c = new Array(this.getLength());
		for (var d = 0; d < this.keyValuePairs.length; d++) {
			c[d] = this.keyValuePairs[d].split("=")[0]
		}
		return c
	};
	this.getLength = function() {
		return this.keyValuePairs.length
	}
}
function setQS() {
	var b = new PageQuery(window.location.search);
	var c = "";
	for (var a = 0; a < b.getLength(); a++) {
		c = b.getParameters()[a];
		if ($('#'+c)) {
			$('#'+c).value = unescape(decodeURI(b.getValue(c)))
		}
	}
}
function getQuery(a) {
	var b = document.location.search.substr(1).split("&");
	for (i = 0; i < b.length; i++) {
		var c = b[i].split("=");
		if (c.length > 1 && c[0] == a) {
			return c[1]
		}
	}
	return ""
}
function addClass(b, a) {
	var c = b.className.trim();
	if (c.indexOf(a) < 0) {
		b.className = c + " " + a
	}
}
function removeClass(b, a) {
	var c = b.className.trim();
	if (c.indexOf(a) >= 0) {
		b.className = c.replace(a, "")
	}
}
function showQQ(a) {
	if (a) {
		document.write("<a href='tencent://message/?uin=" + a + "'><img width='25' height='17' border='0'  src='http://wpa.qq.com/pa?p=1:" + a + ":17' alt='' /></a>")
	}
}
function addEvent(c, b, a) {
	if (c.attachEvent) {
		c["e" + b + a] = a;
		c[b + a] = function() {
			c["e" + b + a](window.event)
		};
		c.attachEvent("on" + b, c[b + a])
	} else {
		c.addEventListener(b, a, false)
	}
}
function removeEvent(c, b, a) {
	if (c.detachEvent) {
		c.detachEvent("on" + b, c[b + a]);
		c[b + a] = null
	} else {
		c.removeEventListener(b, a, false)
	}
}
function doCheck(checks) {
	var result = true;
	isAlert = true;
	for (var i = 0; i < checks.length; i++) {
		var check = checks[i];
		var str = "";
		try {
			for (var j = 0; j < check.length; j++) {
				if (j == 0) {
					str = check[0] + "('"
				}
				if (j == (check.length - 1)) {
					str += check[j] + "')"
				} else {
					if (j > 0) {
						str += check[j] + "','"
					}
				}
			}
			result = eval(str)
		} catch(e) {
			alert(str)
		}
		if (!result && isAlert) {
			isAlert = false
		}
	}
	result = result && isAlert;
	isAlert = true;
	return result
}
function isMatch(b, a) {
	return b.test(a.trim())
}
function doAlert(c, b) {
	b.addClass('txt_fail');
	b.keyup(function(){
		if (event.keyCode > 30) {
			$(this).removeClass("txt_fail");
		}
	});
	b.attr({title:c});
	if (isAlert) {
		alert(c);
		try {
			b.focus()
		} catch(a) {
			
		}
	}
	return false
}
function isEmpty(c, a) {
	if (arguments.length == 1) {
		if (c.trim() == "") {
			return false
		} else {
			return true
		}
	} else {
		var b = $('#'+c);
		if (b.val().trim() == "") {
			return doAlert(a + "  不能为空", b)
		} else {
			return true
		}
	}
}
function isLength(e, b, c, a) {
	var d = $('#'+e);
	if (d.val().trim().length < c || d.val().trim().length > a) {
		if (c == a) {
			return doAlert(b + "  长度必须为 " + c + "位", d)
		} else {
			return doAlert(b + "  长度范围为 " + c + "～" + a, d)
		}
	} else {
		return true
	}
}
function isEqual(c, b, g, f, a) {
	if (arguments.length == 2) {
		return c.trim() == b.trim()
	} else {
		var e = $('#'+c);
		var d = $('#'+b);
		if (e.val().trim() == d.val().trim()) {
			if (a) {
				return doAlert(g + " 和 " + f + "  不允许相同", d)
			} else {
				return true
			}
		} else {
			if (a) {
				return true
			} else {
				return doAlert(g + " 和 " + f + "  不一致", d)
			}
		}
	}
}
function isRange(e, b, c, a) {
	var d = $('#'+e);
	if (parseFloat(d.val().trim()) < c || parseFloat(d.val().trim()) > a) {
		return doAlert(b + "  数值范围为 " + c + "～" + a, d)
	} else {
		return true
	}
}
function isNum(c, a) {
	if (arguments.length == 1) {
		return isMatch(/^\d*$/, c.trim())
	} else {
		var b = $('#'+c);
		if (!isMatch(/^\d*$/, b.val())) {
			return doAlert(a + "  必须为数字", b)
		} else {
			return true
		}
	}
}
function isNumber(c, a) {
	if (arguments.length == 1) {
		return isMatch(/^\d+$/, c.trim())
	} else {
		var b = $('#'+c);
		if (!isMatch(/^\d+$/, b.val())) {
			return doAlert(a + "  必须为整数", b)
		} else {
			return true
		}
	}
}
function isEmail(e, b, a) {
	if (arguments.length == 1) {
		if (isEmpty(e)) {
			return false
		}
		var c = /^[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)*@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/;
		return isMatch(c, e)
	} else {
		var d = $('#'+e);
		if (!a) {
			if (!isEmpty(d.val())) {
				return true
			}
		}
		var c = /^[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)*@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/;
		if (!isMatch(c, d.val())) {
			return doAlert(b + "  不是有效的电子邮件地址", d)
		} else {
			return true
		}
	}
}
function isStock(c, a) {
	if (arguments.length == 1) {
		return isMatch(/^\d+(\.\d{1,2})?$/, c.trim())
	} else {
		var b = $('#'+c);
		if (!isMatch(/^\d+(\.\d{1,2})?$/, b.val())) {
			return doAlert(a + "  小数点后只允许两位", b)
		} else {
			return true
		}
	}
}
function isMoney(c, a) {
	if (arguments.length == 1) {
		return isMatch(/^[+-]?\d*(,\d{3})*(\.\d+)?$/g, c.trim())
	} else {
		var b = $('#'+c);
		if (!isMatch(/^[+-]?\d*(,\d{3})*(\.\d+)?$/g, b.val())) {
			return doAlert(a + "  不是有效的数字", b)
		} else {
			return true
		}
	}
}
function checkAll(c, d, b) {
	var b = b ? b: "chkall";
	for (var a = 0; a < c.elements.length; a++) {
		var f = c.elements[a];
		if (f.name && f.name != b && (!d || (d && f.name.match(d)))) {
			f.checked = c.elements[b].checked
		}
	}
}
function thumbImg(b, a) {
	if (b.width > a) {
		b.height = b.height * a / b.width;
		b.width = a;
		b.title = "新窗口打开预览";
		b.style.cursor = "pointer";
		b.onclick = function() {
			openDynaWin("图片预览", "<img src='" + b.src + "' />")
		}
	}
}
function openDynaWin(c, b) {
	var d = "<html><head><title>" + c + " - 双赢网</title></head><body><table align='center' width='100%'><tr><td align='center' >";
	d += b + "</td></tr><tr><td align='center'><input type='button' style='font-size:9pt' value='关闭窗口' onclick='javascript:window.close()'></td></tr></table></body></html>";
	var a = window.open();
	a.document.write(d);
	a.document.close()
};
//tab切换
//bechange('.tab a','.contchange>ul');
function bechange(tabswitch,curshow) {
        $(tabswitch).mouseover(function()
		{
            $(tabswitch).removeClass();
            $(this).addClass('nov');
            $(curshow).css('display','none');
            $( curshow +':nth-child(' + ($(tabswitch).index($(this)) + 1) + ')').css('display','block');
        });
}
function comm_720fram(k, c, j, a, e) {
	if ($('#'+"fulldiv").length > 0) {
		return false;
	}
	var l = $('<div id="fulldiv" style="position:absolute; clear:both; z-index:1000;left:0px; top:0px;width:100%; height:100%;"></div>');
	$(document.body).append(l);
	var f = $('#'+"comm_720fram");
	if (f.length > 0) {
		f.remove();
	}
	
	f = $('<div id="comm_720fram" class="comm_720fram"></div>');
	f.css({
		marginLeft: "-390px",
		marginTop: "-100px"
	});
	if (is_ie7 || is_moz) {
		f.css({
			position : "fixed"
		});
	} else {
		var d = parent.document.body.scrollTop + parent.document.documentElement.scrollTop;
		var b = d + 80;
		var g = b > 0 ? b: 0;
		f.css({top: g + 'px'});
	}
	var m = '<div class="comm_720fram_top"></div><div class="fram_720container"><div class="fram_720content">';
	if (a) {
		if (a.indexOf("?") < 0) {
			a += "?";
		}
		a += "&thime=" + Math.random();
		m += '<iframe src="' + a + '" width="' + (k - 56) + 'px" height="' + (c - 16) + 'px" frameborder="0" scrolling="no"></iframe>';
	} else {
		m += e;
	}
	m += '</div></div><div class="comm_720fram_bottom"></div>';
	f.html(m);
	$(document.body).append(f);
	this.doCut = function() {
		f.hide();
		l.remove();
	};
	this.doCut2 = function(h) {
		reflesh(h)
	}
}
function kefu_x(a,hash,tit,price){
     comm_fram(413, 205, "请确定购买信息", "", "<form id=qtform action='/user/buycards/' method=post name=myform><div style='height:150px;margin:0px 10px;'><p>"+tit+"</p><p>花费:"+price+"元，你确定购买吗？</p><p>请选择推荐人</p><p><select name='spreader'><option value=''> 无推荐人，自行购买</option><option value='1'> 客服小麦推荐</option><option value='2'> 客服小粉推荐</option><option value='3'> 客服小芸推荐</option><option value='4'> 客服小黄推荐</option><option value='5'> 客服木子推荐</option><option value='6'> 客服小图推荐</option><option value='7'>客服小潘推荐</option><option value='8'>客服小徐推荐</option> <option value='13'>充值客服推荐</option><option value='14'>提现客服推荐</option></select></p><p style='text-align:right;padding:10px;'><input type='hidden' value="+hash+" name='hash2'><input type=hidden value="+a+" name='card'><input type=hidden value='add' name='type'><input type='submit' class='btn_em' value='确 定'><input onclick='doCut();' class=btn_dft type='button' tabindex='9' value='取 消'></p></div></form>");
}
function kefu_cx(a,hash,tit,price){
     comm_fram(413, 205, "请确定购买信息", "", "<form id=qtform action='/user/buycards/' method=post name=myform><div style='height:150px;margin:0px 10px;'><p>"+tit+"</p><p>花费:"+price+"元，你确定购买吗？</p><p>请选择推荐人</p><p><select name='spreader'><option value=''> 无推荐人，自行购买</option><option value='1'> 客服小麦推荐</option><option value='2'> 客服小粉推荐</option><option value='3'> 客服小芸推荐</option><option value='4'> 客服小黄推荐</option><option value='5'> 客服木子推荐</option><option value='6'> 客服小图推荐</option><option value='7'>客服小潘推荐</option><option value='8'>客服小徐推荐</option> <option value='13'>充值客服推荐</option><option value='14'>提现客服推荐</option></select></p><p style='text-align:right;padding:10px;'><input type='hidden' value="+hash+" name='hash2'><input type=hidden value="+a+" name='card'><input type=hidden value='add' name='type'><input type='submit' class='btn_em' value='确 定'><input onclick='doCut();' class=btn_dft type='button' tabindex='9' value='取 消'></p></div></form>");
}
function shua(type){
	dialog(615,554,'开始代刷服务　　无法加载到弹窗内容的用户,请点击<a href="/Shua/Order/" target="_blank" class="chengse">这里</a>','/dialog/shua/?type='+type);
	}
function comm_fram(k, c, j, a, e) {
	if ($('#'+"fulldiv").length > 0) {
		return false;
	}
	var l = $('<div id="fulldiv" style="position:absolute; clear:both; z-index:1000;left:0px; top:0px;width:100%; height:100%;"></div>');
	$(document.body).append(l);
	var f = $('#'+"comm_fram");
	if (f.length > 0) {
		f.remove();
	}
	f = $('<div id="comm_fram" class="comm_fram"></div>');
	f.css({
		marginLeft: "-" + k / 2 +30 + "px",
		marginTop: "-" + c / 2 + 70+ "px"
		
	});
	if (is_ie7 || is_moz) {
		f.css({
			position : "fixed"
		});
	}else if(is_ie6){
		f.css({top: '550px'});
	} else {
		var d = parent.document.body.scrollTop + parent.document.documentElement.scrollTop;
		var b = d + 80;
		var g = b > 0 ? b: 0;
		f.css({top: g + 'px'});
	}
	var m = '<div class="comm_fram_top"></div><div class="fram_container"><div class="r_title"><span id="round_container">'+ j +'</span><a href="javascript:;" class="r_close" onclick="doCut();"></a></div><div class="fram_content">';
	if (a) {
		if (a.indexOf("?") < 0) {
			a += "?";
		}
		a += "&thime=" + Math.random();
		m += '<iframe src="' + a + '" width="' + (k - 56) + 'px" height="' + (c - 16) + 'px" frameborder="0" scrolling="no"></iframe>';
	} else {
		m += e;
	}
	m += '</div></div><div class="comm_fram_bottom"></div>';
	f.html(m);
	$(document.body).append(f);
	this.doCut = function() {
		f.hide();
		l.remove();
	};
	this.doCut2 = function(h) {
		reflesh(h)
	}
}
var loadHTML = function(url, call){
	$.ajax({
		type:'get',
		url:url,
		dataType:"json",
		success: function(rs){
			if (rs.status) call(rs.html);
		}
	});
};
var openUrl = function(url, postData, type, call){
	if (postData == void(0)) postData = '';
	if (type == void(0)) type = 'text';
	if (call == void(0)) call = '';
	$.ajax({
		url:url,
		type:postData?'post':'get',
		data:postData,
		dataType:type,
		success: function(rs){
			if (call) call(rs);
		},
		error:function(rs){
			if (call) call({status:false,msg:rs.responseText});
		}
	});
};
var getJSONUrl = function(url, call){
	openUrl(url, '', 'json', call);
};
var postJSONUrl = function (url, postData, call){
	openUrl(url, postData, 'json', call);
};
$.fn.sendVcode = function(mobile, timeSpace0, showObj){
	var obj = $(this);
	var key = '';
	if (showObj == void(0)) {
		showObj = obj;
		key = 'val';
	} else key = 'html';
	if (timeSpace0 == void(0)) timeSpace0 = time_vcode;
	if (mobile == void(0)) mobile = '';
	var oldVal = showObj[key]();
	var times = 0;
	var time = timeSpace0;
	var timeCount = 0;
	obj.attr('disabled', true);
	postJSONUrl(urlRoot+'/ajax/member/sendVcode/', 'hash='+hash+'&mobilephone='+mobile, function(rs){
		if (rs.status) {
			alert('验证码已发送到您的手机，请注意查收。若长时间无法收到请联系客服。');
			showObj[key]((time - timeCount) + '秒后可重新发送');
			var t = setInterval(function(){
				timeCount++;
				showObj[key]((time - timeCount) + '秒后重新发送');
				if (timeCount == time) {
					obj.attr('disabled', false);
					showObj[key](oldVal);
					clearInterval(t);
				}
			}, 1000);
		} else {
			alert(rs.msg);
			obj.attr('disabled', false);
		}
	});
};
function getvcode(time,hash,cashtype){

		var time;
		var smspass = $("[name='smspass']:checked").val();
		$("#getvcode").attr({disabled: true});
		$("#getvcode").val(time+'秒后重新发送');
		$.ajax({
			url: '/ajax/getvcode.php',
			data: 'hash='+hash+'&cashtype='+cashtype+'&smspass='+smspass,
			type: "POST",
			cache: false,
			dataType:"text",
			success: function(data){
				alert(data);
			}
		});
		gettimeout(120);
	
	
}
function gettimeout(time){
	var time;
	$("#getvcode").attr({disabled: true});
	$("#getvcode").val(time+'秒后可重新发送');
	if(time==0){
	$("#getvcode").attr({disabled: false});
	$("#getvcode").val('获取验证码');
	}else{
	time--;
	setTimeout("gettimeout(" + time + ")",1000);
	}
}
var gotoUrl = function(url){
	window.location.href=url;
	return false;
};
var trimExplode = function(flag, str){
	var sp = str.split(flag);
	var rs = [];
	for (var i = 0; i < sp.length; i++) {
		var s = sp[i].trim();
		if (s != '') rs[rs.length] = s;
	}
	return rs;
};
var lolQu1 = function(name){
	var types = {'d' : '电信', 'w' : '网通', 'j' : '教育'};
	var number = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
	var type = name.substr(0, 1);
	var num = parseInt(name.substr(1));
	var name = types[type];
	if (num < 10) name += number[num];
	else if (num < 100) {
		var num0 = (num - (num % 10)) / 10;
		var num1 = num % 10;
		name += (num0 > 1 ? number[num0] : '') + '十' + (num1 > 0 ? number[num1] : '');
	}
	return name;
};
var lolUrl = function(qu, name){
	return 'http://lolbox.duowan.com/playerDetail.php?serverName='+encodeURIComponent(lolQu1(qu)) + '&playerName=' + encodeURIComponent(name);
};
var showDialog = function(html){
	$("body").append('<div class="ui-mask" id="ui-mask"></div><div class="change_success" id="change_success" style="height:auto;padding:0px;"><span class="send_close" onclick="$(this).parent().remove();$(\'#ui-mask\').remove();"></span>'+html+'</div>');
	var my=mypostion("#change_success");
	$("#change_success").css({"position":"absolute","z-index":"9999999","top":my.top+"px","left":my.left+"px"});
	$("#ui-mask").show();
	$("#change_success").show();
};
var showFightInfo = function(qu, name){
	var url = lolUrl(qu, name);
	var html = '<iframe src="'+url+'" width="630" height="480"></iframe>';
	showDialog(html);
};
var checkBankId = function(id){
	if (!/^\d+$/.test(id)) return false;
	var len = id.length;
	if (len != 16 && len != 18 && len != 19) return false;
	var arr = [];
	for (var i = 0; i < id.length; i++) {
		arr[arr.length] = id.charAt(i).int();
	}
	arr = arr.reverse();
	var v = arr.shift();
	var num = 0;
	for (var i = 0; i < arr.length; i++) {
		var n0 = arr[i];
		if (i % 2 == 0) {
			var n1 = n0 * 2;
			if (n1 > 4) {
				var s = n1.toString();
				num += s.charAt(0).int() + s.charAt(1).int();
			} else num += n1;
		} else num += n0;
	}
	return Math.ceil(num / 10) * 10 - num == v;
};
//$('form').on('submit', '.qsQuickForm', );
var iniValidator = function(){
	$('.qsQuickForm').each(function(){
		if (!$(this).attr('data-validator')) {
			var type = $(this).attr('data-type');
			if (typeof type == 'undefined') type = 'default';
			switch (type) {
				case 'default':
					$(this).formValidator({
						style:{
							tipSetAttr:'data-original-title',
							action:[{className:'has-default', idSuffix:'_color'}, {className:'fa-info-circle', idSuffix:'_tip'}],
							right:[{className:'has-success', idSuffix:'_color'}, {className:'fa-check', idSuffix:'_tip'}],
							wrong:[{className:'has-error', idSuffix:'_color'}, {className:'fa-exclamation', idSuffix:'_tip'}],
							none:[{className:'has-warning', idSuffix:'_color'}, {className:'fa-warning', idSuffix:'_tip'}]
						}
					});
				break;
				case 'simple':
					$(this).formValidator();
				break;
				case 'color-txt':
					$(this).formValidator({
						style:{
							action:[{className:'has-default', idSuffix:'_color'}],
							right:[{className:'has-success', idSuffix:'_color'}],
							wrong:[{className:'has-error', idSuffix:'_color'}],
							none:[{className:'has-warning', idSuffix:'_color'}]
						}
					});
				break;
			}
			$(this).attr('data-validator', 'yes');
		}
	});
};
iniValidator();