<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');

$apiurl = cfg::get('web', 'apiurl');
$apiurl ? $apiurl : WEB_URL;
/*
$weburl = cfg::get('web', 'weburl');
$weburl ? $weburl : WEB_URL;
echo qscms::is_weixin().'<br />';
echo IS_MODULE1.'<br />';exit;
if (qscms::is_weixin() && IS_MODULE1) { //需要微信内部支付的话 就用主域名 泛域名支付不了 原因是 微信设置的回调地址不一致 获取不了openid
	$webInfo = pathinfo($weburl);
	$apiInfo = pathinfo($apiurl);
	if ($webInfo['basename'] != $apiInfo['basename']) $apiurl = $weburl;	
}
*/
exit($apiurl);
?>