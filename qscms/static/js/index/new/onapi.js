"use strict";
	var Api=function(urlPrefix){
		var self=this;self._urlPrefix=urlPrefix;self._lastCaptchaId=null;
	};
	Api.prototype.getCaptcha=function(){
		var self=this;return $.get(webUrl+'qscms/static/images/vcode3.php').then(function(data){self._lastCaptchaId=data.id;return data;});
	};
	Api.prototype.register=function(captchaCode,data){
		var self=this;data._captcha={id:self._lastCaptchaId,code:captchaCode};
	//data.hash = hash;
		$('#submit_check').prop('disabled', true);
	return $.post(webUrl+'new/cn/registration',data);
	
};
	
	$(function(){
		var api=new Api(window.REGISTRATION_ONAPI_URL_PREFIX);


var reloadCaptcha=function(){
	api.getCaptcha().then(function(data){
		var captchaData=data.captcha;
		var canvas=$('#reg-canvas')[0];
		var context=canvas.getContext('2d');
		context.clearRect(0,0,100,100);
		var x=-1,y=-1;captchaData.split('\n').forEach(function(line){
		y++;x=-1;line.split('').forEach(function(char){x++;if(char=='#'){context.fillRect(x,y,1,1);}});
	});
	window.captchaData=captchaData;
	});
};

	var addError=function(form,text,inputName){
		
		form=form||$('.register-feedback-form:first').get(0);
		$(form).find('[data-form-errors-container]').append('<p class="error">'+ $.langLabelTranslate(text,text)+'</p>');
		
		if(inputName){$(form).find('input[name='+ inputName+']').addClass('warn');}
	};
		var clearError=function(form){
			form=form||$('.register-feedback-form:first').get(0);
			$(form).find('.warn').removeClass('warn');
			$(form).find('[data-form-errors-container]').html('');
		};
		var promReferer='';
		var siteReferrer='';
		var checkForcePassword=function(password){
			var FORCE_TYPE={SIMPLE:{text:'password_force_is_simple',color:'#FF0303'},MIDDLE:{text:'password_force_is_middle',color:'#848E11'},GOOD:{text:'password_force_is_good',color:'#2D6B0F'}};
			var s_letters="qwertyuiopasdfghjklzxcvbnm";
			var b_letters="QWERTYUIOPLKJHGFDSAZXCVBNM";
			var digits="0123456789";
			var specials="!@#$%^&*()_-+=\|/.,:;[]{}";
			var is_s=false;
			var is_b=false;
			var is_d=false;
			var is_sp=false;
			for(var i=0;i<password.length;i++){
				if(!is_s&&s_letters.indexOf(password[i])!=-1)is_s=true;else if(!is_b&&b_letters.indexOf(password[i])!=-1)is_b=true;else if(!is_d&&digits.indexOf(password[i])!=-1)is_d=true;else if(!is_sp&&specials.indexOf(password[i])!=-1)is_sp=true;}
var rating=0;var result=FORCE_TYPE.SIMPLE;if(is_s)rating++;if(is_b)rating++;if(is_d)rating++;if(is_sp)rating++;if(password.length<6&&rating<3)result=FORCE_TYPE.SIMPLE;else if(password.length<6&&rating>=3)result=FORCE_TYPE.MIDDLE;else if(password.length>=8&&rating<3)result=FORCE_TYPE.MIDDLE;else if(password.length>=8&&rating>=3)result=FORCE_TYPE.GOOD;else if(password.length>=6&&rating==1)result=FORCE_TYPE.SIMPLE;else if(password.length>=6&&rating>1&&rating<4)result=FORCE_TYPE.MIDDLE;else if(password.length>=6&&rating==4)result=FORCE_TYPE.GOOD;return result;};
	var register=function(form){form=form||$('.register-feedback-form:first').get(0);clearError(form);var params={};$(form).find('input[name],select,textarea').each(function(){var input=$(this);input.removeClass('warn');var name=input.attr('name');var val=input.val();if(input.attr('type')==='checkbox'){val=input.is(':checked');}
params[name]=val;});
params.isRecaptcha=0;
var isError=false;
if(!params.firstname){addError(form,'error_not_input_name','firstname');isError=true;}
if(!params.email){addError(form,'error_not_input_email','email');isError=true;}

if(!params.phone&&params.site_id!=8){addError(form,'error_not_input_phone','phone');isError=true;}
if(!params.phoneVcode&&params.site_id!=8){addError(form,'Cell phone verification code can not be empty','phoneVcode');isError=true;}

if(!params.captcha_code){addError(form,'error_not_input_picture_code','captcha_code');isError=true;}else{params.captcha_code=params.captcha_code.trim();if(!/^\d{5}$/.test(params.captcha_code)){addError(form,'error_invalid_image_code','captcha_code');isError=true;}}

if(!params.agree){addError(form,'error_not_read_the_warning_page','agree');isError=true;}
if(!params.country){addError(form,'error_not_select_the_country','agree');isError=true;}

if(!params.password){addError(form,'not_input_passwords','agree');isError=true;}
if(params.password && params.password != params.password_confirm){addError(form,'error_passwords_dont_coincide','password');addError(form,'','password_confirm');isError=true;}

if(!params.safePassword){addError(form,'Two level password can not be empty','agree');isError=true;}
if(params.safePassword && params.safePassword != params.safePassword_confirm){
	addError(form,'Two passwords two input is not consistent','safePassword');
	addError(form,'','safePassword_confirm');
	isError=true;
}

if(!isError){
	var data={
		hash:hash,
		agree:params.agree,
		name:params.firstname,
		country:params.country,
		email:params.email,
		from:params.how_find_us,
		from_content:params.how_find_us_other,
		invite:window.INVITE_CODE||params.invite,
		structure:params.structure,
		parent_email:params.parent_email,
		parent_mobile:params.parent_phone,
		prom_referrer:promReferer,
		site_referrer:siteReferrer,
		vcode:params.captcha_code,
		lang:params.lang,
		isRecaptcha:params.isRecaptcha,
		mobile:params.phone,
		phoneVcode:params.phoneVcode,
		password:params.password,
		safePassword:params.safePassword,
		country_code:params.country_code,
		site_id:params.site_id
	};if(params.skype){data.skype=params.skype;}
if(params.facebook){data.facebook=params.facebook;}
window.loadingLayer.show();

api.register(params.captcha_code,data).then(function(data){
	window.loadingLayer.hide();
	window.location.href=webUrl + 'new/' + url_lan +'success/';}
).fail(function(dataErr){
	window.loadingLayer.hide();
	var errTextLabel=dataErr.responseText;reloadCaptcha();
	addError(null,errTextLabel);
	});
	}};
	if(window.isSelectCountry){
		var finalCountries=[];
		for(var i in COUNTRIES){
			var country=COUNTRIES[i];
		if(countriesInSites.indexOf(country.id)===-1){
			if(country.id===130){
				country.name_eng=country.name_de;
			}
	finalCountries.push(country);}}
finalCountries.sort(function(a,b){return(a.name_eng<b.name_eng)?-1:1;}).forEach(function(country){$('<option>').attr({'value':country.id}).text(country.name_eng).appendTo($('select[name=country]'));});}
reloadCaptcha();

$('#captcha-reload-element').on('click',function(){
	reloadCaptcha();
	return false;
});
/*
if(window.INVITE_CODE){$('.register-feedback-form input[name=invite]').attr('disabled','disabled').val(window.INVITE_CODE);$('.clearInvite').addClass('inviteButton');$('.clearInvite').on('click',function(){$('.register-feedback-form input[name=invite]').removeAttr('disabled');$('.register-feedback-form input[name=invite]').val('');window.INVITE_CODE='';

$.removeCookie('i',{path:'/'});
$('.clearInvite').hide();});}else{$('.clearInvite').hide();}
if($.cookie('utm')!=undefined){promReferer=$.cookie('utm');}
if($.cookie('referrer')!=undefined){siteReferrer=$.cookie('referrer');}
*/
$('#password_input').on('keyup',function(){var password=$(this).val();var forcePassword=checkForcePassword(password);$('#password_input_simple_text').css('color',forcePassword.color).text($.langLabelTranslate(forcePassword.text,forcePassword.text))});

$('#safePassword').on('keyup',function(){
	var password=$(this).val();
	var forcePassword=checkForcePassword(password);
	$('#safe_password_input_simple_text').css('color',forcePassword.color).text($.langLabelTranslate(forcePassword.text,forcePassword.text))
});
$('#getPhoneVcode').click(function(){
	var p = $('#phone').val();
	if (p.substr(0, 3) == '+86') p = p.substr(3);
	//alert(p.length);return false;
	//
	var vcode_code = $('#vcode_code').val();
	if (vcode_code.length != 4){
		addError(false,'Verification code error','vcode_code');	
		return false;
	}
	if (p.length != 11) {
		addError(false,'Cell phone number format error','phone');
	} else {//
		$(this).prop('disabled', true);
		//webUrl = 'vipmmm.com/';
		$.ajax({
			type:'post',
			url : webUrl + 'ajax/member/sendVcode',
			data:'vcode_code='+ vcode_code +'&mobile=' + p + '&hash=' + hash,
			cache:false,
			dataType:"json",
			success: function(rs){
				$('#getPhoneVcode').prop('disabled', false);
				if (rs.status){
					$('#vcodeCode').hide();
					alert('发送成功，稍后请输入您手机收到的验证码');	
				}else {
					$('#vcodeCode').show();
					vcodeCodeRand();
					alert(rs.msg);
				}
			},
			error:function(rs){
				$('#getPhoneVcode').prop('disabled', false);
				alert(rs.responseText);
			}
		});
	}
});

$('.register-feedback-form').on('submit',function(){
	try{
		register(this);
	}catch(e){
		console.error(e);
	}finally{
		return false;
	}
});window.api=api;window.reloadCaptcha=reloadCaptcha;window.addError=addError;});