<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
define('IN_AJAX', true);
loadFunc('ajax');
$var->setNotInVal('v0', array('sendVcode', 'notShowFreezeTip'));
$rs = ajax_return_false(array('msg' => '非法操作', 'is_login' => 0));
$isFormAjax = false;
/*if (!$var->member->isLogin) {*/
	switch ($var->v0) {
		case 'login':
			$lRs = member_base::loginToForm();
			if ($lRs === false) $rs = ajax_return_false('登录失败');
			elseif ($lRs === true) $rs = ajax_return_true();
			else $rs = ajax_return_false($lRs);
		break;
		case 'sendVcode':
			if (!empty($_POST['vcode_code']) && $vcode_code = $_POST['vcode_code']){
				$img = new securimage();
				if ($img->check($vcode_code)){
					$sRs = $member->sendVcodeForm();
					if ($sRs !== false) {
						if ($sRs === true) {
							$rs = ajax_return_true();
						} else $rs = ajax_return_false($sRs);
					} else $rs = ajax_return_false('错误');
				}else $rs = ajax_return_false('验证码错误');
			} else $rs = ajax_return_false('错误');
		break;
		case 'notShowFreezeTip':
			qscms::setcookie('notShowFreezeTip', '1');
			$rs = ajax_return_true();
		break;
		default:
			$rs = ajax_return_false('用户未登录');
		break;
	}
/*} else {
	switch ($var->gp_op) {
		case 'sendVcode':
			if (form::hash()) {
				$datas = form::get3('mobilephone');
				$sRs = $member->sendVcode($datas['mobilephone']);
				if ($sRs === true) {
					$rs = ajax_return_true();
				} else $rs = ajax_return_false($sRs);
			} else $rs = ajax_return_false('错误');
		break;
		case 'payment':
			switch ($var->gp_type) {
				case 'account':
					switch ($var->gp_method) {
						case 'add':
							$res = $member->addPaymentAccountForm();
							if ($res !== false) {
								if (is_int($res)) $rs = ajax_return_true(array('id' => $res));
								else $rs = ajax_return_false($res);
							} else $rs = ajax_return_false('错误');
						break;
						case 'del':
							$dRs = $member->delPaymentAccount($var->getInt('gp_id'));
							if ($dRs === true) $rs = ajax_return_true();
							else $rs = ajax_return_false();
						break;
						case 'top':echo 123;exit;
							$cRs = $member->changePaymentAccountTop($var->getInt('gp_id'));
							if (is_numeric($cRs)) {
								if ($cRs) $rs = ajax_return_true(array('top' => true));
								else $rs = ajax_return_true(array('top' => false));
							} else $rs = ajax_return_false($rs);
						break;
					}
				break;
			}
		break;
		default:
			$rs = ajax_return_false('用户已登录');
		break;
	}
}*/
if ($isFormAjax) echo '<textarea>'.string::json_encode($rs).'</textarea>';
else echo string::json_encode($rs);
exit;
?>