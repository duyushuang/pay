<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');
$id = (int)$var->p1;
//qscms::showMessage(123123123);
if ($id){
	if (!$member) qscms::gotoUrl('/users/login');
	$id = (int)$var->p1;
	$rs = $member->buyVip($id);
	qscms::showMessage($rs === true ? '购买成功' : $rs, qscms::getUrl('/vip'));
}
?>