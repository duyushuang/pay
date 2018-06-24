<?php !defined("IN_JB")&&exit("error");$__tplUrl = '/js_lib/xheditor/php/tpl/';echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<title>';echo $menus[$type];echo '</title>
<style>
body{';echo '
	padding:5px;
	margin:0px;
	font-size:12px;
	background:#EEEEEE
';echo '}
.box{';echo '
	width:100%;
	text-align:center;
';echo '}
.box .pics{';echo '
	width:300px;
	white-space:nowrap; 
	overflow-x:scroll;
';echo '}
#ids{';echo '
	margin-top:30px;
	width:250px;
	height:20px;
';echo '}
</style>
<script>
function upload()
{';echo '
	callback(';echo string::json_encode($list);echo ');
';echo '}
function closewindow()
{';echo '
	unloadme();
';echo '}
var $=function(id){';echo 'return document.getElementById(id);';echo '}
var checkForm = function(){';echo '
	var ids = $(\'ids\').value.replace(/^\\s+|\\s+$/g, \'\');
	if (/\\d+(?:(?:,|，)\\d+)*/.test(ids)) {';echo '
		return true;
	';echo '} else {';echo '
		alert(\'请输入图片ID，多个ID用逗号隔开\');
		$(\'ids\').focus();
		return false;
	';echo '}
';echo '}
</script>
</head> 
<body> 
	<div class="box">';if($list){echo '
		<div class="pics">
		';foreach ($list as $k=>$v){echo '
		<img src="';echo $v['tiny'];echo '" />
		';}echo '
		</div>
		<br />
		<input type="button" value="插入到编辑器" onclick="upload()" />
		';}echo '
		<form enctype="application/x-www-form-urlencoded" method="post" onsubmit="return checkForm()">';echo $sys_hash_code;echo '
			要插入的ID：<input type="text" name="ids" id="ids" value="';echo $ids;echo '" /><input type="submit" value="插入" />
		</form>
	</div>
</body> 
</html>';?>