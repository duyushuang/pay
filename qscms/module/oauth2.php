<?php
$openid = '';
$goto = $var->gp_goto;
$goto || $goto = qscms::getUrl('/');//默认主页
$code = !empty($_GET['code']) ? $_GET['code'] : '';
if ($code){
	$wxObj = new weixin();
	$get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $wxObj->appid . '&secret=' . $wxObj->appsecret . '&code=' . $code . '&grant_type=authorization_code';
	/*$get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$wxObj->appid.'&secret='.$wxObj->appsecret.'&code='.$code.'&grant_type=authorization_code';
	*/
	$json = weixin::curl_get_contents($get_token_url);
	$arr =json_decode($json, true);
	$wxid = $arr['openid'];
	if ($wxid && db::exists('member', "wxid='$wxid'")){
		member_base::wxLogin($wxid);
	}elseif ($wxid){
		member_base::wxReg($wxid, '', true);
	}
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ".$goto);
	exit;
}
header("Location: ".$goto);
?>