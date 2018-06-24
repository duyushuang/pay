<?php !defined("IN_QSCMS")&&exit("error");$__tplUrl = '/qscms/template/admin/';echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>网站后台管理</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link href="';echo WEB_URL_S1.WDU.'static/';echo '/css/admin/login2.css?spm=v1.1pops" rel="stylesheet" type="text/css" />
<script src="';echo WEB_URL_S1.WDU.'static/';echo 'style/js/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="/qscms/static/js/form_validator.js"></script>
<script language="javascript">
$(function(){';echo '
	$(\'#myForm\').formValidator();
	if(!$(\'#username\').val())$(\'#username\').focus();
	else $(\'#password\').focus();
';echo '});
</script>
</head>
<body style="background: #f1f1f1;margin-top:7%">
<form method="post" id="myForm">
';echo $var->sys_hash_code;echo '
<input type="hidden" name="postType" value="login" />
<table width="809" border="0" height="418" align="center" class="loginBox">
<tr>
 <td><div id="step_1" class="main">
 		<div class="tip">';if (isset($error)) echo $error;echo '</div>
				<div class="main_L">
					<div class="tabbox">
						<ul id="regSpan" class="companyul">
							<li style="z-index:1000">
								<div class="label">
									<label for="email" style="padding-left:28px">登录账号：</label><input type="text" name="username" id="username" class="textinput" maxlength="16" tabindex="1" preg="null=请输入创始人帐号|创始人帐号不能为空" value="';echo $var->gp_username;echo '" /><span id="username_tip"></span>
								</div>
							</li>
							<li>
								<div class="label">
									<label for="password" style="padding-left:28px">登录密码：</label><input type="password" tabindex="2" name="password" id="password" class="textinput" maxlength="20" preg="null=请输入创始人密码|创始人密码不能为空" /><span id="password_tip"></span>

								</div>
								
							</li>
						  
							<li>
								<div class="label">
									<label for="vcode" style="padding-left:42px">验证码：</label><input type="vcode" name="vcode" id="vcode" maxlength="4" tabindex="4" class="textinput" RegStr="/^\\d{';echo '4';echo '}$/" Message="请输入四位数字验证码|请输入四位数字验证码" style="width:80px" /><span id="vcode_tip"></span>
								</div>
							</li>
							<li>
								<img id="img_vcode" src="';echo su('images/vcode.php');echo '" style="margin-left:110px;" onclick="$(this).attr({';echo 'src:\'';echo su('images/vcode.php');echo '?\'+Math.random()';echo '});$(\'#vcode\').focus();" / title="点击更换验证码"><a href="javascript:;" onclick="$(\'#img_vcode\').attr({';echo 'src:\'';echo su('images/vcode.php');echo '?\'+Math.random()';echo '});$(\'#vcode\').focus();" ></a>
							</li>
							
							<li class="btn" id="nextStep">
							  <input type="submit" tabindex="5" class="regsubmit" value="点击登录">
							</li>
						</ul>
					</div>
				</div>
				<div class="main_R">
					<div class="family">
						<img src="../img/web/1505105268115.png" style="margin-left:50px" width="173px" hight="60px">
						<div style="margin-top:20px;margin-left:50px">
							<div>创造出你所有的想象</div>
							<div>Create all of your imagination</div>
						</div>
							 
					</div>
				
				</div>
			</div>
 </td>
</tr>
</table>
</form>

<div class="foot">
	<p>&copy; 2018 www.fengbaopay.com. All rights reserved. </p>
</div>
<br/><br/><br/>

</body>
</html>

';exit();echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>倾世CMS创始人登录</title>
<link href="/qscms/static/css/admin/main.css" rel="stylesheet" type="text/css" /><link href="/qscms/static/css/admin/login.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="/qscms/static/js/form_hack.js"></script>
<script language="javascript">
$(function(){';echo '
	/*$(\'.msout\').each(function(){';echo '
		$(this).mousemove(function(){';echo '
			$(this).removeClass(\'msout\');
			$(this).addClass(\'msmove\');
		';echo '});
		$(this).mouseout(function(){';echo '
			$(this).removeClass(\'msmove\');
			$(this).addClass(\'msout\');
		';echo '})
	';echo '});*/
	if(!$(\'#username\').val())$(\'#username\').focus();
	else $(\'#password\').focus();
';echo '});
</script>
</head>

<body>
		<div class="login">
			<div class="left">
				<div class="h">倾世CMS</div>
				<div class="d">倾世CMS&mdash;&mdash;一个方便的、高速的系统</div>
			</div>
			<div class="right">
				<form method="post" enctype="application/x-www-form-urlencoded">
				';echo qscms::v('_G')->sys_hash_code;echo '
				<input type="hidden" name="postType" value="login" />
				<div>Login</div>
				';if($error){echo '<div class="show_message">';echo $error;echo '</div>';}echo '
				<table>
					<tr>
						<td>创始人：</td>
						<td><input type="text" name="username" id="username" class="msout" preg="null=请输入创始人帐号|创始人帐号不能为空" value="';echo $var->gp_username;echo '" maxlength="16" /></td>
						<td id="username_tip"></td>
					</tr>
					<tr>
						<td>密	码：</td>
						<td><input type="password" name="password" id="password" class="msout" preg="null=请输入创始人密码|创始人密码不能为空" value="" maxlength="20" /></td>
						<td id="password_tip"></td>
					</tr>
					<tr>
						<td>验证码：</td>
						<td><input type="text" name="vcode" id="vcode" maxlength="4" class="msout" RegStr="/^\\d{';echo '4';echo '}$/" Message="请输入四位数字验证码|请输入四位数字验证码" /></td>
						<td id="vcode_tip"></td>
					</tr>
					<tr>
						<td valign="top">验证码：</td>
						<td><img id="img_vcode" src="';echo su('images/vcode.php');echo '" /></td>
						<td><a href="javascript:;" onclick="$(\'#img_vcode\').attr({';echo 'src:\'';echo su('images/vcode.php');echo '?\'+Math.random()';echo '});$(\'#vcode\').focus();">看不清，换一张</a></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" value="submit" /></td>
						<td></td>
					</tr>
				</table>
				</form>
			</div>
			<div class="clear"></div>
		</div>
</body>
</html>
';?>