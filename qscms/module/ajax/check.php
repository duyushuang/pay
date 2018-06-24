<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
loadFunc('ajax');
$var->setNotInVal('p2', array('email', 'nickname', 'vcode', 'mobile', 'mobilephone'));
$rs = ajax_return_false('非法操作');

switch ($var->p2) {
	case 'email':
		if (form::hash()) {
			extract(form::get3('email'));
			$status = member_base::emailExists($email);
			if ($status === true) $rs = ajax_return_false(array('msg' => '邮箱已存在'));
			elseif ($status !== false) $rs = ajax_return_false(array('msg' => $status));
			else $rs = ajax_return_true();
		}
	break;
	case 'mobile':
		if (form::hash()) {
			extract(form::get3('mobile'));
			$status = member_base::mobileExists($mobile);
			if ($status === true) $rs = ajax_return_false(array('msg' => '手机号已存在'));
			elseif ($status !== false) $rs = ajax_return_false(array('msg' => $status));
			else $rs = ajax_return_true();
		}
	break;
	case 'mobilephone':
		if (form::hash()) {
			extract(form::get3('mobilephone'));
			$status = member_base::mobileExists($mobilephone);
			if ($status === true) $rs = ajax_return_false(array('msg' => '手机号已存在'));
			elseif ($status !== false) $rs = ajax_return_false(array('msg' => $status));
			else $rs = ajax_return_true();
		}
	break;
	case 'nickname':
		if (form::hash()) {
			extract(form::get3('nickname'));
			$status = member_base::nicknameExists($nickname);
			if ($status === true) $rs = ajax_return_false(array('msg' => '名称已存在'));
			elseif ($status !== false) $rs = ajax_return_false(array('msg' => $status));
			else $rs = ajax_return_true();
		}
	break;
	case 'vcode';
		if (form::hash()) {
			//securimage::checkForm(false)
			if(form::checkVcode()) $rs = ajax_return_true();
			else $rs =ajax_return_false(array('msg' => '验证码错误'));
			//$rs = ajax_return_true(array('checkStatus' => securimage::checkForm(false)));
		}
	break;
}
echo string::json_encode($rs);
exit;
?>